<?php

namespace App\Services;

use App\Models\Crop;
use App\Models\Field;
use App\Models\SensorReading;

class FertilizerCalculator
{
    protected array $fertilizerTypes = [
        'nitrogen' => [
            'name' => 'Nitrogen (N)',
            'description' => 'Promotes leafy growth and green color',
            'symbols' => ['urea', 'ammonium nitrate', 'blood meal'],
        ],
        'phosphorus' => [
            'name' => 'Phosphorus (P)',
            'description' => 'Promotes root development and flowering',
            'symbols' => ['superphosphate', 'bone meal', 'rock phosphate'],
        ],
        'potassium' => [
            'name' => 'Potassium (K)',
            'description' => 'Promotes overall plant health and disease resistance',
            'symbols' => ['potash', 'sulphate of potash', 'wood ash'],
        ],
        'compound' => [
            'name' => 'Compound NPK',
            'description' => 'Balanced fertilizer for general use',
            'symbols' => ['10-10-10', '20-20-20', '14-14-14'],
        ],
        'organic' => [
            'name' => 'Organic Fertilizer',
            'description' => 'Natural organic matter',
            'symbols' => ['compost', 'manure', 'bone meal'],
        ],
    ];

    protected array $cropNutrientRequirements = [
        'maize' => ['n' => 120, 'p' => 60, 'k' => 40, 'unit' => 'kg/ha'],
        'wheat' => ['n' => 100, 'p' => 50, 'k' => 30, 'unit' => 'kg/ha'],
        'rice' => ['n' => 100, 'p' => 50, 'k' => 50, 'unit' => 'kg/ha'],
        'tomato' => ['n' => 150, 'p' => 80, 'k' => 120, 'unit' => 'kg/ha'],
        'potato' => ['n' => 150, 'p' => 100, 'k' => 150, 'unit' => 'kg/ha'],
        'onion' => ['n' => 80, 'p' => 40, 'k' => 60, 'unit' => 'kg/ha'],
        'carrot' => ['n' => 60, 'p' => 40, 'k' => 80, 'unit' => 'kg/ha'],
        'cabbage' => ['n' => 100, 'p' => 50, 'k' => 80, 'unit' => 'kg/ha'],
        'spinach' => ['n' => 80, 'p' => 40, 'k' => 60, 'unit' => 'kg/ha'],
        'beans' => ['n' => 30, 'p' => 50, 'k' => 40, 'unit' => 'kg/ha'],
        'peas' => ['n' => 0, 'p' => 40, 'k' => 40, 'unit' => 'kg/ha'],
        'lettuce' => ['n' => 80, 'p' => 40, 'k' => 60, 'unit' => 'kg/ha'],
        'pepper' => ['n' => 100, 'p' => 60, 'k' => 80, 'unit' => 'kg/ha'],
        'cucumber' => ['n' => 80, 'p' => 40, 'k' => 60, 'unit' => 'kg/ha'],
        'melon' => ['n' => 100, 'p' => 50, 'k' => 80, 'unit' => 'kg/ha'],
    ];

    public function calculate(Crop $crop, ?Field $field = null, ?SensorReading $soilReading = null): array
    {
        $cropName = strtolower($crop->name);
        $requirements = $this->cropNutrientRequirements[$cropName] ?? $this->getDefaultRequirements();

        $fieldSize = $field?->size_hectares ?? 1;

        $baseRequirements = [
            'nitrogen' => [
                'amount' => $requirements['n'] * $fieldSize,
                'unit' => 'kg',
            ],
            'phosphorus' => [
                'amount' => $requirements['p'] * $fieldSize,
                'unit' => 'kg',
            ],
            'potassium' => [
                'amount' => $requirements['k'] * $fieldSize,
                'unit' => 'kg',
            ],
        ];

        $adjustments = $this->calculateAdjustments($soilReading);

        $adjustedRequirements = $this->applyAdjustments($baseRequirements, $adjustments);

        return [
            'crop' => $crop->name,
            'field_size' => $fieldSize,
            'field_size_unit' => 'hectares',
            'base_requirements' => $baseRequirements,
            'adjustments' => $adjustments,
            'adjusted_requirements' => $adjustedRequirements,
            'total_n' => round($adjustedRequirements['nitrogen']['amount'], 2),
            'total_p' => round($adjustedRequirements['phosphorus']['amount'], 2),
            'total_k' => round($adjustedRequirements['potassium']['amount'], 2),
            'application_splits' => $this->calculateApplicationSplits($adjustedRequirements, $crop),
            'recommendations' => $this->generateRecommendations($adjustedRequirements),
        ];
    }

