<?php

namespace Modules\Hostels\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Models\CommProviderConfig;
use Modules\Hostels\Models\HostelOccupant;
use Modules\Hostels\Models\HostelWhatsAppGroup;
use Modules\Hostels\Models\WhatsAppGroupMessage;

class WhatsAppGroupService
{
    /**
     * Create a new WhatsApp group using Wasender API
     *
     * @return bool
     */
    public function createGroup(HostelWhatsAppGroup $group)
    {
        try {
            Log::debug('Starting createGroup for group:', ['group_id' => $group->id, 'group_name' => $group->name]);

            // Get Wasender provider configuration
            $providerConfig = CommProviderConfig::where('channel', 'whatsapp')
                ->where('provider', 'wasender')
                ->where('is_active', true)
                ->first();

            if (! $providerConfig) {
                Log::debug('Wasender provider config not found');
                throw new \Exception('Wasender provider configuration not found');
            }

            Log::debug('Found provider config:', ['config' => $providerConfig->toArray()]);

            $config = $providerConfig->config;
            Log::debug('Provider config array:', ['config' => $config]);

            // Validate required configuration
            if (empty($config['base_url']) || empty($config['api_key']) || empty($config['device_id'])) {
                throw new \Exception('Wasender configuration is incomplete');
            }

            // Prepare API endpoint
            $baseUrl = rtrim($config['base_url'], '/');
            $url = $baseUrl.'/api/groups/create';

            // Prepare headers
            $headers = [
                'Authorization' => 'Bearer '.$config['api_key'],
                'Content-Type' => 'application/json',
            ];

            // Get participants' phone numbers
            $participants = $group->occupants()->pluck('phone')->filter()->toArray();
            Log::debug('Participants phone numbers:', ['participants' => $participants]);

            // Prepare payload
            $payload = [
                'device_id' => $config['device_id'],
                'name' => $group->name,
                'participants' => $participants,
            ];
            Log::debug('API Payload:', ['payload' => $payload]);

            // Send the request
            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->post($url, $payload);

            Log::debug('HTTP Response status:', ['status' => $response->status()]);
            Log::debug('HTTP Response body:', ['body' => $response->body()]);

            // Check if request was successful
            if ($response->successful()) {
                $responseData = $response->json();

                if (isset($responseData['success']) && $responseData['success']) {
                    // Update group with Wasender group ID
                    $group->update([
                        'group_id' => $responseData['data']['id'] ?? null,
                    ]);

                    return true;
                } else {
                    throw new \Exception('Wasender API returned an error: '.($responseData['message'] ?? 'Unknown error'));
                }
            } else {
                throw new \Exception('Wasender API HTTP error: '.$response->status().' - '.$response->body());
            }
        } catch (\Exception $e) {
            Log::error('WhatsAppGroupService createGroup error: '.$e->getMessage(), [
                'group_id' => $group->id,
                'exception' => $e,
            ]);

            return false;
        }
    }

