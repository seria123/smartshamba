<?php

namespace App\Filament\Resources\CropCycleResource\Pages;

use App\Filament\Resources\CropCycleResource;
use App\Models\Activity;
use App\Models\CropStage;
use App\Models\GrowthMeasurement;
use App\Models\Harvest;
use App\Models\Input;
use App\Models\PestDiseaseTreatment;
use App\Models\Revenue;
use App\Models\WeatherData;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditCropCycle extends EditRecord
{
    protected static string $resource = CropCycleResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return DB::transaction(function () use ($record, $data) {
            // Extract nested arrays
            $inputs = $data['inputs'] ?? [];
            $pestTreatments = $data['pest_disease_treatments'] ?? [];
            $activities = $data['activities'] ?? [];
            $weatherRecords = $data['weather'] ?? [];
            $stages = $data['stages'] ?? [];
            $harvests = $data['harvests'] ?? [];
            $revenues = $data['revenues'] ?? [];

            // Remove nested data to update crop cycle
            unset(
                $data['inputs'],
                $data['pest_disease_treatments'],
                $data['activities'],
                $data['weather'],
                $data['stages'],
                $data['harvests'],
                $data['revenues']
            );

            // Derive farm_id from field if field_id is present
            if (isset($data['field_id'])) {
                $field = \App\Models\Field::find($data['field_id']);
                if ($field) {
                    $data['farm_id'] = $field->farm_id;
                }
            }

            // Update the main CropCycle
            $record->update($data);

            // Delete existing related records
            $record->inputs()->delete();
            $record->pestDiseaseTreatments()->delete();
            $record->weather()->delete();
            $record->stages()->delete(); // cascade to measurements
            $record->activities()->delete();
            $record->harvests()->delete();
            $record->revenues()->delete();

            // Create new Inputs
            if (count($inputs) > 0) {
                foreach ($inputs as $inputData) {
                    $input = new Input($inputData);
                    $input->crop_cycle_id = $record->id;
                    $input->save();
                }
            }

            // Create new Pest/Disease Treatments
            if (count($pestTreatments) > 0) {
                foreach ($pestTreatments as $treatmentData) {
                    $treatment = new PestDiseaseTreatment($treatmentData);
                    $treatment->crop_cycle_id = $record->id;
                    $treatment->save();
                }
            }

            // Create new Weather Records
            if (count($weatherRecords) > 0) {
                foreach ($weatherRecords as $weatherData) {
                    $weather = new WeatherData($weatherData);
                    $weather->crop_cycle_id = $record->id;
                    $weather->farm_id = $record->farm_id;
                    $weather->save();
                }
            }

            // Create new Stages with nested Measurements
            if (count($stages) > 0) {
                foreach ($stages as $stageData) {
                    $measurements = $stageData['measurements'] ?? [];
                    unset($stageData['measurements']);
                    $stage = new CropStage($stageData);
                    $stage->crop_cycle_id = $record->id;
                    $stage->save();

                    if (count($measurements) > 0) {
                        foreach ($measurements as $measureData) {
                            $measure = new GrowthMeasurement($measureData);
                            $measure->crop_stage_id = $stage->id;
                            $measure->save();
                        }
                    }
                }
            }

            // Create new Activities
            if (count($activities) > 0) {
                foreach ($activities as $activityData) {
                    $activity = new Activity($activityData);
                    $activity->crop_cycle_id = $record->id;
                    $activity->save();
                }
            }

            // Create new Harvests
            if (count($harvests) > 0) {
                foreach ($harvests as $harvestData) {
                    $harvest = new Harvest($harvestData);
                    $harvest->crop_id = $record->crop_id;
                    $harvest->field_id = $record->field_id;
                    $harvest->farm_id = $record->farm_id;
                    $harvest->crop_cycle_id = $record->id;
                    $harvest->save();
                }
            }

            // Create new Revenues
            if (count($revenues) > 0) {
                foreach ($revenues as $revenueData) {
                    if (! isset($revenueData['amount']) && isset($revenueData['quantity_sold'], $revenueData['price_per_unit'])) {
                        $revenueData['amount'] = $revenueData['quantity_sold'] * $revenueData['price_per_unit'];
                    }
                    $revenue = new Revenue($revenueData);
                    $revenue->crop_cycle_id = $record->id;
                    $revenue->farm_id = $record->farm_id;
                    $revenue->crop_id = $record->crop_id;
                    $revenue->save();
                }
            }

            return $record;
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::generateUrl('index');
    }
}
