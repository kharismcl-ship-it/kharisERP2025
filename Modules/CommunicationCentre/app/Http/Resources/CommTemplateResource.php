<?php

namespace Modules\CommunicationCentre\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommTemplateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'code' => $this->code,
            'channel' => $this->channel,
            'provider' => $this->provider,
            'name' => $this->name,
            'subject' => $this->subject,
            'body' => $this->body,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'provider_config_id' => $this->provider_config_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Relationships
            'company' => $this->whenLoaded('company', function () {
                return [
                    'id' => $this->company->id,
                    'name' => $this->company->name,
                ];
            }),

            'provider_config' => $this->whenLoaded('providerConfig', function () {
                return [
                    'id' => $this->providerConfig->id,
                    'name' => $this->providerConfig->name,
                    'provider' => $this->providerConfig->provider,
                ];
            }),

            'usage_count' => $this->whenCounted('messages', function () {
                return $this->messages_count;
            }),
        ];
    }
}
