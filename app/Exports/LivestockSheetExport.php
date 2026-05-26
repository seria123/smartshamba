<?php

namespace App\Exports;

use App\Models\Livestock;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class LivestockSheetExport implements FromView
{
    protected $farm;

    public function __construct($farm = null)
    {
        $this->farm = $farm;
    }

    public function view(): View
    {
        $query = Livestock::with(['type', 'farm']);

        if ($this->farm) {
            $query->where('farm_id', $this->farm->id);
        }

        return view('exports._livestock_sheet', [
            'livestock' => $query->orderBy('created_at', 'desc')->get(),
        ]);
    }
}
