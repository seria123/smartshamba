<?php

namespace App\Services;

use App\Models\CropCycle;
use Carbon\Carbon;

class CropCycleAnalysisService
{
    /**
     * Analyze a crop cycle and return current stage and recommended activities.
     */
    public function analyze(CropCycle $cropCycle): array
    {
        $crop = $cropCycle->crop;
        if (! $crop) {
            return [
                'current_stage' => 'unknown',
                'recommended_activities' => [],
                'days_since_planting' => 0,
                'progress_percentage' => 0,
            ];
        }

        $startDate = Carbon::parse($cropCycle->start_date);
        $now = Carbon::now();
        $daysSincePlanting = $startDate->diffInDays($now, false);

        // Get growth stages from crop
        $growthStages = $crop->growth_stages ? $crop->growth_stages : [];

        // If no growth stages defined, use default based on days_to_maturity
        if (empty($growthStages) && $crop->days_to_maturity) {
            $growthStages = $this->getDefaultGrowthStages($crop->days_to_maturity);
        }

        // Determine current stage
        $currentStage = $this->determineCurrentStage($daysSincePlanting, $growthStages);

        // Get recommended activities for current stage
        $recommendedActivities = $this->getRecommendedActivities($crop->name, $currentStage, $daysSincePlanting);

        // Calculate progress percentage
        $progressPercentage = $this->calculateProgressPercentage($daysSincePlanting, $growthStages, $crop->days_to_maturity);

        return [
            'current_stage' => $currentStage,
            'recommended_activities' => $recommendedActivities,
            'days_since_planting' => $daysSincePlanting,
            'progress_percentage' => $progressPercentage,
            'expected_harvest_date' => $cropCycle->expected_harvest_date,
        ];
    }

    /**
     * Determine current growth stage based on days since planting.
     *
     * @param  int  $daysSincePlanting
     */
    private function determineCurrentStage(float $daysSincePlanting, array $growthStages): string
    {
        if (empty($growthStages)) {
            return 'unknown';
        }

        $elapsedDays = 0;
        foreach ($growthStages as $stage) {
            $stageDuration = $stage['duration'] ?? 0;
            $stageUnit = $stage['unit'] ?? 'days';

            // Convert to days if needed
            if ($stageUnit === 'weeks') {
                $stageDuration = $stageDuration * 7;
            } elseif ($stageUnit === 'months') {
                $stageDuration = $stageDuration * 30; // Approximate
            }

            if ($daysSincePlanting < $elapsedDays + $stageDuration) {
               return $stage;
            }

            $elapsedDays += $stageDuration;
        }

        // If we've gone through all stages, return the last stage or 'mature'
        return end($growthStages)['name'] ?? 'mature';
    }

    /**
     * Get recommended activities for a crop at a specific stage.
     *
     * @param  int  $daysSincePlanting
     */
    private function getRecommendedActivities(string $cropName, string $stage, float $daysSincePlanting): array
    {
        // This is a simplified version. In a real app, you might have a database of activities
        // or use a more sophisticated rule engine.
        $activities = [];

        // Normalize inputs
        $cropNameLower = strtolower($cropName);
        $stageLower = strtolower($stage);

        // Maize-specific recommendations (example from user's message)
        if (str_contains($cropNameLower, 'maize') || str_contains($cropNameLower, 'corn')) {
            if (str_contains($stageLower, 'flowering') || str_contains($stageLower, 'tasseling')) {
                $activities[] = 'Apply fertilizer now';
                $activities[] = 'Monitor for pests like fall armyworm';
            } elseif (str_contains($stageLower, 'vegetative')) {
                $activities[] = 'Ensure adequate water supply';
                $activities[] = 'Apply nitrogen fertilizer';
            } elseif (str_contains($stageLower, 'harvest')) {
                $activities[] = 'Prepare for harvest';
                $activities[] = 'Check moisture content';
            }
        }

        // Generic recommendations based on stage
        if (str_contains($stageLower, 'germination') || str_contains($stageLower, 'emergence')) {
            $activities[] = 'Keep soil moist';
            $activities[] = 'Protect from birds';
        } elseif (str_contains($stageLower, 'vegetative') || str_contains($stageLower, 'growth')) {
            $activities[] = 'Weed control';
            $activities[] = 'Monitor nutrient levels';
        } elseif (str_contains($stageLower, 'flowering') || str_contains($stageLower, 'reproductive')) {
            $activities[] = 'Avoid water stress';
            $activities[] = 'Consider pollination assistance if needed';
        } elseif (str_contains($stageLower, 'maturity') || str_contains($stageLower, 'ripening') || str_contains($stageLower, 'harvest')) {
            $activities[] = 'Reduce watering';
            $activities[] = 'Prepare harvesting equipment';
        }

        // If no specific activities found, provide some general ones
        if (empty($activities)) {
            $activities[] = 'Monitor crop health';
            $activities[] = 'Check soil moisture';
        }

        return $activities;
    }

    /**
     * Calculate progress percentage through the crop cycle.
     *
     * @param  int  $daysSincePlanting
     */
    private function calculateProgressPercentage(float $daysSincePlanting, array $growthStages, ?int $daysToMaturity): float
    {
        if ($daysToMaturity && $daysToMaturity > 0) {
            return min(100, ($daysSincePlanting / $daysToMaturity) * 100);
        }

        // If we have growth stages with durations, calculate total duration
        if (! empty($growthStages)) {
            $totalDuration = 0;
            foreach ($growthStages as $stage) {
                $duration = $stage['duration'] ?? 0;
                $unit = $stage['unit'] ?? 'days';

                if ($unit === 'weeks') {
                    $duration = $duration * 7;
                } elseif ($unit === 'months') {
                    $duration = $duration * 30;
                }

                $totalDuration += $duration;
            }

            if ($totalDuration > 0) {
                return min(100, ($daysSincePlanting / $totalDuration) * 100);
            }
        }

        // Fallback: assume 120 days as default growing season
        return min(100, ($daysSincePlanting / 120) * 100);
    }

    /**
     * Get default growth stages based on days to maturity.
     */
    private function getDefaultGrowthStages(int $daysToMaturity): array
    {
        // Default stages as percentages of total growing period
        $stages = [
            ['name' => 'Germination', 'percentage' => 0.1],
            ['name' => 'Vegetative', 'percentage' => 0.4],
            ['name' => 'Flowering', 'percentage' => 0.2],
            ['name' => 'Maturity', 'percentage' => 0.3],
        ];

        $growthStages = [];
        $elapsedDays = 0;

        foreach ($stages as $stage) {
            $stageDays = round($daysToMaturity * $stage['percentage']);
            $growthStages[] = [
                'name' => $stage['name'],
                'duration' => $stageDays,
                'unit' => 'days',
            ];
            $elapsedDays += $stageDays;
        }

        // Adjust last stage to use remaining days
        if (! empty($growthStages)) {
            $growthStages[count($growthStages) - 1]['duration'] += $daysToMaturity - $elapsedDays;
        }

        return $growthStages;
    }
}
