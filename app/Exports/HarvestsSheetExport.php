<?php

namespace App\Exports;

use App\Models\Harvest;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class HarvestsSheetExport implements FromView
{
    protected $farm;

    protected $fromDate;

    protected $toDate;

    public function __construct($farm = null, $fromDate = null, $toDate = null)
    {
        $this->farm = $farm;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    public function view(): View
    {
        $query = Harvest::with(['crop', 'field', 'farm']);

        if ($this->farm) {
            $query->where('farm_id', $this->farm->id);
        }

        if ($this->fromDate instanceof Carbon) {
            $query->where('harvest_date', '>=', $this->fromDate);
        }

        if ($this->toDate instanceof Carbon) {
            $query->where('harvest_date', '<=', $this->toDate);
        }

        return view('exports._harvests_sheet', [
            'harvests' => $query->orderBy('harvest_date', 'desc')->get(),
        ]);
    }
}
