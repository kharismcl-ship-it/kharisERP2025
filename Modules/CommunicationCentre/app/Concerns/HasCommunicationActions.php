<?php

namespace Modules\CommunicationCentre\Concerns;

use Filament\Actions\Action;
use Modules\CommunicationCentre\Filament\Actions\SendEmailAction;
use Modules\CommunicationCentre\Filament\Actions\SendInAppNotificationAction;
use Modules\CommunicationCentre\Filament\Actions\SendSmsAction;
use Modules\CommunicationCentre\Filament\Actions\SendWhatsAppAction;

/**
 * Trait for Filament resource pages and standalone pages.
 *
 * Provides helper methods that return ready-to-use header Actions for
 * sending email, SMS, WhatsApp, or in-app notifications from any page.
 *
 * Usage — in a resource page (e.g. ViewRecord or EditRecord):
 *
 *   use Modules\CommunicationCentre\Concerns\HasCommunicationActions;
 *
 *   class ViewEmployee extends ViewRecord
 *   {
 *       use HasCommunicationActions;
 *
 *       protected function getHeaderActions(): array
 *       {
 *           return [
 *               EditAction::make(),
 *               ...$this->communicationActions(),
 *           ];
 *       }
 *   }
 *
 * You can also call individual channel actions:
 *   $this->emailAction()
 *   $this->smsAction()
 *   $this->whatsAppAction()
 *   $this->inAppAction()
 */
trait HasCommunicationActions
{
    /**
     * Returns all four communication actions pre-filled from the current record.
     *
     * Override getCommEmail(), getCommPhone(), getCommUserId() on this class
     * to customise how recipient data is resolved.
     *
     * @return Action[]
     */
    protected function communicationActions(): array
    {
        return [
            $this->emailAction(),
            $this->smsAction(),
            $this->whatsAppAction(),
            $this->inAppAction(),
        ];
    }

    /**
     * Email header action, pre-filled from the record.
     */
    protected function emailAction(): Action
    {
        return SendEmailAction::make(
            toEmail: fn () => $this->resolveCommEmail(),
            toName:  fn () => $this->resolveCommName(),
        );
    }

    /**
     * SMS header action, pre-filled from the record.
     */
    protected function smsAction(): Action
    {
        return SendSmsAction::make(
            toPhone: fn () => $this->resolveCommPhone(),
            toName:  fn () => $this->resolveCommName(),
        );
    }

    /**
     * WhatsApp header action, pre-filled from the record.
     */
    protected function whatsAppAction(): Action
    {
        return SendWhatsAppAction::make(
            toPhone: fn () => $this->resolveCommPhone(),
            toName:  fn () => $this->resolveCommName(),
        );
    }

    /**
     * In-app notification header action, resolved to the record's user_id.
     */
    protected function inAppAction(): Action
    {
        return SendInAppNotificationAction::make(
            toUserId: fn () => $this->resolveCommUserId(),
        );
    }

    // -------------------------------------------------------------------------
    // Default resolvers — override in your page class for custom behaviour.
    // -------------------------------------------------------------------------

    protected function resolveCommEmail(): string
    {
        $record = $this->record ?? null;
        if (! $record) {
            return '';
        }
        return (string) ($record->email
            ?? $record->to_email
            ?? $record->contact_email
            ?? '');
    }

    protected function resolveCommPhone(): string
    {
        $record = $this->record ?? null;
        if (! $record) {
            return '';
        }
        return (string) ($record->phone
            ?? $record->phone_number
            ?? $record->mobile
            ?? $record->contact_phone
            ?? '');
    }

    protected function resolveCommName(): string
    {
        $record = $this->record ?? null;
        if (! $record) {
            return '';
        }
        return (string) ($record->name
            ?? $record->full_name
            ?? $record->display_name
            ?? '');
    }

    protected function resolveCommUserId(): ?int
    {
        $record = $this->record ?? null;
        if (! $record) {
            return null;
        }
        return $record->user_id
            ?? $record->id ?? null;
    }
}
