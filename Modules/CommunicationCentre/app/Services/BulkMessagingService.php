<?php

namespace Modules\CommunicationCentre\Services;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Jobs\ProcessBulkMessage;
use Modules\CommunicationCentre\Models\CommMessage;
use Modules\CommunicationCentre\Models\CommTemplate;

class BulkMessagingService
{
    protected CommunicationService $communicationService;

    public function __construct(CommunicationService $communicationService)
    {
        $this->communicationService = $communicationService;
    }

    /**
     * Send bulk messages to multiple recipients
     */
    public function sendBulk(
        array $recipients,
        string $channel,
        string $templateCode,
        array $commonData = [],
        ?array $perRecipientData = null,
        ?int $companyId = null,
        bool $async = true
    ): array {
        $results = [
            'total' => count($recipients),
            'successful' => 0,
            'failed' => 0,
            'errors' => [],
            'message_ids' => [],
        ];

        if ($async) {
            return $this->sendBulkAsync($recipients, $channel, $templateCode, $commonData, $perRecipientData, $companyId);
        }

        // Process synchronously
        foreach ($recipients as $index => $recipient) {
            try {
                $recipientData = $perRecipientData[$index] ?? [];
                $data = array_merge($commonData, $recipientData);

                $message = $this->communicationService->sendToModel(
                    $recipient,
                    $channel,
                    $templateCode,
                    $data
                );

                if ($message) {
                    $results['successful']++;
                    $results['message_ids'][] = $message->id;
                } else {
                    $results['failed']++;
                    $results['errors'][] = [
                        'recipient' => $this->getRecipientIdentifier($recipient),
                        'error' => 'Channel disabled for recipient',
                    ];
                }
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'recipient' => $this->getRecipientIdentifier($recipient),
                    'error' => $e->getMessage(),
                ];
                Log::error('Bulk message failed', [
                    'recipient' => $this->getRecipientIdentifier($recipient),
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        return $results;
    }

    /**
     * Send bulk messages asynchronously using jobs
     */
    protected function sendBulkAsync(
        array $recipients,
        string $channel,
        string $templateCode,
        array $commonData = [],
        ?array $perRecipientData = null,
        ?int $companyId = null
    ): array {
        $batchId = uniqid('bulk_');

        // Chunk recipients to avoid memory issues
        $chunks = array_chunk($recipients, 100);
        $totalJobs = count($chunks);

        foreach ($chunks as $chunkIndex => $chunk) {
            $jobData = [
                'batch_id' => $batchId,
                'recipients' => $chunk,
                'channel' => $channel,
                'template_code' => $templateCode,
                'common_data' => $commonData,
                'per_recipient_data' => $perRecipientData,
                'company_id' => $companyId,
                'chunk_index' => $chunkIndex,
                'total_chunks' => $totalJobs,
            ];

            ProcessBulkMessage::dispatch($jobData);
        }

        return [
            'batch_id' => $batchId,
            'total_recipients' => count($recipients),
            'total_jobs' => $totalJobs,
            'status' => 'queued',
            'message' => 'Bulk messages have been queued for processing',
        ];
    }

    /**
     * Get batch status
     */
    public function getBatchStatus(string $batchId): array
    {
        $messages = CommMessage::where('batch_id', $batchId)->get();

        $statusCounts = $messages->groupBy('status')->map->count();

        return [
            'batch_id' => $batchId,
            'total' => $messages->count(),
            'status_counts' => $statusCounts->toArray(),
            'success_rate' => $messages->count() > 0
                ? round(($statusCounts['delivered'] ?? 0) / $messages->count() * 100, 2)
                : 0,
            'messages' => $messages->take(10)->values(), // Sample of messages
        ];
    }

    /**
     * Validate bulk message parameters
     */
    public function validateBulkParameters(array $recipients, string $channel, string $templateCode): array
    {
        $errors = [];

        if (empty($recipients)) {
            $errors[] = 'No recipients provided';
        }

        if (count($recipients) > 10000) {
            $errors[] = 'Maximum 10,000 recipients allowed per bulk operation';
        }

        $validChannels = ['email', 'sms', 'whatsapp', 'database'];
        if (! in_array($channel, $validChannels)) {
            $errors[] = "Invalid channel: {$channel}. Valid channels are: ".implode(', ', $validChannels);
        }

        // Check template exists
        try {
            $template = CommTemplate::where('code', $templateCode)->first();
            if (! $template) {
                $errors[] = "Template not found: {$templateCode}";
            }
        } catch (\Exception $e) {
            $errors[] = "Error validating template: {$e->getMessage()}";
        }

        return $errors;
    }

    /**
     * Get recipient identifier for logging
     */
    protected function getRecipientIdentifier($recipient): string
    {
        if (is_object($recipient)) {
            if (method_exists($recipient, 'getEmail')) {
                return $recipient->getEmail();
            }
            if (method_exists($recipient, 'email')) {
                return $recipient->email;
            }
            if (method_exists($recipient, 'getPhone')) {
                return $recipient->getPhone();
            }
            if (property_exists($recipient, 'phone')) {
                return $recipient->phone;
            }

            return get_class($recipient).'#'.($recipient->id ?? 'unknown');
        }

        return (string) $recipient;
    }

    /**
     * Get bulk messaging statistics
     */
    public function getStatistics(?int $companyId = null, ?string $timePeriod = '30d'): array
    {
        $query = CommMessage::whereNotNull('batch_id');

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        // Apply time period filter
        $this->applyTimeFilter($query, $timePeriod);

        $totalBatches = $query->distinct('batch_id')->count('batch_id');
        $totalMessages = $query->count();

        $statusStats = $query->groupBy('status')
            ->selectRaw('status, count(*) as count')
            ->pluck('count', 'status')
            ->toArray();

        $channelStats = $query->groupBy('channel')
            ->selectRaw('channel, count(*) as count')
            ->pluck('count', 'channel')
            ->toArray();

        return [
            'total_batches' => $totalBatches,
            'total_messages' => $totalMessages,
            'status_statistics' => $statusStats,
            'channel_statistics' => $channelStats,
            'success_rate' => $totalMessages > 0
                ? round(($statusStats['delivered'] ?? 0) / $totalMessages * 100, 2)
                : 0,
        ];
    }

    /**
     * Apply time period filter to query
     */
    protected function applyTimeFilter($query, string $timePeriod): void
    {
        $now = now();

        switch ($timePeriod) {
            case '7d':
                $query->where('created_at', '>=', $now->subDays(7));
                break;
            case '30d':
                $query->where('created_at', '>=', $now->subDays(30));
                break;
            case '90d':
                $query->where('created_at', '>=', $now->subDays(90));
                break;
            case 'ytd':
                $query->where('created_at', '>=', $now->startOfYear());
                break;
            case 'all':
                // No filter
                break;
        }
    }

    /**
     * Clean up old bulk message records
     */
    public function cleanupOldRecords(int $days = 90): int
    {
        $cutoffDate = now()->subDays($days);

        return CommMessage::whereNotNull('batch_id')
            ->where('created_at', '<', $cutoffDate)
            ->delete();
    }
}