    /**
     * Send a message to a WhatsApp group
     *
     * @return bool
     */
    public function sendMessage(HostelWhatsAppGroup $group, string $message, ?string $mediaUrl = null, ?string $messageType = null)
    {
        try {
            // Validate group has a Wasender group ID
            if (empty($group->group_id)) {
                throw new \Exception('Group does not have a valid Wasender group ID');
            }

            // Get Wasender provider configuration
            $providerConfig = CommProviderConfig::where('channel', 'whatsapp')
                ->where('provider', 'wasender')
                ->where('is_active', true)
                ->first();

            if (! $providerConfig) {
                throw new \Exception('Wasender provider configuration not found');
            }

            $config = $providerConfig->config;

            // Validate required configuration
            if (empty($config['base_url']) || empty($config['api_key']) || empty($config['device_id'])) {
                throw new \Exception('Wasender configuration is incomplete');
            }

            // Prepare API endpoint
            $baseUrl = rtrim($config['base_url'], '/');
            $url = $baseUrl.'/api/groups/send-message';

            // Prepare headers
            $headers = [
                'Authorization' => 'Bearer '.$config['api_key'],
                'Content-Type' => 'application/json',
            ];

            // Prepare payload
            $payload = [
                'device_id' => $config['device_id'],
                'group_id' => $group->group_id,
                'message' => $message,
            ];

            // Add media if provided
            if ($mediaUrl && $messageType) {
                switch ($messageType) {
                    case 'image':
                        $payload['imageUrl'] = $mediaUrl;
                        break;
                    case 'video':
                        $payload['videoUrl'] = $mediaUrl;
                        break;
                    case 'document':
                        $payload['documentUrl'] = $mediaUrl;
                        break;
                }
            }

            // Send the request
            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->post($url, $payload);

            // Check if request was successful
            if ($response->successful()) {
                $responseData = $response->json();

                if (isset($responseData['success']) && $responseData['success']) {
                    // Log the message (system-generated messages don't have a specific sender)
                    try {
                        WhatsAppGroupMessage::create([
                            'whatsapp_group_id' => $group->id,
                            'sender_hostel_occupant_id' => null, // System-generated message
                            'message_type' => $messageType ?? 'text',
                            'content' => $message,
                            'media_url' => $mediaUrl,
                            'sent_at' => now(),
                        ]);
                        Log::debug('WhatsAppGroupMessage created successfully');
                    } catch (\Exception $e) {
                        Log::error('Failed to create WhatsAppGroupMessage: '.$e->getMessage());
                        throw $e;
                    }

                    return true;
                } else {
                    throw new \Exception('Wasender API returned an error: '.($responseData['message'] ?? 'Unknown error'));
                }
            } else {
                throw new \Exception('Wasender API HTTP error: '.$response->status().' - '.$response->body());
            }
        } catch (\Exception $e) {
            Log::error('WhatsAppGroupService sendMessage error: '.$e->getMessage(), [
                'group_id' => $group->id,
                'exception' => $e,
            ]);

            return false;
        }
    }

    /**
     * Add participants to an existing WhatsApp group
     *
     * @return bool
     */
    public function addParticipants(HostelWhatsAppGroup $group, array $hostelOccupantIds)
    {
        try {
            // Validate group has a Wasender group ID
            if (empty($group->group_id)) {
                throw new \Exception('Group does not have a valid Wasender group ID');
            }

            // Get the occupants and their phone numbers
            $occupants = HostelOccupant::whereIn('id', $hostelOccupantIds)->get();
            $phoneNumbers = $occupants->pluck('phone')->filter()->toArray();

            if (empty($phoneNumbers)) {
                throw new \Exception('No valid phone numbers found for the selected occupants');
            }

            // Get Wasender provider configuration
            $providerConfig = CommProviderConfig::where('channel', 'whatsapp')
                ->where('provider', 'wasender')
                ->where('is_active', true)
                ->first();

            if (! $providerConfig) {
                throw new \Exception('Wasender provider configuration not found');
            }

            $config = $providerConfig->config;

            // Validate required configuration
            if (empty($config['base_url']) || empty($config['api_key']) || empty($config['device_id'])) {
                throw new \Exception('Wasender configuration is incomplete');
            }

            // Prepare API endpoint
            $baseUrl = rtrim($config['base_url'], '/');
            $url = $baseUrl.'/api/groups/add-participants';

            // Prepare headers
            $headers = [
                'Authorization' => 'Bearer '.$config['api_key'],
                'Content-Type' => 'application/json',
            ];

            // Prepare payload
            $payload = [
                'device_id' => $config['device_id'],
                'group_id' => $group->group_id,
                'participants' => $phoneNumbers,
            ];

            // Send the request
            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->post($url, $payload);

            // Check if request was successful
            if ($response->successful()) {
                $responseData = $response->json();

                if (isset($responseData['success']) && $responseData['success']) {
                    // Attach occupants to the group
                    $group->occupants()->attach($hostelOccupantIds);

                    return true;
                } else {
                    throw new \Exception('Wasender API returned an error: '.($responseData['message'] ?? 'Unknown error'));
                }
            } else {
                throw new \Exception('Wasender API HTTP error: '.$response->status().' - '.$response->body());
            }
        } catch (\Exception $e) {
            Log::error('WhatsAppGroupService addParticipants error: '.$e->getMessage(), [
                'group_id' => $group->id,
                'hostel_occupant_ids' => $hostelOccupantIds,
                'exception' => $e,
            ]);

            return false;
        }
    }
}
