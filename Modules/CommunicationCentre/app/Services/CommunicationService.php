<?php

namespace Modules\CommunicationCentre\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Models\CommMessage;
use Modules\CommunicationCentre\Models\CommProviderConfig;
use Modules\CommunicationCentre\Models\CommTemplate;
use Modules\CommunicationCentre\Services\ChannelProviders\ChannelProviderInterface;
use Modules\CommunicationCentre\Services\ChannelProviders\FilamentDatabaseProvider;
use Modules\CommunicationCentre\Services\ChannelProviders\LaravelMailProvider;
use Modules\CommunicationCentre\Services\ChannelProviders\MailtrapEmailProvider;
use Modules\CommunicationCentre\Services\ChannelProviders\MnotifySmsProvider;
use Modules\CommunicationCentre\Services\ChannelProviders\TwilioWhatsAppProvider;
use Modules\CommunicationCentre\Services\ChannelProviders\WasenderProvider;

class CommunicationService
{
    protected RateLimitingService $rateLimitingService;

    protected TemplateValidationService $templateValidationService;

    public function __construct(RateLimitingService $rateLimitingService, TemplateValidationService $templateValidationService)
    {
        $this->rateLimitingService = $rateLimitingService;
        $this->templateValidationService = $templateValidationService;
    }

    /**
     * Send a message to a model.
     *
     * @param  mixed  $notifiable
     */
    public function sendToModel($notifiable, string $channel, string $templateCode, array $data = []): ?CommMessage
    {
        // Check if user has this channel enabled
        if (method_exists($notifiable, 'notificationPreferences') &&
            ! in_array($channel, $notifiable->notificationChannels)) {
            // Skip sending if channel is disabled for this user
            return null;
        }

        // Get the company ID from the model or current session
        $companyId = $this->resolveCompanyId($notifiable);

        // Load the template with language preference
        $template = $this->getTemplateWithLanguage($templateCode, $companyId);

        if (! $template) {
            throw new \Exception("Template {$templateCode} not found");
        }

        // Validate template variables before rendering
        $this->templateValidationService->validateBeforeSend($template, $data);

        // Render the template with data
        $renderedSubject = $this->renderTemplate($template->subject, $data);
        $renderedBody = $this->renderTemplate($template->body, $data);

        // Get the provider from the template's provider config, fall back to default provider
        $provider = $template->providerConfig ? $template->providerConfig->provider : $this->getDefaultProvider($channel, $companyId);

        // Skip rate limiting for database channel (external channels still need rate limiting even if no provider)
        if ($channel === 'database') {
            // Skip rate limiting for internal database notifications
            Log::debug('Skipping rate limiting for database channel');
        } elseif (! $provider) {
            // No provider configured for external channel - this should be logged as configuration issue
            $this->logProviderConfigurationIssue($channel, null);
        } else {
            // Check rate limiting before sending for external channels
            if (! $this->rateLimitingService->checkRateLimit($provider, $channel, $companyId)) {
                throw new \Exception("Rate limit exceeded for {$provider} on {$channel} channel");
            }
        }

        // Create the message record
        $message = CommMessage::create([
            'company_id' => $companyId,
            'notifiable_type' => get_class($notifiable),
            'notifiable_id' => $notifiable->id,
            'channel' => $channel,
            'provider' => $provider,
            'template_id' => $template->id,
            'to_name' => $this->getCommName($notifiable),
            'to_email' => $this->getCommEmail($notifiable),
            'to_phone' => $this->getCommPhone($notifiable),
            'subject' => $renderedSubject,
            'body' => $renderedBody,
            'status' => 'queued',
        ]);

        // Queue the message for sending
        $this->queueMessage($message);

        // Increment rate limit counter after successful queue (skip for database channel)
        if ($channel !== 'database' && $provider) {
            $this->rateLimitingService->incrementRateLimit($provider, $channel, $companyId);
        }

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
            $template = $this->getTemplateWithLanguage($templateCode, $companyId);

            if ($template) {
                // Validate template variables before rendering
                $this->templateValidationService->validateBeforeSend($template, $data);

                $renderedSubject = $this->renderTemplate($template->subject, $data);
                $renderedBody = $this->renderTemplate($template->body, $data);
                $provider = $template->providerConfig ? $template->providerConfig->provider : null;
            } else {
                $provider = $this->getDefaultProvider($channel, $companyId);
            }
        } else {
            $provider = $this->getDefaultProvider($channel, $companyId);
        }

