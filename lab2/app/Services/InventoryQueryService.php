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
        $item = Inventory::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($itemName) . '%'])
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
        $items = Inventory::whereRaw('LOWER(category) LIKE ?', ['%' . strtolower($category) . '%'])
            ->orderBy('name', 'asc')
            ->limit(20)
            ->get();

        if ($items->isEmpty()) {
            return ['error' => "No items found in category: {$category}"];
        }

        $count = $items->count();
        $totalQuantity = $items->sum('quantity');
        $lowStock = $items->filter(function ($item) {
            return $item->quantity <= $item->minimum_stock;
        })->count();

        $itemsList = [];
        foreach ($items as $item) {
            $itemsList[] = [
                'name' => $item->name,
                'quantity' => $item->quantity,
                'minimum_stock' => $item->minimum_stock,
                'is_low_stock' => $item->quantity <= $item->minimum_stock,
                'expiration_date' => $item->expiration_date ? $item->expiration_date->format('Y-m-d') : null,
            ];
        }

        return [
            'category' => $category,
            'itemCount' => $count,
            'totalQuantity' => $totalQuantity,
            'lowStock' => $lowStock,
            'items' => $itemsList,
        ];
    }

    public function getItemExpiryStatus(string $itemName): array
    {
        $item = Inventory::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($itemName) . '%'])
            ->first(['name', 'category', 'expiration_date']);

        if (!$item) {
            return ['error' => 'Item not found'];
        }

        $status = $item->expiry_status; // Uses the model attribute

        $message = match ($status) {
            'expired' => 'This item has already expired.',
            'warning' => 'This item is expiring soon (within 5 months).',
            'safe' => 'This item is safe and not expiring soon.',
            default => 'Expiry status unknown.'
        };

        return [
            'name' => $item->name,
            'expiry_status' => $status,
            'expiration_date' => $item->expiration_date?->format('Y-m-d'),
            'message' => $message,
        ];
    }

    public function getExpiredItems(): array
    {
        $items = Inventory::whereNotNull('expiration_date')
            ->whereDate('expiration_date', '<', now())
            ->orderBy('expiration_date', 'desc')
            ->limit(10)
            ->get(['name', 'category', 'quantity', 'expiration_date']);

        if ($items->isEmpty()) {
            return [
                'items' => [],
                'count' => 0,
                'message' => 'No expired items found.'
            ];
        }

        return [
            'items' => $items->toArray(),
            'count' => $items->count(),
            'message' => "Found {$items->count()} expired item(s) that need to be discarded."
        ];
    }
}
