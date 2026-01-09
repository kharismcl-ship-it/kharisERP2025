<?php

namespace Modules\CommunicationCentre\Services;

use Modules\CommunicationCentre\Models\CommMessage;
use Modules\CommunicationCentre\Models\CommProviderConfig;
use Modules\CommunicationCentre\Models\CommTemplate;

class CommunicationService
{
    /**
     * Send a message to a model.
     *
     * @param  mixed  $notifiable
     */
    public function sendToModel($notifiable, string $channel, string $templateCode, array $data = []): CommMessage
    {
        // Get the company ID from the model or current session
        $companyId = $this->resolveCompanyId($notifiable);

        // Load the template
        $template = CommTemplate::where('code', $templateCode)
            ->where(function ($query) use ($companyId) {
                $query->where('company_id', $companyId)
                    ->orWhereNull('company_id');
            })
            ->first();

        if (! $template) {
            throw new \Exception("Template {$templateCode} not found");
        }

        // Render the template with data
        $renderedSubject = $this->renderTemplate($template->subject, $data);
        $renderedBody = $this->renderTemplate($template->body, $data);

        // Get the provider from the template's provider config
        $provider = $template->providerConfig ? $template->providerConfig->provider : null;

        // Create the message record
        $message = CommMessage::create([
            'company_id' => $companyId,
            'notifiable_type' => get_class($notifiable),
            'notifiable_id' => $notifiable->id,
            'channel' => $channel,
            'provider' => $provider,
            'template_id' => $template->id,
            'to_name' => $notifiable->getCommName(),
            'to_email' => $notifiable->getCommEmail(),
            'to_phone' => $notifiable->getCommPhone(),
            'subject' => $renderedSubject,
            'body' => $renderedBody,
            'status' => 'queued',
        ]);

        // Queue the message for sending
        $this->queueMessage($message);

        return $message;
    }

    /**
     * Send a message to a contact.
     */
    public function sendToContact(
        string $channel,
        ?string $toEmail,
        ?string $toPhone,
        ?string $subject,
        ?string $templateCode,
        array $data = []
    ): CommMessage {
        // Get the company ID from current session
        $companyId = $this->resolveCurrentCompanyId();

        $renderedSubject = $subject;
        $renderedBody = '';

        // Load and render template if provided
        if ($templateCode) {
            $template = CommTemplate::where('code', $templateCode)
                ->where(function ($query) use ($companyId) {
                    $query->where('company_id', $companyId)
                        ->orWhereNull('company_id');
                })
                ->first();

            if ($template) {
                $renderedSubject = $this->renderTemplate($template->subject, $data);
                $renderedBody = $this->renderTemplate($template->body, $data);
                $provider = $template->providerConfig ? $template->providerConfig->provider : null;
            } else {
                $provider = $this->getDefaultProvider($channel, $companyId);
            }
        } else {
            $provider = $this->getDefaultProvider($channel, $companyId);
        }

        // Create the message record
        $message = CommMessage::create([
            'company_id' => $companyId,
            'channel' => $channel,
            'provider' => $provider,
            'to_email' => $toEmail,
            'to_phone' => $toPhone,
            'subject' => $renderedSubject,
            'body' => $renderedBody,
            'status' => 'queued',
        ]);

        // Queue the message for sending
        $this->queueMessage($message);

        return $message;
    }

    /**
     * Send a raw message.
     */
    public function sendRaw(
        string $channel,
        ?string $toPhone,
        ?string $subject,
        string $body
    ): CommMessage {
        // Get the company ID from current session
        $companyId = $this->resolveCurrentCompanyId();

        // Get the default provider for the channel
        $provider = $this->getDefaultProvider($channel, $companyId);

        // Create the message record
        $message = CommMessage::create([
            'company_id' => $companyId,
            'channel' => $channel,
            'provider' => $provider,
            'to_phone' => $toPhone,
            'subject' => $subject,
            'body' => $body,
            'status' => 'queued',
        ]);

        // Queue the message for sending
        $this->queueMessage($message);

        return $message;
    }

    /**
     * Queue a message for sending.
     */
    protected function queueMessage(CommMessage $message): void
    {
        // In a real implementation, you would dispatch a job to the queue
        // dispatch(new SendCommMessageJob($message));

        // For now, we'll just mark it as sent
        $message->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    /**
     * Get the default provider for a channel.
     */
    protected function getDefaultProvider(string $channel, ?int $companyId): ?string
    {
        $providerConfig = CommProviderConfig::where('channel', $channel)
            ->where('is_default', true)
            ->where(function ($query) use ($companyId) {
                $query->where('company_id', $companyId)
                    ->orWhereNull('company_id');
            })
            ->first();

        return $providerConfig ? $providerConfig->provider : null;
    }

    /**
     * Resolve company ID from a model.
     *
     * @param  mixed  $model
     */
    protected function resolveCompanyId($model): ?int
    {
        // Try to get company ID from the model
        if (method_exists($model, 'getAttribute')) {
            return $model->getAttribute('company_id') ??
                   $model->getAttribute('current_company_id') ??
                   null;
        }

        return $this->resolveCurrentCompanyId();
    }

    /**
     * Resolve current company ID from session.
     */
    protected function resolveCurrentCompanyId(): ?int
    {
        // Try to get company ID from the authenticated user
        if (auth()->check() && method_exists(auth()->user(), 'getAttribute')) {
            return auth()->user()->getAttribute('current_company_id') ?? null;
        }

        return null;
    }

    /**
     * Render a template with data.
     */
    protected function renderTemplate(?string $template, array $data): string
    {
        if (! $template) {
            return '';
        }

        // Replace placeholders with data
        $rendered = $template;
        foreach ($data as $key => $value) {
            $rendered = str_replace('{{'.$key.'}}', $value, $rendered);
        }

        return $rendered;
    }
}
