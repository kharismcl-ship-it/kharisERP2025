<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold">Chart of Accounts</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded p-4">
        @if($accounts->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($accounts as $account)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $account->code }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $account->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($account->type === 'asset') bg-blue-100 text-blue-800
                                        @elseif($account->type === 'liability') bg-yellow-100 text-yellow-800
                                        @elseif($account->type === 'equity') bg-purple-100 text-purple-800
                                        @elseif($account->type === 'income') bg-green-100 text-green-800
                                        @elseif($account->type === 'expense') bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($account->type) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p>No accounts found.</p>
        @endif
    </div>
</div>