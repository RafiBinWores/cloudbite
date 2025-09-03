<?php

namespace App\Livewire\Admin\Cuisine;

use App\Models\Cuisine;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Livewire\Attributes\On;

class CuisineForm extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $cuisineId = null;
    public $isView = false;
    public $existingImage = null;

    public $name = null, $image = null, $meta_title = null, $meta_description = null, $meta_keywords = null, $status = 'active';

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:cuisines,name,' . $this->cuisineId,
            'image' => 'nullable|image|max:2048|mimes:jpeg,png,jpg,svg,webp',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'status' => 'required|in:active,disable',
        ];
    }

    // Clear the image upload
    public function clearImage()
    {
        $this->reset('image');
    }

    public function updatedImage(): void
    {
        $this->resetErrorBag('image');
    }

     // Submit the form data
    public function submit()
    {
        $this->validate();

        $imagePath = null;
        if ($this->image) {
            $imagePath = $this->image->store('cuisine', 'public'); // => storage/app/public/cuisine/...
            // (Optional) optimize here with spatie/laravel-image-optimizer if you installed it:
            // \Spatie\LaravelImageOptimizer\Facades\ImageOptimizer::optimize(storage_path('app/public/'.$imagePath));
        }

        if ($this->cuisineId) {

            $cuisine = Cuisine::find($this->cuisineId);
            if (!$cuisine) {
                $this->dispatch('toast', type: 'error', message: 'Cuisine not found.');
                return;
            }

            $dataNoImage = [
                'name'             => $this->name,
                'slug'             => Str::slug($this->name),
                'meta_title'       => $this->meta_title,
                'meta_description' => $this->meta_description,
                'meta_keywords'    => $this->meta_keywords,
                'status'           => $this->status,
            ];

            // Check for changes WITHOUT persisting
            $original = $cuisine->getAttributes();
            $cuisine->fill($dataNoImage);
            $hasFieldChanges = $cuisine->isDirty();
            $cuisine->fill($original);

            $hasImageChange = (bool) $this->image;

            if (!$hasFieldChanges && !$hasImageChange) {
                $this->dispatch('toast', type: 'warning', message: 'Nothing to update.');
                $this->dispatch('cuisines:refresh');
                Flux::modal('cuisine-modal')->close();
                return;
            }

            // Handle image upload first (only if changed)
            $newImagePath = null;
            if ($hasImageChange) {
                $newImagePath = $this->image->store('cuisine', 'public');

                // delete old file if there was one
                if (!empty($cuisine->image)) {
                    Storage::disk('public')->delete($cuisine->image);
                }
            }

            // Build final payload
            $saveData = $dataNoImage;
            if ($hasImageChange) {
                $saveData['image'] = $newImagePath;
            }

            // Persist only once
            $cuisine->update($saveData);

            // Keep UI in sync (if you show existing image preview after save)
            if ($hasImageChange) {
                $this->existingImage = $newImagePath;
                $this->reset('image');
            }

            $this->dispatch('toast', type: 'success', message: 'Cuisine updated successfully.');
        } else {

            cuisine::create([
                'name' => $this->name,
                'slug' => Str::slug($this->name),
                'image' => $imagePath,
                'meta_title' => $this->meta_title,
                'meta_description' => $this->meta_description,
                'meta_keywords' => $this->meta_keywords,
                'status' => $this->status,
            ]);

            $this->reset();
            $this->status = 'active';

            $this->dispatch('toast', type: 'success', message: 'Cuisine created successfully.');
        }

        $this->dispatch('cuisines:refresh');
        Flux::modal('cuisine-modal')->close();
    }

    #[On('open-cuisine-modal')]
    public function cuisineDetail($mode, $cuisine = null)
    {

        $this->isView = $mode === 'view';

        if ($mode === 'create') {

            $this->isView = false;
            $this->reset();
            $this->status = 'active';
        } else {
            // dd($cuisine);
            $this->cuisineId = $cuisine['id'];

            $this->name = $cuisine['name'];
            $this->existingImage = $cuisine['image'];
            $this->meta_title = $cuisine['meta_title'];
            $this->meta_description = $cuisine['meta_description'];
            $this->meta_keywords = $cuisine['meta_keywords'];
            $this->status = $cuisine['status'];
        }
    }

    public function render()
    {
        return view('livewire.admin.cuisine.cuisine-form');
    }
}
