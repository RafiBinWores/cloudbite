<?php

namespace App\Livewire\Admin\Banners;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Dish;
use Developermithu\Tallcraftui\Traits\WithTcToast;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class BannerForm extends Component
{
    use WithFileUploads;
    use WithTcToast;

    public $bannerId = null;
    public $isView = false;
    public $existingImage = null;

    public $title = null, $image = null, $item_id = null, $status = 'active';

    public string $item_type = 'dish';

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|unique:banners,title,' . $this->bannerId,
            'image' => 'nullable|image|max:5048|mimes:jpeg,png,jpg,svg,webp',
            'item_type' => 'required|string|max:255',
            'item_id' => 'required|numeric',
            'status' => 'required|in:active,disable',
        ];
    }

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
            $imagePath = $this->image->store('banners', 'public');
        }

        if ($this->bannerId) {

            $banner = Banner::find($this->bannerId);
            if (!$banner) {
                $this->error(
                    title: 'Banner not found!',
                    position: 'top-right',
                    showProgress: true,
                    showCloseIcon: true,
                );
                return;
            }

            $dataNoImage = [
                'title'             => $this->title,
                'item_type'         => $this->item_type,
                'item_id'              => $this->item_id,
                'status'            => $this->status,
            ];

            // Check for changes WITHOUT persisting
            $original = $banner->getAttributes();
            $banner->fill($dataNoImage);
            $hasFieldChanges = $banner->isDirty();
            $banner->fill($original);

            $hasImageChange = (bool) $this->image;

            if (!$hasFieldChanges && !$hasImageChange) {
                $this->warning(
                    title: 'Nothing to update.',
                    position: 'top-right',
                    showProgress: true,
                    showCloseIcon: true,
                );
                $this->dispatch('banners:refresh');
                Flux::modal('banner-modal')->close();
                return;
            }

            // Handle image upload first (only if changed)
            $newImagePath = null;
            if ($hasImageChange) {
                $newImagePath = $this->image->store('banners', 'public');

                // delete old file if there was one
                if (!empty($banner->image)) {
                    Storage::disk('public')->delete($banner->image);
                }
            }

            // Build final payload
            $saveData = $dataNoImage;
            if ($hasImageChange) {
                $saveData['image'] = $newImagePath;
            }

            // Persist only once
            $banner->update($saveData);

            // Keep UI in sync (if you show existing image preview after save)
            if ($hasImageChange) {
                $this->existingImage = $newImagePath;
                $this->reset('image');
            }

            $this->success(
                title: 'Banner updated successfully.',
                position: 'top-right',
                showProgress: true,
                showCloseIcon: true,
            );
        } else {

            Banner::create([
                'title' => $this->title,
                'image' => $imagePath,
                'item_type' => $this->item_type,
                'item_id' => $this->item_id,
                'status' => $this->status,
            ]);

            $this->reset();
            $this->status = 'active';

            $this->success(
                title: 'Banner created successfully.',
                position: 'top-right',
                showProgress: true,
                showCloseIcon: true,
            );
        }

        $this->dispatch('banners:refresh');
        Flux::modal('banner-modal')->close();
    }

    #[On('open-banner-modal')]
    public function bannerDetail($mode, $banner = null)
    {

        $this->isView = $mode === 'view';

        if ($mode === 'create') {

            $this->isView = false;
            $this->reset();
            $this->status = 'active';
        } else {
            // dd($category);
            $this->bannerId = $banner['id'];

            $this->title = $banner['title'];
            $this->existingImage = $banner['image'];
            $this->item_type = $banner['item_type'];
            $this->item_id = $banner['item_id'];
            $this->status = $banner['status'];
        }
    }

    public function render()
    {
        return view('livewire.admin.banners.banner-form');
    }
}
