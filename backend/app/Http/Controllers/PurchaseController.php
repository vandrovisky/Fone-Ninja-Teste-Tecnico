<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Services\PurchaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class PurchaseController extends Controller
{
    protected $purchaseService;

    public function __construct(PurchaseService $purchaseService)
    {
        $this->purchaseService = $purchaseService;
    }

    public function index()
    {
        return response()->json(Purchase::with('items.product')->latest()->get());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fornecedor'               => 'required|string',
            'produtos'                 => 'required|array|min:1',
            'produtos.*.id'            => 'required|exists:products,id',
            'produtos.*.quantidade'    => 'required|integer|min:1',
            'produtos.*.preco_unitario'=> 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $purchase = $this->purchaseService->createPurchase($request->all());
            return response()->json($purchase, 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
