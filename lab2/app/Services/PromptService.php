<?php

namespace App\Services;

use Gemini\Data\Content;
use Gemini\Data\FunctionDeclaration;
use Gemini\Data\Schema;
use Gemini\Data\Tool;
use Gemini\Enums\DataType;

class PromptService
{
    public function systemInstruction(): Content
    {
        return Content::parse("
        Your job is to help users by answering questions about inventory data.

        IMPORTANT RULES:
        - ALWAYS use the provided functions when the question involves inventory data.
        - DO NOT guess or fabricate inventory values.
        - If the question is unclear, ask for clarification.
        - If no function applies, respond conversationally.

        You can handle:
        - Overall stock summaries (total items, quantities, out-of-stock counts)
        - Low stock and critical items that need restocking
        - Items expiring soon (within a configurable number of days)
        - Inventory breakdown by category
        - Specific item quantities by name

        Keep responses clear, concise, and user-friendly.
        When presenting lists, format them in a readable way.
        If a function returns an error, explain it clearly to the user.
        ");
    }

    //instructions for CRUD operations for AI assistant 
    public function assistantSystemInstruction(): Content
    {
        return Content::parse("
        You are an AI assistant for an Emergency Inventory Management System.
        You can answer questions AND perform CRUD operations on inventory data.

        IMPORTANT RULES:
        - ALWAYS use the provided functions when the question involves inventory data.
        - DO NOT guess or fabricate inventory values.
        - If the question is unclear, ask for clarification.

        FOR CREATE:
        - Extract name, category, quantity, minimum_stock, and expiration_date.
        - If minimum_stock is not mentioned, use 5 as default.
        - Confirm what was created after the operation.

        FOR UPDATE:
        - Find the item by name and only update the fields the user mentioned.
        - Confirm what was changed after updating.

        FOR DELETE (VERY IMPORTANT):
        - NEVER call delete_inventory_item unless the user has explicitly said yes/confirm/proceed.
        - When a user asks to delete, respond with: 'Are you sure you want to delete [item name]? Reply yes to confirm.'
        - Only call delete_inventory_item with confirmed=true AFTER they say yes.

        After any CRUD operation, summarize what was done clearly.
        Keep responses clear, concise, and user-friendly.
        ");
    }

    //for inquiry only 
    public function getTools(): Tool
    {
        return new Tool(functionDeclarations: [

            new FunctionDeclaration(
                name: 'get_stock_summary',
                description: 'Returns overall inventory statistics including total items, total quantity, out-of-stock count, and low stock count. Use when the user asks for an inventory overview or summary.',
                parameters: new Schema(type: DataType::OBJECT)
            ),

            new FunctionDeclaration(
                name: 'get_low_stock_items',
                description: 'Returns items that are at or below their minimum stock level and need restocking. Use when the user asks about low stock, critical items, or what needs to be reordered.',
                parameters: new Schema(type: DataType::OBJECT)
            ),

            new FunctionDeclaration(
                name: 'get_item_quantity',
                description: 'Returns the current quantity of a specific inventory item by name. Use when the user asks how many of a specific item are in stock.',
                parameters: new Schema(
                    type: DataType::OBJECT,
                    properties: [
                        'item_name' => new Schema(
                            type: DataType::STRING,
                            description: 'The name or partial name of the inventory item to look up'
                        ),
                    ],
                    required: ['item_name']
                )
            ),

            new FunctionDeclaration(
                name: 'get_expiring_items',
                description: 'Returns items that are expiring within a given number of days. Use when the user asks about expiring items, items about to expire, or expiration dates.',
                parameters: new Schema(
                    type: DataType::OBJECT,
                    properties: [
                        'days' => new Schema(
                            type: DataType::INTEGER,
                            description: 'Number of days to look ahead for expiring items. Defaults to 30 if not specified.'
                        ),
                    ]
                )
            ),

            new FunctionDeclaration(
                name: 'get_category_count',
                description: 'Returns item count, total quantity, and low stock count for a specific inventory category. Use when the user asks about a specific product category.',
                parameters: new Schema(
                    type: DataType::OBJECT,
                    properties: [
                        'category' => new Schema(
                            type: DataType::STRING,
                            description: 'The category name to look up'
                        ),
                    ],
                    required: ['category']
                )
            ),

        ]);
    }

    public function getAssistantTools(): Tool
    {
        return new Tool(functionDeclarations: [

            new FunctionDeclaration(
                name: 'get_stock_summary',
                description: 'Returns overall inventory statistics including total items, total quantity, out-of-stock count, and low stock count. Use when the user asks for an inventory overview or summary.',
                parameters: new Schema(type: DataType::OBJECT)
            ),

            new FunctionDeclaration(
                name: 'get_low_stock_items',
                description: 'Returns items that are at or below their minimum stock level and need restocking. Use when the user asks about low stock, critical items, or what needs to be reordered.',
                parameters: new Schema(type: DataType::OBJECT)
            ),

            new FunctionDeclaration(
                name: 'get_item_quantity',
                description: 'Returns the current quantity of a specific inventory item by name. Use when the user asks how many of a specific item are in stock.',
                parameters: new Schema(
                    type: DataType::OBJECT,
                    properties: [
                        'item_name' => new Schema(
                            type: DataType::STRING,
                            description: 'The name or partial name of the inventory item to look up'
                        ),
                    ],
                    required: ['item_name']
                )
            ),

            new FunctionDeclaration(
                name: 'get_expiring_items',
                description: 'Returns items that are expiring within a given number of days. Use when the user asks about expiring items, items about to expire, or expiration dates.',
                parameters: new Schema(
                    type: DataType::OBJECT,
                    properties: [
                        'days' => new Schema(
                            type: DataType::INTEGER,
                            description: 'Number of days to look ahead for expiring items. Defaults to 30 if not specified.'
                        ),
                    ]
                )
            ),

            new FunctionDeclaration(
                name: 'get_category_count',
                description: 'Returns item count, total quantity, and low stock count for a specific inventory category. Use when the user asks about a specific product category.',
                parameters: new Schema(
                    type: DataType::OBJECT,
                    properties: [
                        'category' => new Schema(
                            type: DataType::STRING,
                            description: 'The category name to look up'
                        ),
                    ],
                    required: ['category']
                )
            ),

            new FunctionDeclaration(
                name: 'create_inventory_item',
                description: 'Creates a new inventory item with the given details. Use when the user wants to add a new item to the inventory.',
                parameters: new Schema(
                    type: DataType::OBJECT,
                    properties: [
                        'name' => new Schema(type: DataType::STRING, description: 'Name of the inventory item'),
                        'category' => new Schema(type: DataType::STRING, description: 'Category of the inventory item'),
                        'quantity' => new Schema(type: DataType::INTEGER, description: 'Quantity of the inventory item'),
                        'minimum_stock' => new Schema(type: DataType::INTEGER, description: 'Minimum stock level for the item'),
                        'expiration_date' => new Schema(type: DataType::STRING, description: 'Expiration date in YYYY-MM-DD format (optional)'),
                    ],
                    required: ['name', 'category', 'quantity']
                )
            ),

            new FunctionDeclaration(
                name: 'update_inventory_item',
                description: 'Updates an existing inventory item with the given details. Use when the user wants to modify an existing item in the inventory.',
                parameters: new Schema(
                    type: DataType::OBJECT,
                    properties: [
                        'name' => new Schema(type: DataType::STRING, description: 'Name of the inventory item to update'),
                        'category' => new Schema(type: DataType::STRING, description: 'New category of the inventory item (optional)'),
                        'quantity' => new Schema(type: DataType::INTEGER, description: 'New quantity of the inventory item (optional)'),
                        'minimum_stock' => new Schema(type: DataType::INTEGER, description: 'New minimum stock level for the item (optional)'),
                        'expiration_date' => new Schema(type: DataType::STRING, description: 'New expiration date in YYYY-MM-DD format (optional)'),
                    ],
                    required: ['name']
                )
            ),

            new FunctionDeclaration(
                name: 'delete_inventory_item',
                description: 'Deletes an inventory item by name. Use when the user wants to remove an item from the inventory. This action should only be performed after explicit confirmation from the user.',
                parameters: new Schema(
                    type: DataType::OBJECT,
                    properties: [
                        'name' => new Schema(type: DataType::STRING, description: 'Name of the inventory item to delete'),
                        'confirmed' => new Schema(type: DataType::BOOLEAN, description: 'Confirmation flag that must be true to proceed with deletion'),
                    ],
                    required: ['name', 'confirmed']
                )
            ),

        ]);
    }
}
