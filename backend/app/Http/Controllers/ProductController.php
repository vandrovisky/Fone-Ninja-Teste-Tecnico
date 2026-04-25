<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);

        $paginated = Product::orderBy('name')->paginate($perPage);

        $paginated->getCollection()->transform(fn($p) => [
            'id'          => $p->id,
            'nome'        => $p->name,
            'custo_medio' => (float) $p->average_cost,
            'preco_venda' => (float) $p->sale_price,
            'estoque'     => $p->current_stock,
        ]);

        return response()->json($paginated);
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
            'custo_medio' => (float) $product->average_cost,
            'preco_venda' => (float) $product->sale_price,
            'estoque'     => $product->current_stock,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nome'        => 'required|string|min:3|unique:products,name,' . $product->id,
            'preco_venda' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product->update([
            'name'       => $request->nome,
            'sale_price' => $request->preco_venda,
        ]);

        return response()->json([
            'id'          => $product->id,
            'nome'        => $product->name,
            'custo_medio' => (float) $product->average_cost,
            'preco_venda' => (float) $product->sale_price,
            'estoque'     => $product->current_stock,
        ]);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->current_stock > 0) {
            return response()->json([
                'message' => 'Não é possível excluir um produto com estoque.'
            ], 422);
        }

        if ($product->saleItems()->exists() || $product->purchaseItems()->exists()) {
            return response()->json([
                'message' => 'Não é possível excluir um produto com histórico de movimentações.'
            ], 422);
        }

        $product->delete();

        return response()->json(['message' => 'Produto excluído com sucesso.']);
    }
}
