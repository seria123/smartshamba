<?php

namespace App\Exports;

use App\Models\Equipment;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class EquipmentSheetExport implements FromView
{
    protected $farm;

    public function __construct($farm = null)
    {
        $this->farm = $farm;
    }

    public function view(): View
    {
        $query = Equipment::with(['farm', 'assignedStaff']);

        if ($this->farm) {
            $query->where('farm_id', $this->farm->id);
        }

        return view('exports._equipment_sheet', [
            'equipment' => $query->orderBy('name')->get(),
        ]);
    }
}
