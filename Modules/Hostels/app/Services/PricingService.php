<?php

namespace Modules\Hostels\Services;

use Illuminate\Support\Carbon;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\PricingPolicy;

class PricingService
{
    public function calculateDynamicPrice(Hostel $hostel, Carbon $checkInDate, Carbon $checkOutDate, int $numberOfNights, float $basePrice): array
    {
        $applicablePolicies = $this->getApplicablePolicies($hostel, $checkInDate, $checkOutDate, $numberOfNights);

        $adjustments = [];
        $finalPrice = $basePrice;
        $totalAdjustment = 0;

        foreach ($applicablePolicies as $policy) {
            $adjustmentAmount = $policy->calculateAdjustment($basePrice);
            $adjustments[] = [
                'policy' => $policy->name,
                'type' => $policy->adjustment_type,
                'value' => $policy->adjustment_value,
                'amount' => $adjustmentAmount,
                'description' => $this->getAdjustmentDescription($policy),
            ];

            $finalPrice += $adjustmentAmount;
            $totalAdjustment += $adjustmentAmount;
        }

        return [
            'base_price' => $basePrice,
            'final_price' => max(0, $finalPrice), // Ensure price doesn't go negative
            'total_adjustment' => $totalAdjustment,
            'adjustments' => $adjustments,
            'applicable_policies' => $applicablePolicies->count(),
        ];
    }

    public function getApplicablePolicies(Hostel $hostel, Carbon $checkInDate, Carbon $checkOutDate, int $numberOfNights)
    {
        return $hostel->pricingPolicies()
            ->active()
            ->validForDate($checkInDate)
            ->get()
            ->filter(function ($policy) use ($checkInDate, $checkOutDate, $numberOfNights) {
                return $policy->appliesToBooking($checkInDate, $checkOutDate, $numberOfNights);
            });
    }

    protected function getAdjustmentDescription(PricingPolicy $policy): string
    {
        $type = $policy->adjustment_type === 'percentage' ? '%' : 'GHS';
        $sign = $policy->adjustment_value >= 0 ? '+' : '-';
        $value = abs($policy->adjustment_value);

        return sprintf(
            '%s %s%s %s',
            $policy->policy_type,
            $sign,
            $value.$type,
            $this->getPolicyConditionDescription($policy)
        );
    }

    protected function getPolicyConditionDescription(PricingPolicy $policy): string
    {
        $conditions = $policy->conditions ?? [];
        $descriptions = [];

        foreach ($conditions as $condition) {
            $descriptions[] = match ($condition['type'] ?? '') {
                'min_nights' => "min {$condition['value']} nights",
                'max_nights' => "max {$condition['value']} nights",
                'day_of_week' => 'specific days: '.implode(', ', array_map(fn ($d) => Carbon::getDays()[$d], $condition['values'])),
                'month' => 'months: '.implode(', ', array_map(fn ($m) => Carbon::create()->month($m)->monthName, $condition['values'])),
                'advance_booking' => "within {$condition['value']} days",
                default => '',
            };
        }

        return $descriptions ? ('('.implode('; ', array_filter($descriptions)).')') : '';
    }

    public function getSeasonalMultiplier(Hostel $hostel, Carbon $date): float
    {
        $policies = $hostel->pricingPolicies()
            ->active()
            ->where('policy_type', 'seasonal')
            ->validForDate($date)
            ->get();

        $multiplier = 1.0;

        foreach ($policies as $policy) {
            if ($policy->appliesToBooking($date, $date->copy()->addDay(), 1)) {
                $adjustment = $policy->calculateAdjustment(1); // Calculate adjustment on base 1
                $multiplier += $adjustment;
            }
        }

        return max(0.1, $multiplier); // Ensure minimum 10% of base price
    }

    public function getDemandSurcharge(Hostel $hostel, Carbon $checkInDate): float
    {
        $policies = $hostel->pricingPolicies()
            ->active()
            ->where('policy_type', 'demand')
            ->validForDate($checkInDate)
            ->get();

        $surcharge = 0.0;

        foreach ($policies as $policy) {
            if ($policy->appliesToBooking($checkInDate, $checkInDate->copy()->addDay(), 1)) {
                $surcharge += $policy->calculateAdjustment(0); // Get fixed surcharge amount
            }
        }

        return max(0, $surcharge);
    }
}
