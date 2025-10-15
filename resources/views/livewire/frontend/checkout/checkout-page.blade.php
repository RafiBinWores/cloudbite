<div>

    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <style>
            #address-map {
                height: 320px;
                border-radius: 0.75rem;
                position: relative;
            }

            .leaflet-control-attribution {
                font-size: 11px;
            }

            .accuracy-circle {
                fill: #3b82f6;
                fill-opacity: .1;
                stroke: #3b82f6;
                stroke-opacity: .4;
            }

            /* in-map locate button */
            .locate-btn.leaflet-bar {
                width: 34px;
                height: 34px;
                display: grid;
                place-items: center;
                background: #fff;
                border-radius: 4px;
                cursor: pointer;
                font-size: 18px;
            }

            .locate-btn:hover {
                background: #f3f4f6;
            }

            .locate-btn[disabled] {
                opacity: .6;
                cursor: not-allowed;
            }

            /* map loading overlay */
            .map-loading {
                position: absolute;
                inset: 0;
                background: rgba(255, 255, 255, .6);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 500;
                /* above tiles & marker */
            }

            .map-spinner {
                width: 28px;
                height: 28px;
                border-radius: 50%;
                border: 3px solid rgba(0, 0, 0, .2);
                border-top-color: #ef4444;
                animation: spin .8s linear infinite;
            }

            @keyframes spin {
                to {
                    transform: rotate(360deg);
                }
            }
        </style>
    @endpush



    <!-- Breadcrumb -->
    <div
        class="bg-[url(/assets/images/breadcrumb-bg.jpg)] py-20 md:py-32 bg-no-repeat bg-cover bg-center text-center text-white grid place-items-center font-oswald">
        <h4 class="text-4xl md:text-6xl font-medium">Cart</h4>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12">
        <h1 class="text-2xl font-semibold mb-6">Checkout</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Form --}}
            <div class="lg:col-span-2 space-y-8">

                <!-- Contact -->
                <section class="rounded-2xl border border-slate-200/70 bg-white/70 shadow-sm backdrop-blur p-6">
                    <header class="flex items-center gap-3 mb-5">
                        <div
                            class="size-10 rounded-xl grid place-items-center bg-gradient-to-tr from-customRed-100/25 to-customRed-200/10 text-customRed-100">
                            <!-- user icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-user-round-icon lucide-user-round">
                                <circle cx="12" cy="8" r="5" />
                                <path d="M20 21a8 8 0 0 0-16 0" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold leading-none">Contact information</h2>
                            <p class="text-xs opacity-70">We’ll use this to reach you about your order.</p>
                        </div>
                    </header>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium">Full name</label>
                            <input type="text" class="input input-bordered w-full mt-1"
                                wire:model.defer="contact_name" placeholder="e.g. Fazlay Rabbi Smit">
                            @error('contact_name')
                                <p class="text-error text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-medium">Phone</label>
                            <input type="tel" inputmode="tel" class="input input-bordered w-full mt-1"
                                wire:model.defer="phone" placeholder="01XXXXXXXXX">
                            @error('phone')
                                <p class="text-error text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                <!-- Shipping -->
                <section class="rounded-2xl border border-slate-200/70 bg-white/70 shadow-sm backdrop-blur p-6">
                    <header class="flex items-center gap-3 mb-5">
                        <div
                            class="size-10 rounded-xl grid place-items-center bg-gradient-to-tr from-customRed-100/25 to-customRed-200/10 text-customRed-100">
                            <!-- map pin icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-map-pin-icon lucide-map-pin">
                                <path
                                    d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0" />
                                <circle cx="12" cy="10" r="3" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold leading-none">Shipping address</h2>
                            <p class="text-xs opacity-70">Pick on map or search a place, we’ll auto-fill the address.
                            </p>
                        </div>
                    </header>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Map / Search -->
                        <div x-data="mapPicker({
                            wire: $wire,
                            initialLat: @js($lat ?? 23.8103),
                            initialLng: @js($lng ?? 90.4125),
                            initialZoom: 13
                        })" x-init="init($refs.mapEl)" class="md:col-span-2 space-y-3"
                            wire:ignore.self>
                            <div>
                                <label class="text-sm font-medium">Search location</label>
                                <div class="flex gap-2 mt-1">
                                    <input x-model="query" @keydown.enter.prevent="search()" type="text"
                                        placeholder="Area / road / landmark… then press Enter"
                                        class="input input-bordered w-full" />
                                    <button type="button" class="btn btn-outline" @click="search()">
                                        <!-- search icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 mr-1.5"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <circle cx="11" cy="11" r="8" />
                                            <path d="m21 21-3.5-3.5" />
                                        </svg>
                                        Search
                                    </button>
                                </div>
                                <p class="text-xs opacity-60 mt-1">Tip: click the map or drag the pin to fine-tune.</p>
                            </div>

                            <div x-ref="mapEl" id="address-map"
                                class="h-72 md:h-80 w-full rounded-xl overflow-hidden ring-1 ring-slate-200/70 dark:ring-slate-700/60"
                                wire:ignore></div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="text-sm font-medium">Delivery Address (autofilled)</label>
                                    <input type="text" class="input input-bordered w-full mt-1"
                                        :disabled="!manualOverride"
                                        :class="manualOverride ? '' : 'opacity-80 cursor-not-allowed'"
                                        x-model="addressLine1" @input="wire.set('address_line1', addressLine1)" />
                                    @error('address_line1')
                                        <p class="text-error text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                    <label class="inline-flex items-center gap-2 text-sm mt-2">
                                        <input id="manual_override" type="checkbox" class="checkbox"
                                            x-model="manualOverride"
                                            @change="!manualOverride && (addressLine1 = savedAddressLine1); wire.set('address_line1', addressLine1)" />
                                        <span>Edit address manually.</span>
                                    </label>
                                </div>

                                <div>
                                    <label class="text-sm font-medium">City (auto)</label>
                                    <input type="text" class="input input-bordered w-full mt-1" x-model="city"
                                        @input="wire.set('city', city)" placeholder="City">
                                </div>

                                <div>
                                    <label class="text-sm font-medium">Postcode (auto)</label>
                                    <input type="text" class="input input-bordered w-full mt-1" x-model="postcode"
                                        @input="wire.set('postcode', postcode)" placeholder="e.g. 1212">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="inline-flex items-center gap-2 text-sm">
                                        <input type="checkbox" class="checkbox" x-model="saveAsDefault"
                                            @change="wire.set('save_as_default', saveAsDefault)" />
                                        <span>Also save this account to my profile.</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Note -->
                        <div class="md:col-span-2" x-data="{ note: @entangle('customer_note').defer }">
                            <div class="flex items-center justify-between">
                                <label class="text-sm font-medium">Note to rider (optional)</label>
                                <span class="text-[11px] opacity-60" x-text="`${(note || '').length}/200`"></span>
                            </div>
                            <textarea class="textarea textarea-bordered w-full mt-1" rows="3" maxlength="200" x-model="note"></textarea>
                            @error('customer_note')
                                <p class="text-error text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                <!-- Payment -->
                <section class="rounded-2xl border border-slate-200/70 bg-white/70 shadow-sm backdrop-blur p-6">
                    <header class="flex items-center gap-3 mb-5">
                        <div
                            class="size-10 rounded-xl grid place-items-center bg-gradient-to-tr  from-customRed-100/25 to-customRed-200/10 text-customRed-100">
                            <!-- credit card icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-wallet-icon lucide-wallet">
                                <path
                                    d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1" />
                                <path d="M3 5v14a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-4" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold leading-none">Payment method</h2>
                            <p class="text-xs opacity-70">Choose how you want to pay.</p>
                        </div>
                    </header>

                    <!-- Selectable tiles -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- COD -->
                        <label
                            class="group relative cursor-pointer rounded-xl border border-customRed-100/70 p-4 hover:border-customRed-100/90 transition">
                            <input type="radio" class="radio absolute opacity-0" value="cod"
                                wire:model="payment_method">
                            <div class="flex items-center gap-3">
                                <div class="size-10 rounded-lg grid place-items-center bg-customRed-100 text-white">
                                    <!-- cash icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-banknote-icon lucide-banknote">
                                        <rect width="20" height="12" x="2" y="6" rx="2" />
                                        <circle cx="12" cy="12" r="2" />
                                        <path d="M6 12h.01M18 12h.01" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium leading-none">Cash on delivery</p>
                                    <p class="text-xs opacity-70 mt-1">Pay at your doorstep</p>
                                </div>
                                <div
                                    class="ml-auto size-5 rounded-full border group-has-[input:checked]:bg-customRed-100/90 group-has-[input:checked]:border-customRed-100/90">
                                </div>
                            </div>
                        </label>

                        <!-- SSLCommerz -->
                        <label
                            class="group relative cursor-pointer rounded-xl border border-customRed-100/70 p-4 hover:border-customRed-200/90 transition">
                            <input type="radio" class="radio absolute opacity-0" value="sslcommerz"
                                wire:model="payment_method">
                            <div class="flex items-center gap-3">
                                <div class="size-10 rounded-lg grid place-items-center bg-customRed-100 text-white">
                                    <!-- shield icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-shield-check-icon lucide-shield-check">
                                        <path
                                            d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z" />
                                        <path d="m9 12 2 2 4-4" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium leading-none">SSLCommerz</p>
                                    <p class="text-xs opacity-70 mt-1">Secure online payment</p>
                                </div>
                                <div
                                    class="ml-auto size-5 rounded-full border group-has-[input:checked]:bg-customRed-100/90 group-has-[input:checked]:border-customRed-100/90">
                                </div>
                            </div>
                        </label>
                    </div>

                    @error('payment_method')
                        <p class="text-error text-xs mt-3">{{ $message }}</p>
                    @enderror
                </section>
            </div>


            {{-- Summary --}}
            <div>
                <div class="rounded-xl border p-6 space-y-3">
                    <h2 class="text-lg font-semibold">Order Summary</h2>

                    <div class="flex items-center justify-between text-sm">
                        <span class="opacity-80">Items subtotal</span>
                        <span>{{ number_format($subtotal, 2) }} <span class="font-oswald">৳</span></span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="opacity-80">Discount</span>
                        <span>(-) {{ number_format($discount_total, 2) }} <span class="font-oswald">৳</span></span>
                    </div>

                    @if ($shipSetting)
                        <div class="flex items-center justify-between text-sm">
                            <span class="opacity-80">Delivery fee
                                @if ($shipSetting->free_delivery)
                                    <span class="text-xs opacity-60">(Free over
                                        {{ number_format($shipSetting->free_minimum, 2) }} ৳)</span>
                                @endif
                            </span>
                            @if ($shipSetting->free_delivery && $subtotal >= $shipSetting->free_minimum)
                                <span class="text-success">0.00 <span class="font-oswald">৳</span></span>
                            @else
                                <span>(+) {{ number_format($shipping_total, 2) }} <span
                                        class="font-oswald">৳</span></span>
                            @endif
                        </div>
                    @endif

                    <div class="border-t pt-3 flex items-center justify-between">
                        <span class="font-semibold">Grand total</span>
                        <span class="text-xl font-semibold text-red-500">
                            {{ number_format($grand_total, 2) }} <span class="font-oswald">৳</span>
                        </span>
                    </div>

                    <button wire:click="placeOrder" wire:loading.attr="disabled" wire:target="placeOrder"
                        class="btn bg-customRed-100 text-white w-full h-12 rounded-xl disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="placeOrder">Place order</span>
                        <span wire:loading wire:target="placeOrder" class="inline-flex items-center gap-2">
                            <svg class="animate-spin size-4" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z" />
                            </svg>
                            Placing…
                        </span>
                    </button>

                    <p class="text-xs opacity-60">By placing this order, you agree to our Terms & Refund Policy.</p>
                </div>
            </div>
        </div>
    </div>


    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

        <script>
            function mapPicker({
                wire,
                initialLat,
                initialLng,
                initialZoom
            }) {
                return {
                    map: null,
                    mapEl: null,
                    marker: null,
                    accCircle: null,
                    locating: false,

                    query: '',
                    lat: null,
                    lng: null,
                    zoom: initialZoom,

                    addressLine1: '',
                    savedAddressLine1: '',
                    city: '',
                    postcode: '',
                    manualOverride: false,
                    saveAsDefault: false,

                    init(mapEl) {
                        this.mapEl = mapEl;
                        const center = [Number(initialLat) || 23.8103, Number(initialLng) || 90.4125];

                        this.map = L.map('address-map').setView(center, this.zoom);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            attribution: '&copy; OpenStreetMap'
                        }).addTo(this.map);

                        // In-map locate control
                        const self = this;
                        const LocateControl = L.Control.extend({
                            onAdd: function() {
                                const btn = L.DomUtil.create('button', 'leaflet-bar locate-btn');
                                btn.type = 'button';
                                btn.title = 'Use current location';
                                btn.innerHTML = '📍';
                                L.DomEvent.on(btn, 'click', (e) => {
                                    L.DomEvent.stop(e);
                                    self.locateNow(btn);
                                });
                                return btn;
                            }
                        });
                        this.map.addControl(new LocateControl({
                            position: 'topleft'
                        }));

                        // Click on map to set marker + reverse geocode
                        this.map.on('click', (e) => {
                            const {
                                lat,
                                lng
                            } = e.latlng;
                            this._ensureMarker();
                            this.marker.setLatLng([lat, lng]);
                            this._updatePosition(lat, lng, true);
                            this._clearAccuracy();
                        });

                        if (navigator.permissions && navigator.permissions.query) {
                            try {
                                navigator.permissions.query({
                                    name: 'geolocation'
                                }).then((p) => {
                                    if (p.state === 'denied') this._toast(
                                        'Location permission is denied for this site. Check browser/site settings.');
                                });
                            } catch (_) {}
                        }


                        // clear all on init
                        this._pushNullsToWire();
                    },

                    _ensureMarker() {
                        if (this.marker) return;
                        this.marker = L.marker([this.map.getCenter().lat, this.map.getCenter().lng], {
                            draggable: true
                        }).addTo(this.map);
                        this.marker.on('moveend', (e) => {
                            const {
                                lat,
                                lng
                            } = e.target.getLatLng();
                            this._updatePosition(lat, lng, true);
                            this._clearAccuracy();
                        });
                    },

                    // Show/hide map overlay
                    _overlay(on = true) {
                        if (!this.mapEl) return;
                        let overlay = this.mapEl.querySelector('.map-loading');
                        if (on) {
                            if (!overlay) {
                                overlay = document.createElement('div');
                                overlay.className = 'map-loading';
                                overlay.innerHTML = '<div class="map-spinner"></div>';
                                this.mapEl.appendChild(overlay);
                            }
                        } else if (overlay) {
                            overlay.remove();
                        }
                    },

                    // Faster geolocation: quick fix + background refine
                    locateNow(btnEl) {
                        if (!navigator.geolocation) return this._toast('Geolocation not supported.');

                        this.locating = true;
                        if (btnEl) btnEl.setAttribute('disabled', 'disabled');
                        this._overlay(true);

                        const quickSuccess = (pos) => {
                            const {
                                latitude: lat,
                                longitude: lng,
                                accuracy
                            } = pos.coords || {};
                            this._ensureMarker();
                            this.map.setView([lat, lng], Math.max(this.map.getZoom(), 15));
                            this.marker.setLatLng([lat, lng]);
                            this._updatePosition(lat, lng, true);
                            this._drawAccuracy(lat, lng, accuracy);
                            // keep overlay a bit for visual feedback, but we're basically done
                            setTimeout(() => this._overlay(false), 400);
                            this.locating = false;
                            if (btnEl) btnEl.removeAttribute('disabled');
                            // Start background refine (won't block UI)
                            this._refineLocation(lat, lng);
                        };

                        const quickError = (err) => {
                            this._overlay(false);
                            this.locating = false;
                            if (btnEl) btnEl.removeAttribute('disabled');
                            this._toast(
                                err.code === 1 ? 'Location permission denied.' :
                                err.code === 2 ? 'Location unavailable.' :
                                err.code === 3 ? 'Location timed out.' : 'Unable to fetch your location.'
                            );
                        };

                        // Quick reading: faster UX
                        navigator.geolocation.getCurrentPosition(
                            quickSuccess,
                            quickError, {
                                enableHighAccuracy: true,
                                maximumAge: 0,
                                timeout: 6000
                            }
                        );
                    },

                    // Optional refinement: accept better accuracy if it arrives soon
                    _refineLocation(prevLat, prevLng) {
                        let settled = false;
                        const ACC_GOOD = 50; // meters
                        const STOP_AFTER = 8000;

                        const watchId = navigator.geolocation.watchPosition(
                            (pos) => {
                                if (settled) return;
                                const {
                                    latitude: lat,
                                    longitude: lng,
                                    accuracy
                                } = pos.coords || {};
                                if (typeof accuracy === 'number' && accuracy <= ACC_GOOD) {
                                    settled = true;
                                    navigator.geolocation.clearWatch(watchId);
                                    // Only update if significantly different
                                    const moved = Math.abs(lat - prevLat) > 0.0002 || Math.abs(lng - prevLng) > 0.0002;
                                    if (moved) {
                                        this._ensureMarker();
                                        this.marker.setLatLng([lat, lng]);
                                        this.map.setView([lat, lng], Math.max(this.map.getZoom(), 15));
                                        this._updatePosition(lat, lng, true);
                                        this._drawAccuracy(lat, lng, accuracy);
                                    }
                                }
                            },
                            () => {
                                if (!settled) try {
                                    navigator.geolocation.clearWatch(watchId);
                                } catch {}
                            }, {
                                enableHighAccuracy: true,
                                maximumAge: 0,
                                timeout: STOP_AFTER
                            }
                        );

                        setTimeout(() => {
                            if (!settled) {
                                try {
                                    navigator.geolocation.clearWatch(watchId);
                                } catch {}
                            }
                        }, STOP_AFTER + 500);
                    },

                    async search() {
                        if (!this.query?.trim()) return;
                        const url = new URL('https://nominatim.openstreetmap.org/search');
                        url.searchParams.set('q', this.query.trim());
                        url.searchParams.set('format', 'json');
                        url.searchParams.set('addressdetails', '1');
                        url.searchParams.set('limit', '1');
                        const res = await fetch(url, {
                            headers: {
                                'Accept': 'application/json',
                                'User-Agent': 'CloudBite/1.0 (contact@example.com)'
                            }
                        });
                        const data = await res.json();
                        if (Array.isArray(data) && data.length) {
                            const place = data[0];
                            const lat = parseFloat(place.lat),
                                lng = parseFloat(place.lon);
                            this.map.setView([lat, lng], 16);
                            this._ensureMarker();
                            this.marker.setLatLng([lat, lng]);
                            this._applyAddressFromDetails(place.display_name, place.address, lat, lng);
                            this._clearAccuracy();
                        }
                    },

                    async _updatePosition(lat, lng, reverse = false) {
                        this.lat = lat;
                        this.lng = lng;
                        wire.set('lat', lat);
                        wire.set('lng', lng);

                        if (!reverse) return;

                        const url = new URL('https://nominatim.openstreetmap.org/reverse');
                        url.searchParams.set('lat', lat);
                        url.searchParams.set('lon', lng);
                        url.searchParams.set('format', 'json');
                        url.searchParams.set('addressdetails', '1');

                        try {
                            const res = await fetch(url, {
                                headers: {
                                    'Accept': 'application/json',
                                    'User-Agent': 'CloudBite/1.0 (contact@example.com)'
                                }
                            });
                            const data = await res.json();
                            const addr = data?.address ?? {};

                            const line1 = data?.display_name || [
                                addr.house_number,
                                addr.road || addr.pedestrian,
                                addr.neighbourhood || addr.suburb || addr.village,
                                addr.city || addr.town || addr.county
                            ].filter(Boolean).join(', ');

                            this.addressLine1 = line1 || this.addressLine1;
                            this.savedAddressLine1 = this.addressLine1;

                            this.city = addr.city || addr.town || addr.village || this.city;
                            this.postcode = addr.postcode || this.postcode;

                            wire.set('address_line1', this.addressLine1);
                            wire.set('city', this.city);
                            wire.set('postcode', this.postcode);
                        } catch (e) {
                            console.warn('Reverse geocoding failed', e);
                        }
                    },

                    _applyAddressFromDetails(displayName, address, lat, lng) {
                        this.addressLine1 = displayName || this.addressLine1;
                        this.savedAddressLine1 = this.addressLine1;
                        this.city = address?.city || address?.town || address?.village || this.city;
                        this.postcode = address?.postcode || this.postcode;

                        this.lat = lat;
                        this.lng = lng;
                        wire.set('lat', lat);
                        wire.set('lng', lng);
                        wire.set('address_line1', this.addressLine1);
                        wire.set('city', this.city);
                        wire.set('postcode', this.postcode);
                    },

                    _drawAccuracy(lat, lng, radiusMeters) {
                        if (!radiusMeters || radiusMeters > 5000) {
                            this._clearAccuracy();
                            return;
                        }
                        if (!this.accCircle) {
                            this.accCircle = L.circle([lat, lng], {
                                radius: radiusMeters,
                                className: 'accuracy-circle'
                            }).addTo(this.map);
                        } else {
                            this.accCircle.setLatLng([lat, lng]);
                            this.accCircle.setRadius(radiusMeters);
                        }
                    },

                    _clearAccuracy() {
                        if (this.accCircle) {
                            this.map.removeLayer(this.accCircle);
                            this.accCircle = null;
                        }
                    },

                    _pushNullsToWire() {
                        wire.set('lat', null);
                        wire.set('lng', null);
                        wire.set('address_line1', '');
                        wire.set('city', '');
                        wire.set('postcode', '');
                        this.addressLine1 = '';
                        this.city = '';
                        this.postcode = '';
                    },

                    _toast(msg) {
                        console.warn(msg);
                        try {
                            window.dispatchEvent(new CustomEvent('toast', {
                                detail: {
                                    type: 'error',
                                    message: msg
                                }
                            }));
                        } catch {}
                        if (!window?.dispatchEvent) alert(msg);
                    }
                }
            }
        </script>
    @endpush



</div>
