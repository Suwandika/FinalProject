<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreUpdateProductRequest;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $validSortColumns = ['name', 'price', 'created_at'];
        $validSortDirections = ['asc', 'desc'];

        $products = Product::with('brand')
            ->when($request->keyword, function ($query, $keyword) {
                return $query->where('name', 'like', '%' . $keyword . '%');
            })
            ->when($request->sort_by, function ($query, $sortBy) use ($request, $validSortColumns, $validSortDirections) {
                if (in_array($sortBy, $validSortColumns)) {
                    $sortDirection = $request->get('sort_direction', 'asc');
                    if (in_array($sortDirection, $validSortDirections)) {
                        return $query->orderBy($sortBy, $sortDirection);
                    }
                }
            })
            ->paginate(10);

        return response()->json($products, 200);
    }


    public function show($id)
    {
        $product = Product::with('category', 'brand')->find($id);

        if ($product) {
            return response()->json($product, 200);
        } else {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }
    }

    public function store(StoreUpdateProductRequest $request)
    {
        $validatedData = $request->validated();

        $product = Product::create($validatedData);

        return response()->json(['message' => 'Product successfully created', 'product' => $product], 201);
    }

    public function update(StoreUpdateProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);

        $product->update($request->validated());

        return response()->json([
            'message' => 'Produk berhasil diupdate',
            'data' => $product
        ], 200);
    }

    public function destroy(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => "Product dengan id $id tidak ditemukan"
            ], 404);
        }

        $productName = $product->name;
        $product->delete();

        return response()->json([
            'message' => "Product: $productName successfully deleted"
        ], 200);
    }
}
