<?php

namespace App\Livewire\Admin\Business;

use App\Models\CompanyInfo;
use App\Models\ShippingSetting;
use Developermithu\Tallcraftui\Traits\WithTcToast;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

// Intervention Image (v3)
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver; // use Imagick driver if you prefer
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Encoders\PngEncoder;

class BusinessSetup extends Component
{
    use WithFileUploads, WithTcToast;

    public string $activeTab = 'business';

    // Company Info fields
    public string $company_name = '';
    public string $phone = '';
    public string $email = '';
    public string $address = '';
    public string $facebook = '';
    public string $instagram = '';
    public string $twitter = '';
    public string $tiktok = '';
    public string $youtube = '';
    public string $whatsapp = '';
    public string $footer_description_text = '';


    public $logo_upload;
    public $favicon_upload;

    public ?string $logo = null;
    public ?string $favicon = null;

    public ?CompanyInfo $info = null;

    // Delivery settings fields
    public float $base_fee = 0.00;
    public bool  $free_delivery = false;
    public float $free_minimum = 0.00;

    // cache to restore previous typed value after toggling OFF
    public ?float $free_minimum_cache = null;

    public ?ShippingSetting $record = null;

    // Lifecycle 
    public function mount(): void
    {
        // Company info singleton (id=1)
        $this->info = CompanyInfo::find(1) ?? new CompanyInfo();
        if (! $this->info->exists) {
            $this->info->id = 1;
            $this->info->save();
        }

        $this->company_name = (string) ($this->info->company_name ?? '');
        $this->phone        = (string) ($this->info->phone ?? '');
        $this->email        = (string) ($this->info->email ?? '');
        $this->address      = (string) ($this->info->address ?? '');
        $this->logo         = $this->info->logo;
        $this->favicon      = $this->info->favicon;
        $this->facebook  = (string) ($this->info->facebook ?? '');
        $this->instagram = (string) ($this->info->instagram ?? '');
        $this->twitter   = (string) ($this->info->twitter ?? '');
        $this->tiktok    = (string) ($this->info->tiktok ?? '');
        $this->youtube   = (string) ($this->info->youtube ?? '');
        $this->whatsapp  = (string) ($this->info->whatsapp ?? '');
        $this->footer_description_text  = (string) ($this->info->footer_description_text ?? '');

        // Shipping settings singleton (id=1)
        $this->record = ShippingSetting::find(1) ?? new ShippingSetting();
        if (! $this->record->exists) {
            $this->record->id = 1;
            $this->record->base_fee = 0;
            $this->record->free_delivery = false;
            $this->record->free_minimum = 0;
            $this->record->save();
        }

        $this->base_fee      = (float) $this->record->base_fee;
        $this->free_delivery = (bool)  $this->record->free_delivery;
        $this->free_minimum  = (float) $this->record->free_minimum;

        $this->free_minimum_cache = $this->free_minimum ?: null;
    }

    // Validation
    protected function rules(): array
    {
        return [
            // company info
            'company_name'   => ['required', 'string', 'max:255'],
            'phone'          => ['nullable', 'string', 'max:30'],
            'email'          => ['nullable', 'email', 'max:255'],
            'address'        => ['nullable', 'string', 'max:2000'],
            'footer_description_text'        => ['nullable', 'string', 'max:2000'],
            'logo_upload'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'favicon_upload' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,ico,svg', 'max:2048'],
            'facebook'       => ['nullable', 'url'],
            'instagram'      => ['nullable', 'url'],
            'twitter'        => ['nullable', 'url'],
            'tiktok'         => ['nullable', 'url'],
            'youtube'        => ['nullable', 'url'],
            'whatsapp'       => ['nullable', 'url'],

            // delivery
            'base_fee'       => ['required', 'numeric', 'min:0'],
            'free_delivery'  => ['boolean'],
            'free_minimum'   => ['required', 'numeric', 'min:0'],
        ];
    }

    // Helpers
    protected function imageManager(): ImageManager
    {
        // Switch to Imagick driver if you have it: new \Intervention\Image\Drivers\Imagick\Driver()
        return new ImageManager(new Driver());
    }

    // Purge files in 'company' dir that start with a given prefix
    protected function purgeFilesByPrefix(string $prefix): void
    {
        foreach (Storage::disk('public')->files('company') as $existing) {
            $basename = basename($existing);
            if (Str::startsWith($basename, $prefix)) {
                Storage::disk('public')->delete($existing);
            }
        }
    }

