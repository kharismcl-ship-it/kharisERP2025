<?php

namespace Modules\Hostels\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\Incident;
use Modules\Hostels\Models\Room;
use Modules\Hostels\Models\Tenant;

class IncidentSeeder extends Seeder
{
    public function run(): void
    {
        $hostels = Hostel::all();

        if ($hostels->isEmpty()) {
            $this->call(HostelSeeder::class);
            $hostels = Hostel::all();
        }

        $rooms = Room::all();
        if ($rooms->isEmpty()) {
            $this->call(RoomSeeder::class);
            $rooms = Room::all();
        }

        $tenants = Tenant::all();
        if ($tenants->isEmpty()) {
            $this->call(TenantSeeder::class);
            $tenants = Tenant::all();
        }

        $users = User::whereIn('role', ['admin', 'manager', 'supervisor', 'security'])->get();
        if ($users->isEmpty()) {
            $users = User::factory()->count(4)->create([
                'role' => 'security',
            ]);
        }

        $incidentTypes = [
            'Noise Complaint' => [
                'Loud music after hours',
                'Excessive noise from room',
                'Party disturbance',
                'Shouting and loud conversations',
            ],
            'Security Issue' => [
                'Unauthorized entry',
                'Suspicious activity',
                'Property theft',
                'Security breach',
            ],
            'Health & Safety' => [
                'Illness outbreak',
                'Safety hazard',
                'Food poisoning',
                'Emergency medical situation',
            ],
            'Property Damage' => [
                'Vandalism',
                'Intentional damage',
                'Broken furniture',
                'Damaged common areas',
            ],
            'Behavioral Issue' => [
                'Disruptive behavior',
                'Harassment',
                'Fighting',
                'Substance abuse',
            ],
            'Facility Issue' => [
                'Power outage',
                'Water leak emergency',
                'Fire alarm activation',
                'Elevator malfunction',
            ],
            'Guest Policy Violation' => [
                'Unauthorized guests',
                'Overnight visitor violation',
                'Pet policy violation',
                'Smoking in room',
            ],
        ];

        $severityLevels = [
            'low' => 40,
            'medium' => 35,
            'high' => 20,
            'critical' => 5,
        ];

        $statusTypes = [
            'reported' => 25,
            'under_investigation' => 20,
            'in_progress' => 15,
            'resolved' => 35,
            'closed' => 5,
        ];

        $actionsTaken = [
            'Noise Complaint' => [
                'Warning issued to tenant',
                'Noise reduction measures implemented',
                'Room visit and verbal warning',
                'Written notice served',
            ],
            'Security Issue' => [
                'Security patrol increased',
                'CCTV footage reviewed',
                'Police notified and report filed',
                'Additional security measures implemented',
            ],
            'Health & Safety' => [
                'First aid administered',
                'Emergency services called',
                'Quarantine measures implemented',
                'Health department notified',
            ],
            'Property Damage' => [
                'Damage assessment conducted',
                'Repair team dispatched',
                'Insurance claim initiated',
                'Replacement costs calculated',
            ],
            'Behavioral Issue' => [
                'Behavioral warning issued',
                'Counseling services offered',
                'Disciplinary action taken',
                'Tenant meeting scheduled',
            ],
            'Facility Issue' => [
                'Maintenance team dispatched',
                'Emergency repairs initiated',
                'Alternative arrangements made',
                'Service providers contacted',
            ],
            'Guest Policy Violation' => [
                'Policy reminder issued',
                'Guest removed from premises',
                'Fine imposed',
                'Tenant agreement reviewed',
            ],
        ];

        foreach ($hostels as $hostel) {
            $hostelRooms = $rooms->where('hostel_id', $hostel->id);
            $hostelTenants = $tenants->whereIn('bed_id', $hostelRooms->pluck('id'));

            $incidentsPerHostel = rand(8, 25);

            for ($i = 0; $i < $incidentsPerHostel; $i++) {
                $type = array_rand($incidentTypes);
                $specificIssue = $incidentTypes[$type][array_rand($incidentTypes[$type])];

                $severity = $this->getWeightedStatus($severityLevels);
                $status = $this->getWeightedStatus($statusTypes);

                $room = $hostelRooms->random();
                $tenant = $hostelTenants->where('bed_id', $room->id)->first();
                $reporter = $users->random();

                $actionTaken = $actionsTaken[$type][array_rand($actionsTaken[$type])];

                $incidentData = [
                    'hostel_id' => $hostel->id,
                    'tenant_id' => $tenant?->id,
                    'room_id' => $room->id,
                    'title' => $specificIssue,
                    'description' => $this->generateIncidentDescription($type, $specificIssue, $room->room_number, $severity),
                    'severity' => $severity,
                    'reported_by_user_id' => $reporter->id,
                    'action_taken' => $actionTaken,
                    'status' => $status,
                    'reported_at' => now()->subDays(rand(1, 180)),
                ];

                if (in_array($status, ['resolved', 'closed'])) {
                    $incidentData['resolved_at'] = now()->subDays(rand(0, 30));
                }

                Incident::create($incidentData);
            }
        }
    }

