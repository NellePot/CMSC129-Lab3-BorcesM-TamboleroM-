<?php

namespace App\Services;

use App\Models\Inventory;

class InventoryQueryService
{
    public function getStockSummary(): array
    {
        $totalItems = Inventory::count();
        $totalQuantity = Inventory::sum('quantity');
        $outOfStock = Inventory::where('quantity', '<=', 0)->count();
        $lowStock = Inventory::whereColumn('quantity', '<=', 'minimum_stock')->where('quantity', '>', 0)->count();
        $inStock = Inventory::where('quantity', '>', 0)->whereColumn('quantity', '>', 'minimum_stock')->count();

        return [
            'totalItems' => $totalItems,
            'totalQuantity' => $totalQuantity,
            'outOfStock' => $outOfStock,
            'lowStock' => $lowStock,
            'inStock' => $inStock,
        ];
    }

    public function getLowStockItems(): array
    {
        $items = Inventory::whereColumn('quantity', '<=', 'minimum_stock')
            ->orderBy('quantity', 'asc')
            ->limit(10)
            ->get(['name', 'category', 'quantity', 'minimum_stock']);

        return [
            'items' => $items->toArray(),
            'count' => $items->count(),
        ];
    }

    public function getItemQuantity(string $itemName): array
    {
        $item = Inventory::where('name', 'like', '%' . $itemName . '%')
            ->first(['name', 'category', 'quantity']);

        if (!$item) {
            return ['error' => 'Item not found'];
        }

        return [
            'name' => $item->name,
            'quantity' => $item->quantity,
            'category' => $item->category,
        ];
    }

    public function getExpiringItems(int $days = 30): array
    {
        $items = Inventory::whereNotNull('expiration_date')
            ->whereDate('expiration_date', '<=', now()->addDays($days))
            ->whereDate('expiration_date', '>=', now())
            ->orderBy('expiration_date', 'asc')
            ->limit(10)
            ->get(['name', 'category', 'quantity', 'expiration_date']);

        return [
            'items' => $items->toArray(),
            'count' => $items->count(),
            'days_window' => $days,
        ];
    }

    public function getCategoryCount(string $category): array
    {
        $count = Inventory::where('category', 'like', '%' . $category . '%')->count();
        $totalQuantity = Inventory::where('category', 'like', '%' . $category . '%')->sum('quantity');
        $lowStock = Inventory::where('category', 'like', '%' . $category . '%')
            ->whereColumn('quantity', '<=', 'minimum_stock')
            ->count();

        if ($count === 0) {
            return ['error' => "No items found in category: {$category}"];
        }

        return [
            'category' => $category,
            'itemCount' => $count,
            'totalQuantity' => $totalQuantity,
            'lowStock' => $lowStock,
        ];
    }

    public function createItem(array $data): array
    {
        try {
            $item = Inventory::create([
                'name' => $data['name'],
                'quantity' => (int) $data['quantity'],
                'minimum_stock' => (int) ($data['minimum_stock'] ?? 5),
                'expiration_date' => $data['expiration_date'] ?? null,
                'category' => $data['category'] ?? null,
            ]);

            return [
                'success' => true,
                'message' => "Successfully added '{$item->name}' to inventory.",
                'item'    => [
                    'name'          => $item->name,
                    'category'      => $item->category,
                    'quantity'      => $item->quantity,
                    'minimum_stock' => $item->minimum_stock,
                ],
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Failed to create: ' . $e->getMessage()];
        }
    }

    public function updateItem(string $name, array $fields): array
    {
        $item = Inventory::where('name', 'ILIKE', "%{$name}%")->first();

        if (!$item) {
            return ['success' => false, 'error' => 'Item not found'];
        }

        $updateData = array_filter($fields, fn($v) => $v !== null);
        $item->update($updateData);
        $item->refresh();

        return [
            'success' => true,
            'message' => "Successfully updated '{$item->name}'.",
            'update_fields' => array_keys($updateData),
            'item'    => [
                'name'          => $item->name,
                'category'      => $item->category,
                'quantity'      => $item->quantity,
                'minimum_stock' => $item->minimum_stock,
            ]
        ];
    }

    public function deleteItem(string $name): array
    {
        $item = Inventory::where('name', 'ILIKE', "%{$name}%")->first();

        if (!$item) {
            return ['success' => false, 'error' => 'Item not found'];
        }

        $itemName = $item->name;
        $item->delete();

        return [
            'success' => true,
            'message' => "Successfully deleted '{$itemName}' from inventory.",
        ];
    }

}
