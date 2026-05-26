<?php

namespace App\Exports;

use App\Models\Harvest;
use App\Models\Livestock;
use App\Models\Equipment;
use App\Models\CropCycle;
use App\Models\Crop;
use App\Models\Field;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

/**
 * Single sheet export that spits out every major farm table for a given farm.
 */
class FarmDataExport implements FromView
{
    protected ?Farm $farm;

    protected ?Carbon $fromDate;

    protected ?Carbon $toDate;

    public function __construct(?Farm $farm = null, ?Carbon $fromDate = null, ?Carbon $toDate = null)
    {
        $this->farm = $farm;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    public function view(): View
    {
        $farm = $this->farm;

        // ------- Livestock -------
        $livestockQuery = Livestock::with(['type', 'farm']);
        if ($farm) {
            $livestockQuery->where('farm_id', $farm->id);
        }
        $livestock = $livestockQuery->orderBy('created_at', 'desc')->get();

        // ------- Harvests -------
        $harvestQuery = Harvest::with(['crop', 'field', 'farm']);
        if ($farm) {
            $harvestQuery->where('farm_id', $farm->id);
        }
        if ($this->fromDate) {
            $harvestQuery->where('harvest_date', '>=', $this->fromDate);
        }
        if ($this->toDate) {
            $harvestQuery->where('harvest_date', '<=', $this->toDate);
        }
        $harvests = $harvestQuery->orderBy('harvest_date', 'desc')->get();

        // ------- Crops -------
        $cropQuery = Crop::with(['field', 'field.farm']);
        if ($farm) {
            $cropQuery->whereHas('field.farm', fn ($q) => $q->where('id', $farm->id));
        }
        $crops = $cropQuery->orderBy('planting_date', 'desc')->get();

        // ------- Fields -------
        $fieldQuery = Field::with('farm');
        if ($farm) {
            $fieldQuery->where('farm_id', $farm->id);
        }
        $fields = $fieldQuery->orderBy('name')->get();

        // ------- Equipment -------
        $equipmentQuery = Equipment::with(['farm', 'assignedStaff']);
        if ($farm) {
            $equipmentQuery->where('farm_id', $farm->id);
        }
        $equipment = $equipmentQuery->orderBy('name')->get();

        // ------- Crop Cycles -------
        $cycleQuery = CropCycle::with(['crop', 'field', 'field.farm']);
        if ($farm) {
            $cycleQuery->whereHas('field.farm', fn ($q) => $q->where('id', $farm->id));
        }
        if ($this->fromDate) {
            $cycleQuery->where('start_date', '>=', $this->fromDate);
        }
        if ($this->toDate) {
            $cycleQuery->where('end_date', '<=', $this->toDate);
        }
        $cropCycles = $cycleQuery->orderByDesc('start_date')->get();

        return view('exports._farm_data', compact(
            'livestock', 'harvests', 'crops', 'fields', 'equipment', 'cropCycles', 'farm'
        ));
    }
}
