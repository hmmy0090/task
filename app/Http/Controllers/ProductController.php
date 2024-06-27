<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return ProductResource::collection(Product::paginate());
    }

    public function store(Request $request): ProductResource
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'categories' => 'array|exists:categories,id',
        ]);

        $product = Product::create($validated);
        if ($request->has('categories')) {
            $product->categories()->sync($request->input('categories'));
        }

        return new ProductResource($product);
    }

    public function show(int $id): ProductResource
    {
        $product = Product::findOrFail($id);
        return new ProductResource($product);
    }

    public function update(Request $request, int $id): ProductResource
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'categories' => 'array|exists:categories,id',
        ]);

        $product->update($validated);
        if ($request->has('categories')) {
            $product->categories()->sync($request->input('categories'));
        }

        return new ProductResource($product);
    }

    public function destroy(int $id): \Illuminate\Http\JsonResponse
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(null, 204);
    }
    
    public function assignCategories(Request $request, Product $product)
    {
        $validated = $request->validate([
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
        ]);

        $product->categories()->sync($validated['categories']);

        return response()->json([
            'data' => $product->load('categories'),
        ], 200);
    }
}
