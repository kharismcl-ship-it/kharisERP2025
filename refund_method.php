/**
     * Process a refund for a payment.
     */
    public function refund(PayIntent \$intent, int \$amount, string \$reason = ''): array
    {
        // Get the config for the company
        \$config = \$this->getProviderConfig(\$intent, 'flutterwave');

        // Validate required config
        if (empty(\$config['secret_key'])) {
            throw new \Exception('Flutterwave secret key is not configured');
        }

        \$secretKey = \$config['secret_key'];
        \$baseUrl = \$this->getApiBaseUrl(\$config['mode'] ?? 'live');

        // Get the transaction ID from the intent
        \$transactionId = \$intent->provider_reference;

        if (!\$transactionId) {
            throw new \Exception('Transaction ID not found for refund');
        }

        // Prepare refund data
        \$data = [
            'amount' => \$amount,
            'comment' => \$reason ?: 'Refund requested'
        ];

        // Make API request to process refund
        \$response = Http::withToken(\$secretKey)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post("{\$baseUrl}/transactions/{\$transactionId}/refund", \$data);

        Log::debug('Flutterwave refund request', ['data' => \$data, 'transaction_id' => \$transactionId]);

        if (!\$response->successful()) {
            throw new \Exception('Failed to process Flutterwave refund: ' . \$response->body());
        }

        \$result = \$response->json();

        // Log the response for debugging
        Log::debug('Flutterwave refund response', ['response' => \$result]);

        if (!isset(\$result['status']) || \$result['status'] !== 'success') {
            \$message = \$result['message'] ?? 'Refund processing failed';
            throw new \Exception('Flutterwave refund failed: ' . \$message);
        }

        return [
            'success' => true,
            'refund_reference' => \$result['data']['id'] ?? null,
            'message' => \$result['message'] ?? 'Refund processed successfully',
            'raw_response' => \$result
        ];
    }