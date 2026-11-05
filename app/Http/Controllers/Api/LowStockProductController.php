<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
class LowStockProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'threshold' => ['sometimes', 'integer', 'min:0'],
        ]);

        $threshold = (int) ($validated['threshold'] ?? 5);

        $products = Product::where('stock', '<=', $threshold)
            ->orderBy('stock')
            ->get();

        return response()->json([
            'threshold' => $threshold,
            'products' => ProductResource::collection($products),
        ]);
    }
}
