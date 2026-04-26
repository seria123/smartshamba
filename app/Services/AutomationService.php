<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\AutomationRule;
use App\Models\Field;
use App\Models\Sensor;
use App\Models\SensorReading;
use Illuminate\Support\Facades\Log;

class AutomationService
{
    /**
     * Process all active fields and evaluate automation rules
     */
    public function processAutomation(): array
    {
        $results = [
            'processed' => 0,
            'triggered' => 0,
            'errors' => [],
        ];

        $fields = Field::with(['sensors', 'automationRules'])->get();

        foreach ($fields as $field) {
            try {
                $fieldResults = $this->processField($field);
                $results['processed']++;
                $results['triggered'] += $fieldResults['triggered'];
            } catch (\Exception $e) {
                $results['errors'][] = "Field {$field->id}: {$e->getMessage()}";
                Log::error("Automation error for field {$field->id}: {$e->getMessage()}");
            }
        }

        return $results;
    }

    /**
     * Process automation rules for a specific field
     */
    public function processField(Field $field): array
    {
        $results = [
            'triggered' => 0,
            'actions' => [],
        ];

        // Get the latest sensor readings for this field grouped by sensor type
        $latestReadings = $this->getLatestReadingsByType($field);

        // Get active automation rules for this field
        $rules = $field->automationRules()->where('is_active', true)->get();

        foreach ($rules as $rule) {
            // Check if rule is in cooldown period
            if (! $rule->canTrigger()) {
                continue;
            }

            // Get the relevant sensor reading for this rule's sensor type
            $reading = $latestReadings[$rule->sensor_type] ?? null;

            if (! $reading) {
                continue;
            }

            // Get the value based on sensor type
            $value = $this->getReadingValue($reading, $rule->sensor_type);

            if ($value === null) {
                continue;
            }

            // Evaluate the rule
            if ($rule->evaluate($value)) {
                // Trigger the action
                $actionResult = $this->triggerAction($rule, $field, $value);
                $results['triggered']++;
                $results['actions'][] = [
                    'rule' => $rule->name,
                    'action' => $rule->action,
                    'value' => $value,
                    'threshold' => $rule->threshold,
                    'result' => $actionResult,
                ];

                // Mark rule as triggered
                $rule->markTriggered();
            } else {
                // Intelligent solution searching: if no rule triggered, search for optimal solutions
                $solution = $this->findOptimalSolution($field, $rule, $value);
                if ($solution) {
                    $results['actions'][] = [
                        'rule' => $rule->name,
                        'action' => 'suggested_solution',
                        'value' => $value,
                        'threshold' => $rule->threshold,
                        'result' => $solution,
                    ];
                }
            }
        }

        return $results;
    }

    /**
     * Find optimal solutions automatically based on sensor data
     */
    protected function findOptimalSolution(Field $field, AutomationRule $rule, float $value): ?string
    {
        $sensorType = $rule->sensor_type;
        $currentValue = $value;
        $threshold = $rule->threshold;
        $operator = $rule->operator;

        // Intelligent solution mapping based on sensor type and deviation from threshold
        $solutions = [
            'soil_moisture' => [
                '<' => 'Irrigation system activated - soil moisture below threshold',
                '>' => 'Consider drainage improvement - soil moisture above threshold',
            ],
            'temperature' => [
                '<' => 'Activate heating system - temperature below optimal range',
                '>' => 'Activate cooling system - temperature above optimal range',
            ],
            'humidity' => [
                '<' => 'Activate humidification - humidity below optimal range',
                '>' => 'Activate dehumidification - humidity above optimal range',
            ],
            'light_intensity' => [
                '<' => 'Activate supplemental lighting - insufficient light',
                '>' => 'Consider shade protection - excessive light intensity',
            ],
        ];

        $suggestions = $solutions[$sensorType] ?? [];

        return $suggestions[$operator] ?? null;
    }

    /**
     * Get the latest readings for each sensor type in a field
     */
    protected function getLatestReadingsByType(Field $field): array
    {
        $readings = [];

        foreach ($field->sensors as $sensor) {
            $latestReading = $sensor->latestReading;

            if ($latestReading) {
                $readings[$sensor->type] = $latestReading;
            }
        }

        return $readings;
    }

