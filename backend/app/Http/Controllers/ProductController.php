<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all()->map(fn($p) => [
            'id'          => $p->id,
            'nome'        => $p->name,
            'custo_medio' => $p->average_cost,
            'preco_venda' => $p->sale_price,
            'estoque'     => $p->current_stock,
        ]);

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome'        => 'required|string|min:3|unique:products,name',
            'preco_venda' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = Product::create([
            'name'          => $request->nome,
            'sale_price'    => $request->preco_venda,
            'current_stock' => 0,
            'average_cost'  => 0,
        ]);

        return response()->json([
            'id'          => $product->id,
            'nome'        => $product->name,
            'custo_medio' => $product->average_cost,
            'preco_venda' => $product->sale_price,
            'estoque'     => $product->current_stock,
        ], 201);
    }
}
