<?php

namespace App\Livewire\Frontend\Account;

use Developermithu\Tallcraftui\Traits\WithTcToast;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.frontend')]
class UserProfile extends Component
{
    use WithFileUploads, WithTcToast;

    public string $name = '';
    public string $email = '';
    public ?string $phone = '';
    public ?string $country_code = 'BD';
    public $avatar;

    public ?string $current_password = null;
    public ?string $password = null;
    public ?string $password_confirmation = null;

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = (string) $user->name;
        $this->email = (string) $user->email;
        $this->phone = $user->phone ?? '';
        $this->country_code = $user->country_code ?? 'BD';
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'min:2'],

            'phone' => [
                'required',
                'string',
                'regex:/^\+[1-9]\d{7,14}$/',
                Rule::unique('users', 'phone')->ignore(Auth::id()),
            ],

            'country_code' => ['nullable', 'string', 'max:2'],
            'avatar' => ['nullable', 'image', 'max:2048', 'mimes:jpg,jpeg,png,gif'],

            'current_password' => ['nullable', 'required_with:password', 'current_password'],

            'password' => [
                'nullable',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            ],

            'password_confirmation' => ['nullable', 'same:password'],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Please enter your full name.',
            'name.min' => 'Name must be at least 2 characters.',

            'phone.required' => 'Phone number is required.',
            'phone.unique' => 'This phone number is already registered.',
            'phone.regex' => 'Please enter a valid international phone number (E.164).',

            'avatar.image' => 'Please upload a valid image file.',
            'avatar.max' => 'Image size should not exceed 2MB.',
            'avatar.mimes' => 'Only JPG, PNG, and GIF images are allowed.',

            'current_password.required_with' => 'Current password is required to change password.',
            'current_password.current_password' => 'The current password is incorrect.',

            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.regex' => 'Password must contain uppercase, lowercase, number and special character.',
        ];
    }

    public function updated($property): void
    {
        if (in_array($property, ['name', 'phone', 'avatar', 'current_password', 'password', 'password_confirmation'], true)) {
            $this->validateOnly($property);
        }
    }

    public function clearAvatarSelection(): void
    {
        $this->reset('avatar');
        $this->resetErrorBag('avatar');
    }

    public function removeAvatar(): void
    {
        $user = Auth::user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->update(['avatar' => null]);
        $this->reset('avatar');

        $this->success(
            title: 'Profile photo removed!',
            position: 'top-right',
            showProgress: true,
            showCloseIcon: true,
        );
    }

    public function updateProfile(): void
    {
        $validated = $this->validate();
        $user = Auth::user();

        // Avatar
        if ($this->avatar instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = $this->avatar->store('avatars', 'public');
        } else {
            unset($validated['avatar']);
        }

        // Password
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        unset($validated['current_password'], $validated['password_confirmation']);

        $user->update($validated);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->success(
            title: 'Profile updated successfully!',
            position: 'top-right',
            showProgress: true,
            showCloseIcon: true,
        );
    }

    public function render()
    {
        return view('livewire.frontend.account.user-profile');
    }
}