    /**
     * Get the value from a reading based on sensor type
     */
    protected function getReadingValue(SensorReading $reading, string $sensorType): ?float
    {
        return match ($sensorType) {
            'soil_moisture' => $reading->soil_moisture,
            'soil_ph' => $reading->soil_ph,
            'temperature' => $reading->temperature,
            'humidity' => $reading->humidity,
            'light_intensity' => $reading->light_intensity,
            'rain_detection' => $reading->rain_detected ? 1 : 0,
            default => null,
        };
    }

    /**
     * Trigger the action defined in the rule
     */
    protected function triggerAction(AutomationRule $rule, Field $field, float $value): string
    {
        $action = $rule->action;
        $sensorTypeName = $rule->getSensorTypeNameAttribute();
        $actionName = $rule->getActionNameAttribute();

        Log::info("Automation triggered: {$rule->name} - {$actionName} for field {$field->name}");

        return match ($action) {
            'irrigation_on', 'irrigation_off' => $this->handleIrrigation($action, $field),
            'cooling_on', 'cooling_off' => $this->handleCooling($action, $field),
            'shade_on', 'shade_off' => $this->handleShade($action, $field),
            'alert' => $this->handleAlert($rule, $field, $value),
            default => "Unknown action: {$action}",
        };
    }

    /**
     * Handle irrigation action
     */
    protected function handleIrrigation(string $action, Field $field): string
    {
        $isOn = str_ends_with($action, '_on');
        $status = $isOn ? 'ON' : 'OFF';

        // In a real system, this would communicate with IoT devices
        // For now, we log the action and could store it in a device state table
        Log::info("Irrigation {$status} for field: {$field->name}");

        // Create an alert for the action
        Alert::create([
            'field_id' => $field->id,
            'type' => 'automation',
            'title' => "Irrigation {$status}",
            'message' => "Irrigation system turned {$status} for field {$field->name}",
            'severity' => 'info',
        ]);

        return "Irrigation {$status} completed";
    }

    /**
     * Handle cooling action
     */
    protected function handleCooling(string $action, Field $field): string
    {
        $isOn = str_ends_with($action, '_on');
        $status = $isOn ? 'activated' : 'deactivated';

        Log::info("Cooling system {$status} for field: {$field->name}");

        Alert::create([
            'field_id' => $field->id,
            'type' => 'automation',
            'title' => "Cooling System {$status}",
            'message' => "Cooling system {$status} for field {$field->name}",
            'severity' => 'info',
        ]);

        return "Cooling {$status} completed";
    }

    /**
     * Handle shade action
     */
    protected function handleShade(string $action, Field $field): string
    {
        $isOn = str_ends_with($action, '_on');
        $status = $isOn ? 'activated' : 'deactivated';

        Log::info("Shade system {$status} for field: {$field->name}");

        Alert::create([
            'field_id' => $field->id,
            'type' => 'automation',
            'title' => "Shade System {$status}",
            'message' => "Shade system {$status} for field {$field->name}",
            'severity' => 'info',
        ]);

        return "Shade {$status} completed";
    }

    /**
     * Handle alert action
     */
    protected function handleAlert(AutomationRule $rule, Field $field, float $value): string
    {
        $sensorTypeName = $rule->getSensorTypeNameAttribute();
        $message = sprintf(
            'Alert: %s is %.2f (threshold: %.2f %s) in field %s',
            $sensorTypeName,
            $value,
            $rule->threshold,
            $rule->operator,
            $field->name
        );

        Alert::create([
            'field_id' => $field->id,
            'type' => 'sensor',
            'title' => "{$sensorTypeName} Alert",
            'message' => $message,
            'severity' => 'warning',
        ]);

        return $message;
    }

    /**
     * Process a single sensor reading and check relevant rules
     */
    public function processSensorReading(SensorReading $reading): array
    {
        $sensor = $reading->sensor;
        $field = $sensor->field;

        if (! $field) {
            return ['triggered' => 0, 'actions' => []];
        }

        $rules = $field->automationRules()
            ->where('is_active', true)
            ->where('sensor_type', $sensor->type)
            ->get();

        $results = ['triggered' => 0, 'actions' => []];

        foreach ($rules as $rule) {
            if (! $rule->canTrigger()) {
                continue;
            }

            $value = $this->getReadingValue($reading, $rule->sensor_type);

            if ($value === null) {
                continue;
            }

            if ($rule->evaluate($value)) {
                $actionResult = $this->triggerAction($rule, $field, $value);
                $results['triggered']++;
                $results['actions'][] = [
                    'rule' => $rule->name,
                    'action' => $rule->action,
                    'result' => $actionResult,
                ];

                $rule->markTriggered();
            }
        }

        return $results;
    }
}
