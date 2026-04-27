<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class FunctionCallService
{
    public function __construct(protected InventoryQueryService $inventory)
    {
    }

    public function execute($functionCall): string
    {
        $name = $functionCall->name;
        $args = $functionCall->args ?? [];

        Log::info('Executing function call', ['name' => $name, 'args' => $args]);

        $result = match ($name) {

            'get_stock_summary' =>
                $this->inventory->getStockSummary(),

            'get_low_stock_items' =>
                $this->inventory->getLowStockItems(),

            'get_item_quantity' =>
                !empty($args['item_name'])
                    ? $this->inventory->getItemQuantity($args['item_name'])
                    : ['error' => 'Item name is required to look up quantity.'],

            'get_expiring_items' =>
                $this->inventory->getExpiringItems(
                    isset($args['days']) ? (int) $args['days'] : 30
                ),

            'get_category_count' =>
                !empty($args['category'])
                    ? $this->inventory->getCategoryCount($args['category'])
                    : ['error' => 'Category name is required.'],
            
            'create_inventory_item' =>
                $this->inventory->createItem($args),
            
            'update_inventory_item' =>
                !empty($args['name'])
                    ? $this->inventory->updateItem($args['name'], [
                        'quantity'        => $args['quantity'] ?? null,
                        'minimum_stock'   => $args['minimum_stock'] ?? null,
                        'category'        => $args['category'] ?? null,
                        'expiration_date' => $args['expiration_date'] ?? null,
                    ])
                    : ['error' => 'Item name is required to update.'],
                    
            'delete_inventory_item' =>
                (!empty($args['name']) && !empty($args['confirmed']) && $args['confirmed'] === true)
                    ? $this->inventory->deleteItem($args['name'])
                    : ['message' => 'Deletion cancelled or not yet confirmed.'],

            default => ['error' => "Unknown function: {$name}"],
        };

        return json_encode($result);
    }
}
