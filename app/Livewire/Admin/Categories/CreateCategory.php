<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;

class CreateCategory extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $categoryId = null;
    public $isView = false;
    public $existingImage = null;

    public $name = null, $image = null, $meta_title = null, $meta_description = null, $meta_keywords = null, $status = 'active';

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:categories,name,' . $this->categoryId,
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
            $imagePath = $this->image->store('category', 'public'); // => storage/app/public/category/...
            // (Optional) optimize here with spatie/laravel-image-optimizer if you installed it:
            // \Spatie\LaravelImageOptimizer\Facades\ImageOptimizer::optimize(storage_path('app/public/'.$imagePath));
        }

        if ($this->categoryId) {

            $category = Category::find($this->categoryId);
            if (!$category) {
                $this->dispatch('toast', type: 'error', message: 'Category not found.');
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
            $original = $category->getAttributes();
            $category->fill($dataNoImage);
            $hasFieldChanges = $category->isDirty();
            $category->fill($original);

            $hasImageChange = (bool) $this->image;

            if (!$hasFieldChanges && !$hasImageChange) {
                $this->dispatch('toast', type: 'warning', message: 'Nothing to update.');
                $this->dispatch('categories:refresh');
                Flux::modal('category-modal')->close();
                return;
            }

            // Handle image upload first (only if changed)
            $newImagePath = null;
            if ($hasImageChange) {
                $newImagePath = $this->image->store('category', 'public');

                // delete old file if there was one
                if (!empty($category->image)) {
                    Storage::disk('public')->delete($category->image);
                }
            }

            // Build final payload
            $saveData = $dataNoImage;
            if ($hasImageChange) {
                $saveData['image'] = $newImagePath;
            }

            // Persist only once
            $category->update($saveData);

            // Keep UI in sync (if you show existing image preview after save)
            if ($hasImageChange) {
                $this->existingImage = $newImagePath;
                $this->reset('image');
            }

            $this->dispatch('toast', type: 'success', message: 'Category updated successfully.');
        } else {

            Category::create([
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

            $this->dispatch('toast', type: 'success', message: 'Category created successfully.');
        }

        $this->dispatch('categories:refresh');
        Flux::modal('category-modal')->close();
    }

    #[On('open-category-modal')]
    public function categoryDetail($mode, $category = null)
    {

        $this->isView = $mode === 'view';

        if ($mode === 'create') {

            $this->isView = false;
            $this->reset();
            $this->status = 'active';
        } else {
            // dd($category);
            $this->categoryId = $category['id'];

            $this->name = $category['name'];
            $this->existingImage = $category['image'];
            $this->meta_title = $category['meta_title'];
            $this->meta_description = $category['meta_description'];
            $this->meta_keywords = $category['meta_keywords'];
            $this->status = $category['status'];
        }
    }

    // Render the livewire component
    public function render()
    {
        return view('livewire.admin.categories.create-category');
    }
}
