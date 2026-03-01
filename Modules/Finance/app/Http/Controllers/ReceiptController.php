<?php

namespace Modules\Finance\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\Finance\Models\Receipt;

class ReceiptController extends Controller
{
    /**
     * Display the receipt.
     */
    public function show(string $id)
    {
        $receipt = Receipt::with(['invoice', 'payment'])->findOrFail($id);

        // Mark as viewed if not already
        if ($receipt->status !== 'viewed') {
            $receipt->markAsViewed();
        }

        return view('finance::receipts.show', compact('receipt'));
    }

    /**
     * Download the receipt as PDF (placeholder for future implementation).
     */
    public function download(string $id)
    {
        $receipt = Receipt::with(['invoice', 'payment'])->findOrFail($id);

        // Mark as downloaded
        $receipt->markAsDownloaded();

        // For now, return the HTML view
        // In the future, this will generate a PDF using DomPDF or similar
        return view('finance::receipts.show', compact('receipt'));
    }

    /**
     * Send receipt via email (placeholder for future implementation).
     */
    public function sendEmail(string $id)
    {
        $receipt = Receipt::findOrFail($id);

        if (! $receipt->customer_email) {
            return response()->json([
                'message' => 'No email address on record for this receipt.',
                'status' => 'error',
            ], 422);
        }

        try {
            app(CommunicationService::class)->sendToContact(
                channel: 'email',
                toEmail: $receipt->customer_email,
                toPhone: null,
                subject: null,
                templateCode: 'payment_receipt',
                data: [
                    'name'              => $receipt->customer_name ?? 'Customer',
                    'booking_reference' => $receipt->receipt_number,
                    'amount'            => number_format((float) $receipt->amount, 2),
                    'date'              => $receipt->receipt_date?->format('d M Y') ?? now()->format('d M Y'),
                ]
            );

            $receipt->markAsSent();
        } catch (\Exception $e) {
            Log::error('Failed to send receipt email', [
                'receipt_id' => $receipt->id,
                'error'      => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to send receipt email: '.$e->getMessage(),
                'status'  => 'error',
            ], 500);
        }

        return response()->json([
            'message' => 'Receipt sent to '.$receipt->customer_email,
            'status'  => 'success',
        ]);
    }
}
