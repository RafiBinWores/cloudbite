<?php

namespace App\Livewire\Admin\Coupons;

use App\Models\Coupon;
use Developermithu\Tallcraftui\Traits\WithTcToast;
use Flux\Flux;
use Livewire\Component;
use Illuminate\support\Str;
use Livewire\Attributes\On;
use Livewire\WithPagination;

class CouponForm extends Component
{
    use WithPagination;
    use WithTcToast;

    public $couponId = null;
    public $isView = false;

    public $coupon_type = null, $title = null, $coupon_code = null, $same_user_limit = null, $discount = null, $start_date = null, $expire_date = null, $minimum_purchase = null, $status = 'active';

    public string $discount_type = 'percent';


    public function rules(): array
    {
        return [
            'coupon_type' => 'required|in:default,first_order',
            'title' => 'required|string',
            'coupon_code' => 'required|string',
            'same_user_limit' => 'required|numeric',
            'discount_type' => 'required|in:percent,amount',
            'discount' => 'required|numeric',
            'start_date' => 'required|date',
            'expire_date' => 'required|date',
            'minimum_purchase' => 'nullable|numeric',
            'status' => 'required|in:active,disable',
        ];
    }

    // For generate code
    public function generateCouponCode()
    {
        $this->coupon_code = Str::random(8);
    }

    // For form submit
    public function submit()
    {
        $this->validate();

        $payload = [
            'coupon_type'   => $this->coupon_type,
            'title'  => $this->title,
            'coupon_code'  => $this->coupon_code,
            'same_user_limit'  => $this->same_user_limit,
            'discount_type'  => $this->discount_type,
            'discount'  => $this->discount,
            'start_date'  => $this->start_date,
            'expire_date'  => $this->expire_date,
            'minimum_purchase'  => $this->minimum_purchase,
            'status' => $this->status ?: 'active',
        ];

        if ($this->couponId) {
            // UPDATE
            $coupon = Coupon::find($this->couponId);
            if (!$coupon) {
                  $this->error(
                title: 'Coupon not found.',
                position: 'top-right',
                showProgress: true,
                showCloseIcon: true,
            );
                return;
            }

            // Check if there are any real changes
            $hasChanges = false;
            foreach ($payload as $k => $v) {
                if ($coupon->{$k} !== $v) {
                    $hasChanges = true;
                    break;
                }
            }

            if (!$hasChanges) {
                  $this->warning(
                title: 'Noting to found.',
                position: 'top-right',
                showProgress: true,
                showCloseIcon: true,
            );
                $this->dispatch('coupons:refresh');
                Flux::modal('coupon-modal')->close();
                return;
            }

            // Persist
            $coupon->fill($payload);
            $coupon->save();

              $this->success(
                title: 'Coupon updated successfully.',
                position: 'top-right',
                showProgress: true,
                showCloseIcon: true,
            );
        } else {
            // CREATE
            Coupon::create($payload);

            // Reset form for next entry
            $this->reset(['couponId', 'title', 'coupon_type', 'coupon_code', 'same_user_limit', 'discount_type', 'discount', 'start_date', 'expire_date', 'minimum_purchase', 'status']);
            $this->status = 'active';

              $this->success(
                title: 'Coupon created successfully.',
                position: 'top-right',
                showProgress: true,
                showCloseIcon: true,
            );
        }

        $this->dispatch('coupons:refresh');
        Flux::modal('coupon-modal')->close();
    }

    #[On('open-coupon-modal')]
    public function couponDetail($mode, $coupon = null)
    {

        $this->isView = $mode === 'view';

        if ($mode === 'create') {

            $this->isView = false;
            $this->reset();
            $this->status = 'active';
        } else {
            // dd($category);
            $this->couponId = $coupon['id'];

            $this->coupon_type = $coupon['coupon_type'];
            $this->title = $coupon['title'];
            $this->coupon_code = $coupon['coupon_code'];
            $this->same_user_limit = $coupon['same_user_limit'];
            $this->discount_type = $coupon['discount_type'];
            $this->discount = $coupon['discount'];
            $this->start_date = $coupon['start_date'];
            $this->expire_date = $coupon['expire_date'];
            $this->minimum_purchase = $coupon['minimum_purchase'];
            $this->status = $coupon['status'];
        }
    }

    public function render()
    {
        return view('livewire.admin.coupons.coupon-form');
    }
}
