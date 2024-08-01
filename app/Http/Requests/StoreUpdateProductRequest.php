<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:50',
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'qty' => 'required|integer',
            'price' => 'required|numeric',
            'description' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama produk wajib diisi.',
            'brand_id.required' => 'Merek produk wajib dipilih.',
            'brand_id.exists' => 'Merek yang dipilih tidak valid.',
            'category_id.required' => 'Kategori produk wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'qty.required' => 'Stok produk wajib diisi.',
            'price.required' => 'Harga produk wajib diisi.',
            'description.required' => 'Deskripsi produk wajib diisi.',
        ];
    }
}
