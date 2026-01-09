<?php

namespace Modules\Finance\Http\Controllers;

use Illuminate\Routing\Controller;
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

        // Mark as sent
        $receipt->markAsSent();

        // TODO: Implement email sending logic
        // This will send the receipt to the customer's email

        return response()->json([
            'message' => 'Receipt will be sent to '.$receipt->customer_email,
            'status' => 'success',
        ]);
    }
}
