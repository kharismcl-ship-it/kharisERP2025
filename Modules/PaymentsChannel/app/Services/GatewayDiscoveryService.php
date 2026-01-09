<?php

namespace Modules\PaymentsChannel\Services;

use Illuminate\Support\Facades\File;

class GatewayDiscoveryService
{
    public function getAvailableGateways(): array
    {
        $gatewayPath = module_path('PaymentsChannel').'/app/Services/Gateway';
        $files = File::files($gatewayPath);

        $gateways = [];

        foreach ($files as $file) {
            $filename = $file->getFilename();

            // Skip interface and non-gateway files
            if ($filename === 'PaymentGatewayInterface.php' ||
                $filename === 'PaymentInitResponse.php' ||
                $filename === 'PaymentVerifyResult.php') {
                continue;
            }

            // Extract gateway name from filename (e.g., FlutterwaveGateway.php -> flutterwave)
            if (preg_match('/(.*)Gateway\.php$/', $filename, $matches)) {
                $gatewayName = strtolower($matches[1]);
                $displayName = $this->getDisplayName($matches[1]);
                $gateways[$gatewayName] = $displayName;
            }
        }

        return $gateways;
    }

    protected function getDisplayName(string $gatewayName): string
    {
        $displayNames = [
            'flutterwave' => 'Flutterwave',
            'paystack' => 'Paystack',
            'payswitch' => 'PaySwitch',
            'stripe' => 'Stripe',
            'ghanapay' => 'GhanaPay',
            'manual' => 'Manual',
        ];

        return $displayNames[strtolower($gatewayName)] ?? ucfirst($gatewayName);
    }
}
