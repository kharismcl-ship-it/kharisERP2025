<?php

namespace Modules\Hostels\Services\Automation;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\Core\Models\AutomationSetting;
use Modules\Hostels\Models\Deposit;

class DepositReminderHandler
{
    public function execute(AutomationSetting $setting): array
    {
        $config      = $setting->config ?? [];
        $daysOverdue = (int) ($config['days_overdue'] ?? 7);

        $pendingDeposits = Deposit::query()
            ->with(['booking.hostelOccupant', 'booking.hostel'])
            ->where('status', Deposit::STATUS_PENDING)
            ->where('created_at', '<=', now()->subDays($daysOverdue))
            ->when($setting->company_id, fn ($q) => $q->whereHas('booking', fn ($bq) =>
                $bq->where('company_id', $setting->company_id)
            ))
            ->get();

        $sent = 0;

        foreach ($pendingDeposits as $deposit) {
            $occupant = $deposit->booking?->hostelOccupant;

            if (! $occupant || ! $occupant->phone) {
                continue;
            }

            try {
                app(CommunicationService::class)->sendToContact(
                    channel: 'sms',
                    toEmail: null,
                    toPhone: $occupant->phone,
                    subject: null,
                    templateCode: 'hostel_deposit_reminder',
                    data: [
                        'name'             => $occupant->full_name ?? $occupant->first_name,
                        'hostel_name'      => $deposit->booking?->hostel?->name ?? 'Hostel',
                        'deposit_amount'   => number_format($deposit->amount, 2),
                        'booking_reference' => $deposit->booking?->booking_reference ?? '',
                    ]
                );

                $sent++;
            } catch (\Exception $e) {
                Log::warning('Deposit reminder SMS failed', [
                    'deposit_id'  => $deposit->id,
                    'occupant_id' => $occupant->id,
                    'error'       => $e->getMessage(),
                ]);
            }
        }

        return [
            'success'          => true,
            'records_processed' => $sent,
            'details'          => [
                'pending_deposits_found' => $pendingDeposits->count(),
                'reminders_sent'         => $sent,
            ],
        ];
    }
}