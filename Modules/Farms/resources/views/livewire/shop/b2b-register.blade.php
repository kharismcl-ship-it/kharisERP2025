<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    @if($submitted)
        {{-- Success state --}}
        <div class="bg-white rounded-2xl shadow-sm p-10 text-center">
            <div class="text-6xl mb-4">🏢</div>
            <h1 class="text-2xl font-bold text-gray-900 mb-3">Application Submitted!</h1>
            <p class="text-gray-600 mb-4">
                Thank you for applying for a wholesale account. Our team will review your application
                and get back to you within <strong>1–2 business days</strong>.
            </p>
            <p class="text-sm text-gray-400 mb-8">You can log in with your email and password. Wholesale pricing will activate once your account is approved.</p>
            <a href="{{ route('farm-shop.login') }}"
               class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-xl transition-colors">
                Log In Now
            </a>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-green-600 to-emerald-700 px-8 py-6">
                <h1 class="text-2xl font-bold text-white mb-1">Wholesale Account Application</h1>
                <p class="text-green-100 text-sm">For restaurants, hotels, caterers & bulk buyers</p>
            </div>

            <form wire:submit="submit" class="p-8 space-y-8">

                {{-- Personal / Login Details --}}
                <div>
                    <h2 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 bg-green-100 text-green-700 rounded-full flex items-center justify-center text-xs font-bold">1</span>
                        Your Login Details
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input wire:model="name" type="text" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none" placeholder="Your full name" />
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input wire:model="phone" type="tel" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none" placeholder="0XX XXX XXXX" />
                            @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input wire:model="email" type="email" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none" placeholder="you@business.com" />
                            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <input wire:model="password" type="password" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none" />
                            @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                            <input wire:model="password_confirmation" type="password" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none" />
                        </div>
                    </div>
                </div>

                {{-- Business Details --}}
                <div>
                    <h2 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 bg-green-100 text-green-700 rounded-full flex items-center justify-center text-xs font-bold">2</span>
                        Business Information
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Business Name</label>
                            <input wire:model="business_name" type="text" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none" placeholder="e.g. Golden Palace Restaurant" />
                            @error('business_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Business Type</label>
                            <select wire:model="business_type" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none">
                                @foreach(\Modules\Farms\Models\FarmB2bAccount::TYPES as $type)
                                    <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">TIN / VAT Number <span class="text-gray-400">(optional)</span></label>
                            <input wire:model="tax_id" type="text" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none" placeholder="Ghana Tax ID" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Procurement Contact Name</label>
                            <input wire:model="contact_name" type="text" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none" placeholder="Name of buyer/procurement officer" />
                            @error('contact_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Procurement Phone</label>
                            <input wire:model="contact_phone" type="tel" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none" />
                            @error('contact_phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Procurement Email <span class="text-gray-400">(optional)</span></label>
                            <input wire:model="contact_email" type="email" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none" />
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Business Address <span class="text-gray-400">(optional)</span></label>
                            <textarea wire:model="business_address" rows="2" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none" placeholder="Street, area, city"></textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-800">
                    ℹ️ Your application will be reviewed within 1–2 business days. Once approved, you'll automatically receive wholesale pricing on all purchases.
                </div>

                <button type="submit"
                        wire:loading.attr="disabled"
                        class="w-full bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white font-semibold py-3.5 rounded-xl transition-colors">
                    <span wire:loading.remove>Submit Wholesale Application</span>
                    <span wire:loading>Submitting...</span>
                </button>

                <p class="text-center text-sm text-gray-500">
                    Already have an account?
                    <a href="{{ route('farm-shop.login') }}" class="text-green-700 font-medium hover:underline">Log in here</a>
                </p>

            </form>
        </div>
    @endif

</div>
