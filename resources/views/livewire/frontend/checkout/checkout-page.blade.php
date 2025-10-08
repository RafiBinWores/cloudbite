<div>

    @push('styles')
        {{-- Leaflet CSS/JS (place once on the page) --}}
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

        <style>
            #address-map {
                height: 320px;
                border-radius: 0.75rem;
            }

            .leaflet-control-attribution {
                font-size: 11px;
            }
        </style>
    @endpush

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12">
        <h1 class="text-2xl font-semibold mb-6">Checkout</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Form --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-xl border p-6">
                    <h2 class="text-lg font-semibold mb-4">Contact information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium">Full name</label>
                            <input type="text" class="input input-bordered w-full" wire:model.defer="contact_name">
                            @error('contact_name')
                                <p class="text-error text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium">Phone</label>
                            <input type="text" class="input input-bordered w-full" wire:model.defer="phone">
                            @error('phone')
                                <p class="text-error text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium">Email (optional)</label>
                            <input type="email" class="input input-bordered w-full" wire:model.defer="email">
                            @error('email')
                                <p class="text-error text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border p-6">
                    <h2 class="text-lg font-semibold mb-4">Shipping address</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium">Address line 1</label>
                            <input type="text" class="input input-bordered w-full" wire:model.defer="address_line1">
                            @error('address_line1')
                                <p class="text-error text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium">Address line 2 (optional)</label>
                            <input type="text" class="input input-bordered w-full" wire:model.defer="address_line2">
                            @error('address_line2')
                                <p class="text-error text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium">City</label>
                            <input type="text" class="input input-bordered w-full" wire:model.defer="city">
                            @error('city')
                                <p class="text-error text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium">Postcode</label>
                            <input type="text" class="input input-bordered w-full" wire:model.defer="postcode">
                            @error('postcode')
                                <p class="text-error text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium">Note to rider (optional)</label>
                            <textarea class="textarea textarea-bordered w-full" rows="3" wire:model.defer="customer_note"></textarea>
                            @error('customer_note')
                                <p class="text-error text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div 
  x-data="mapPicker({
    wire: $wire,
    initialLat: @js($lat ?? 23.8103),
    initialLng: @js($lng ?? 90.4125),
    initialZoom: 13
  })"
  x-init="init($refs.mapEl)"
  class="space-y-3"
  wire:ignore.self
>
  <div class="flex items-center justify-between">
    <div class="w-full">
      <label class="text-sm font-medium">Search location</label>
      <div class="flex gap-2">
        <input x-model="query" @keydown.enter.prevent="search()" type="text"
               placeholder="Type area/road/landmark and press Enter"
               class="input input-bordered w-full" />
        <button type="button" class="btn btn-outline" @click="search()">Search</button>
      </div>
      <p class="text-xs opacity-60 mt-1">Tip: drag the marker to fine-tune the location.</p>
    </div>
  </div>

  {{-- NOTE: use x-ref + wire:ignore on the actual map container --}}
  <div x-ref="mapEl" id="address-map" class="w-full" wire:ignore></div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="md:col-span-2">
      <label class="text-sm font-medium">Address line 1 (autofilled)</label>
      <input type="text" class="input input-bordered w-full"
             :disabled="!manualOverride"
             :class="manualOverride ? '' : 'opacity-80'"
             x-model="addressLine1"
             @input="wire.set('address_line1', addressLine1)" />
      <div class="mt-2 flex items-center gap-2">
        <input id="manual_override" type="checkbox" class="checkbox"
               x-model="manualOverride"
               @change="!manualOverride && (addressLine1 = savedAddressLine1); wire.set('address_line1', addressLine1)" />
        <label for="manual_override" class="text-sm">Edit address manually</label>
      </div>
    </div>

    <div>
      <label class="text-sm font-medium">City (auto)</label>
      <input type="text" class="input input-bordered w-full" x-model="city"
             @input="wire.set('city', city)" />
    </div>
    <div>
      <label class="text-sm font-medium">Postcode (auto)</label>
      <input type="text" class="input input-bordered w-full" x-model="postcode"
             @input="wire.set('postcode', postcode)" />
    </div>
  </div>

  <template x-if="lat && lng">
    <p class="text-xs opacity-60">Lat: <span x-text="lat.toFixed(6)"></span>,
       Lng: <span x-text="lng.toFixed(6)"></span></p>
  </template>
</div>

                <div class="rounded-xl border p-6">
                    <h2 class="text-lg font-semibold mb-4">Payment method</h2>
                    <div class="flex flex-col gap-2">
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" class="radio" value="cod" wire:model="payment_method"> Cash on
                            delivery
                        </label>
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" class="radio" value="sslcommerz" wire:model="payment_method">
                            SSLCommerz
                        </label>
                        @error('payment_method')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>


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
                    {{-- <div class="flex items-center justify-between text-sm">
                    <span class="opacity-80">Vat/Tax</span>
                    <span>(+) {{ number_format($tax_total, 2) }} <span class="font-oswald">৳</span></span>
                </div> --}}

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

                    <button wire:click="placeOrder"
                        class="btn bg-customRed-100 text-white w-full h-12 rounded-xl">Place
                        order</button>

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
                    marker: null,
                    query: '',
                    lat: initialLat,
                    lng: initialLng,
                    zoom: initialZoom,
                    addressLine1: @json(old('address_line1') ?? ($address_line1 ?? '')),
                    savedAddressLine1: '',
                    city: @json(old('city') ?? ($city ?? '')),
                    postcode: @json(old('postcode') ?? ($postcode ?? '')),
                    manualOverride: false,

                    init() {
                        this.savedAddressLine1 = this.addressLine1;

                        this.map = L.map('address-map').setView([this.lat, this.lng], this.zoom);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            attribution: '&copy; OpenStreetMap'
                        }).addTo(this.map);

                        this.marker = L.marker([this.lat, this.lng], {
                            draggable: true
                        }).addTo(this.map);

                        // Try browser geolocation (optional)
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition((pos) => {
                                const {
                                    latitude,
                                    longitude
                                } = pos.coords;
                                this.map.setView([latitude, longitude], 15);
                                this.marker.setLatLng([latitude, longitude]);
                                this._updatePosition(latitude, longitude, true);
                            });
                        }

                        // On drag end -> reverse geocode
                        this.marker.on('moveend', (e) => {
                            const {
                                lat,
                                lng
                            } = e.target.getLatLng();
                            this._updatePosition(lat, lng, true);
                        });

                        // Initial reverse geocode to fill if empty
                        this._updatePosition(this.lat, this.lng, !this.addressLine1);
                    },

                    async search() {
                        if (!this.query?.trim()) return;
                        // Nominatim forward geocoding
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
                            this.marker.setLatLng([lat, lng]);
                            this._applyAddressFromDetails(place.display_name, place.address, lat, lng);
                        }
                    },

                    async _updatePosition(lat, lng, reverse = false) {
                        this.lat = lat;
                        this.lng = lng;
                        wire.set('lat', lat);
                        wire.set('lng', lng);

                        if (!reverse) return;

                        // Nominatim reverse geocoding
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
                            // Build a nice line1: road + house/plot + suburb + city
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
                        this.marker.setLatLng([lat, lng]);

                        this.map.setView([lat, lng], 16);

                        wire.set('address_line1', this.addressLine1);
                        wire.set('city', this.city);
                        wire.set('postcode', this.postcode);
                        wire.set('lat', lat);
                        wire.set('lng', lng);
                    }
                }
            }
        </script>
    @endpush
</div>
