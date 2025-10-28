<div>
    @push('styles')
        <link rel="stylesheet"
              href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
              integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <link rel="stylesheet"
              href="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/css/intlTelInput.css">

        <style>
            #address-map { height: 340px; border-radius: 0.875rem; position: relative; }
            .leaflet-control-attribution { font-size: 11px; }
            .accuracy-circle { fill:#ef4444; fill-opacity:.08; stroke:#ef4444; stroke-opacity:.35; }
            .locate-btn.leaflet-bar {
                width:36px; height:36px; display:grid; place-items:center; background:#fff; border-radius:8px;
                cursor:pointer; font-size:18px; box-shadow:0 6px 16px rgba(239,68,68,.12);
                border:1px solid rgba(239,68,68,.25); color:#ef4444;
            }
            .locate-btn:hover { background:#fff5f5; }
            .locate-btn[disabled] { opacity:.6; cursor:not-allowed; }
            .map-loading {
                position:absolute; inset:0; background:rgba(255,255,255,.65); display:flex; align-items:center;
                justify-content:center; z-index:500; backdrop-filter:blur(2px);
            }
            .map-spinner { width:30px; height:30px; border-radius:9999px; border:3px solid rgba(239,68,68,.25);
                border-top-color:#ef4444; animation:spin .8s linear infinite; }
            @keyframes spin { to { transform: rotate(360deg); } }
            .focus-red:focus { outline:none; box-shadow:0 0 0 3px rgba(239,68,68,.2); }
        </style>
    @endpush

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-6">
        {{-- Top: title + quick labels --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold tracking-tight">Address</h2>
                <p class="text-xs text-slate-500">Pick on map or search a place — we’ll auto-fill the fields below.</p>
            </div>

            <div class="flex items-center gap-2">
                @foreach (['home' => 'Home', 'workplace' => 'Workplace', 'others' => 'Others'] as $k => $v)
                    <button type="button" wire:click="$set('label','{{ $k }}')"
                        class="px-3 py-2 rounded-xl border text-sm transition
                               {{ $label === $k
                                   ? 'bg-red-500 text-white border-red-500 shadow-md shadow-red-500/20'
                                   : 'border-red-200 text-red-600 hover:bg-red-50' }}">
                        {{ $v }}
                    </button>
                @endforeach
            </div>
        </div>

        <section class="rounded-2xl border border-slate-200/70 bg-white shadow-sm p-6">
            <header class="flex items-center gap-3 mb-6">
                <div class="size-11 rounded-xl grid place-items-center bg-gradient-to-tr from-red-50 to-red-100 text-red-500 ring-1 ring-red-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0" />
                        <circle cx="12" cy="10" r="3" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold leading-none">Address ({{ ucfirst($label) }})</h3>
                    <p class="text-xs text-slate-500 mt-1">Click the map or drag the pin. Use “Current” for faster pinpointing.</p>
                </div>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Contact name + phone --}}
                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="font-medium">Contact Name</label>
                        <input type="text"
                               class="focus:outline-none focus:border-none focus:ring-2 focus:ring-customRed-100 rounded w-full mt-1"
                               wire:model.defer="contact_name" />
                        @error('contact_name')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1 flex flex-col"
                         x-data="phoneField(@entangle('contact_phone').defer, @entangle('contact_country').defer)"
                         wire:ignore>
                        <label class="font-medium">Contact Phone</label>
                        <input x-ref="input" type="tel"
                               class="focus:outline-none focus:border-none focus:ring-2 focus:ring-customRed-100 rounded w-full" />
                        <p x-show="error" x-text="error" class="text-xs text-red-500 mt-1"></p>
                    </div>
                    {{-- important: server error OUTSIDE wire:ignore --}}
                    @error('contact_phone')
                        <p class="text-red-600 text-xs mt-1 md:col-span-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Map + Search (entangled to prefill from saved data) --}}
                <div
                    x-data="mapPicker({
                        initialLat: @js($lat ?? 23.8103),
                        initialLng: @js($lng ?? 90.4125),
                        initialZoom: 13,
                        hasExisting: @js($hasCurrent),
                        addressText: @entangle('address'),
                        city:        @entangle('city'),
                        postcode:    @entangle('postcode'),
                    })"
                    x-init="$nextTick(() => init($refs.mapEl))"
                    class="md:col-span-2 space-y-3"
                    wire:ignore.self
                >
                    <div>
                        <label class="font-medium">Search location</label>
                        <div class="mt-1 flex gap-2">
                            <div class="relative w-full">
                                <input x-model="query" @keydown.enter.prevent="search()" type="text"
                                       placeholder="Area / road / landmark… then press Enter"
                                       class="w-full pr-9 focus:outline-none focus:border-none focus:ring-2 focus:ring-customRed-100 rounded" />
                            </div>

                            <button type="button"
                                    class="hover:bg-customRed-200 flex items-center gap-1 bg-customRed-100 text-white rounded px-5 cursor-pointer"
                                    @click="search()">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21 21-4.34-4.34" /><circle cx="11" cy="11" r="8" /></svg>
                                Search
                            </button>

                            <button type="button"
                                    class="hover:bg-customRed-200 flex items-center gap-1 bg-customRed-100 text-white rounded px-5 cursor-pointer"
                                    @click="locateNow($event.target)">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="2" x2="5" y1="12" y2="12" /><line x1="19" x2="22" y1="12" y2="12" /><line x1="12" x2="12" y1="2" y2="5" /><line x1="12" x2="12" y1="19" y2="22" /><circle cx="12" cy="12" r="7" /></svg>
                                Location
                            </button>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">Tip: click on the map or drag the pin to fine-tune the address.</p>
                    </div>

                    <div x-ref="mapEl" id="address-map" class="h-80 w-full rounded-xl overflow-hidden ring-1 ring-slate-200/70" wire:ignore></div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="font-medium">
                                Delivery Address <span class="text-sm text-gray-600">(Auto filled if address selected from map)</span>
                            </label>
                            <input type="text"
                                   class="w-full focus:outline-none focus:border-none focus:ring-2 focus:ring-customRed-100 rounded mt-1"
                                   :disabled="!manualOverride"
                                   :class="manualOverride ? '' : 'opacity-80 cursor-not-allowed'"
                                   x-model="addressText" />
                            @error('address')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror

                            <label class="inline-flex items-center gap-2 text-sm mt-2 select-none">
                                <input id="manual_override" type="checkbox"
                                       class="checkbox checkbox-sm border-red-300 text-red-500 focus-red"
                                       x-model="manualOverride"
                                       @change="!manualOverride && (addressText = savedAddressText)" />
                                <span class="text-slate-700">Edit address manually</span>
                            </label>
                        </div>

                        <div>
                            <label class="font-medium">City <span class="text-sm text-gray-600">(Auto filled from map)</span></label>
                            <input type="text"
                                   class="focus:outline-none focus:border-none focus:ring-2 focus:ring-customRed-100 rounded w-full mt-1"
                                   x-model="city" placeholder="City">
                        </div>
                        <div>
                            <label class="font-medium">Postcode <span class="text-sm text-gray-600">(Auto filled from map)</span></label>
                            <input type="text"
                                   class="focus:outline-none focus:border-none focus:ring-2 focus:ring-customRed-100 rounded w-full mt-1"
                                   x-model="postcode" placeholder="e.g. 1212">
                        </div>
                    </div>
                </div>

                {{-- Note --}}
                <div class="md:col-span-2" x-data="{ note: @entangle('note') }">
                    <div class="flex items-center justify-between">
                        <label class="font-medium">Note to rider <span class="text-sm text-gray-600">(optional)</span></label>
                        <span class="text-[11px] text-slate-500" x-text="`${(note || '').length}/200`"></span>
                    </div>
                    <textarea class="focus:outline-none focus:border-none focus:ring-2 focus:ring-customRed-100 rounded w-full mt-1"
                              rows="3" maxlength="200" x-model="note"></textarea>
                    @error('note')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Footer actions --}}
            <div class="mt-8 pt-5 border-t border-dashed border-slate-200 flex flex-col md:flex-row items-center justify-end gap-3">
                <div class="flex items-center gap-2">
                    <a href="{{ route('account.address') }}"
                       class="btn border-red-200 text-customRed-100 hover:bg-red-50 border px-4 py-2 rounded cursor-pointer">
                        Cancel
                    </a>
                    <button wire:click="save"
                            class="btn bg-customRed-100 hover:bg-customRed-200 text-white shadow-md shadow-red-500/20 px-4 py-2 rounded cursor-pointer">
                        Save address
                    </button>
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
                integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/intlTelInput.min.js"></script>

        {{-- Phone field Alpine --}}
        <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('phoneField', (phoneEntangle, countryEntangle) => ({
                iti: null,
                value: phoneEntangle,           // Livewire <-> Alpine
                country: countryEntangle ?? 'BD',
                error: '',
                init() {
                    this.iti = window.intlTelInput(this.$refs.input, {
                        initialCountry: (this.country || 'BD').toLowerCase(),
                        separateDialCode: true,
                        nationalMode: false,
                        autoPlaceholder: 'aggressive',
                        preferredCountries: ['bd','in','us','gb','my','sa','ae'],
                        utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/utils.js',
                    });

                    // Set existing on first paint
                    if (this.value) this.iti.setNumber(this.value);
                    if (this.country) this.iti.setCountry(this.country.toLowerCase());

                    // Reflect future Livewire updates
                    this.$watch('value', (v) => {
                        if (!this.iti) return;
                        if (v) this.iti.setNumber(v); else this.$refs.input.value = '';
                    });
                    this.$watch('country', (c) => {
                        if (this.iti && c) this.iti.setCountry(c.toLowerCase());
                    });

                    // When user changes country via dropdown
                    this.$refs.input.addEventListener('countrychange', () => {
                        const data = this.iti.getSelectedCountryData();
                        const iso2 = (data?.iso2 || 'bd').toUpperCase();
                        this.country = iso2;
                        this.$wire.set('contact_country', iso2, true);
                        const current = this.iti.getNumber();
                        if (current) {
                            this.value = current;
                            this.$wire.set('contact_phone', current, true);
                        }
                    });

                    // Push number to Livewire on input/blur
                    const sync = () => {
                        const raw = this.$refs.input.value.trim();
                        if (!raw) {
                            this.value = null;
                            this.$wire.set('contact_phone', null, true);
                            this.error = '';
                            return;
                        }
                        const valid = this.iti.isValidNumber();
                        const e164  = this.iti.getNumber();
                        this.value  = e164;
                        this.$wire.set('contact_phone', e164, true);
                        this.error  = valid ? '' : 'Invalid phone number';
                    };
                    this.$refs.input.addEventListener('change', sync);
                    this.$refs.input.addEventListener('blur', sync);
                    this.$refs.input.addEventListener('keyup', () => this.error = '');

                    // Optional: rehydrate on label switch
                    window.addEventListener('address:label-switched', (e) => {
                        const { phone, country } = e.detail || {};
                        if (country) { this.country = country; this.iti.setCountry(country.toLowerCase()); }
                        if (phone)   { this.value = phone;   this.iti.setNumber(phone); }
                        else         { this.value = null;    this.$refs.input.value = ''; }
                    });
                },
            }));
        });
        </script>

        {{-- Map picker Alpine --}}
        <script>
        function mapPicker({ initialLat, initialLng, initialZoom, hasExisting, addressText = '', city = '', postcode = '' }) {
            return {
                map: null, mapEl: null, marker: null, accCircle: null, locating: false,
                query: '', lat: null, lng: null, zoom: initialZoom,
                addressText, savedAddressText: '', city, postcode, manualOverride: false, hasExisting,

                init(mapEl) {
                    this.mapEl = mapEl;
                    const center = [Number(initialLat) || 23.8103, Number(initialLng) || 90.4125];

                    this.map = L.map(this.mapEl, { zoomControl: true }).setView(center, this.zoom);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(this.map);
                    this.map.zoomControl.setPosition('topright');

                    // If we already have coords, place marker & keep existing values
                    if (this.hasExisting && initialLat && initialLng) {
                        this._ensureMarker();
                        this.marker.setLatLng([Number(initialLat), Number(initialLng)]);
                        this.map.setView([Number(initialLat), Number(initialLng)], Math.max(this.zoom, 15));
                        this.savedAddressText = this.addressText || '';
                        this.lat = Number(initialLat);
                        this.lng = Number(initialLng);
                    }

                    // Locate control
                    const self = this;
                    const LocateControl = L.Control.extend({
                        onAdd: function() {
                            const btn = L.DomUtil.create('button', 'locate-btn leaflet-bar');
                            btn.type = 'button'; btn.title = 'Use current location'; btn.innerHTML = '📍';
                            L.DomEvent.on(btn, 'click', (e) => { L.DomEvent.stop(e); self.locateNow(btn); });
                            return btn;
                        }
                    });
                    this.map.addControl(new LocateControl({ position: 'topright' }));

                    // Click to set marker
                    this.map.on('click', (e) => {
                        const { lat, lng } = e.latlng;
                        this._ensureMarker();
                        this.marker.setLatLng([lat, lng]);
                        this._updatePosition(lat, lng, true);
                        this._clearAccuracy();
                    });

                    requestAnimationFrame(() => this.map.invalidateSize());

                    // Do not wipe values if we have existing
                    if (!this.hasExisting) this._pushNullsToWire();
                },

                _ensureMarker() {
                    if (this.marker) return;
                    this.marker = L.marker([this.map.getCenter().lat, this.map.getCenter().lng], { draggable: true }).addTo(this.map);
                    this.marker.on('moveend', (e) => {
                        const { lat, lng } = e.target.getLatLng();
                        this._updatePosition(lat, lng, true);
                        this._clearAccuracy();
                    });
                },

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
                    } else if (overlay) overlay.remove();
                },

                locateNow(btnEl) {
                    if (!navigator.geolocation) return this._toast('Geolocation not supported.');
                    this.locating = true;
                    btnEl?.setAttribute('disabled', 'disabled');
                    this._overlay(true);

                    const ok = (pos) => {
                        const { latitude: lat, longitude: lng, accuracy } = pos.coords || {};
                        this._ensureMarker();
                        this.marker.setLatLng([lat, lng]);
                        this.map.setView([lat, lng], Math.max(this.map.getZoom(), 15));
                        this._updatePosition(lat, lng, true);
                        this._drawAccuracy(lat, lng, accuracy);
                        setTimeout(() => this._overlay(false), 300);
                        this.locating = false;
                        btnEl?.removeAttribute('disabled');
                    };
                    const fail = (err) => {
                        this._overlay(false);
                        this.locating = false;
                        btnEl?.removeAttribute('disabled');
                        this._toast(err?.message || 'Unable to fetch your location.');
                    };

                    navigator.geolocation.getCurrentPosition(ok, fail, {
                        enableHighAccuracy: true, maximumAge: 0, timeout: 6000
                    });
                },

                async search() {
                    if (!this.query?.trim()) return;
                    const url = new URL('https://nominatim.openstreetmap.org/search');
                    url.searchParams.set('q', this.query.trim());
                    url.searchParams.set('format', 'json');
                    url.searchParams.set('addressdetails', '1');
                    url.searchParams.set('limit', '1');
                    const res = await fetch(url, { headers: { 'Accept':'application/json','User-Agent':'CloudBite/1.0 (contact@example.com)' }});
                    const data = await res.json();
                    if (Array.isArray(data) && data.length) {
                        const place = data[0];
                        const lat = parseFloat(place.lat), lng = parseFloat(place.lon);
                        this.map.setView([lat, lng], 16);
                        this._ensureMarker();
                        this.marker.setLatLng([lat, lng]);
                        this._applyAddressFromDetails(place.display_name, place.address, lat, lng);
                        this._clearAccuracy();
                    }
                },

                async _updatePosition(lat, lng, reverse = false) {
                    this.lat = lat; this.lng = lng;

                    if (!reverse) return;

                    const url = new URL('https://nominatim.openstreetmap.org/reverse');
                    url.searchParams.set('lat', lat);
                    url.searchParams.set('lon', lng);
                    url.searchParams.set('format', 'json');
                    url.searchParams.set('addressdetails', '1');

                    try {
                        const res = await fetch(url, { headers: { 'Accept':'application/json','User-Agent':'CloudBite/1.0 (contact@example.com)' }});
                        const data = await res.json();
                        const addr = data?.address ?? {};

                        const line = data?.display_name || [
                            addr.house_number,
                            addr.road || addr.pedestrian,
                            addr.neighbourhood || addr.suburb || addr.village,
                            addr.city || addr.town || addr.county
                        ].filter(Boolean).join(', ');

                        this.addressText = line || this.addressText;
                        this.savedAddressText = this.addressText;
                        this.city     = addr.city || addr.town || addr.village || this.city;
                        this.postcode = addr.postcode || this.postcode;
                    } catch (_) {}
                },

                _applyAddressFromDetails(displayName, address, lat, lng) {
                    this.addressText = displayName || this.addressText;
                    this.savedAddressText = this.addressText;
                    this.city     = address?.city || address?.town || address?.village || this.city;
                    this.postcode = address?.postcode || this.postcode;
                    this.lat = lat; this.lng = lng;
                },

                _drawAccuracy(lat, lng, radiusMeters) {
                    if (!radiusMeters || radiusMeters > 5000) { this._clearAccuracy(); return; }
                    if (!this.accCircle) {
                        this.accCircle = L.circle([lat, lng], { radius: radiusMeters, className: 'accuracy-circle' }).addTo(this.map);
                    } else {
                        this.accCircle.setLatLng([lat, lng]);
                        this.accCircle.setRadius(radiusMeters);
                    }
                },

                _clearAccuracy() {
                    if (this.accCircle) { this.map.removeLayer(this.accCircle); this.accCircle = null; }
                },

                _pushNullsToWire() {
                    // only used when creating fresh (guarded by hasExisting)
                    this.lat = null; this.lng = null;
                    this.addressText = ''; this.city = ''; this.postcode = '';
                },

                _toast(msg) {
                    try { window.dispatchEvent(new CustomEvent('toast', { detail: { type:'error', message: msg }})); }
                    catch { alert(msg); }
                }
            }
        }
        </script>
    @endpush
</div>
