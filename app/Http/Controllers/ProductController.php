<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->with('brand');

        if ($request->has('keyword')) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }

        $products = $query->paginate(10);
        return response()->json($products, 200);
    }

    public function show($id)
    {
        $product = Product::with('category', 'brand')->findOrFail($id);
        return response()->json($product, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'qty' => 'required|integer',
            'price' => 'required|numeric',
            'description' => 'required|string',
        ], [
            'name.required' => 'Nama produk wajib diisi.',
            'brand_id.required' => 'Nama merek wajib diisi.',
            'brand_id.exists' => 'Nama merek yang dipilih tidak valid.',
            'category_id.required' => 'Kategori produk wajib diisi.',
            'category_id.exists' => 'Kategori produk yang dipilih tidak valid.',
            'qty.required' => 'Stok produk wajib diisi.',
            'price.required' => 'Harga produk wajib diisi.',
            'description.required' => 'Deskripsi produk wajib diisi.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $product = Product::create($validator->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Produk berhasil disimpan',
            'data' => $product
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'qty' => 'required|integer',
            'price' => 'required|numeric',
            'description' => 'required|string',
        ], [
            'name.required' => 'Nama produk wajib diisi.',
            'brand_id.required' => 'Merek produk wajib dipilih.',
            'brand_id.exists' => 'Merek yang dipilih tidak valid.',
            'category_id.required' => 'Kategori produk wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'qty.required' => 'Stok produk wajib diisi.',
            'price.required' => 'Harga produk wajib diisi.',
            'description.required' => 'Deskripsi produk wajib diisi.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $product->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Produk berhasil diupdate',
            'data' => $product
        ], 200);
    }

    public function destroy($id)
    {
        try {
        $product = Product::findOrFail($id);

        $product->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Produk berhasil dihapus'
        ], 200);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Produk tidak ditemukan'
        ], 404);
    }
    }
}
