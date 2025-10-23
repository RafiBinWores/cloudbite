<?php

namespace App\Livewire\Admin\Delivery;

use App\Models\DeliveryMan;
use Developermithu\Tallcraftui\Traits\WithTcToast;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class EditDeliveryMan extends Component
{
    use WithFileUploads, WithTcToast;

    public DeliveryMan $deliveryMan;

    // Form fields
    public ?string $first_name = null;
    public ?string $last_name = null;
    public ?string $phone_number = null;
    public ?string $identity_type = 'nid';
    public ?string $identity_number = null;
    public ?string $email = null;
    public ?string $password = null; // optional on edit
    public string $status = 'active';

    // Profile image (single)
    public $image = null;            // new upload
    public ?string $existingImage = null; // preview path

    // Identity images (max 2)
    public array $identity_images = [];
    public array $identity_uploads = [];
    public ?array $existingIdentityImages = [];

    public function mount(DeliveryMan $deliveryMan): void
    {
        $this->deliveryMan = $deliveryMan;

        // Seed fields
        $this->first_name      = $deliveryMan->first_name;
        $this->last_name       = $deliveryMan->last_name;
        $this->phone_number    = $deliveryMan->phone_number;
        $this->identity_type   = $deliveryMan->identity_type ?? 'nid';
        $this->identity_number = $deliveryMan->identity_number;
        $this->email           = $deliveryMan->email;
        $this->status          = $deliveryMan->status ?? 'active';

        // Existing media
        $this->existingImage = $deliveryMan->profile_image; // storage path
        $this->existingIdentityImages = array_values(array_filter(
            (array) ($deliveryMan->identity_images ?? [])
        ));
        // Ensure max 2
        $this->existingIdentityImages = array_slice($this->existingIdentityImages, 0, 2);
    }

    public function rules(): array
    {
        $id = $this->deliveryMan->id;

        return [
            'first_name'       => ['required', 'string', 'max:255'],
            'last_name'        => ['nullable', 'string', 'max:255'],
            'phone_number'     => [
                'required',
                'string',
                'regex:/^(?:\+?88)?01[3-9]\d{8}$/',
                Rule::unique('delivery_men', 'phone_number')->ignore($id),
            ],
            'identity_type'    => ['required', 'in:nid,driving_license,passport'],
            'identity_number'  => ['required', 'string', 'max:255'],
            'email'            => [
                'required',
                'string',
                'email',
                Rule::unique('delivery_men', 'email')->ignore($id),
            ],
            'password'         => ['nullable', 'string', 'min:8'],
            'status'           => ['required', 'in:active,disable'],

            // profile image
            'image'            => ['nullable', 'image', 'max:2048', 'mimes:jpeg,png,jpg,webp', 'dimensions:ratio=1/1'],

            // identity images (new uploads; total existing+new must be <= 2)
            'identity_images'  => ['array', 'max:2'],
            'identity_images.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }

    protected $messages = [
        'phone_number.regex'      => 'Enter a valid Bangladeshi mobile number.',
        'image.max'               => 'Profile image must be less than 2MB.',
        'image.dimensions'        => 'Profile image must be square (1:1).',
        'identity_images.max'     => 'Maximum 2 identity images allowed.',
        'identity_images.*.max'   => 'Each identity image must be less than 2MB.',
        'identity_images.*.mimes' => 'Allowed formats: JPG, JPEG, PNG, WEBP.',
    ];

    public function updatedImage(): void
    {
        $this->resetErrorBag('image');
    }

    public function clearImage(): void
    {
        $this->reset('image');
    }

    public function updatedIdentityUploads(): void
    {
        // Add into final new uploads list but respect total max (2 - existing count)
        $currentExisting = count($this->existingIdentityImages ?? []);
        $room = max(0, 2 - $currentExisting);

        if ($room <= 0) {
            $this->reset('identity_uploads');
            return;
        }

        foreach ($this->identity_uploads as $file) {
            if (count($this->identity_images) >= $room) break;
            $this->identity_images[] = $file;
        }

        $this->reset('identity_uploads');
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

    public function removeExistingIdentityImage(int $index): void
    {
        if (isset($this->existingIdentityImages[$index])) {
            // Optionally delete the file from storage
            $path = $this->existingIdentityImages[$index];
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            array_splice($this->existingIdentityImages, $index, 1);
        }
    }

    public function save(): void
    {
        // Ensure total images (existing + new) <= 2 before validation
        $total = count($this->existingIdentityImages) + count($this->identity_images);
        if ($total > 2) {
            $this->addError('identity_images', 'You can keep at most 2 identity images in total.');
            return;
        }

        $this->validate();

        // Handle profile image replacement
        if ($this->image) {
            // delete previous if desired
            if ($this->existingImage && Storage::disk('public')->exists($this->existingImage)) {
                Storage::disk('public')->delete($this->existingImage);
            }
            $newPath = $this->image->store('avatar', 'public');
            $this->existingImage = $newPath;
            $this->deliveryMan->profile_image = $newPath;
        }

        // Handle identity images merge (existing + newly uploaded)
        $identityPaths = $this->existingIdentityImages ?? [];
        foreach ($this->identity_images as $img) {
            if (count($identityPaths) >= 2) break;
            $identityPaths[] = $img->store('identity', 'public');
        }
        // Ensure max 2
        $identityPaths = array_slice($identityPaths, 0, 2);

        // Assign data
        $this->deliveryMan->first_name      = $this->first_name;
        $this->deliveryMan->last_name       = $this->last_name;
        $this->deliveryMan->phone_number    = $this->phone_number;
        $this->deliveryMan->identity_type   = $this->identity_type;
        $this->deliveryMan->identity_number = $this->identity_number;
        $this->deliveryMan->email           = $this->email;
        $this->deliveryMan->status          = $this->status;
        $this->deliveryMan->identity_images = $identityPaths;

        if (!empty($this->password)) {
            $this->deliveryMan->password = bcrypt($this->password);
        }

        $this->deliveryMan->save();

        // Refresh local state
        $this->existingIdentityImages = $identityPaths;
        $this->clearImage();
        $this->reset('identity_images', 'identity_uploads', 'password');
        $this->success(
            title: 'Delivery man updated successfully.',
            position: 'top-right',
            showProgress: true,
            showCloseIcon: true,
        );
    }

    public function render()
    {
        return view('livewire.admin.delivery.edit-delivery-man');
    }
}
