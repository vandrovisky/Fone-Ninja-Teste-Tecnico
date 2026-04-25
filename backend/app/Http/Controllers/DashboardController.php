<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalSalesValue = (float) Sale::sum('total');
        $totalPurchasesValue = (float) Purchase::sum('total_value');
        $totalProfit = (float) Sale::sum('profit');

        $recentSales = Sale::with('items.product')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($sale) => [
                'id' => $sale->id,
                'client' => $sale->customer,
                'total_value' => (float) $sale->total,
                'profit' => (float) $sale->profit,
                'created_at' => $sale->created_at,
                'items_count' => $sale->items->count(),
            ]);

        $lowStockProducts = Product::where('current_stock', '<=', 10)
            ->orderBy('current_stock')
            ->take(10)
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'nome' => $p->name,
                'estoque' => $p->current_stock,
                'preco_venda' => (float) $p->sale_price,
            ]);

        return response()->json([
            'total_products' => $totalProducts,
            'total_sales' => $totalSalesValue,
            'total_purchases' => $totalPurchasesValue,
            'total_profit' => $totalProfit,
            'recent_sales' => $recentSales,
            'low_stock_products' => $lowStockProducts,
        ]);
    }
}
