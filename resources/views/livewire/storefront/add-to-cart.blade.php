<div class="add-to-cart-panel">
    <fieldset>
        <legend>Select size <a href="#size-guide">Size guide</a></legend>
        <div class="size-grid">
            @foreach($product->variants as $variant)
                <label wire:key="variant-{{ $variant->id }}" class="size-option">
                    <input type="radio" wire:model.live="variantId" value="{{ $variant->id }}">
                    <span>{{ $variant->size }}</span>
                </label>
            @endforeach
        </div>
        @error('variantId')<p class="field-error" role="alert">{{ $message }}</p>@enderror
    </fieldset>

    <button type="button" class="primary-button add-button" wire:click="add" wire:loading.attr="disabled">
        <span wire:loading.remove wire:target="add">Add to bag</span>
        <span wire:loading wire:target="add">Adding…</span>
    </button>
    @error('quantity')<p class="field-error" role="alert">{{ $message }}</p>@enderror
    @if($added)<p class="inline-success" role="status"><flux:icon.check class="size-4" /> Added to your bag. <a href="{{ route('cart') }}">View bag</a></p>@endif
</div>
