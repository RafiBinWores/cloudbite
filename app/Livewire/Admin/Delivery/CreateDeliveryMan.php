<?php

namespace App\Livewire\Admin\Delivery;

use App\Models\DeliveryMan;
use Developermithu\Tallcraftui\Traits\WithTcToast;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateDeliveryMan extends Component
{
    use WithFileUploads;
    use WithTcToast;

    public $existingImage = null;

    // Identity (max 2, multi-select or one-by-one)
    public array $identity_images = [];

    public array $identity_uploads = [];

    public ?array $existingIdentityImages = null;

    public $first_name = null, $last_name = null, $image = null, $identity_type = 'nid', $phone_number = null, $identity_number = null, $email = null, $password = null, $status = 'active';

    public function rules(): array
    {
        return [
            'first_name'         => 'required|string|max:255',
            'last_name'          => 'nullable|string|max:255',
            'phone_number'       => 'required|string|max:32',
            'identity_number'       => 'required|string',
            'email'       => 'required|string|email|unique:delivery_men,email',
            'password'       => 'required|string|min:8',
            // 'confirm_password'       => 'required|string|min:8',

            // Profile image (single)
            'image'              => 'nullable|image|max:2048|mimes:jpeg,png,jpg,webp|dimensions:ratio=1/1',

            // Identity images (multi)
            'identity_images'    => 'required|array|max:2',
            'identity_images.*'  => 'image|mimes:jpeg,png,jpg,webp|max:2048',

            'identity_type'      => 'required|in:nid,driving_license,passport',
            'status'             => 'required|in:active,disable',
        ];
    }

    protected $messages = [
        'image.max'                => 'Profile image must be less than 2MB.',
        'image.dimensions'         => 'Profile image must be square (1:1).',

        'identity_images.max'      => 'Maximum 2 identity images allowed.',
        'identity_images.*.max'    => 'Each identity image must be less than 2MB.',
        'identity_images.*.mimes'  => 'Allowed formats: JPG, JPEG, PNG, WEBP.',
    ];

    public function clearImage()
    {
        $this->reset('image');
    }

    public function updatedImage(): void
    {
        $this->resetErrorBag('image');
    }

    // Identity helpers
    public function updatedIdentityUploads(): void
    {
        // Merge newly picked files into the confirmed list up to max 2
        if (!empty($this->identity_uploads)) {
            foreach ($this->identity_uploads as $file) {
                if (count($this->identity_images) >= 2) break;
                $this->identity_images[] = $file;
            }
            // Clear buffer so user can pick again freely
            $this->reset('identity_uploads');
        }

        // Validate incrementally
        $this->resetErrorBag('identity_images');
        $this->validateOnly('identity_images');
    }

    public function removeIdentityImage(int $index): void
    {
        if (isset($this->identity_images[$index])) {
            array_splice($this->identity_images, $index, 1);
            $this->resetErrorBag('identity_images');
        }
    }

    public function clearAllIdentity(): void
    {
        $this->reset(['identity_images', 'identity_uploads']);
        $this->resetErrorBag('identity_images');
    }


    // SUbmit the form
    public function submit(): void
    {
        $this->validate();

        $profilePath = null;
        $identityPaths = [];

        if ($this->image) {
            $profilePath = $this->image->store('avatar', 'public');
        }

        foreach ($this->identity_images as $img) {
            $identityPaths[] = $img->store('identity', 'public');
        }

        // TODO: Save to DB here (example)
        DeliveryMan::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone_number' => $this->phone_number,
            'identity_type' => $this->identity_type,
            'status' => $this->status,
            'profile_image' => $profilePath,
            'identity_images' => $identityPaths,
            'identity_number' => $this->identity_number,
            'email' => $this->email,
            'password' => bcrypt($this->password),
        ]);

        // Keep previews after save (edit-mode like)
        $this->existingImage = $profilePath;
        $this->existingIdentityImages = $identityPaths;

        // Reset file inputs
        $this->clearImage();
        $this->clearAllIdentity();

        // ✅ Reset form
        $this->reset();

        // Optional: keep defaults
        $this->status = 'active';

        $this->success(
            title: 'Delivery man created successfully.',
            position: 'top-right',
            showProgress: true,
            showCloseIcon: true,
        );
    }

    public function render()
    {
        return view('livewire.admin.delivery.create-delivery-man');
    }
}
