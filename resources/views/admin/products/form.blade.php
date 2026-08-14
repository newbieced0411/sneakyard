@php
    $editing = isset($product);
    $variants = old('variants', $editing ? $product->variants->map(fn ($variant) => ['id' => $variant->id, 'sku' => $variant->sku, 'size' => $variant->size, 'color' => $variant->color, 'stock_quantity' => $variant->stock_quantity])->values()->all() : [['id' => null, 'sku' => '', 'size' => '', 'color' => '', 'stock_quantity' => 0]]);
@endphp
@if($errors->any())<div class="form-alert" role="alert"><strong>Please fix the highlighted fields.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<section class="admin-form-section"><h2>Listing details</h2><div class="admin-form-grid">
    <label class="admin-field"><span>Product name</span><input class="admin-input" name="name" value="{{ old('name', $product->name ?? '') }}" required></label>
    <label class="admin-field"><span>Base SKU</span><input class="admin-input" name="sku" value="{{ old('sku', $product->sku ?? '') }}" required></label>
    <label class="admin-field"><span>Brand</span><select class="admin-select" name="brand_id" required><option value="">Select brand</option>@foreach($brands as $brand)<option value="{{ $brand->id }}" @selected((string) old('brand_id', $product->brand_id ?? '') === (string) $brand->id)>{{ $brand->name }}</option>@endforeach</select></label>
    <label class="admin-field"><span>Category</span><select class="admin-select" name="category_id" required><option value="">Select category</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((string) old('category_id', $product->category_id ?? '') === (string) $category->id)>{{ $category->name }}</option>@endforeach</select></label>
    <label class="admin-field"><span>Price (PHP)</span><input class="admin-input" type="number" name="price" min="1" step="0.01" value="{{ old('price', $editing ? $product->price / 100 : '') }}" required></label>
    <label class="admin-field"><span>Compare-at price (optional)</span><input class="admin-input" type="number" name="compare_at_price" min="1" step="0.01" value="{{ old('compare_at_price', $editing && $product->compare_at_price ? $product->compare_at_price / 100 : '') }}"></label>
    <label class="admin-field"><span>Customer group</span><select class="admin-select" name="gender" required>@foreach(['unisex' => 'Unisex', 'men' => 'Men', 'women' => 'Women'] as $value => $label)<option value="{{ $value }}" @selected(old('gender', $product->gender ?? 'unisex') === $value)>{{ $label }}</option>@endforeach</select></label>
    <label class="admin-field"><span>Publishing status</span><select class="admin-select" name="status" required>@foreach($statuses as $status)<option value="{{ $status->value }}" @selected(old('status', $product->status->value ?? 'draft') === $status->value)>{{ ucfirst($status->value) }}</option>@endforeach</select></label>
    <label class="admin-field full-span"><span>Description</span><textarea class="admin-textarea" name="description" rows="6" required>{{ old('description', $product->description ?? '') }}</textarea></label>
    <label class="admin-field"><span>Primary product image {{ $editing ? '(optional)' : '' }}</span><input class="admin-input" type="file" name="image" accept="image/jpeg,image/png,image/webp" {{ $editing ? '' : 'required' }}></label>
    <label class="admin-checkbox"><input type="hidden" name="is_featured" value="0"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured ?? false))><span>Feature on storefront</span></label>
</div></section>

<section class="admin-form-section" x-data='{ variants: @json($variants) }'><div class="admin-page-heading"><div><h2>Sizes & inventory</h2><p>Stock is tracked separately for every size and color.</p></div><flux:button type="button" icon="plus" x-on:click="variants.push({id:null,sku:'',size:'',color:'',stock_quantity:0})">Add size</flux:button></div>
    <template x-for="(variant, index) in variants" :key="variant.id ?? `new-${index}`"><div class="variant-row">
        <input type="hidden" :name="`variants[${index}][id]`" x-model="variant.id">
        <label class="admin-field"><span>SKU</span><input class="admin-input" :name="`variants[${index}][sku]`" x-model="variant.sku" required></label>
        <label class="admin-field"><span>Size</span><input class="admin-input" :name="`variants[${index}][size]`" x-model="variant.size" placeholder="US 9" required></label>
        <label class="admin-field"><span>Color</span><input class="admin-input" :name="`variants[${index}][color]`" x-model="variant.color" required></label>
        <label class="admin-field"><span>Stock</span><input class="admin-input" type="number" min="0" :name="`variants[${index}][stock_quantity]`" x-model="variant.stock_quantity" required></label>
        <flux:button variant="ghost" type="button" icon="trash" aria-label="Remove size" x-on:click="if (variants.length > 1) variants.splice(index, 1)"></flux:button>
    </div></template>
</section>

<section class="admin-form-section"><h2>Search & social</h2><div class="admin-form-grid"><label class="admin-field"><span>SEO title</span><input class="admin-input" name="meta_title" maxlength="60" value="{{ old('meta_title', $product->meta_title ?? '') }}"><small>Up to 60 characters.</small></label><label class="admin-field full-span"><span>SEO description</span><textarea class="admin-textarea" name="meta_description" maxlength="160" rows="3">{{ old('meta_description', $product->meta_description ?? '') }}</textarea><small>Used by search engines and Facebook sharing.</small></label></div></section>
