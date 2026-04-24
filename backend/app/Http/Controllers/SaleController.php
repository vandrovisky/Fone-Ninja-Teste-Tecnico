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

    public function index()
    {
        return response()->json(Sale::with('items.product')->latest()->get());
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
            return response()->json($sale, 201);
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
