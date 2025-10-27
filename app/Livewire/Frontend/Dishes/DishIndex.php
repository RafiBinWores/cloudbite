<?php

namespace App\Livewire\Frontend\Dishes;

use App\Models\Category;
use App\Models\Cuisine;
use App\Models\Dish;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.frontend')]
class DishIndex extends Component
{
    use WithPagination;

    #[Url(history: true)] public string $search = '';
    #[Url(history: true)] public array  $categories = [];
    #[Url(history: true)] public array  $cuisines   = [];
    #[Url(history: true)] public string $sort = 'all';
    #[Url(history: true)] public int    $perPage = 18;

    // Filter option lists
    public array $categoryOptions = [];
    public array $cuisineOptions  = [];

    public function mount(): void
    {
        $categories = Category::query()
            ->where('status', 'active')
            ->orderByDesc('created_at')
            ->get(['id', 'name', 'slug']);

        $cuisines = Cuisine::query()
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        $this->categoryOptions = $categories->toArray();
        $this->cuisineOptions  = $cuisines->toArray();

        // Back-compat for old numeric links:
        if (!empty($this->categories) && is_numeric(reset($this->categories))) {
            $idToSlug = $categories->pluck('slug', 'id'); // [id => slug]
            $this->categories = collect($this->categories)
                ->map(fn($v) => $idToSlug[(int)$v] ?? null)
                ->filter()->values()->all();
        }

        if (!empty($this->cuisines) && is_numeric(reset($this->cuisines))) {
            $idToSlug = $cuisines->pluck('slug', 'id'); // [id => slug]
            $this->cuisines = collect($this->cuisines)
                ->map(fn($v) => $idToSlug[(int)$v] ?? null)
                ->filter()->values()->all();
        }
    }

    // Reset page on changes
    public function updatedSearch(): void
    {
        $this->resetPage();
    }
    public function updatedCategories(): void
    {
        $this->resetPage();
    }
    public function updatedCuisines(): void
    {
        $this->resetPage();
    }
    public function updatedSort(): void
    {
        $this->resetPage();
    }
    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    protected function baseQuery(): Builder
    {
        $now = now();

        // Map slugs -> IDs
        $categoryIds = [];
        if (!empty($this->categories)) {
            $slugToId = collect($this->categoryOptions)->pluck('id', 'slug'); // [slug=>id]
            $categoryIds = collect($this->categories)->map(fn($s) => $slugToId[$s] ?? null)->filter()->values()->all();
        }

        $cuisineIds = [];
        if (!empty($this->cuisines)) {
            $slugToId = collect($this->cuisineOptions)->pluck('id', 'slug'); // [slug=>id]
            $cuisineIds = collect($this->cuisines)->map(fn($s) => $slugToId[$s] ?? null)->filter()->values()->all();
        }

        return Dish::query()
            ->where('visibility', 'Yes')
            ->where('available_from', '<=', $now)
            ->where('available_till', '>=', $now)
            ->when($this->search !== '', function (Builder $q) {
                $term = '%' . str_replace('%', '\%', $this->search) . '%';
                $q->where(function (Builder $sub) use ($term) {
                    $sub->where('title', 'like', $term)
                        ->orWhere('short_description', 'like', $term);
                });
            })
            ->when(!empty($categoryIds), fn($q) => $q->whereIn('category_id', $categoryIds))
            ->when(!empty($cuisineIds),  fn($q) => $q->whereIn('cuisine_id',  $cuisineIds));
    }

    protected function applySort(Builder $q): Builder
    {
        return match ($this->sort) {
            'name'       => $q->orderBy('title'),
            'price_asc'  => $q->orderByRaw('CAST(price AS DECIMAL(12,2)) ASC'),
            'price_desc' => $q->orderByRaw('CAST(price AS DECIMAL(12,2)) DESC'),
            'popularity' => $q->withCount('orderItems')->orderByDesc('order_items_count')->orderBy('title'),
            default      => $q->orderByDesc('created_at'),
        };
    }

    public function getDishesProperty()
    {
        return $this->applySort($this->baseQuery())->paginate($this->perPage);
    }

    public function render()
    {
        // Keep collection if your legacy view needs it
        $categories = Category::where('status', 'active')->orderByDesc('created_at')->get();

        return view('livewire.frontend.dishes.dish-index', [
            'dishes'          => $this->dishes,
            'categories'      => $categories,
            'categoryOptions' => $this->categoryOptions,
            'cuisineOptions'  => $this->cuisineOptions,
        ]);
    }
}
