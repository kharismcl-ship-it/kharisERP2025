<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold">Invoices</h1>
        <a href="{{ route('finance.invoices.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Create Invoice
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded p-4">
        @if($invoices->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($invoices as $invoice)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $invoice->invoice_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $invoice->customer_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $invoice->invoice_date->format('M d, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $invoice->due_date?->format('M d, Y') ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($invoice->total, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($invoice->status === 'paid') bg-green-100 text-green-800
                                        @elseif($invoice->status === 'overdue') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $invoices->links() }}
            </div>
        @else
            <p>No invoices found.</p>
        @endif
    </div>
</div>