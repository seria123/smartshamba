<?php

namespace App\Exports;

use App\Models\CropCycle;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CropCyclesSheetExport implements FromView
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
        $query = CropCycle::with(['crop', 'field', 'field.farm']);

        if ($this->farm) {
            $query->whereHas('field.farm', fn ($q) => $q->where('id', $this->farm->id));
        }

        if ($this->fromDate instanceof Carbon) {
            $query->where('start_date', '>=', $this->fromDate);
        }

        if ($this->toDate instanceof Carbon) {
            $query->where('end_date', '<=', $this->toDate);
        }

        return view('exports._cropcycles_sheet', [
            'cropCycles' => $query->orderByDesc('start_date')->get(),
        ]);
    }
}
