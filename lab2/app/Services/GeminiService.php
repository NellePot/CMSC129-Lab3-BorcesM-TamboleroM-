<?php

namespace App\Services;

use Gemini\Data\Content;
use Gemini\Data\Part;
use Gemini\Enums\Role;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    public function __construct(
        protected PromptService $prompt,
        protected FunctionCallService $functionCall
    ) {}

    public function ask(string $message, array $history = [], bool $isAssistant = false): string
    {
        try {
            // Handle simple queries locally — no API call needed
            $localResponses = [
                'hi'               => "Hello! I'm your inventory assistant. How can I help you?",
                'hello'            => "Hello! I'm your inventory assistant. How can I help you?",
                'hey'              => "Hello! I'm your inventory assistant. How can I help you?",
                'good morning'     => "Good morning! How can I help you with your inventory today?",
                'good afternoon'   => "Good afternoon! How can I help you with your inventory today?",
                'good evening'     => "Good evening! How can I help you with your inventory today?",
                'what can you do'  => "I can check stock levels, expiring items, and inventory summaries!",
            ];

            $trimmed = strtolower(trim($message));
            if (isset($localResponses[$trimmed])) {
                return $localResponses[$trimmed];
            }

            // Cache repeated identical queries for 5 minutes
            $cacheKey = 'gemini_' . md5($message . ($isAssistant ? '_assistant' : '_user'));
            if (cache()->has($cacheKey)) {
                Log::info('Gemini cache hit', ['message' => $message]);
                return cache()->get($cacheKey);
            }

            $systemInstruction = $isAssistant
                ? $this->prompt->assistantSystemInstruction()
                : $this->prompt->systemInstruction();

            $tools = $isAssistant
                ? $this->prompt->getAssistantTools()
                : $this->prompt->getTools();

            $modelName = config('gemini.model', 'models/gemini-2.5-flash');
            $model = Gemini::generativeModel($modelName)
                ->withSystemInstruction($systemInstruction)
                ->withTool($tools);

            // Build contents once — this is reused across loop iterations
            // so history is NOT resent on every function call cycle
            $contents = [];

            foreach ($history as $msg) {
                $role = $msg['role'] === 'model' ? Role::MODEL : Role::USER;
                $contents[] = Content::parse($msg['text'], $role);
            }

            $contents[] = Content::parse($message, Role::USER);

            // First API call
            $response = $model->generateContent(...$contents);

            // Handle function calls — reduced to 2 iterations max
            // (chained calls beyond 2 are rare and waste quota)
            $maxIterations = 2;
            $i = 0;

            while ($i++ < $maxIterations) {
                $functionCallPart = null;

                foreach ($response->parts() as $part) {
                    if ($part->functionCall !== null) {
                        $functionCallPart = $part->functionCall;
                        break;
                    }
                }

                if ($functionCallPart === null) {
                    break; // No function call — we have our final text response
                }

                Log::info('Executing function call', [
                    'name' => $functionCallPart->name,
                    'args' => $functionCallPart->args,
                ]);

                $result = $this->functionCall->execute($functionCallPart);

                // Append model's function call turn to existing $contents
                // (no re-sending history — $contents already has it)
                $contents[] = new Content(
                    parts: $response->parts(),
                    role: Role::MODEL
                );

                // Append function result using proper Part::fromFunctionResponse
                // This is the correct format Gemini expects for function responses
                $contents[] = new Content(
                    parts: [
                        new Part(
                            functionResponse: new \Gemini\Data\FunctionResponse(
                                name: $functionCallPart->name,
                                response: json_decode($result, true) ?? []
                            )
                        )
                    ],
                    role: Role::USER
                );

                // Next API call — only sends $contents (history not duplicated)
                $response = $model->generateContent(...$contents);
            }

            $finalResponse = $response->text() ?? "Sorry, I couldn't process that.";

            // Cache the final response
            cache()->put($cacheKey, $finalResponse, now()->addMinutes(5));

            return $finalResponse;

        } catch (\Exception $e) {
            Log::error('GeminiService error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            // More helpful error message for overload vs real errors
            if (str_contains($e->getMessage(), 'high demand')) {
                return "Gemini is currently busy. Please try again in a few seconds.";
            }

            return "Sorry, I couldn't process that.";
        }
    }
}
