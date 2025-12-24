<?php

namespace App\Livewire\Admin\Banners;

use App\Models\Banner;
use Carbon\Carbon;
use Developermithu\Tallcraftui\Traits\WithTcToast;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

// Intervention Image v3
use Intervention\Image\ImageManager;
use Intervention\Image\Encoders\WebpEncoder;

class BannerForm extends Component
{
    use WithFileUploads;
    use WithTcToast;

    public $bannerId = null;
    public $isView = false;
    public $existingImage = null;

    public $title = null,
        $description = null,
        $image = null,
        $item_id = null,
        $status = 'active',
        $start_at = null,
        $end_at = null;

    public bool $is_slider = false;

    public string $item_type = 'dish';

    protected $casts = [
        'is_slider' => 'boolean',
    ];

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|unique:banners,title,' . $this->bannerId,
            'description' => 'nullable|string',
            'is_slider' => 'boolean',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',

            'image' => $this->bannerId
                ? 'nullable|image|max:5048|mimes:jpeg,png,jpg,svg,webp'
                : 'required|image|max:5048|mimes:jpeg,png,jpg,svg,webp',

            'item_type' => 'required|string|max:255',
            'item_id' => 'required|numeric',
            'status' => 'required|in:active,disable',
        ];
    }

    public function updated($propertyName)
    {
        $this->resetValidation($propertyName);
    }

    public function updatedItemType($value)
    {
        $this->item_id = null;
        $this->resetValidation(['item_type', 'item_id']);
    }

    public function updatedIsSlider()
    {
        // if user switches type, clear old image-related errors
        $this->resetValidation('image');
    }

    public function clearImage()
    {
        $this->reset('image');
        $this->resetValidation('image');
    }

    public function updatedImage(): void
    {
        $this->resetValidation('image');
    }

    /**
     * ✅ Optimize + store banner image
     * - SVG: store as-is
     * - Others: orient + cover + webp encode
     *
     * @param  string  $prefix   filename prefix
     * @return string|null       stored path (relative to public disk)
     */
    protected function storeOptimizedBannerImage(string $prefix = 'banner_'): ?string
    {
        if (!$this->image) return null;

        $ts = now()->format('Ymd_His');
        $mime = $this->image->getMimeType();
        $ext  = strtolower($this->image->getClientOriginalExtension() ?: '');

        // SVG: keep original
        if ($ext === 'svg' || $mime === 'image/svg+xml') {
            $filename = "{$prefix}{$ts}.svg";
            return $this->image->storeAs('banners', $filename, 'public');
        }

        // Raster: optimize
        $img = ImageManager::gd(); // use ::imagick() if your server supports
        $image = $img->read($this->image->getRealPath());
        $image->orient();

        /**
         * Target sizes:
         * - Slider: enforce 3:1 final render size (good default for hero/slider)
         * - Single: keep nice banner size (still 3:1-ish for consistency)
         */
        if ($this->is_slider) {
            // strict 3:1 output
            $image = $image->cover(1500, 500, 'center'); // 3:1
        } else {
            // single banner: still clean and light (you can change size)
            $image = $image->cover(1200, 600, 'center'); // 2:1 (or change to 1200,400 if you want same ratio)
        }

        $encoded = $image->encode(new WebpEncoder(quality: 82));
        $filename = "{$prefix}{$ts}.webp";
        $path = "banners/{$filename}";

        Storage::disk('public')->put($path, $encoded);

        return $path;
    }

    public function submit()
    {
        $this->validate();

        // ✅ Optimize image (instead of store())
        $imagePath = null;
        if ($this->image) {
            $imagePath = $this->storeOptimizedBannerImage('banner_');
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
                'title' => $this->title,
                'description' => $this->description,
                'is_slider' => (bool) $this->is_slider,
                'start_at' => $this->start_at ?: null,
                'end_at' => $this->end_at ?: null,

                'item_type' => $this->item_type,
                'item_id' => $this->item_id,
                'status' => $this->status,
            ];

            // check changes without saving
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

            // upload new image if changed (optimized)
            $newImagePath = null;
            if ($hasImageChange) {
                $newImagePath = $imagePath; // already optimized & stored

                // delete old file
                if (!empty($banner->image)) {
                    Storage::disk('public')->delete($banner->image);
                }
            }

            $saveData = $dataNoImage;
            if ($hasImageChange) {
                $saveData['image'] = $newImagePath;
            }

            $banner->update($saveData);

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
                'description' => $this->description,
                'is_slider' => (bool) $this->is_slider,
                'start_at' => $this->start_at ?: null,
                'end_at' => $this->end_at ?: null,

                'image' => $imagePath,
                'item_type' => $this->item_type,
                'item_id' => $this->item_id,
                'status' => $this->status,
            ]);

            $this->reset();
            $this->status = 'active';
            $this->item_type = 'dish';
            $this->is_slider = false;

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
            $this->item_type = 'dish';
            $this->is_slider = false;
            return;
        }

        $this->bannerId = $banner['id'];

        $this->title = $banner['title'];
        $this->description = $banner['description'] ?? null;
        $this->is_slider = (bool) ($banner['is_slider'] ?? false);

        $this->start_at = !empty($banner['start_at'])
            ? Carbon::parse($banner['start_at'])->format('Y-m-d\TH:i')
            : null;

        $this->end_at = !empty($banner['end_at'])
            ? Carbon::parse($banner['end_at'])->format('Y-m-d\TH:i')
            : null;

        $this->existingImage = $banner['image'];
        $this->item_type = $banner['item_type'];
        $this->item_id = $banner['item_id'];
        $this->status = $banner['status'];
    }

    public function render()
    {
        return view('livewire.admin.banners.banner-form');
    }
}
