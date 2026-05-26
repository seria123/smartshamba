<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable;

/**
 * Multi-sheet Excel workbook: one sheet per major farm entity.
 * Used when the user selects XLSX format for the full farm export.
 */
class FarmMultiSheetExport implements WithMultipleSheets
{
    use Exportable;

    protected ?Farm $farm;

    protected ?Carbon $fromDate;

    protected ?Carbon $toDate;

    public function __construct(?Farm $farm = null, ?Carbon $fromDate = null, ?Carbon $toDate = null)
    {
        $this->farm = $farm;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    public function sheets(): array
    {
        return [
            'Livestock'  => new LivestockSheetExport($this->farm),
            'Harvests'   => new HarvestsSheetExport($this->farm, $this->fromDate, $this->toDate),
            'Crops'      => new CropsSheetExport($this->farm),
            'Fields'     => new FieldsSheetExport($this->farm),
            'Equipment'  => new EquipmentSheetExport($this->farm),
            'Crop Cycles'=> new CropCyclesSheetExport($this->farm, $this->fromDate, $this->toDate),
        ];
    }
}
