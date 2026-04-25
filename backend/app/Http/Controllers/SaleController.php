<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class SaleController extends Controller
{
    protected $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);

        $paginated = Sale::with('items.product')->latest()->paginate($perPage);

        $paginated->getCollection()->transform(fn($sale) => [
            'id'           => $sale->id,
            'client'       => $sale->customer,
            'total_value'  => (float) $sale->total,
            'total_profit' => (float) $sale->profit,
            'created_at'   => $sale->created_at,
            'items'        => $sale->items->map(fn($item) => [
                'id'            => $item->id,
                'product_id'    => $item->product_id,
                'product_name'  => $item->product?->name,
                'quantity'      => $item->quantity,
                'unit_price'    => (float) $item->unit_price,
                'average_cost_at_sale' => (float) $item->average_cost_at_sale,
            ]),
        ]);

        return response()->json($paginated);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cliente'                  => 'required|string',
            'produtos'                 => 'required|array|min:1',
            'produtos.*.id'            => 'required|exists:products,id',
            'produtos.*.quantidade'    => 'required|integer|min:1',
            'produtos.*.preco_unitario'=> 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $sale = $this->saleService->createSale($request->all());
            return response()->json([
                'id'          => $sale->id,
                'total_venda' => (float) $sale->total,
                'lucro_total' => (float) $sale->profit,
                'message'     => 'Venda registrada com sucesso!',
            ], 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $result = $this->saleService->cancelSale((int) $id);
            return response()->json($result);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