    private function getWeightedStatus(array $weights): string
    {
        $total = array_sum($weights);
        $random = rand(1, $total);
        $current = 0;

        foreach ($weights as $status => $weight) {
            $current += $weight;
            if ($random <= $current) {
                return $status;
            }
        }

        return 'medium';
    }

    private function generateIncidentDescription(string $type, string $specificIssue, string $roomNumber, string $severity): string
    {
        $descriptions = [
            'Noise Complaint' => [
                "{$specificIssue} reported from Room {$roomNumber}. Noise level exceeds acceptable limits during quiet hours.",
                "Complaint received regarding {$specificIssue} in Room {$roomNumber}. Disturbance affecting other tenants.",
                "{$specificIssue} issue in Room {$roomNumber}. Requires immediate attention to maintain peaceful environment.",
            ],
            'Security Issue' => [
                "{$specificIssue} reported at Room {$roomNumber}. Security breach detected requiring investigation.",
                "Security incident: {$specificIssue}. Room {$roomNumber} involved. Immediate action required.",
                "{$specificIssue} occurrence in Room {$roomNumber}. Security protocols activated for investigation.",
            ],
            'Health & Safety' => [
                "{$specificIssue} reported in Room {$roomNumber}. Health and safety concern requiring urgent attention.",
                "Health emergency: {$specificIssue} at Room {$roomNumber}. Medical assistance and safety measures needed.",
                "{$specificIssue} situation in Room {$roomNumber}. Potential health risk requiring immediate response.",
            ],
            'Property Damage' => [
                "{$specificIssue} discovered in Room {$roomNumber}. Property damage assessment required.",
                "Damage report: {$specificIssue} in Room {$roomNumber}. Repair and cost evaluation needed.",
                "{$specificIssue} incident at Room {$roomNumber}. Property vandalism or damage documented.",
            ],
            'Behavioral Issue' => [
                "{$specificIssue} reported from Room {$roomNumber}. Behavioral concern requiring intervention.",
                "Behavioral incident: {$specificIssue} involving Room {$roomNumber} tenant. Disciplinary action may be needed.",
                "{$specificIssue} issue in Room {$roomNumber}. Tenant behavior violates community guidelines.",
            ],
            'Facility Issue' => [
                "{$specificIssue} affecting Room {$roomNumber}. Facility emergency requiring immediate repair.",
                "Facility malfunction: {$specificIssue} impacting Room {$roomNumber}. Urgent maintenance required.",
                "{$specificIssue} reported for Room {$roomNumber}. Building systems failure affecting tenant comfort.",
            ],
            'Guest Policy Violation' => [
                "{$specificIssue} reported in Room {$roomNumber}. Guest policy violation documented.",
                "Policy breach: {$specificIssue} at Room {$roomNumber}. Unauthorized activity requires addressing.",
                "{$specificIssue} violation in Room {$roomNumber}. Tenant non-compliance with guest regulations.",
            ],
        ];

        $description = $descriptions[$type][array_rand($descriptions[$type])];

        return "Severity: {$severity}. ".$description;
    }
}
