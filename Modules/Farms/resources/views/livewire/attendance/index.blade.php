<div class="py-8">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-5">

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Worker Attendance</h1>
                <p class="text-sm text-gray-500">{{ $farm->name }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 flex items-center gap-4">
            <label class="text-sm font-medium text-gray-700">Date:</label>
            <input type="date" wire:model.live="attendanceDate"
                   class="rounded-md border-gray-300 text-sm" />
            @if($saved)
                <span class="text-green-600 text-sm font-medium">Saved!</span>
            @endif
        </div>

        @if(empty($entries))
            <div class="bg-white rounded-lg shadow p-12 text-center text-gray-400">
                No active workers found.
            </div>
        @else
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Worker</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hours</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($entries as $index => $entry)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $entry['worker_name'] }}</td>
                                <td class="px-4 py-3">
                                    <select wire:model="entries.{{ $index }}.status"
                                            class="rounded-md border-gray-300 text-sm">
                                        <option value="present">Present</option>
                                        <option value="absent">Absent</option>
                                        <option value="half_day">Half Day</option>
                                        <option value="leave">Leave</option>
                                    </select>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" wire:model="entries.{{ $index }}.hours"
                                           min="0" max="24" step="0.5"
                                           class="w-20 rounded-md border-gray-300 text-sm" />
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" wire:model="entries.{{ $index }}.notes"
                                           placeholder="Optional note"
                                           class="w-full rounded-md border-gray-300 text-sm" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="p-4 border-t">
                    <button wire:click="markAttendance"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">
                        Save Attendance
                    </button>
                </div>
            </div>
        @endif

    </div>
</div>
