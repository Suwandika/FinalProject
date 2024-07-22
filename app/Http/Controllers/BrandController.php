<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = Brand::all();

        return response()->json([ "data" => $brands ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = Validator::make($request->all(),[
            'brand_name' => 'required',
        ],
        [ 
            'brand_name.required' => 'wajib ada, harus berupa teks string',
        ]);

        if ($validateData->fails()) {
            return response()->json(['errors' => $validateData->errors()], 422);
        } 
        
        Brand::create($validateData->validated());
        return response()->json(['message' => 'Brand berhasil disimpan'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {   
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $brand = Brand::find($id);

        if(!$brand) {
            return response()->json([
                'message' => "Brand dengan id $id tidak ditemukan"
            ], 404);
        }

        $validateData = Validator::make($request->all(),[
            'brand_name' => 'required',
        ],
        [ 
            'brand_name.required' => 'wajib ada, harus berupa teks string',
        ]);

        if ($validateData->fails()) {
            return response()->json(['errors' => $validateData->errors()], 422);
        } 

        $brand->update($validateData->validated());
        return response()->json(['message' => 'Brand berhasil disimpan'], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $brand = Brand::find($id);

        if(!$brand) {
            return response()->json([
                'message' => "Brand dengan id $id tidak ditemukan"
            ], 404);
        }

        $products = $brand->products()->get();

        $brand->delete();
        return response()->json(['message' => "Brand : $brand->brand_name Berhasil dihapus", 'product_init' => $products], 200);
    }
}