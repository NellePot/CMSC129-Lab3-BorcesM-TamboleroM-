<?php

namespace App\Http\Controllers;

use App\Services\GeminiService;
use Illuminate\Http\Request;

class AIAssistantController extends Controller
{
    protected $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
            'history' => 'nullable|array',
        ]);

        $message = $request->message;

        $history = session()->get('assistant_history', []);

        $reply = $this->gemini->ask($message, $history, isAssistant: true);

        $history[] = ['role' => 'user', 'text' => $message];
        $history[] = ['role' => 'model', 'text' => $reply];

        $history = array_slice($history, -10);

        session()->put('assistant_history', $history);

        return response()->json(['reply' => $reply]);
    }
}