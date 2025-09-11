<?php

namespace App\Livewire\Admin\Dishes;

use App\Models\Dish;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EditDish extends Component
{
    use WithFileUploads;

    public ?Dish $dish = null;

    // Form props
    public $title = null, $short_description = null, $description = null;
    public $category_id = null, $related_dish_id = null, $cuisine_id = null;
    public $price = null, $discount = null, $vat = null, $discount_type = null, $sku = null;
    public $track_stock = 'No', $daily_stock = null;
    public $available_from = null, $available_till = null;
    public $visibility = 'Yes';

    public $thumbnail = null; // new upload (nullable)
    public $gallery = [];     // new uploads (nullable, max 4)

    public $meta_title, $meta_description, $meta_keyword;

    // Arrays / Pivots
    public $tags = [];            // stored as JSON cast
    public $buns = [];            // pivot IDs
    public $crusts = [];          // pivot IDs
    public $addOns = [];          // pivot IDs
    public $related_dishes = [];  // pivot IDs (dishes)

    public function mount(Dish $dish)
    {
        $this->dish = $dish;

        // Base
        $this->title             = $dish->title;
        $this->short_description = $dish->short_description;
        $this->description       = $dish->description;
        $this->category_id       = $dish->category_id;
        $this->cuisine_id        = $dish->cuisine_id;

        $this->price         = $dish->price;
        $this->discount_type = $dish->discount_type;
        $this->discount      = $dish->discount;
        $this->vat           = $dish->vat;
        $this->sku           = $dish->sku;

        $this->track_stock   = $dish->track_stock ?? 'No';
        $this->daily_stock   = $dish->daily_stock;

        $this->available_from = $dish->available_from;
        $this->available_till = $dish->available_till;

        $this->visibility     = $dish->visibility ?? 'Yes';

        $this->meta_title       = $dish->meta_title;
        $this->meta_description = $dish->meta_description;
        $this->meta_keyword     = $dish->meta_keyword;

        // Arrays
        $this->tags             =$dish->tags;
        $this->buns             = $dish->buns()->allRelatedIds()->toArray();
        $this->crusts           = $dish->crusts()->allRelatedIds()->toArray();
        $this->addOns           = $dish->addOns()->allRelatedIds()->toArray();
        $this->related_dishes   = $dish->relatedDishes()->allRelatedIds()->toArray();
    }

    public function rules(): array
    {
        return [
            'title'             => ['required', 'string', 'max:255', Rule::unique('dishes', 'title')->ignore($this->dish?->id)],
            'short_description' => 'required|string',
            'description'       => 'required|string',
            'category_id'       => 'required|numeric',
            'related_dish_id'   => 'nullable',
            'cuisine_id'        => 'required|numeric',

            'price'         => 'required|numeric',
            'discount_type' => 'nullable|in:amount,percent|required_with:discount',
            'discount'      => 'nullable|numeric|gte:0|required_with:discount_type',
            'vat'           => 'nullable|numeric',
            'sku'           => 'nullable|string',

            'tags'      => 'nullable|array',
            'buns'      => 'nullable|array',
            'crusts'    => 'nullable|array',
            'addOns'    => 'nullable|array',

            'track_stock'   => 'required|in:Yes,No',
            'daily_stock'   => 'required_if:track_stock,Yes|nullable|integer|min:0',
            'available_from' => 'required',
            'available_till' => 'required|after_or_equal:available_from',
            'visibility'    => 'required|in:Yes,No',

            // On update, images are optional:
            'thumbnail'   => 'nullable|image|max:5048|mimes:jpg,jpeg,png,webp,svg',
            'gallery'     => 'nullable|array|max:4',
            'gallery.*'   => 'nullable|image|max:5048|mimes:jpg,jpeg,png,webp',

            'meta_title'       => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_keyword'     => 'nullable|string',
        ];
    }

    // Clear field-level error on change
    public function updated($property, $value)
    {
        $this->resetErrorBag($property);
    }

    /** Alpine preview helper */
    public function previewUrls(): array
    {
        $thumbnail = $this->thumbnail ? $this->thumbnail->temporaryUrl() : null;
        $gallery = [];
        foreach ($this->gallery as $file) {
            if (method_exists($file, 'temporaryUrl')) $gallery[] = $file->temporaryUrl();
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

    /** Update/save changes */
    public function updateDish()
    {

        // dd($this->available_from, $this->available_till);
        $this->validate();

        $dailyStock = ($this->track_stock === 'Yes') ? (int)($this->daily_stock ?? 0) : null;

        $newThumb = null;
        $newGallery = [];

        $oldThumb    = $this->dish->thumbnail;
        $oldGallery  = (array) ($this->dish->gallery ?? []);

        try {
            $ts   = now()->format('YmdHis');

            // Keep slug unless title changed
            $slug = $this->dish->slug;
            if ($this->title !== $this->dish->title) {
                $slug = Str::slug($this->title ?: $slug);
            }

            // Save NEW thumbnail if provided
            if ($this->thumbnail) {
                $ext = $this->thumbnail->getClientOriginalExtension() ?: $this->thumbnail->extension() ?: 'jpg';
                $newThumb = $this->thumbnail->storeAs(
                    'dishes/thumbnail',
                    "{$slug}-{$ts}-thumb.{$ext}",
                    'public'
                );
            }

            // Save NEW gallery if provided (max 4)
            if (!empty($this->gallery)) {
                foreach (array_slice($this->gallery, 0, 4) as $i => $file) {
                    $ext = $file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg';
                    $newGallery[] = $file->storeAs(
                        'dishes/gallery',
                        "{$slug}-{$ts}-g" . ($i + 1) . ".{$ext}",
                        'public'
                    );
                }
            }

            $data = [
                'title'             => $this->title,
                'slug'              => $slug,
                'short_description' => $this->short_description,
                'description'       => $this->description,
                'category_id'       => $this->category_id ? (int)$this->category_id : null,
                'cuisine_id'        => $this->cuisine_id ? (int)$this->cuisine_id : null,

                'price'         => $this->price !== null ? (float)$this->price : 0,
                'discount_type' => $this->discount_type ?: null,
                'discount'      => $this->discount !== null ? (float)$this->discount : null,
                'vat'           => $this->vat !== null ? (float)$this->vat : null,
                'sku'           => $this->sku ?: null,

                'track_stock'    => $this->track_stock,
                'daily_stock'    => $dailyStock,
                'available_from' => $this->available_from ?: null,
                'available_till' => $this->available_till ?: null,
                'visibility'     => $this->visibility,

                // Images: keep old unless new provided
                'thumbnail' => $newThumb ?: $oldThumb,
                'gallery'   => !empty($newGallery) ? $newGallery : $oldGallery,

                'meta_title'       => $this->meta_title,
                'meta_description' => $this->meta_description,
                'meta_keyword'     => $this->meta_keyword,

                'tags' => $this->tags ?: [],
            ];

            // Capture pivots
            $buns    = array_values(array_filter((array)$this->buns));
            $crusts  = array_values(array_filter((array)$this->crusts));
            $addOns  = array_values(array_filter((array)$this->addOns));
            $related_dishes = array_values(array_filter((array)$this->related_dishes));

            DB::transaction(function () use ($data, $buns, $crusts, $addOns, $related_dishes) {
                $this->dish->update($data);
                $this->dish->buns()->sync($buns);
                $this->dish->crusts()->sync($crusts);
                $this->dish->addOns()->sync($addOns);
                $this->dish->relatedDishes()->sync($related_dishes);
            });

            // If we saved a new thumbnail, delete old file
            if ($newThumb && $oldThumb && Storage::disk('public')->exists($oldThumb)) {
                Storage::disk('public')->delete($oldThumb);
            }

            // If we saved a new gallery, delete old gallery files
            if (!empty($newGallery) && !empty($oldGallery)) {
                foreach ($oldGallery as $path) {
                    if ($path && Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }
            }

            $this->dispatch('dish-updated', id: $this->dish->id);
            $this->dispatch('toast', type: 'success', message: 'Dish updated successfully.');
        } catch (\Throwable $e) {
            // Roll back new uploads on failure
            if ($newThumb) Storage::disk('public')->delete($newThumb);
            if (!empty($newGallery)) Storage::disk('public')->delete($newGallery);

            report($e);
            $this->dispatch('toast', type: 'error', message: 'Failed to update dish. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.admin.dishes.edit-dish');
    }
}