        // Check rate limiting before sending
        if (! $this->rateLimitingService->checkRateLimit($provider, $channel, $companyId)) {
            throw new \Exception("Rate limit exceeded for {$provider} on {$channel} channel");
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

        // Increment rate limit counter after successful queue
        $this->rateLimitingService->incrementRateLimit($provider, $channel, $companyId);

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
        // Dispatch the message to the appropriate provider
        $this->dispatchToProvider($message);
    }

    /**
     * Dispatch message to the appropriate provider based on channel.
     */
    protected function dispatchToProvider(CommMessage $message): void
    {
        try {
            $provider = $this->getProviderForChannel($message->channel, $message->provider);

            if ($provider) {
                $provider->send($message);
            } else {
                // Fallback: mark as failed if no provider found
                $message->update(['status' => 'failed', 'error_message' => 'No provider available for channel']);
            }

        } catch (\Exception $e) {
            $message->update([
                'status' => 'failed',
                'error_message' => 'Provider error: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Get the provider instance for a given channel.
     */
    protected function getProviderForChannel(string $channel, ?string $providerName = null): ?ChannelProviderInterface
    {
        $providerMap = [
            'email' => $providerName === 'mailtrap' ? MailtrapEmailProvider::class : LaravelMailProvider::class,
            'sms' => $providerName === 'mnotify' ? MnotifySmsProvider::class : null,
            'whatsapp' => $this->resolveWhatsAppProvider($providerName),
            'database' => FilamentDatabaseProvider::class,
        ];

        $providerClass = $providerMap[$channel] ?? null;

        if ($providerClass && class_exists($providerClass)) {
            return app($providerClass);
        }

        // Fallback to first available provider if none specified
        return $this->getFallbackProvider($channel);
    }

    /**
     * Intelligent WhatsApp provider resolver with fallbacks.
     */
    protected function resolveWhatsAppProvider(?string $providerName): ?string
    {
        if ($providerName === 'wasender') {
            return WasenderProvider::class;
        }
        if ($providerName === 'twilio') {
            return TwilioWhatsAppProvider::class;
        }

        // Fallback: Check if any WhatsApp provider is configured and available
        $hasWasender = class_exists(WasenderProvider::class);
        $hasTwilio = class_exists(TwilioWhatsAppProvider::class);

        if ($hasWasender) {
            return WasenderProvider::class;
        }
        if ($hasTwilio) {
            return TwilioWhatsAppProvider::class;
        }

        return null; // No WhatsApp providers available
    }

    /**
     * Get fallback provider when none specified or configured.
     */
    protected function getFallbackProvider(string $channel): ?ChannelProviderInterface
    {
        $fallbacks = [
            'email' => LaravelMailProvider::class,
            'sms' => MnotifySmsProvider::class,
            'whatsapp' => $this->resolveWhatsAppProvider(null),
            'database' => FilamentDatabaseProvider::class,
        ];

        $providerClass = $fallbacks[$channel] ?? null;

        $provider = $providerClass && class_exists($providerClass) ? app($providerClass) : null;

        // Log warning if using fallback provider (indicates missing configuration)
        if ($provider && $channel !== 'database') {
            Log::warning("Using fallback provider for channel: {$channel}", [
                'channel' => $channel,
                'provider_class' => $providerClass,
                'recommendation' => 'Configure a default provider in CommProviderConfig',
            ]);
        }

        return $provider;
    }

    /**
     * Log provider configuration issues for admin awareness.
     */
    protected function logProviderConfigurationIssue(string $channel, ?string $providerName): void
    {
        // Check if any provider exists for this channel
        $hasProvider = CommProviderConfig::where('channel', $channel)->exists();

        if (! $hasProvider) {
            Log::error("No provider configuration found for channel: {$channel}", [
                'channel' => $channel,
                'provider_name' => $providerName,
                'action_required' => 'Create provider configuration in Filament admin',
            ]);
        } else {
            // Check if default provider exists
            $hasDefault = CommProviderConfig::where('channel', $channel)
                ->where('is_default', true)
                ->exists();

            if (! $hasDefault) {
                Log::warning("No default provider configured for channel: {$channel}", [
                    'channel' => $channel,
                    'provider_name' => $providerName,
                    'recommendation' => 'Mark a provider as default in CommProviderConfig',
                ]);
            }
        }
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
        $user = Auth::user();
        if ($user && method_exists($user, 'getAttribute')) {
            return $user->getAttribute('current_company_id') ?? null;
        }

        return null;
    }

    /**
     * Send notification to a model through all enabled channels.
     */
    public function sendToModelThroughEnabledChannels($notifiable, string $templateCode, array $data = []): array
    {
        $results = [];

        // Get enabled channels for this notifiable
        if (method_exists($notifiable, 'notificationPreferences')) {
            $enabledChannels = $notifiable->notificationChannels;
        } else {
            // If model doesn't have preferences, use appropriate channels based on model type
            $enabledChannels = ['email', 'sms', 'whatsapp'];

            // Only include database channel for models that support Notifiable trait
            // But don't add it if it's already in the default channels
            $modelClass = get_class($notifiable);
            if ((method_exists($modelClass, 'notify') ||
                (class_exists('Modules\\HR\\Models\\Employee') && $modelClass === 'Modules\\HR\\Models\\Employee' && method_exists($notifiable, 'user') && $notifiable->user()->exists())) &&
                ! in_array('database', $enabledChannels)) {
                $enabledChannels[] = 'database';
            }
        }

        // Send through each enabled channel
        foreach ($enabledChannels as $channel) {
            $result = $this->sendToModel($notifiable, $channel, $templateCode, $data);
            if ($result) {
                $results[$channel] = $result;
            }
        }

        return $results;
    }

    /**
     * Get template with language preference.
     */
    protected function getTemplateWithLanguage(string $templateCode, ?int $companyId, ?string $preferredLanguage = null): ?CommTemplate
    {
        $preferredLanguage = $preferredLanguage ?? $this->resolvePreferredLanguage();

        // First try to get template with exact language match
        $template = CommTemplate::where('code', $templateCode)
            ->where('language', $preferredLanguage)
            ->where(function ($query) use ($companyId) {
                $query->where('company_id', $companyId)
                    ->orWhereNull('company_id');
            })
            ->first();

        // If not found, try to get template with fallback language (en)
        if (! $template && $preferredLanguage !== 'en') {
            $template = CommTemplate::where('code', $templateCode)
                ->where('language', 'en')
                ->where(function ($query) use ($companyId) {
                    $query->where('company_id', $companyId)
                        ->orWhereNull('company_id');
                })
                ->first();
        }

        // If still not found, try to get any template without language preference
        if (! $template) {
            $template = CommTemplate::where('code', $templateCode)
                ->where(function ($query) use ($companyId) {
                    $query->where('company_id', $companyId)
                        ->orWhereNull('company_id');
                })
                ->first();
        }

        return $template;
    }

    /**
     * Resolve preferred language from user or system.
     */
    protected function resolvePreferredLanguage(): string
    {
        // Try to get language from authenticated user
        $user = Auth::user();
        if ($user && method_exists($user, 'getAttribute')) {
            $userLanguage = $user->getAttribute('language') ??
                          $user->getAttribute('preferred_language');

            if ($userLanguage) {
                return $userLanguage;
            }
        }

        // Fallback to system default language
        return config('app.locale', 'en');
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

    /**
     * Get communication name from a notifiable model.
     */
    protected function getCommName($notifiable): string
    {
        // If model has getCommName method, use it
        if (method_exists($notifiable, 'getCommName')) {
            return $notifiable->getCommName();
        }

        // If model has employee relationship, use employee's getCommName
        if (method_exists($notifiable, 'employee') && $notifiable->employee) {
            return $notifiable->employee->getCommName();
        }

        // Fallback to model class name
        return class_basename($notifiable);
    }

    /**
     * Get communication email from a notifiable model.
     */
    protected function getCommEmail($notifiable): ?string
    {
        // If model has getCommEmail method, use it
        if (method_exists($notifiable, 'getCommEmail')) {
            return $notifiable->getCommEmail();
        }

        // If model has employee relationship, use employee's getCommEmail
        if (method_exists($notifiable, 'employee') && $notifiable->employee) {
            return $notifiable->employee->getCommEmail();
        }

        return null;
    }

    /**
     * Get communication phone from a notifiable model.
     */
    protected function getCommPhone($notifiable): ?string
    {
        // If model has getCommPhone method, use it
        if (method_exists($notifiable, 'getCommPhone')) {
            return $notifiable->getCommPhone();
        }

        // If model has employee relationship, use employee's getCommPhone
        if (method_exists($notifiable, 'employee') && $notifiable->employee) {
            return $notifiable->employee->getCommPhone();
        }

        return null;
    }
}
