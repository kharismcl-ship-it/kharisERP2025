<?php

namespace Modules\Hostels\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelOccupant;
use Modules\Hostels\Models\Incident;
use Modules\Hostels\Models\Room;

class IncidentSeeder extends Seeder
{
    public function run(): void
    {
        if (Incident::exists()) {
            return;
        }

        $hostels = Hostel::all();
        if ($hostels->isEmpty()) {
            return;
        }

        $rooms   = Room::limit(20)->get();
        $tenants = HostelOccupant::limit(20)->get();
        $users   = User::limit(5)->get();
        if ($users->isEmpty()) {
            return;
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
            'minor' => 40,
            'major'  => 35,
            'critical' => 20,
            'critical' => 5,
        ];

        $statusTypes = [
            'open'      => 45,
            'escalated' => 20,
            'resolved'  => 35,
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
            if ($hostelRooms->isEmpty()) {
                continue;
            }
            $hostelOccupants = $tenants->where('hostel_id', $hostel->id);

            $incidentsPerHostel = rand(3, 8);

            for ($i = 0; $i < $incidentsPerHostel; $i++) {
                $type = array_rand($incidentTypes);
                $specificIssue = $incidentTypes[$type][array_rand($incidentTypes[$type])];

                $severity = $this->getWeightedStatus($severityLevels);
                $status = $this->getWeightedStatus($statusTypes);

                $room = $hostelRooms->random();
                $tenant = $hostelOccupants->isNotEmpty() ? $hostelOccupants->random() : null;
                $reporter = $users->random();

                $actionTaken = $actionsTaken[$type][array_rand($actionsTaken[$type])];

                $incidentData = [
                    'hostel_id' => $hostel->id,
                    'hostel_occupant_id' => $tenant?->id,
                    'room_id' => $room->id,
                    'title' => $specificIssue,
                    'description' => $this->generateIncidentDescription($type, $specificIssue, $room->room_number, $severity),
                    'severity' => $severity,
                    'reported_by_user_id' => $reporter->id,
                    'action_taken' => $actionTaken,
                    'status' => $status,
                    'reported_at' => now()->subDays(rand(1, 180)),
                ];

                if ($status === 'resolved') {
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

        return 'minor';
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
