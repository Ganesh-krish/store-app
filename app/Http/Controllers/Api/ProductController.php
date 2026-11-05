<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['products' => ProductResource::collection(Product::orderBy('name')->get())]);
    }
}
