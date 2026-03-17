<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Account</h1>
            <p class="text-sm text-gray-500 mt-1">Manage your profile, addresses and password</p>
        </div>
        <a href="{{ route('farm-shop.my-orders') }}" class="text-sm font-medium text-green-700 hover:text-green-900">← My Orders</a>
    </div>

    {{-- Profile Section --}}
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-5">Profile Information</h2>

        @if(session('profile_success'))
            <div class="mb-4 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                {{ session('profile_success') }}
            </div>
        @endif

        <form wire:submit.prevent="saveProfile" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input wire:model="name" type="text"
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" />
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input wire:model="phone" type="tel"
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" />
                    @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Default Delivery Address</label>
                <input wire:model="defaultAddress" type="text"
                    placeholder="Street, Area, City"
                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" />
                @error('defaultAddress') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nearest Landmark</label>
                <input wire:model="defaultLandmark" type="text"
                    placeholder="e.g. Near Shell Station, Kasoa Road"
                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" />
                @error('defaultLandmark') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="pt-2">
                <button type="submit"
                    wire:loading.attr="disabled"
                    class="bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white font-semibold py-2.5 px-6 rounded-xl transition-colors">
                    <span wire:loading.remove wire:target="saveProfile">Save Changes</span>
                    <span wire:loading wire:target="saveProfile">Saving...</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Saved Addresses --}}
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-semibold text-gray-900">Saved Addresses</h2>
            <button wire:click="$set('showAddressForm', true)"
                class="text-sm font-medium text-green-700 hover:text-green-900 border border-green-300 px-3 py-1.5 rounded-lg">
                + Add Address
            </button>
        </div>

        @if(session('address_success'))
            <div class="mb-4 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                {{ session('address_success') }}
            </div>
        @endif

        {{-- Add Address Form --}}
        @if($showAddressForm)
        <div class="mb-5 border border-gray-200 rounded-xl p-5 bg-gray-50">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">New Address</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Label <span class="text-red-500">*</span></label>
                    <input wire:model="addrLabel" type="text" placeholder="e.g. Home, Office, Parent's House"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm" />
                    @error('addrLabel') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Address <span class="text-red-500">*</span></label>
                    <textarea wire:model="addrAddress" rows="2" placeholder="Street, Town, City, Region"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm"></textarea>
                    @error('addrAddress') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Nearest Landmark</label>
                    <input wire:model="addrLandmark" type="text" placeholder="e.g. Near Shell Station"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm" />
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input wire:model="addrIsDefault" type="checkbox" class="rounded border-gray-300 text-green-600 focus:ring-green-500" />
                    <span class="text-sm text-gray-700">Set as default address</span>
                </label>
                <div class="flex gap-3 pt-1">
                    <button wire:click="addAddress"
                        wire:loading.attr="disabled"
                        class="bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white text-sm font-semibold py-2 px-5 rounded-lg transition-colors">
                        <span wire:loading.remove wire:target="addAddress">Save Address</span>
                        <span wire:loading wire:target="addAddress">Saving...</span>
                    </button>
                    <button wire:click="$set('showAddressForm', false)" class="text-sm text-gray-500 hover:text-gray-700 font-medium">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
        @endif

        {{-- Address List --}}
        @if(count($addresses) === 0 && !$showAddressForm)
            <p class="text-sm text-gray-400 py-4 text-center">No saved addresses yet. Add one for faster checkout.</p>
        @else
            <div class="space-y-3">
                @foreach($addresses as $addr)
                <div class="flex items-start justify-between border border-gray-100 rounded-xl p-4 {{ $addr->is_default ? 'bg-green-50 border-green-200' : '' }}">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-0.5">
                            <span class="font-medium text-gray-900 text-sm">{{ $addr->label }}</span>
                            @if($addr->is_default)
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">Default</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600">{{ $addr->address }}</p>
                        @if($addr->landmark)
                            <p class="text-xs text-gray-400 mt-0.5">📍 {{ $addr->landmark }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 ml-4 flex-shrink-0">
                        @if(!$addr->is_default)
                            <button wire:click="setDefaultAddress({{ $addr->id }})"
                                class="text-xs text-gray-500 hover:text-green-700 border border-gray-200 hover:border-green-300 px-2 py-1 rounded-lg transition-colors">
                                Set Default
                            </button>
                        @endif
                        <button wire:click="deleteAddress({{ $addr->id }})"
                            wire:confirm="Delete this address?"
                            class="text-xs text-red-500 hover:text-red-700">
                            ✕
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Referral Program --}}
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-1">Refer Friends & Earn</h2>
        <p class="text-sm text-gray-500 mb-5">Share your referral link. When a friend places their first paid order, you both earn loyalty points.</p>

        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-5"
            x-data="{ copied: false }"
            x-on:click="
                navigator.clipboard.writeText('{{ route('farm-shop.register') }}?ref={{ $referralCode }}');
                copied = true;
                setTimeout(() => copied = false, 2000);
            ">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Your Referral Link</p>
            <div class="flex items-center justify-between gap-2 cursor-pointer">
                <code class="text-sm text-green-800 font-mono break-all">{{ route('farm-shop.register') }}?ref={{ $referralCode }}</code>
                <span x-show="!copied" class="text-xs text-green-700 font-medium whitespace-nowrap border border-green-300 px-2 py-1 rounded-lg flex-shrink-0">Copy 📋</span>
                <span x-show="copied" class="text-xs text-green-700 font-medium whitespace-nowrap flex-shrink-0">Copied ✓</span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 text-center">
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-2xl font-bold text-gray-900">{{ $referralCount }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Friends Referred</p>
            </div>
            <div class="bg-amber-50 rounded-xl p-4">
                <p class="text-2xl font-bold text-amber-700">{{ $referralCredited * 50 }}</p>
                <p class="text-xs text-amber-600 mt-0.5">Points Earned from Referrals</p>
            </div>
        </div>
    </div>

    {{-- Change Password Section --}}
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-5">Change Password</h2>

        @if(session('password_success'))
            <div class="mb-4 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                {{ session('password_success') }}
            </div>
        @endif

        <form wire:submit.prevent="changePassword" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                <input wire:model="currentPassword" type="password"
                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" />
                @error('currentPassword') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <input wire:model="newPassword" type="password"
                        placeholder="At least 8 characters"
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" />
                    @error('newPassword') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                    <input wire:model="newPasswordConfirm" type="password"
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" />
                    @error('newPasswordConfirm') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="pt-2">
                <button type="submit"
                    wire:loading.attr="disabled"
                    class="bg-gray-800 hover:bg-gray-900 disabled:opacity-50 text-white font-semibold py-2.5 px-6 rounded-xl transition-colors">
                    <span wire:loading.remove wire:target="changePassword">Update Password</span>
                    <span wire:loading wire:target="changePassword">Updating...</span>
                </button>
            </div>
        </form>
    </div>
</div>
