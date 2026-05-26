<?php

namespace App\Exports;

use App\Models\Crop;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CropsSheetExport implements FromView
{
    protected $farm;

    public function __construct($farm = null)
    {
        $this->farm = $farm;
    }

    public function view(): View
    {
        $query = Crop::with(['field', 'field.farm']);

        if ($this->farm) {
            $query->whereHas('field.farm', fn ($q) => $q->where('id', $this->farm->id));
        }

        return view('exports._crops_sheet', [
            'crops' => $query->orderBy('planting_date', 'desc')->get(),
        ]);
    }
}
