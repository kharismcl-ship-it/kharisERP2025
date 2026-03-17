<x-filament-panels::page>
    @if(! $employee)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-yellow-800 text-sm">
            No employee profile is linked to your account. Please contact HR.
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Profile Card --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 text-center">
                <div class="w-20 h-20 rounded-full bg-teal-100 flex items-center justify-center text-teal-700 font-bold text-2xl mx-auto mb-3">
                    {{ strtoupper(substr($employee->first_name, 0, 1)) }}{{ strtoupper(substr($employee->last_name ?? '', 0, 1)) }}
                </div>
                <h2 class="text-lg font-bold text-gray-800">{{ $employee->full_name }}</h2>
                <p class="text-sm text-gray-500">{{ $employee->jobPosition?->title }}</p>
                <p class="text-xs text-teal-600 mt-1">{{ $employee->department?->name }}</p>
                <div class="mt-4 text-left space-y-2 text-sm text-gray-600 border-t border-gray-100 pt-4">
                    <div><span class="font-medium text-gray-700">Employee #:</span> {{ $employee->employee_number ?? '—' }}</div>
                    <div><span class="font-medium text-gray-700">Email:</span> {{ $employee->email }}</div>
                    <div><span class="font-medium text-gray-700">Status:</span>
                        <span class="ml-1 text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">{{ ucfirst($employee->employment_status) }}</span>
                    </div>
                    <div><span class="font-medium text-gray-700">Hire Date:</span> {{ $employee->hire_date?->format('M d, Y') ?? '—' }}</div>
                </div>
            </div>

            {{-- Editable Contact Details --}}
            <div class="lg:col-span-2 bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Contact Information <span class="text-xs font-normal text-gray-400">(You can update these fields)</span></h3>
                <form wire:submit.prevent="updateContact" class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Phone</label>
                        <input type="text" wire:model="phone"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Address</label>
                        <textarea wire:model="address" rows="2"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Emergency Contact Name</label>
                            <input type="text" wire:model="emergency_contact_name"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Emergency Contact Phone</label>
                            <input type="text" wire:model="emergency_contact_phone"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                    </div>
                    <div class="pt-2">
                        <button type="submit"
                                class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</x-filament-panels::page>