    protected function getDefaultRequirements(): array
    {
        return ['n' => 100, 'p' => 50, 'k' => 50, 'unit' => 'kg/ha'];
    }

    protected function calculateAdjustments(?SensorReading $soilReading): array
    {
        $adjustments = [
            'soil_test_done' => false,
            'n_adjustment' => 0,
            'p_adjustment' => 0,
            'k_adjustment' => 0,
            'reason' => 'Default calculation based on crop requirements',
        ];

        if (! $soilReading) {
            return $adjustments;
        }

        $adjustments['soil_test_done'] = true;
        $adjustments['reason'] = 'Adjusted based on soil test results';

        if (isset($soilReading->nitrogen_level)) {
            $n = $soilReading->nitrogen_level;
            if ($n < 20) {
                $adjustments['n_adjustment'] = 1.2;
            } elseif ($n > 60) {
                $adjustments['n_adjustment'] = 0.8;
            }
        }

        if (isset($soilReading->phosphorus_level)) {
            $p = $soilReading->phosphorus_level;
            if ($p < 15) {
                $adjustments['p_adjustment'] = 1.3;
            } elseif ($p > 40) {
                $adjustments['p_adjustment'] = 0.7;
            }
        }

        if (isset($soilReading->potassium_level)) {
            $k = $soilReading->potassium_level;
            if ($k < 120) {
                $adjustments['k_adjustment'] = 1.2;
            } elseif ($k > 300) {
                $adjustments['k_adjustment'] = 0.8;
            }
        }

        return $adjustments;
    }

    protected function applyAdjustments(array $requirements, array $adjustments): array
    {
        $nFactor = $adjustments['n_adjustment'] ?: 1;
        $pFactor = $adjustments['p_adjustment'] ?: 1;
        $kFactor = $adjustments['k_adjustment'] ?: 1;

        return [
            'nitrogen' => [
                'amount' => $requirements['nitrogen']['amount'] * $nFactor,
                'unit' => 'kg',
            ],
            'phosphorus' => [
                'amount' => $requirements['phosphorus']['amount'] * $pFactor,
                'unit' => 'kg',
            ],
            'potassium' => [
                'amount' => $requirements['potassium']['amount'] * $kFactor,
                'unit' => 'kg',
            ],
        ];
    }

    protected function calculateApplicationSplits(array $requirements, Crop $crop): array
    {
        return [
            [
                'stage' => 'Basal/Planting',
                'percentage' => 50,
                'nitrogen' => round($requirements['nitrogen']['amount'] * 0.5, 2),
                'phosphorus' => round($requirements['phosphorus']['amount'] * 0.5, 2),
                'potassium' => round($requirements['potassium']['amount'] * 0.5, 2),
                'timing' => 'At planting time',
            ],
            [
                'stage' => 'Top-dressing 1',
                'percentage' => 30,
                'nitrogen' => round($requirements['nitrogen']['amount'] * 0.3, 2),
                'phosphorus' => 0,
                'potassium' => round($requirements['potassium']['amount'] * 0.3, 2),
                'timing' => '4-6 weeks after planting',
            ],
            [
                'stage' => 'Top-dressing 2',
                'percentage' => 20,
                'nitrogen' => round($requirements['nitrogen']['amount'] * 0.2, 2),
                'phosphorus' => round($requirements['phosphorus']['amount'] * 0.5, 2),
                'potassium' => round($requirements['potassium']['amount'] * 0.2, 2),
                'timing' => '8-10 weeks after planting',
            ],
        ];
    }

    protected function generateRecommendations(array $requirements): array
    {
        $recommendations = [];

        if ($requirements['nitrogen']['amount'] > 100) {
            $recommendations[] = 'Apply nitrogen in split doses to minimize leaching losses';
        }
        if ($requirements['phosphorus']['amount'] > 80) {
            $recommendations[] = 'Apply phosphorus near the root zone for better uptake';
        }
        if ($requirements['potassium']['amount'] > 100) {
            $recommendations[] = 'Split potassium application for long-season crops';
        }

        $recommendations[] = 'Conduct soil test before each planting season';
        $recommendations[] = 'Adjust based on previous crop yield and residue';

        return $recommendations;
    }

    public function getFertilizerTypes(): array
    {
        return $this->fertilizerTypes;
    }

    public function getCropRequirements(string $cropName): ?array
    {
        return $this->cropNutrientRequirements[strtolower($cropName)] ?? null;
    }

    public function getAllCropRequirements(): array
    {
        return $this->cropNutrientRequirements;
    }
}
