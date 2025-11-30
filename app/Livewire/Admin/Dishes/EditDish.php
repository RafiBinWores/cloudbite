<?php

namespace App\Livewire\Admin\Dishes;

use App\Models\Dish;
use Developermithu\Tallcraftui\Traits\WithTcToast;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EditDish extends Component
{
    use WithFileUploads, WithTcToast;

    public ?Dish $dish = null;

    // Form props
    public $title = null, $short_description = null;
    public $category_id = null, $related_dish_id = null, $cuisine_id = null;
    public $price = null, $discount = null, $vat = null, $discount_type = null, $sku = null;
    public $track_stock = 'No', $daily_stock = null;
    public $available_from = null, $available_till = null;
    public $visibility = 'Yes';

    public $thumbnail = null; // new upload (nullable)
    public $gallery = [];     // new uploads (nullable, max 4)

    public $meta_title, $meta_description, $meta_keyword;

    // Arrays / Pivots
    public $tags = [];
    public $buns = [];
    public $crusts = [];
    public $addOns = [];
    public $related_dishes = [];

    // Variations
    public $variations = [];

    // 🔥 Hero fields
    public $show_in_hero = 'No';
    public $hero_image = null;
    public $hero_discount_image = null;

    public function mount(Dish $dish)
    {
        $this->dish = $dish;

        $this->title             = $dish->title;
        $this->short_description = $dish->short_description;
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

        $this->tags           = $dish->tags ?? [];
        $this->buns           = $dish->buns()->allRelatedIds()->toArray();
        $this->crusts         = $dish->crusts()->allRelatedIds()->toArray();
        $this->addOns         = $dish->addOns()->allRelatedIds()->toArray();
        $this->related_dishes = $dish->relatedDishes()->allRelatedIds()->toArray();

        // load variations json
        $this->variations = $dish->variations ?? [];

        // 🔥 hero flags from DB
        $this->show_in_hero = $dish->show_in_hero ? 'Yes' : 'No';
    }

    public function rules(): array
    {
        return [
            'title'             => ['required', 'string', 'max:255', Rule::unique('dishes', 'title')->ignore($this->dish?->id)],
            'short_description' => 'required|string|max:500',

            'category_id'       => 'required|numeric',
            'related_dish_id'   => 'nullable',
            'cuisine_id'        => 'required|numeric',

            'price'         => 'required|numeric|min:0',
            'discount_type' => 'nullable|in:amount,percent|required_with:discount',
            'discount'      => 'nullable|numeric|gte:0|required_with:discount_type',
            'vat'           => 'nullable|numeric|min:0',
            'sku'           => 'nullable|string|max:255',

            'tags'      => 'nullable|array',
            'buns'      => 'nullable|array',
            'crusts'    => 'nullable|array',
            'addOns'    => 'nullable|array',

            'track_stock'    => 'required|in:Yes,No',
            'daily_stock'    => 'required_if:track_stock,Yes|nullable|integer|min:0',
            'available_from' => 'required',
            'available_till' => 'required',
            'visibility'     => 'required|in:Yes,No',

            // Images optional on update
            'thumbnail'   => 'nullable|image|max:5048|mimes:jpg,jpeg,png,webp,svg',
            'gallery'     => 'nullable|array|max:4',
            'gallery.*'   => 'nullable|image|max:5048|mimes:jpg,jpeg,png,webp',

            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keyword'     => 'nullable|string|max:255',

            // variations
            'variations' => 'nullable|array',
            'variations.*.name' => 'nullable|string|max:100',
            'variations.*.options' => 'nullable|array',
            'variations.*.options.*.label' => 'required_with:variations.*.name|string|max:100',
            'variations.*.options.*.price' => 'required_with:variations.*.name|numeric|min:0',

            // 🔥 hero rules
            'show_in_hero' => 'required|in:Yes,No',
            'hero_image'   => 'nullable|image|max:5048|mimes:jpg,jpeg,png,webp,svg',
            'hero_discount_image' => 'nullable|image|max:5048|mimes:png,webp,svg',
        ];
    }

    public function updated($property, $value)
    {
        $this->resetErrorBag($property);
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
        $this->validate();

        $dailyStock = ($this->track_stock === 'Yes') ? (int)($this->daily_stock ?? 0) : null;

        $newThumb = null;
        $newGallery = [];

        $oldThumb    = $this->dish->thumbnail;
        $oldGallery  = (array) ($this->dish->gallery ?? []);

        // 🔥 hero old paths
        $oldHeroImage = $this->dish->hero_image;
        $oldHeroBadge = $this->dish->hero_discount_image;
        $newHeroImagePath = null;
        $newHeroBadgePath = null;

        try {
            $ts   = now()->format('YmdHis');

            $slug = $this->dish->slug;
            if ($this->title !== $this->dish->title) {
                $slug = Str::slug($this->title ?: $slug);
            }

            if ($this->thumbnail) {
                $ext = $this->thumbnail->getClientOriginalExtension()
                    ?: $this->thumbnail->extension() ?: 'jpg';

                $newThumb = $this->thumbnail->storeAs(
                    'dishes/thumbnail',
                    "{$slug}-{$ts}-thumb.{$ext}",
                    'public'
                );
            }

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

            // 🔥 store hero images if new ones uploaded
            if ($this->hero_image) {
                $ext = $this->hero_image->getClientOriginalExtension()
                    ?: $this->hero_image->extension() ?: 'png';

                $newHeroImagePath = $this->hero_image->storeAs(
                    'dishes/hero',
                    "{$slug}-{$ts}-hero.{$ext}",
                    'public'
                );
            }

            if ($this->hero_discount_image) {
                $ext = $this->hero_discount_image->getClientOriginalExtension()
                    ?: $this->hero_discount_image->extension() ?: 'png';

                $newHeroBadgePath = $this->hero_discount_image->storeAs(
                    'dishes/hero_badges',
                    "{$slug}-{$ts}-badge.{$ext}",
                    'public'
                );
            }

            $data = [
                'title'             => $this->title,
                'slug'              => $slug,
                'short_description' => $this->short_description,
                'category_id'       => (int)$this->category_id,
                'cuisine_id'        => (int)$this->cuisine_id,

                'price'         => (float)$this->price,
                'discount_type' => $this->discount_type ?: null,
                'discount'      => $this->discount !== null ? (float)$this->discount : null,
                'vat'           => $this->vat !== null ? (float)$this->vat : null,
                'sku'           => $this->sku ?: null,

                'track_stock'    => $this->track_stock,
                'daily_stock'    => $dailyStock,
                'available_from' => $this->available_from ?: null,
                'available_till' => $this->available_till ?: null,
                'visibility'     => $this->visibility,

                'thumbnail' => $newThumb ?: $oldThumb,
                'gallery'   => !empty($newGallery) ? $newGallery : $oldGallery,

                'meta_title'       => $this->meta_title,
                'meta_description' => $this->meta_description,
                'meta_keyword'     => $this->meta_keyword,

                'tags'       => $this->tags ?: [],
                'variations' => $this->normalizeVariations(),

                // hero fields
                'show_in_hero'       => $this->show_in_hero === 'Yes',
                'hero_image'         => $newHeroImagePath ?: $oldHeroImage,
                'hero_discount_image'=> $newHeroBadgePath ?: $oldHeroBadge,
            ];

            $buns    = array_values(array_filter((array)$this->buns));
            $crusts  = array_values(array_filter((array)$this->crusts));
            $addOns  = array_values(array_filter((array)$this->addOns));
            $related = array_values(array_filter((array)$this->related_dishes));

            DB::transaction(function () use ($data, $buns, $crusts, $addOns, $related) {
                $this->dish->update($data);
                $this->dish->buns()->sync($buns);
                $this->dish->crusts()->sync($crusts);
                $this->dish->addOns()->sync($addOns);
                $this->dish->relatedDishes()->sync($related);
            });

            // cleanup old images
            if ($newThumb && $oldThumb) Storage::disk('public')->delete($oldThumb);

            if (!empty($newGallery) && !empty($oldGallery)) {
                foreach ($oldGallery as $path) {
                    if ($path) Storage::disk('public')->delete($path);
                }
            }

            if ($newHeroImagePath && $oldHeroImage) {
                Storage::disk('public')->delete($oldHeroImage);
            }

            if ($newHeroBadgePath && $oldHeroBadge) {
                Storage::disk('public')->delete($oldHeroBadge);
            }

            $this->dispatch('dish-updated', id: $this->dish->id);
            $this->success(
                title: 'Dish updated successfully',
                position: 'top-right',
                showProgress: true,
                showCloseIcon: true,
            );

        } catch (\Throwable $e) {
            if ($newThumb) Storage::disk('public')->delete($newThumb);
            if (!empty($newGallery)) Storage::disk('public')->delete($newGallery);
            if ($newHeroImagePath) Storage::disk('public')->delete($newHeroImagePath);
            if ($newHeroBadgePath) Storage::disk('public')->delete($newHeroBadgePath);

            report($e);

            $this->error(
                title: 'Failed to update dish. Please try again.',
                position: 'top-right',
                showProgress: true,
                showCloseIcon: true,
            );
        }
    }

    public function render()
    {
        return view('livewire.admin.dishes.edit-dish');
    }
}
