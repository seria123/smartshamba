<?php

namespace App\Exports;

use App\Models\Field;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class FieldsSheetExport implements FromView
{
    protected $farm;

    public function __construct($farm = null)
    {
        $this->farm = $farm;
    }

    public function view(): View
    {
        $query = Field::with('farm');

        if ($this->farm) {
            $query->where('farm_id', $this->farm->id);
        }

        return view('exports._fields_sheet', [
            'fields' => $query->orderBy('name')->get(),
        ]);
    }
}
