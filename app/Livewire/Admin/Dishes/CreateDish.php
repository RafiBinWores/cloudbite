<?php

namespace App\Livewire\Admin\Dishes;

use App\Models\Dish;
use Developermithu\Tallcraftui\Traits\WithTcToast;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class CreateDish extends Component
{
    use WithFileUploads, WithTcToast;

    // Form properties
    public $title = null, $short_description = null;
    public $category_id = [], $related_dish_id = [], $cuisine_id = [];
    public $price = null, $discount = null, $vat = null, $discount_type = null, $sku = null;
    public $track_stock = 'No', $daily_stock = null;
    public $available_from = null, $available_till = null, $visibility = 'Yes';

    public $thumbnail = null;
    public $gallery = [];

    public $meta_title, $meta_description, $meta_keyword;

    // Arrays
    public $tags = [];
    public $buns = [];
    public $crusts = [];
    public $addOns = [];
    public $related_dishes = [];

    // NEW: Variations (group -> options)
    public $variations = [];

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|unique:dishes,title',
            'short_description' => 'required|string|max:500',

            'category_id' => 'required|numeric',
            'related_dish_id' => 'nullable',
            'cuisine_id' => 'required|numeric',

            'price' => 'required|numeric|min:0',

            'tags' => 'nullable|array',
            'buns' => 'nullable|array',
            'crusts' => 'nullable|array',
            'addOns' => 'nullable|array',

            'discount_type' => 'nullable|in:amount,percent|required_with:discount',
            'discount'      => 'nullable|numeric|gte:0|required_with:discount_type',
            'vat' => 'nullable|numeric|min:0',

            'sku' => 'required_if:track_stock,Yes|nullable|string|max:255',
            'track_stock' => 'required|in:Yes,No',
            'daily_stock' => 'required_if:track_stock,Yes|nullable|integer|min:0',

            'available_from' => 'required',
            'available_till' => 'required',
            'visibility' => 'required|in:Yes,No',

            'thumbnail'     => 'required|image|max:5048|mimes:jpg,jpeg,png,webp,svg',
            'gallery'       => 'array|max:4',
            'gallery.*'     => 'image|max:5048|mimes:jpg,jpeg,png,webp',

            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keyword' => 'nullable|string|max:255',

            // NEW: variation validation
            'variations' => 'nullable|array',
            'variations.*.name' => 'nullable|string|max:100',
            'variations.*.options' => 'nullable|array',
            'variations.*.options.*.label' => 'required_with:variations.*.name|string|max:100',
            'variations.*.options.*.price' => 'required_with:variations.*.name|numeric|min:0',
        ];
    }

    public function updated($property, $value)
    {
        $this->resetErrorBag($property);
    }

    /** Called by Alpine to get temp URLs for preview */
    public function previewUrls(): array
    {
        $thumbnail = $this->thumbnail ? $this->thumbnail->temporaryUrl() : null;

        $gallery = [];
        foreach ($this->gallery as $file) {
            if (method_exists($file, 'temporaryUrl')) {
                $gallery[] = $file->temporaryUrl();
            }
            if (count($gallery) >= 4) break;
        }

        return ['thumbnail' => $thumbnail, 'gallery' => $gallery];
    }

    public function clearThumbnail(): void
    {
        $this->thumbnail = null;
    }

    public function removeFromGallery(int $index): void
    {
        if (isset($this->gallery[$index])) {
            unset($this->gallery[$index]);
            $this->gallery = array_values($this->gallery);
        }
    }

    // =========================
    // Variation helpers (UI)
    // =========================
    public function addVariationGroup()
    {
        $this->variations[] = [
            'name' => '',
            'options' => [
                ['label' => '', 'price' => 0],
            ],
        ];
    }

    public function removeVariationGroup($index)
    {
        unset($this->variations[$index]);
        $this->variations = array_values($this->variations);
    }

    public function addVariationOption($vIndex)
    {
        $this->variations[$vIndex]['options'][] = ['label' => '', 'price' => 0];
    }

    public function removeVariationOption($vIndex, $oIndex)
    {
        unset($this->variations[$vIndex]['options'][$oIndex]);
        $this->variations[$vIndex]['options'] = array_values($this->variations[$vIndex]['options']);
    }

    // Clean empty rows before save
    private function normalizeVariations(): array
    {
        $out = [];

        foreach ((array) $this->variations as $group) {
            $name = trim($group['name'] ?? '');
            if ($name === '') continue;

            $opts = [];
            foreach ((array)($group['options'] ?? []) as $opt) {
                $label = trim($opt['label'] ?? '');
                $price = $opt['price'] ?? null;
                if ($label === '' || $price === null || $price === '') continue;

                $opts[] = [
                    'label' => $label,
                    'price' => (float) $price,
                ];
            }

            if ($opts) {
                $out[] = [
                    'name' => $name,
                    'options' => $opts,
                ];
            }
        }

        return $out;
    }

    public function submit()
    {
        $this->validate();

        $dailyStock = ($this->track_stock === 'Yes') ? (int)($this->daily_stock ?? 0) : null;

        $storedThumb = null;
        $storedGallery = [];

        try {
            $slug = Str::slug($this->title ?: 'dish');
            $ts   = now()->format('YmdHis');

            // Thumbnail
            if ($this->thumbnail) {
                $ext = $this->thumbnail->getClientOriginalExtension()
                    ?: $this->thumbnail->extension() ?: 'jpg';

                $storedThumb = $this->thumbnail->storeAs(
                    'dishes/thumbnail',
                    "{$slug}-{$ts}-thumb.{$ext}",
                    'public'
                );
            }

            // Gallery (max 4)
            foreach (array_slice($this->gallery ?? [], 0, 4) as $i => $file) {
                $ext = $file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg';
                $storedGallery[] = $file->storeAs(
                    'dishes/gallery',
                    "{$slug}-{$ts}-g" . ($i + 1) . ".{$ext}",
                    'public'
                );
            }

            $data = [
                'title'             => $this->title,
                'slug'              => $slug,
                'short_description' => $this->short_description,

                'category_id'       => $this->category_id ? (int)$this->category_id : null,
                'cuisine_id'        => $this->cuisine_id ? (int)$this->cuisine_id : null,

                'price'             => $this->price !== null ? (float)$this->price : 0,
                'discount_type'     => $this->discount_type ?: null,
                'discount'          => $this->discount !== null ? (float)$this->discount : null,
                'vat'               => $this->vat !== null ? (float)$this->vat : null,
                'sku'               => $this->sku ?: null,

                'track_stock'       => $this->track_stock,
                'daily_stock'       => $dailyStock,

                'available_from'    => $this->available_from ?: null,
                'available_till'    => $this->available_till ?: null,
                'visibility'        => $this->visibility,

                'thumbnail'         => $storedThumb,
                'gallery'           => $storedGallery,

                'meta_title'        => $this->meta_title,
                'meta_description'  => $this->meta_description,
                'meta_keyword'      => $this->meta_keyword,

                'tags'              => $this->tags ?: [],

                // NEW: save cleaned variations
                'variations'        => $this->normalizeVariations(),
            ];

            // capture pivot arrays before closure
            $buns    = array_filter((array) $this->buns);
            $crusts  = array_filter((array) $this->crusts);
            $addOns  = array_filter((array) $this->addOns);
            $related = array_filter((array) $this->related_dishes);

            $dishId = DB::transaction(function () use ($data, $buns, $crusts, $addOns, $related) {
                $dish = Dish::create($data);

                if ($buns)    $dish->buns()->sync($buns);
                if ($crusts)  $dish->crusts()->sync($crusts);
                if ($addOns)  $dish->addOns()->sync($addOns);
                if ($related) $dish->relatedDishes()->sync($related);

                return $dish->id;
            });

            $this->dispatch('dish-created', id: $dishId);

            // Reset
            $this->reset([
                'title',
                'short_description',
                'category_id',
                'cuisine_id',
                'price',
                'discount_type',
                'discount',
                'vat',
                'sku',
                'track_stock',
                'daily_stock',
                'available_from',
                'available_till',
                'visibility',
                'thumbnail',
                'gallery',
                'meta_title',
                'meta_description',
                'meta_keyword',
                'tags',
                'buns',
                'crusts',
                'addOns',
                'related_dishes',
                'variations',
            ]);

            $this->track_stock = 'No';
            $this->visibility  = 'Yes';

            $this->success(
                title: 'Dish created successfully',
                position: 'top-right',
                showProgress: true,
                showCloseIcon: true,
            );

        } catch (\Throwable $e) {
            if ($storedThumb) Storage::disk('public')->delete($storedThumb);
            if (!empty($storedGallery)) Storage::disk('public')->delete($storedGallery);

            report($e);

            $this->error(
                title: 'Failed to create dish. Please try again.',
                position: 'top-right',
                showProgress: true,
                showCloseIcon: true,
            );
        }
    }

    public function render()
    {
        return view('livewire.admin.dishes.create-dish');
    }
}
