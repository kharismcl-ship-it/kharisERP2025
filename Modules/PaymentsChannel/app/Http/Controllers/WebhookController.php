<?php

namespace Modules\PaymentsChannel\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\PaymentsChannel\Facades\Payment;

class WebhookController extends Controller
{
    /**
     * Handle Flutterwave webhook.
     */
    public function flutterwave(Request $request)
    {
        try {
            // Validate webhook signature before processing
            $this->validateFlutterwaveWebhook($request);

            $intent = Payment::handleWebhook('flutterwave', $request->all());

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            \Log::error('Flutterwave webhook error: '.$e->getMessage(), [
                'exception' => $e,
                'payload' => $request->all(),
            ]);

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Handle Paystack webhook.
     */
    public function paystack(Request $request)
    {
        try {
            // Validate webhook signature before processing
            $this->validatePaystackWebhook($request);

            $intent = Payment::handleWebhook('paystack', $request->all());

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            \Log::error('Paystack webhook error: '.$e->getMessage(), [
                'exception' => $e,
                'payload' => $request->all(),
            ]);

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Handle PaySwitch webhook.
     */
    public function payswitch(Request $request)
    {
        try {
            $intent = Payment::handleWebhook('payswitch', $request->all());

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Handle Stripe webhook.
     */
    public function stripe(Request $request)
    {
        try {
            $intent = Payment::handleWebhook('stripe', $request->all());

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Handle GhanaPay webhook.
     */
    public function ghanapay(Request $request)
    {
        try {
            $intent = Payment::handleWebhook('ghanapay', $request->all());

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Validate Flutterwave webhook signature.
     */
    protected function validateFlutterwaveWebhook(Request $request)
    {
        $signature = $request->header('flutterwave-signature');
        $secretHash = config('services.flutterwave.secret_hash') ?? env('FLW_SECRET_HASH');

        // If no secret hash is configured, we can't validate
        if (empty($secretHash)) {
            \Log::warning('Flutterwave webhook secret hash not configured');

            return;
        }

        // Get raw body for signature verification
        $rawBody = $request->getContent();

        // Generate the expected signature using HMAC-SHA256
        $expectedSignature = base64_encode(hash_hmac('sha256', $rawBody, $secretHash, true));

        // Validate the signature using hash_equals for security
        if (! hash_equals($expectedSignature, $signature)) {
            throw new \Exception('Invalid Flutterwave webhook signature');
        }
    }

    /**
     * Validate Paystack webhook signature.
     */
    protected function validatePaystackWebhook(Request $request)
    {
        $signature = $request->header('x-paystack-signature');
        $secretKey = config('services.paystack.secret_key') ?? env('PAYSTACK_SECRET_KEY');

        // If no secret key is configured, we can't validate
        if (empty($secretKey)) {
            \Log::warning('Paystack webhook secret key not configured');

            return;
        }

        // Get raw body for signature verification
        $rawBody = $request->getContent();

        // Generate the expected signature using HMAC-SHA512
        $expectedSignature = hash_hmac('sha512', $rawBody, $secretKey);

        // Validate the signature using hash_equals for security
        if (! hash_equals($expectedSignature, $signature)) {
            throw new \Exception('Invalid Paystack webhook signature');
        }
    }
}
