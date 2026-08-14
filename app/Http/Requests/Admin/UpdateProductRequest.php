<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Enums\ProductStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        $product = $this->route('product');

        return [
            'brand_id' => ['required', 'exists:brands,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:80', Rule::unique('products', 'sku')->ignore($product)],
            'description' => ['required', 'string', 'min:20'],
            'price' => ['required', 'numeric', 'min:1'],
            'compare_at_price' => ['nullable', 'numeric', 'gt:price'],
            'gender' => ['required', Rule::in(['men', 'women', 'unisex'])],
            'status' => ['required', Rule::enum(ProductStatus::class)],
            'is_featured' => ['nullable', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:60'],
            'meta_description' => ['nullable', 'string', 'max:160'],
            'image' => ['nullable', 'image', 'max:4096'],
            'variants' => ['required', 'array', 'min:1'],
            'variants.*.id' => ['nullable', 'integer', Rule::exists('product_variants', 'id')->where('product_id', $product->id)],
            'variants.*.sku' => ['required', 'string', 'max:100', 'distinct'],
            'variants.*.size' => ['required', 'string', 'max:20'],
            'variants.*.color' => ['required', 'string', 'max:100'],
            'variants.*.stock_quantity' => ['required', 'integer', 'min:0'],
        ];
    }
}
