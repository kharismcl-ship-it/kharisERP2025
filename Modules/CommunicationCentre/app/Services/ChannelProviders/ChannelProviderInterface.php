<?php

namespace Modules\CommunicationCentre\Services\ChannelProviders;

use Modules\CommunicationCentre\Models\CommMessage;

interface ChannelProviderInterface
{
    /**
     * Send a message through the provider.
     */
    public function send(CommMessage $message): void;
}
