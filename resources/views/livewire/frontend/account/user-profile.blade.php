{{-- resources/views/livewire/frontend/account/user-profile.blade.php --}}
<div>
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/css/intlTelInput.css">
        <style>
            .iti {
                display: block;
                width: 100%;
            }

            .iti__flag {
                background-image: url("https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/img/flags.png");
            }

            @media (-webkit-min-device-pixel-ratio: 2),
            (min-resolution: 192dpi) {
                .iti__flag {
                    background-image: url("https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/img/flags@2x.png");
                }
            }

            .iti input {
                padding-left: 52px !important;
                width: 100%;
            }

            .iti--separate-dial-code .iti__selected-flag {
                background-color: rgba(0, 0, 0, 0.06);
                border-radius: .75rem 0 0 .75rem;
            }

            .iti--allow-dropdown .iti__flag-container:hover .iti__selected-flag {
                background-color: rgba(0, 0, 0, 0.10);
            }

            .phone-error {
                border-color: #ef4444 !important;
            }
        </style>
    @endpush


    {{-- PAGE --}}
    <div class="bg-slate-50">
        <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8 sm:py-10">

            <div
                class="rounded-3xl border border-slate-200/70 bg-white shadow-[0_10px_30px_rgba(15,23,42,0.08)] overflow-hidden">
                <div
                    class="px-4 py-5 border-b sm:px-6 lg:px-8 sm:py-6 border-slate-200/70 bg-gradient-to-r from-white to-slate-50">
                    <h2 class="text-lg font-semibold sm:text-xl text-slate-900">Profile Settings</h2>
                    <p class="text-sm text-slate-500">Manage your account details</p>
                </div>

                <form id="profileForm" wire:submit.prevent="updateProfile" class="p-4 space-y-10 sm:p-6 lg:p-8">

                    {{-- Avatar --}}
                    {{-- keep your avatar section as-is --}}

                    {{-- Basic Info --}}
                    <section class="p-4 bg-white border shadow-sm rounded-2xl border-slate-200 sm:p-6">
                        <div class="mb-5">
                            <h3 class="font-semibold text-slate-900">Basic Information</h3>
                            <p class="text-sm text-slate-500">Keep your info up to date</p>
                        </div>

                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <label class="text-sm font-medium text-slate-700">Full Name <span
                                        class="text-red-500">*</span></label>
                                <input type="text" wire:model.defer="name"
                                    class="mt-1 w-full rounded-xl border-slate-300 bg-white shadow-sm focus:border-red-500 focus:ring focus:ring-red-200/70 @error('name') border-red-500 @enderror"
                                    placeholder="Enter your full name">
                                @error('name')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="text-sm font-medium text-slate-700">Email</label>
                                <input type="email" value="{{ $email }}" readonly
                                    class="w-full mt-1 cursor-not-allowed rounded-xl border-slate-200 bg-slate-50 text-slate-600">
                                <p class="mt-1 text-xs text-slate-500">Email cannot be changed</p>
                            </div>

                            {{-- Phone --}}
                            <div class="md:col-span-2">
                                <div wire:ignore x-data="intlPhoneField(@js($this->getId()), @js($phone ?? ''), @js($country_code ?? 'BD'))">
                                    <label class="text-sm font-medium text-slate-700">
                                        Phone Number <span class="text-red-500">*</span>
                                    </label>

                                    <input x-ref="input" type="tel"
                                        class="mt-1 w-full rounded-xl border-slate-300 bg-white shadow-sm focus:border-red-500 focus:ring focus:ring-red-200/70"
                                        :class="{ 'phone-error': errorMsg }" placeholder="Enter your phone number" />

                                    {{-- like your example --}}
                                    <p x-ref="error" class="mt-1 text-xs text-red-600" x-show="errorMsg"
                                        x-text="errorMsg" style="display:none"></p>
                                    <p x-ref="valid" class="mt-1 text-xs text-emerald-700 font-semibold"
                                        x-show="isValid" style="display:none">
                                        ✓ Valid number
                                    </p>
                                </div>

                                @error('phone')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                    </section>

                    {{-- Password --}}
                    <section class="p-4 bg-white border shadow-sm rounded-2xl border-slate-200 sm:p-6">
                        <div class="mb-5">
                            <h3 class="font-semibold text-slate-900">Change Password</h3>
                            <p class="text-sm text-slate-500">Leave blank to keep current</p>
                        </div>

                        <div class="grid gap-6 md:grid-cols-3">
                            <div>
                                <label class="text-sm font-medium text-slate-700">Current Password</label>
                                <input type="password" wire:model.defer="current_password"
                                    class="mt-1 w-full rounded-xl border-slate-300 bg-white shadow-sm focus:border-red-500 focus:ring focus:ring-red-200/70 @error('current_password') border-red-500 @enderror"
                                    placeholder="••••••••">
                                @error('current_password')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="text-sm font-medium text-slate-700">New Password</label>
                                <input type="password" wire:model.defer="password"
                                    class="mt-1 w-full rounded-xl border-slate-300 bg-white shadow-sm focus:border-red-500 focus:ring focus:ring-red-200/70 @error('password') border-red-500 @enderror"
                                    placeholder="At least 8 characters">
                                @error('password')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="text-sm font-medium text-slate-700">Confirm Password</label>
                                <input type="password" wire:model.defer="password_confirmation"
                                    class="mt-1 w-full rounded-xl border-slate-300 bg-white shadow-sm focus:border-red-500 focus:ring focus:ring-red-200/70 @error('password_confirmation') border-red-500 @enderror"
                                    placeholder="Re-enter">
                                @error('password_confirmation')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        @php $pw = $password ?? ''; @endphp
                        @php $hasMinLength = preg_match('/.{8,}/', $pw); @endphp
                        @php $hasUppercase = preg_match('/[A-Z]/', $pw); @endphp
                        @php $hasLowercase = preg_match('/[a-z]/', $pw); @endphp
                        @php $hasNumber = preg_match('/[0-9]/', $pw); @endphp
                        @php $hasSpecialChar = preg_match('/[@$!%*?&]/', $pw); @endphp

                        <div
                            class="p-4 mt-6 border rounded-2xl border-slate-200 bg-gradient-to-br from-slate-50 to-white sm:p-5">
                            <p class="mb-3 text-sm font-semibold text-slate-800">Password requirements</p>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    class="px-3 py-1.5 rounded-full text-xs font-semibold border {{ $hasMinLength ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-white text-slate-600 border-slate-200' }}">8+
                                    chars</span>
                                <span
                                    class="px-3 py-1.5 rounded-full text-xs font-semibold border {{ $hasUppercase ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-white text-slate-600 border-slate-200' }}">Uppercase</span>
                                <span
                                    class="px-3 py-1.5 rounded-full text-xs font-semibold border {{ $hasLowercase ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-white text-slate-600 border-slate-200' }}">Lowercase</span>
                                <span
                                    class="px-3 py-1.5 rounded-full text-xs font-semibold border {{ $hasNumber ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-white text-slate-600 border-slate-200' }}">Number</span>
                                <span
                                    class="px-3 py-1.5 rounded-full text-xs font-semibold border {{ $hasSpecialChar ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-white text-slate-600 border-slate-200' }}">Special
                                    (@$!%*?&)</span>
                            </div>
                        </div>
                    </section>

                    {{-- Footer --}}
                    <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                        <button type="button" wire:click="$refresh"
                            class="w-full sm:w-auto px-6 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold transition cursor-pointer">
                            Cancel
                        </button>

                        {{-- Save: sync phone to Livewire then submit --}}
                        <button type="button" x-data
                            x-on:click.prevent="
            (async () => {
                const ok = await (window.__syncPhoneOnSubmit ? window.__syncPhoneOnSubmit() : true);
                if (ok) {
                    document.getElementById('profileForm')
                      .dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
                }
            })()
        "
                            wire:loading.attr="disabled"
                            class="w-full sm:w-auto px-7 py-2.5 rounded-xl text-white font-semibold bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 disabled:opacity-60 disabled:cursor-not-allowed transition">
                            <span wire:loading.remove>Save Changes</span>
                            <span wire:loading wire:target="updateProfile"
                                class="inline-flex justify-center">Saving...</span>
                        </button>


                    </div>

                </form>
            </div>
        </div>
    </div>

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/intlTelInput.min.js"></script>

  <script>
    document.addEventListener('alpine:init', () => {
      // error messages from intl-tel-input docs
      const errorMap = ["Invalid number", "Invalid country code", "Too short", "Too long", "Invalid number"];

      // default (overwritten per component init)
      window.__syncPhoneOnSubmit = async () => true;

      Alpine.data('intlPhoneField', (componentId, initialPhone, initialCountry) => ({
        iti: null,
        errorMsg: '',
        isValid: false,
        utilsReady: false,

        async init() {
          const input = this.$refs.input;

          const reset = () => {
            this.errorMsg = '';
            this.isValid = false;
          };

          const showError = (msg) => {
            this.errorMsg = msg;
            this.isValid = false;
          };

          // ✅ init plugin (GLOBAL / all countries)
          this.iti = window.intlTelInput(input, {
            initialCountry: String(initialCountry || 'BD').toLowerCase(),
            separateDialCode: true,

            // ✅ recommended for global validation + E.164 output
            nationalMode: false,
            formatOnDisplay: false,
            autoPlaceholder: 'polite',
            preferredCountries: ['bd', 'in', 'us', 'gb', 'my', 'sa', 'ae'],

            // ✅ must be loaded for proper validation
            utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/utils.js',
          });

          // ✅ wait for utils to truly be available (prevents short junk being "valid")
          const isUtilsReady = () =>
            window.intlTelInputUtils &&
            typeof window.intlTelInputUtils.isValidNumber === 'function';

          const waitUtils = async () => {
            let tries = 0;
            while (!isUtilsReady() && tries < 80) { // ~4s
              await new Promise(r => setTimeout(r, 50));
              tries++;
            }
            return isUtilsReady();
          };

          this.utilsReady = await waitUtils();

          // preload initial phone if any
          if (initialPhone) {
            try { this.iti.setNumber(initialPhone); }
            catch (e) { input.value = initialPhone; }
          }

          // reset on typing / flag change (like docs)
          input.addEventListener('change', reset);
          input.addEventListener('keyup', reset);
          input.addEventListener('countrychange', reset);

          // ✅ submit-time validation + sync to Livewire
          window.__syncPhoneOnSubmit = async () => {
            const lw = window.Livewire?.find(componentId);
            if (!lw) {
              showError('Livewire not ready. Refresh the page.');
              return false;
            }

            reset();

            const raw = (input.value || '').trim();
            if (!raw) {
              showError('Required');
              await lw.set('phone', '');
              return false;
            }

            if (!this.utilsReady) {
              showError('Phone validator still loading. Try again.');
              await lw.set('phone', '');
              return false;
            }

            // ✅ prefer precise validation if available
            const valid = (typeof this.iti.isValidNumberPrecise === 'function')
              ? this.iti.isValidNumberPrecise()
              : this.iti.isValidNumber();

            if (valid) {
              this.isValid = true;

              const e164 = this.iti.getNumber(); // +880..., +1..., etc
              const iso2 = (this.iti.getSelectedCountryData()?.iso2 || '')
                .toUpperCase();

              await lw.set('phone', e164);
              await lw.set('country_code', iso2);

              return true;
            }

            const errorCode = this.iti.getValidationError();
            showError(errorMap[errorCode] || 'Invalid number');
            await lw.set('phone', '');
            return false;
          };
        },
      }));
    });
  </script>
@endpush



</div>
