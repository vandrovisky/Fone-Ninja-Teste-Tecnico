<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use Exception;

class SaleService
{
    public function createSale(array $data)
    {
        return DB::transaction(function () use ($data) {
            $sale = Sale::create([
                'customer' => $data['cliente'],
            ]);

            $totalValue = 0;
            $totalProfit = 0;

            foreach ($data['produtos'] as $itemData) {
                $product = Product::findOrFail($itemData['id']);
                
                $quantity = $itemData['quantidade'];
                $unitPrice = $itemData['preco_unitario'];
                
                if ($product->current_stock < $quantity) {
                    throw new Exception("Estoque insuficiente para o produto: {$product->name}");
                }

                $subtotal = $quantity * $unitPrice;
                $profit = ($unitPrice - $product->average_cost) * $quantity;

                // Update stock
                $product->decrement('current_stock', $quantity);

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'average_cost_at_sale' => $product->average_cost,
                ]);

                $totalValue += $subtotal;
                $totalProfit += $profit;
            }

            $sale->update([
                'total' => $totalValue,
                'profit' => $totalProfit,
            ]);

            return $sale->load('items.product');
        });
    }

    public function cancelSale(int $id): array
    {
        return DB::transaction(function () use ($id) {
            $sale = Sale::with('items')->findOrFail($id);

            foreach ($sale->items as $item) {
                $item->product->increment('current_stock', $item->quantity);
            }

            $sale->delete();

            return ['message' => 'Venda cancelada com sucesso'];
        });
    }
}
