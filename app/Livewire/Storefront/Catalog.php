<?php

declare(strict_types=1);

namespace App\Livewire\Storefront;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.storefront')]
#[Title('Shop Authentic Sneakers | Sneakyard')]
final class Catalog extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $brand = '';

    #[Url]
    public string $gender = '';

    #[Url]
    public string $sort = 'latest';

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'brand', 'gender', 'sort'], true)) {
            $this->resetPage();
        }
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'brand', 'gender']);
        $this->sort = 'latest';
        $this->resetPage();
    }

    public function render(): View
    {
        $products = Product::query()
            ->active()
            ->with(['brand', 'primaryImage', 'variants' => fn ($query) => $query->available()])
            ->search($this->search)
            ->when($this->brand, fn ($query) => $query->whereHas('brand', fn ($brand) => $brand->where('slug', $this->brand)))
            ->when($this->gender, fn ($query) => $query->where('gender', $this->gender))
            ->when($this->sort === 'price-low', fn ($query) => $query->orderBy('price'))
            ->when($this->sort === 'price-high', fn ($query) => $query->orderByDesc('price'))
            ->when($this->sort === 'latest', fn ($query) => $query->latest('published_at'))
            ->paginate(12);

        return view('livewire.storefront.catalog', [
            'products' => $products,
            'brands' => Brand::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}
