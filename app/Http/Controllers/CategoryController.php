<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();

        return response()->json([ "data" => $categories ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = Validator::make($request->all(),[
            'name' => 'required',
        ],
        [ 
            'name.required' => 'wajib ada',
        ]);

        if ($validateData->fails()) {
            return response()->json(['errors' => $validateData->errors()], 422);
        } 
        
        Category::create($validateData->validated());
        return response()->json(['message' => 'Category berhasil disimpan'], 201);
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
        $category = Category::find($id);

        if(!$category) {
            return response()->json([
                'message' => "Category dengan id $id tidak ditemukan"
            ], 404);
        }

        $validateData = Validator::make($request->all(),[
            'name' => 'required',
        ],
        [ 
            'name.required' => 'wajib ada',
        ]);

        if ($validateData->fails()) {
            return response()->json(['errors' => $validateData->errors()], 422);
        } 

        $category->update($validateData->validated());
        return response()->json(['message' => 'Category berhasil di-update'], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);

        if(!$category) {
            return response()->json([
                'message' => "Category dengan id $id tidak ditemukan"
            ], 404);
        }

        $products = $category->products()->get();

        $category->delete();
        return response()->json(['message' => "Category : $category->category_name Berhasil dihapus",], 200);
    }
}