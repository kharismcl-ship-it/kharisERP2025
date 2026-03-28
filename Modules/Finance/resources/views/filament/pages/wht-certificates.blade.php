<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filter Bar --}}
        <div class="fi-section rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <h3 class="text-base font-semibold text-gray-950 dark:text-white mb-4">Generate WHT Certificates</h3>
            <div class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-48">
                    <label class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase mb-1 block">Vendor Name (optional)</label>
                    <input type="text" wire:model.live="vendorName" placeholder="Search vendor..."
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase mb-1 block">Month</label>
                    <select wire:model="periodMonth"
                        class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm">
                        @foreach(range(1,12) as $m)
                            <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase mb-1 block">Year</label>
                    <select wire:model="periodYear"
                        class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm">
                        @foreach(range(2020, date('Y') + 1) as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <button wire:click="generate"
                    class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg">
                    Generate Certificates
                </button>
            </div>
        </div>

        {{-- Results --}}
        @if($generated)
            @if($certificates->isEmpty())
                <div class="fi-section rounded-xl bg-white p-8 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 text-center text-gray-500">
                    No vendor invoices with withholding tax found for this period.
                </div>
            @else
                <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-white/10 flex justify-between items-center">
                        <h3 class="text-base font-semibold text-gray-950 dark:text-white">
                            WHT Certificates — {{ \Carbon\Carbon::create()->month($periodMonth)->format('F') }} {{ $periodYear }}
                        </h3>
                        <span class="text-sm text-gray-500">{{ $certificates->count() }} vendors</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendor</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Invoices</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Gross Amount</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">WHT Rate</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">WHT Amount</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Certificate</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                                @foreach($certificates as $i => $cert)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $cert['vendor'] }}</td>
                                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400">{{ $cert['invoice_count'] }}</td>
                                        <td class="px-4 py-3 text-right font-medium">GHS {{ number_format($cert['gross_amount'], 2) }}</td>
                                        <td class="px-4 py-3 text-right">{{ $cert['wht_rate'] }}%</td>
                                        <td class="px-4 py-3 text-right font-semibold text-danger-600 dark:text-danger-400">GHS {{ number_format($cert['wht_amount'], 2) }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <button onclick="printCert({{ $i }})"
                                                class="text-xs px-3 py-1 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-md text-gray-700 dark:text-gray-300">
                                                Print
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50 dark:bg-gray-800 font-semibold">
                                <tr>
                                    <td colspan="2" class="px-4 py-3 text-gray-700 dark:text-gray-300">Total</td>
                                    <td class="px-4 py-3 text-right">GHS {{ number_format($certificates->sum('gross_amount'), 2) }}</td>
                                    <td class="px-4 py-3 text-right">—</td>
                                    <td class="px-4 py-3 text-right text-danger-600 dark:text-danger-400">GHS {{ number_format($certificates->sum('wht_amount'), 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- Printable Certificate Templates (hidden, shown on print) --}}
                @foreach($certificates as $i => $cert)
                    <div id="cert-{{ $i }}" class="hidden print-cert" style="display:none;">
                        <div style="font-family: Arial, sans-serif; padding: 40px; max-width: 700px; margin: 0 auto; border: 2px solid #333;">
                            <div style="text-align: center; margin-bottom: 20px;">
                                <h2 style="font-size: 20px; font-weight: bold;">WITHHOLDING TAX CERTIFICATE</h2>
                                <p style="font-size: 12px; color: #666;">Ghana Revenue Authority — Certificate of WHT Deduction</p>
                            </div>
                            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                                <tr>
                                    <td style="padding: 6px; width: 40%; font-weight: bold;">Vendor Name:</td>
                                    <td style="padding: 6px;">{{ $cert['vendor'] }}</td>
                                </tr>
                                <tr style="background: #f9f9f9;">
                                    <td style="padding: 6px; font-weight: bold;">Period:</td>
                                    <td style="padding: 6px;">{{ $cert['period'] }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px; font-weight: bold;">Invoice References:</td>
                                    <td style="padding: 6px;">{{ $cert['invoices'] }}</td>
                                </tr>
                                <tr style="background: #f9f9f9;">
                                    <td style="padding: 6px; font-weight: bold;">Gross Amount:</td>
                                    <td style="padding: 6px;">GHS {{ number_format($cert['gross_amount'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px; font-weight: bold;">WHT Rate:</td>
                                    <td style="padding: 6px;">{{ $cert['wht_rate'] }}%</td>
                                </tr>
                                <tr style="background: #fff3f3;">
                                    <td style="padding: 6px; font-weight: bold; color: #c00;">WHT Deducted:</td>
                                    <td style="padding: 6px; font-weight: bold; color: #c00;">GHS {{ number_format($cert['wht_amount'], 2) }}</td>
                                </tr>
                            </table>
                            <p style="font-size: 11px; color: #666; border-top: 1px solid #ccc; padding-top: 10px;">
                                This certificate is issued in accordance with Section 118 of the Income Tax Act, 2015 (Act 896).
                                Retain this certificate as evidence of WHT deducted for tax filing purposes.
                            </p>
                            <div style="margin-top: 30px; display: flex; justify-content: space-between;">
                                <div>
                                    <p style="font-size: 11px;">Authorised Signatory</p>
                                    <div style="border-top: 1px solid #333; width: 200px; margin-top: 30px;"></div>
                                </div>
                                <div style="text-align: right; font-size: 11px; color: #666;">
                                    <p>Date: {{ now()->format('d F Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        @endif
    </div>

    <script>
        function printCert(index) {
            const cert = document.getElementById('cert-' + index);
            if (!cert) return;
            const printWindow = window.open('', '_blank', 'width=800,height=600');
            printWindow.document.write('<html><head><title>WHT Certificate</title></head><body>');
            printWindow.document.write(cert.innerHTML);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        }
    </script>
</x-filament-panels::page>