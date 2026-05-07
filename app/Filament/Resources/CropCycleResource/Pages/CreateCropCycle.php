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
use DB;
use Filament\Resources\Pages\CreateRecord;

class CreateCropCycle extends CreateRecord
{
    protected static string $resource = CropCycleResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return DB::transaction(function () use ($data) {
            // Extract nested arrays
            $inputs = $data['inputs'] ?? [];
            $pestTreatments = $data['pest_disease_treatments'] ?? [];
            $activities = $data['activities'] ?? [];
            $weatherRecords = $data['weather'] ?? [];
            $stages = $data['stages'] ?? [];
            $harvests = $data['harvests'] ?? [];
            $revenues = $data['revenues'] ?? [];

            // Remove nested data to create crop cycle
            unset(
                $data['inputs'],
                $data['pest_disease_treatments'],
                $data['activities'],
                $data['weather'],
                $data['stages'],
                $data['harvests'],
                $data['revenues']
            );

            // Derive farm_id from field if not provided
            if (isset($data['field_id']) && ! isset($data['farm_id'])) {
                $field = \App\Models\Field::find($data['field_id']);
                if ($field) {
                    $data['farm_id'] = $field->farm_id;
                }
            }

            // Create the main CropCycle
            $cropCycle = CropCycle::create($data);

            // Create Inputs
            if (count($inputs) > 0) {
                foreach ($inputs as $inputData) {
                    $input = new Input($inputData);
                    $input->crop_cycle_id = $cropCycle->id;
                    $input->save();
                }
            }

            // Create Pest/Disease Treatments
            if (count($pestTreatments) > 0) {
                foreach ($pestTreatments as $treatmentData) {
                    $treatment = new PestDiseaseTreatment($treatmentData);
                    $treatment->crop_cycle_id = $cropCycle->id;
                    $treatment->save();
                }
            }

            // Create Weather Records
            if (count($weatherRecords) > 0) {
                foreach ($weatherRecords as $weatherData) {
                    $weather = new WeatherData($weatherData);
                    $weather->crop_cycle_id = $cropCycle->id;
                    $weather->farm_id = $cropCycle->farm_id;
                    $weather->save();
                }
            }

            // Create Stages with nested Measurements
            if (count($stages) > 0) {
                foreach ($stages as $stageData) {
                    $measurements = $stageData['measurements'] ?? [];
                    unset($stageData['measurements']);
                    $stage = new CropStage($stageData);
                    $stage->crop_cycle_id = $cropCycle->id;
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

            // Create Activities
            if (count($activities) > 0) {
                foreach ($activities as $activityData) {
                    $activity = new Activity($activityData);
                    $activity->crop_cycle_id = $cropCycle->id;
                    $activity->save();
                }
            }

            // Create Harvests
            if (count($harvests) > 0) {
                foreach ($harvests as $harvestData) {
                    $harvest = new Harvest($harvestData);
                    $harvest->crop_id = $cropCycle->crop_id;
                    $harvest->field_id = $cropCycle->field_id;
                    $harvest->farm_id = $cropCycle->farm_id;
                    $harvest->crop_cycle_id = $cropCycle->id;
                    $harvest->save();
                }
            }

            // Create Revenues
            if (count($revenues) > 0) {
                foreach ($revenues as $revenueData) {
                    // Calculate amount if not provided
                    if (! isset($revenueData['amount']) && isset($revenueData['quantity_sold'], $revenueData['price_per_unit'])) {
                        $revenueData['amount'] = $revenueData['quantity_sold'] * $revenueData['price_per_unit'];
                    }
                    $revenue = new Revenue($revenueData);
                    $revenue->crop_cycle_id = $cropCycle->id;
                    $revenue->farm_id = $cropCycle->farm_id;
                    $revenue->crop_id = $cropCycle->crop_id;
                    $revenue->save();
                }
            }

            return $cropCycle;
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::generateUrl('index');
    }
}
