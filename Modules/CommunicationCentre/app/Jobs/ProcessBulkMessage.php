<?php

namespace Modules\CommunicationCentre\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;

class ProcessBulkMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public array $jobData
    ) {}

    public function handle(CommunicationService $communicationService): void
    {
        $batchId = $this->jobData['batch_id'];
        $recipients = $this->jobData['recipients'];
        $channel = $this->jobData['channel'];
        $templateCode = $this->jobData['template_code'];
        $commonData = $this->jobData['common_data'];
        $perRecipientData = $this->jobData['per_recipient_data'] ?? [];
        $companyId = $this->jobData['company_id'];

        Log::info('Processing bulk message chunk', [
            'batch_id' => $batchId,
            'chunk_size' => count($recipients),
            'channel' => $channel,
            'template_code' => $templateCode,
        ]);

        foreach ($recipients as $index => $recipient) {
            try {
                $recipientData = $perRecipientData[$index] ?? [];
                $data = array_merge($commonData, $recipientData);

                $message = $communicationService->sendToModel(
                    $recipient,
                    $channel,
                    $templateCode,
                    $data
                );

                if ($message) {
                    // Update message with batch ID
                    $message->update(['batch_id' => $batchId]);
                }

            } catch (\Exception $e) {
                Log::error('Failed to process bulk message recipient', [
                    'batch_id' => $batchId,
                    'recipient_index' => $index,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        Log::info('Completed processing bulk message chunk', [
            'batch_id' => $batchId,
            'chunk_size' => count($recipients),
        ]);
    }

    public function failed(\Exception $exception): void
    {
        Log::error('Bulk message job failed', [
            'batch_id' => $this->jobData['batch_id'] ?? 'unknown',
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
