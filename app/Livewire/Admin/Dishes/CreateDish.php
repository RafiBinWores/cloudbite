<?php

namespace App\Livewire\Admin\Dishes;

use App\Models\Dish;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class CreateDish extends Component
{
    use WithFileUploads;

    // Form properties
    public $title = null, $short_description = null, $description = null;
    public $category_id = [], $related_dish_id = [], $cuisine_id = [];
    public $price = null, $discount = null, $vat = null, $discount_type = null, $sku = null;
    public $track_stock = 'No', $daily_stock = null;
    public $available_from = null, $available_till = null;
    public $visibility = 'Yes';

    public $thumbnail = null;
    public $gallery = [];

    public $meta_title, $meta_description, $meta_keyword;

    // Arrays
    public $tags = [];           // JSON via cast
    public $buns = [];           // pivot IDs
    public $crusts = [];         // pivot IDs
    public $addOns = [];         // pivot IDs
    public $related_dishes = []; // pivot IDs (dishes)

    // Validation rules
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|unique:dishes,title',
            'short_description' => 'required|string',
            'description' => 'required|string',
            'category_id' => 'required|numeric',
            'related_dish_id' => 'nullable',
            'cuisine_id' => 'required|numeric',
            'price' => 'required',
            'tags' => 'nullable',
            'buns' => 'nullable',
            'crusts' => 'nullable',
            'addOns' => 'nullable',
            'discount_type' => 'nullable|in:amount,percent|required_with:discount',
            'discount'      => 'nullable|numeric|gte:0|required_with:discount_type',
            'vat' => 'nullable',
            'sku' => 'required_if:track_stock,Yes|nullable',
            'track_stock' => 'required|in:Yes,No',
            'daily_stock' => 'required_if:track_stock,Yes|nullable|integer|min:0',
            'available_from' => 'required',
            'available_till' => 'required',
            'visibility' => 'required',
            'thumbnail'        => 'required|image|max:5048|mimes:jpg,jpeg,png,webp,svg',
            'gallery'      => 'array|max:4',
            'gallery.*'    => 'image|max:5048|mimes:jpg,jpeg,png,webp',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_keyword' => 'nullable|string',
        ];
    }

    // Fires for ANY wire:model change
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

    public function submit()
    {
        // dd($this->tags);
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
                'description'       => $this->description,
                'category_id'       => $this->category_id ? (int)$this->category_id : null,
                'cuisine_id'        => $this->cuisine_id ? (int)$this->cuisine_id : null,

                'price'             => $this->price !== null ? (float)$this->price : 0,
                'discount_type'     => $this->discount_type ?: null,
                'discount'          => $this->discount !== null ? (float)$this->discount : null,
                'vat'               => $this->vat !== null ? (float)$this->vat : null,
                'sku'               => $this->sku ?: null,

                // keep as "Yes"/"No" strings (matches your model/DB)
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
                'description',
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
            ]);

            // Defaults
            $this->track_stock = 'No';
            $this->visibility  = 'Yes';
            $this->tags = [];
            $this->buns = $this->crusts = $this->addOns = $this->related_dishes = [];

            $this->dispatch('toast', type: 'success', message: 'Cuisine created successfully.');
        } catch (\Throwable $e) {
            if ($storedThumb) Storage::disk('public')->delete($storedThumb);
            if (!empty($storedGallery)) Storage::disk('public')->delete($storedGallery);

            report($e);
            // Temporarily show the actual reason while debugging:
            // $this->dispatch('toast', type: 'error', message: 'Failed: '.$e->getMessage());
            $this->dispatch('toast', type: 'error', message: 'Failed to create dish. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.admin.dishes.create-dish');
    }
}
