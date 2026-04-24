<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public function createPurchase(array $data)
    {
        return DB::transaction(function () use ($data) {
            $purchase = Purchase::create([
                'supplier' => $data['fornecedor'],
            ]);

            $totalValue = 0;

            foreach ($data['produtos'] as $itemData) {
                $product = Product::findOrFail($itemData['id']);
                
                $quantity = $itemData['quantidade'];
                $unitPrice = $itemData['preco_unitario'];
                $subtotal = $quantity * $unitPrice;
                
                // Update Product average cost and stock
                $currentStock = $product->current_stock;
                $oldCost = $product->average_cost;
                
                $newTotalValue = ($currentStock * $oldCost) + $subtotal;
                $newTotalQuantity = $currentStock + $quantity;
                
                $newAverageCost = $newTotalValue / $newTotalQuantity;
                
                $product->update([
                    'average_cost' => $newAverageCost,
                    'current_stock' => $newTotalQuantity,
                ]);

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                ]);

                $totalValue += $subtotal;
            }

            $purchase->update(['total_value' => $totalValue]);

            return $purchase->load('items.product');
        });
    }
}
