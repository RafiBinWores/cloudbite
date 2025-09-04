<?php

namespace App\Livewire\Admin\Dishes;

use Livewire\Component;
use Livewire\WithFileUploads;

class CreateDish extends Component
{
    use WithFileUploads;

    public $title, $short_description, $description, $status = 'active', $category, $related_dish, $tags, $crusts, $buns, $addOns, $price, $discount, $vat, $discount_type, $sku, $track_stock = 'No', $daily_stock, $available_from, $available_till, $visibility = "Yes", $thumbnail;
    
    public $gallery = [];


    // Validation rules
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|unique:dishes,title',
            'short_description' => 'required',
            'description' => 'required',
            'category_id' => 'required',
            'related_dish_id' => 'required',
            'price' => 'required',
            'tags' => 'required',
            'buns' => 'nullable',
            'crusts' => 'nullable',
            'addOns' => 'nullable',
            'discount_type' => 'nullable',
            'discount' => 'nullable',
            'vat' => 'nullable',
            'sku' => 'nullable',
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

        // dd($this->user_id, $this->category);
        $this->validate();
    }

    public function render()
    {
        return view('livewire.admin.dishes.create-dish');
    }
}
