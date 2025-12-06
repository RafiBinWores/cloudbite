<?php

namespace App\Livewire\Admin\EmailTemplate;

use App\Models\CompanyInfo;
use App\Models\EmailTemplate;
use Developermithu\Tallcraftui\Traits\WithTcToast;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class EmailTemplates extends Component
{
    use WithFileUploads, WithTcToast;

    // Tabs
    public string $activeTab = 'dish_order';
    public string $pageTitle = 'Dish Order Email Template';

    // Template row
    public EmailTemplate $template;

    // Upload
    public $logo;
    public ?string $logo_path = null;

    // Content fields (all nullable / no default text)
    public string $main_title = '';
    public string $header_title = '';
    public string $body = '';
    public string $button_text = '';
    public string $footer_section = '';
    public string $copyright = '';

    // Policy flags
    public bool $show_privacy_policy = true;
    public bool $show_refund_policy = true;
    public bool $show_cancellation_policy = true;
    public bool $show_contact_us = true;

    // Social flags
    public bool $show_facebook = true;
    public bool $show_instagram = true;
    public bool $show_twitter = true;
    public bool $show_tiktok = true;
    public bool $show_youtube = true;
    public bool $show_whatsapp = false;

    // Preview data
    public array $preview = [];

    // Social URLs from company_info
    public array $companySocials = [];

    public function mount(): void
    {
        // Load socials from company_info (first row)
        $info = CompanyInfo::first();

        $this->companySocials = [
            'facebook'  => $info->facebook  ?? null,
            'instagram' => $info->instagram ?? null,
            'twitter'   => $info->twitter   ?? null,
            'tiktok'    => $info->tiktok    ?? null,
            'youtube'   => $info->youtube   ?? null,
            'whatsapp'  => $info->whatsapp  ?? null,
        ];

        // Initial tab
        $this->loadTemplateForTab($this->activeTab);
    }

    // Called from Blade
    public function setTab(string $tab): void
    {
        $this->loadTemplateForTab($tab);
    }

    protected function loadTemplateForTab(string $tab): void
    {
        if (! in_array($tab, ['dish_order', 'meal_plan_order'])) {
            $tab = 'dish_order';
        }

        $this->activeTab = $tab;

        // Map tab -> DB key
        $key = $tab === 'meal_plan_order' ? 'meal_plan_booking' : 'single_order';

        // Page title
        $this->pageTitle = $tab === 'dish_order'
            ? 'Dish Order Email Template'
            : 'Meal Plan Booking Email Template';

        // Defaults (only used for first create + boolean flags)
        $defaults = $this->defaultValuesForKey($key);

        // Find or create template row
        $this->template = EmailTemplate::firstOrCreate(
            ['key' => $key],
            $defaults
        );

        // Hydrate content: TEXT → no default, just empty if null
        $this->logo_path      = $this->template->logo_path;
        $this->main_title     = $this->template->main_title     ?? '';
        $this->header_title   = $this->template->header_title   ?? '';
        $this->body           = $this->template->body           ?? '';
        $this->button_text    = $this->template->button_text    ?? '';
        $this->footer_section = $this->template->footer_section ?? '';
        $this->copyright      = $this->template->copyright      ?? '';

        // Policies
        $this->show_privacy_policy      = $this->template->show_privacy_policy      ?? true;
        $this->show_refund_policy       = $this->template->show_refund_policy       ?? true;
        $this->show_cancellation_policy = $this->template->show_cancellation_policy ?? true;
        $this->show_contact_us          = $this->template->show_contact_us          ?? true;

        // Socials
        $this->show_facebook  = $this->template->show_facebook  ?? true;
        $this->show_instagram = $this->template->show_instagram ?? true;
        $this->show_twitter   = $this->template->show_twitter   ?? true;
        $this->show_tiktok    = $this->template->show_tiktok    ?? true;
        $this->show_youtube   = $this->template->show_youtube   ?? true;
        $this->show_whatsapp  = $this->template->show_whatsapp  ?? false;

        // Preview mock data (only for admin preview; REAL emails use real order data)
        $this->preview = $this->previewDataForKey($key);

        // Reset temp upload
        $this->reset('logo');
    }

    protected function defaultValuesForKey(string $key): array
    {
        // All textual fields are NULL by default – you fill them in from UI
        if ($key === 'meal_plan_booking') {
            return [
                'logo_path'      => null,
                'main_title'     => null,
                'header_title'   => null,
                'body'           => null,
                'button_text'    => null,
                'footer_section' => null,
                'copyright'      => null,

                'show_privacy_policy'      => true,
                'show_refund_policy'       => true,
                'show_cancellation_policy' => true,
                'show_contact_us'          => true,

                'show_facebook'  => true,
                'show_instagram' => true,
                'show_twitter'   => true,
                'show_tiktok'    => true,
                'show_youtube'   => true,
                'show_whatsapp'  => false,
            ];
        }

        // single_order
        return [
            'logo_path'      => null,
            'main_title'     => null,
            'header_title'   => null,
            'body'           => null,
            'button_text'    => null,
            'footer_section' => null,
            'copyright'      => null,

            'show_privacy_policy'      => true,
            'show_refund_policy'       => true,
            'show_cancellation_policy' => true,
            'show_contact_us'          => true,

            'show_facebook'  => true,
            'show_instagram' => true,
            'show_twitter'   => true,
            'show_tiktok'    => true,
            'show_youtube'   => true,
            'show_whatsapp'  => false,
        ];
    }

    protected function previewDataForKey(string $key): array
    {
        if ($key === 'meal_plan_booking') {
            return [
                'order_code'        => '20250012',
                'date'              => '01 Aug, 2025',
                'time'              => '9:00 AM',
                'payment_method'    => 'Online Payment',
                'order_type'        => 'Meal Plan',
                'customer_name'     => 'Jhon Doe',
                'customer_phone'    => '+8801XXXXXXXXX',
                'customer_address'  => 'House 12, Road 5, Dhanmondi',
                'customer_city'     => 'Dhaka 1209',
                'items' => [
                    [
                        'id'    => 1,
                        'name'  => 'Weekly Lunch Plan',
                        'meta'  => '7 days • 1 meal per day',
                        'qty'   => 1,
                        'total' => 1750,
                    ],
                    [
                        'id'    => 2,
                        'name'  => 'Addon – Evening Snacks',
                        'meta'  => '7 days',
                        'qty'   => 1,
                        'total' => 700,
                    ],
                ],
                'subtotal'        => 2450,
                'discount'        => 150,
                'delivery_charge' => 0,
                'vat'             => 150,
                'total'           => 2450,
            ];
        }

        // single_order
        return [
            'order_code'        => '20250001',
            'date'              => '23 Jul, 2025',
            'time'              => '4:30 PM',
            'payment_method'    => 'Cash on Delivery',
            'order_type'        => 'Delivery',
            'customer_name'     => 'Jhon Doe',
            'customer_phone'    => '+8801XXXXXXXXX',
            'customer_address'  => '4517 Washington Ave. Dhaka',
            'customer_city'     => 'Dhaka 1207',
            'items' => [
                [
                    'id'    => 1,
                    'name'  => 'Loaded Fries – Mega Box',
                    'meta'  => 'Peri Peri • Extra cheese',
                    'qty'   => 1,
                    'total' => 545,
                ],
                [
                    'id'    => 2,
                    'name'  => 'CloudBite Special Burger',
                    'meta'  => 'Beef • Double patty',
                    'qty'   => 2,
                    'total' => 820,
                ],
            ],
            'subtotal'        => 1365,
            'discount'        => 100,
            'delivery_charge' => 60,
            'vat'             => 30,
            'total'           => 1355,
        ];
    }

    public function save(): void
    {
        $this->validate([
            'logo'           => 'nullable|image|max:2048',
            'main_title'     => 'nullable|string|max:255',
            'header_title'   => 'nullable|string|max:255',
            'body'           => 'nullable|string',
            'button_text'    => 'nullable|string|max:255',
            'footer_section' => 'nullable|string',
            'copyright'      => 'nullable|string|max:255',
        ]);

        // Logo upload with custom name
        if ($this->logo) {
            // Delete old logo if exists
            if ($this->logo_path && Storage::disk('public')->exists($this->logo_path)) {
                Storage::disk('public')->delete($this->logo_path);
            }

            // Decide type based on active tab
            $type = $this->activeTab === 'dish_order' ? 'dish-order' : 'meal-plan';

            // Current timestamp
            $timestamp = now()->format('Ymd_His');

            // File extension (fallback to 'png' if not found)
            $extension = $this->logo->getClientOriginalExtension() ?: 'png';

            // Final filename: email-logo_dish-order_20251206_145530_ab12c.png
            $filename = "email-logo_{$type}_{$timestamp}.{$extension}";

            // Store with custom name
            $this->logo_path = $this->logo->storeAs('email-logos', $filename, 'public');
        }

        $this->template->update([
            'logo_path'      => $this->logo_path,
            'main_title'     => $this->main_title ?: null,
            'header_title'   => $this->header_title ?: null,
            'body'           => $this->body ?: null,
            'button_text'    => $this->button_text ?: null,
            'footer_section' => $this->footer_section ?: null,
            'copyright'      => $this->copyright ?: null,

            'show_privacy_policy'      => $this->show_privacy_policy,
            'show_refund_policy'       => $this->show_refund_policy,
            'show_cancellation_policy' => $this->show_cancellation_policy,
            'show_contact_us'          => $this->show_contact_us,

            'show_facebook'  => $this->show_facebook,
            'show_instagram' => $this->show_instagram,
            'show_twitter'   => $this->show_twitter,
            'show_tiktok'    => $this->show_tiktok,
            'show_youtube'   => $this->show_youtube,
            'show_whatsapp'  => $this->show_whatsapp,
        ]);

        $this->success(
            title: 'Template saved for ' . ($this->activeTab === 'dish_order' ? 'Dish Order' : 'Meal Plan Booking') . '.',
            position: 'top-right',
            showProgress: true,
            showCloseIcon: true,
        );

        $this->reset('logo');
    }

    public function currency($amount): string
    {
        $num = (float) ($amount ?? 0);
        return '৳ ' . number_format($num, 2);
    }

    public function render()
    {
        return view('livewire.admin.email-template.email-templates');
    }
}
