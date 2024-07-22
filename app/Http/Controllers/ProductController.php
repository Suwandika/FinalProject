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

        // $sort = $request->get('sort', 'asc');
        // $query->orderBy('price', $sort);

        $products = $query->paginate(10);
        return response()->json($products);

        // return view('index', compact('products', 'sort'));
    }

    public function show($id)
    {
        $product = Product::with('category', 'brand')->findOrFail($id);
        return response()->json($product);
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
        return response()->json($validator->errors(), 422);
    }

    $product = Product::create($validator->validated());

    $brandName = $product->brand->name;
    $categoryName = $product->category->name;

    return response()->json([
        'message' => 'Produk berhasil disimpan',
        'brand_name' => $brandName,
        'category_name' => $categoryName,
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
            'qty.required' => 'Stok produk wajib diisi.',
            'price.required' => 'Harga produk wajib diisi.',
            'description.required' => 'Deskripsi produk wajib diisi.',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $product->name = $request->input('name');
        $product->category_id = $request->input('category_id');
        $product->brand_id = $request->input('brand_id');
        $product->qty = $request->input('qty');
        $product->price = $request->input('price');
        $product->description = $request->input('description');
        $product->save();

        return response()->json(['message' => 'Produk berhasil diupdate'], 200);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Produk berhasil dihapus'], 200);
    }

    public function productsByCategory($category_id)
    {
        $products = Product::where('category_id', $category_id)->paginate(10);

        return response()->json($products);
    }
}
