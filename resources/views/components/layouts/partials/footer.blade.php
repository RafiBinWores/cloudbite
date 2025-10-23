  <!-- Footer -->
  <footer class="bg-[#062f33] text-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">

          <!-- 3:1 layout on lg; stacks on smaller screens -->
          <div class="grid grid-cols-1 lg:grid-cols-[3fr_1fr] gap-12 lg:gap-16">

              <!-- LEFT: Brand + Explore + Contact (with inner 2/1/2 layout) -->
              <div>
                  <div class="grid grid-cols-1 lg:grid-cols-5 gap-10 lg:gap-12">

                      <!-- Brand -->
                      <div class="lg:col-span-2 lg:pr-8">
                          <div class="flex items-center gap-4">
                              <img src="{{ asset($businessSetting->logo_light) }}" alt="Logo" class="h-12 md:h-16">
                          </div>

                          <p class="text-slate-200/90 leading-7 max-w-md mt-6">
                              {{ $businessSetting->footer_description_text }}
                          </p>

                          <div class="space-y-4 pt-6">
                              <div class="flex items-center justify-between gap-6">
                                  <span class="tracking-[0.2em] text-slate-200/80">MON - FRI</span>
                                  <span class="text-slate-100">8:00 AM - 6:00 PM</span>
                              </div>
                              <div class="h-px bg-white/10"></div>
                              <div class="flex items-center justify-between gap-6">
                                  <span class="tracking-[0.2em] text-slate-200/80">SATURDAY</span>
                                  <span class="text-slate-100">9:00 AM - 5:00 PM</span>
                              </div>
                          </div>
                      </div>

                      <!-- Explore -->
                      <nav class="lg:col-start-3 lg:pr-8">
                          <h3 class="text-2xl font-oswald mb-6">Explore</h3>
                          <ul class="space-y-4 text-slate-100/90">
                              <li><a href="#" class="hover:text-white transition">About</a></li>
                              <li><a href="#" class="hover:text-white transition">Help Center</a></li>
                              <li><a href="#" class="hover:text-white transition">Shops</a></li>
                              <li><a href="#" class="hover:text-white transition">Features</a></li>
                              <li><a href="#" class="hover:text-white transition">Contact</a></li>
                          </ul>
                      </nav>

                      <!-- Contact Info -->
                      <div class="lg:col-start-4 lg:col-span-2 lg:pl-8">
                          <h3 class="text-2xl font-oswald mb-6">Contact Info</h3>
                          <ul class="space-y-6 text-slate-100/90">
                              <li class="flex items-start gap-4">
                                  <span class="grid place-items-center w-10 h-10 rounded-full border border-white/20">
                                      <i class="fa-solid fa-location-dot"></i>
                                  </span>
                                  <address class="not-italic leading-7">
                                      {{ $businessSetting->address }}
                                  </address>
                              </li>
                              <li class="flex items-start gap-4">
                                  <span class="grid place-items-center w-10 h-10 rounded-full border border-white/20">
                                      <i class="fa-solid fa-phone"></i>
                                  </span>
                                  <div class="leading-7">
                                      <a href="tel:{{ $businessSetting->phone }}">{{ $businessSetting->phone }}</a>
                                  </div>
                              </li>
                              <li class="flex items-start gap-4">
                                  <span class="grid place-items-center w-10 h-10 rounded-full border border-white/20">
                                      <i class="fa-solid fa-envelope"></i>
                                  </span>
                                  <a href="mailto:{{ $businessSetting->email }}"
                                      class="hover:underline">{{ $businessSetting->email }}</a>
                              </li>
                          </ul>
                      </div>
                  </div>

                  <!-- Bottom divider + copyright (left side only) -->
                  <div class="mt-14 pt-6 border-t border-white/10 text-slate-200/80 font-jost hidden lg:block">
                      © Copyright @php
                          echo date('Y');
                      @endphp {{ $businessSetting->company_name }}. All Rights Reserved
                  </div>
              </div>

              <!-- RIGHT: Newsletter (perfectly aligned vertical border) -->
              <aside class="lg:border-l lg:border-white/10 lg:pl-12">
                  <h3 class="text-2xl font-oswald mb-6">Newsletter</h3>
                  <p class="text-slate-200/90 leading-7 mb-6">
                      Join our subscribers list to get the latest news and special offers.
                  </p>

                  <form class="mb-8">
                      <label for="email" class="block text-slate-200/80 mb-2">Your Email</label>
                      <input id="email" type="email" placeholder="name@email.com"
                          class="w-full bg-transparent border-0 border-b border-white/30 focus:border-white/60 outline-none py-2 placeholder:text-slate-300/60" />
                      <button type="submit"
                          class="mt-6 inline-flex items-center justify-center w-full h-12 rounded-md bg-[#e80f3a] text-white font-oswald text-lg tracking-wide hover:opacity-95 transition">
                          Subscribe
                          <i class="fa-solid fa-arrow-up-right-from-square ml-2 text-base"></i>
                      </button>
                  </form>

                  <div>
                      <p class="text-2xl font-oswald mb-4">Social Media:</p>
                      <div class="flex items-center gap-4">
                          @if (!empty($businessSetting->facebook))
                              <a href="{{ $businessSetting->facebook }}" aria-label="Facebook" target="_blank"
                                  class="w-12 h-12 rounded-full bg-white text-slate-900 grid place-items-center hover:opacity-90 transition">
                                  <i class="fa-brands fa-facebook-f"></i>
                              </a>
                          @endif

                          @if (!empty($businessSetting->instagram))
                              <a href="{{ $businessSetting->instagram }}" aria-label="Instagram"
                                  class="w-12 h-12 rounded-full bg-white text-slate-900 grid place-items-center hover:opacity-90 transition">
                                  <i class="fa-brands fa-instagram"></i>
                              </a>
                          @endif

                          @if (!empty($businessSetting->twitter))
                              <a href="{{ $businessSetting->twitter }}" aria-label="Instagram"
                                  class="w-12 h-12 rounded-full bg-white text-slate-900 grid place-items-center hover:opacity-90 transition">
                                  <i class="fa-brands fa-twitter"></i>
                              </a>
                          @endif

                          @if (!empty($businessSetting->tiktok))
                              <a href="{{ $businessSetting->tiktok }}" aria-label="Instagram"
                                  class="w-12 h-12 rounded-full bg-white text-slate-900 grid place-items-center hover:opacity-90 transition">
                                  <i class="fa-brands fa-tiktok"></i>
                              </a>
                          @endif

                          @if (!empty($businessSetting->youtube))
                              <a href="{{ $businessSetting->youtube }}" aria-label="Instagram"
                                  class="w-12 h-12 rounded-full bg-white text-slate-900 grid place-items-center hover:opacity-90 transition">
                                  <i class="fa-brands fa-youtube"></i>
                              </a>
                          @endif
                      </div>
                  </div>
              </aside>
          </div>

          <div class="mt-14 pt-6 border-t border-white/10 text-slate-200/80 text-sm text-center font-jost lg:hidden">
              © Copyright 2025 Foodu. All Rights Reserved
          </div>
      </div>
  </footer>
