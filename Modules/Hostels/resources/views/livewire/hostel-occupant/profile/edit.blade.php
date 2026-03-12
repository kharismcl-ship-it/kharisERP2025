<div class="space-y-6">

    {{-- ── Page header ──────────────────────────────────────────────────── --}}
    <div>
        <h1 class="text-xl font-semibold text-gray-900">Profile Settings</h1>
        <p class="mt-0.5 text-sm text-gray-500">Update your personal information and password.</p>
    </div>

    @if(session('message'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="updateProfile" class="space-y-6">

        {{-- ── Personal Information ──────────────────────────────────────── --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-5">Personal Information</h2>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">First Name *</label>
                    <input type="text" wire:model="first_name"
                           class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('first_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Last Name *</label>
                    <input type="text" wire:model="last_name"
                           class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('last_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email *</label>
                    <input type="email" wire:model="email"
                           class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Phone *</label>
                    <input type="text" wire:model="phone"
                           class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('phone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Student ID</label>
                    <input type="text" wire:model="student_id"
                           class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('student_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Institution</label>
                    <input type="text" wire:model="institution"
                           class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('institution') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Address</label>
                    <textarea wire:model="address" rows="3"
                              class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    @error('address') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

            </div>
        </div>

        {{-- ── Emergency Contact ─────────────────────────────────────────── --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-5">Emergency Contact</h2>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Contact Name</label>
                    <input type="text" wire:model="emergency_contact_name"
                           placeholder="Full name"
                           class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('emergency_contact_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Contact Phone</label>
                    <input type="text" wire:model="emergency_contact_phone"
                           placeholder="Phone number"
                           class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('emergency_contact_phone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

            </div>
        </div>

        {{-- ── Change Password ───────────────────────────────────────────── --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-1">Change Password</h2>
            <p class="text-xs text-gray-400 mb-5">Leave blank to keep your current password.</p>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                <div class="sm:col-span-2 sm:max-w-sm">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Current Password</label>
                    <input type="password" wire:model="current_password"
                           class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('current_password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">New Password</label>
                    <input type="password" wire:model="new_password"
                           class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('new_password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirm New Password</label>
                    <input type="password" wire:model="new_password_confirmation"
                           class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

            </div>
        </div>

        {{-- ── Submit ────────────────────────────────────────────────────── --}}
        <div class="flex sm:justify-end">
            <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition-colors sm:w-auto">
                Save Changes
            </button>
        </div>

    </form>

</div>