    // Company Info Submit
    public function companyInformationSubmit(): void
    {
        // Validate only the company info fields + uploads
        $this->validate();

        $info = CompanyInfo::find(1) ?? new CompanyInfo();
        $info->id = 1;

        $img = $this->imageManager();
        $ts  = now()->timestamp;

        // --- LOGO upload (optimize + timestamp name) ---
        if ($this->logo_upload) {
            $this->purgeFilesByPrefix('logo_');

            $mime = $this->logo_upload->getMimeType();
            $ext  = strtolower($this->logo_upload->getClientOriginalExtension() ?: '');

            if ($ext === 'svg' || $mime === 'image/svg+xml') {
                $filename = "logo_{$ts}.svg";
                $path = $this->logo_upload->storeAs('company', $filename, 'public');
            } else {
                // 3:1 cover @ 1200x400 -> webp(80)
                $image = $img->read($this->logo_upload->getRealPath());
                $image->orient();
                $image = $image->cover(1200, 400, 'center');
                $encoded = $image->encode(new WebpEncoder(quality: 80));

                $filename = "logo_{$ts}.webp";
                $path = "company/{$filename}";
                Storage::disk('public')->put($path, $encoded);
            }

            $info->logo = $path;
            $this->logo = $path;
            $this->logo_upload = null;
        }

        // FAVICON upload (optimize + timestamp name)
        if ($this->favicon_upload) {
            $this->purgeFilesByPrefix('favicon_');

            $mime = $this->favicon_upload->getMimeType();
            $ext  = strtolower($this->favicon_upload->getClientOriginalExtension() ?: '');

            if ($ext === 'svg' || $mime === 'image/svg+xml') {
                $filename = "favicon_{$ts}.svg";
                $path = $this->favicon_upload->storeAs('company', $filename, 'public');
            } else {
                // square cover -> 32x32 -> PNG
                $image = $img->read($this->favicon_upload->getRealPath());
                $image->orient();
                $image = $image->cover(512, 512, 'center'); // start larger for crisp downscale
                $png32 = $image->scale(32)->encode(new PngEncoder());

                $filename = "favicon_{$ts}.png";
                $path = "company/{$filename}";
                Storage::disk('public')->put($path, $png32);
            }

            $info->favicon = $path;
            $this->favicon = $path;
            $this->favicon_upload = null;
        }

        // Scalars
        $info->company_name = $this->company_name;
        $info->phone        = $this->phone;
        $info->email        = $this->email;
        $info->address      = $this->address;
        $info->facebook  = $this->facebook;
        $info->instagram = $this->instagram;
        $info->twitter   = $this->twitter;
        $info->tiktok    = $this->tiktok;
        $info->youtube   = $this->youtube;
        $info->whatsapp  = $this->whatsapp;
        $info->footer_description_text  = $this->footer_description_text;
        $info->save();

        $this->info = $info;

        $this->success(
            title: 'Company information updated successfully.',
            position: 'top-right',
            showProgress: true,
            showCloseIcon: true,
        );
    }

    // Instant Toggle Logic
    public function updatedFreeDelivery(bool $value): void
    {
        if ($value) {
            // turning ON: remember current value then lock to 0
            $this->free_minimum_cache = $this->free_minimum ?: ($this->free_minimum_cache ?? 0.00);
            $this->free_minimum = 0.00;
        } else {
            // turning OFF: restore last typed value
            $this->free_minimum = $this->free_minimum_cache ?? 0.00;
        }
    }

    public function updatedFreeMinimum($val): void
    {
        if (! $this->free_delivery) {
            $num = is_numeric($val) ? (float) $val : 0.00;
            $this->free_minimum = max(0.00, $num);
            $this->free_minimum_cache = $this->free_minimum;
        }
    }

    // Save Delivery Settings
    public function save(): void
    {
        // Validate only delivery fields
        $this->validate([
            'base_fee'      => ['required', 'numeric', 'min:0'],
            'free_delivery' => ['boolean'],
            'free_minimum'  => ['required', 'numeric', 'min:0'],
        ]);

        $setting = ShippingSetting::find(1) ?? new ShippingSetting();
        $setting->id = 1;

        if ($this->free_delivery) {
            $this->free_minimum = 0.00;
        }

        $setting->base_fee      = round($this->base_fee, 2);
        $setting->free_delivery = (bool) $this->free_delivery;
        $setting->free_minimum  = round($this->free_minimum, 2);
        $setting->save();

        $this->record = $setting;

        $this->success(
            title: 'Delivery setting updated successfully.',
            position: 'top-right',
            showProgress: true,
            showCloseIcon: true,
        );
    }

    // Render
    public function render()
    {
        return view('livewire.admin.business.business-setup');
    }
}
