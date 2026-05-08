<?php

namespace App\Modules\Sales\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Farm;
use App\Modules\Sales\Models\SalesCustomer;
use App\Modules\Sales\Services\SalesReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesReportController extends Controller
{
    public function summary(Request $request, SalesReportService $reports): View { return $this->view('summary', ['summary' => $reports->summary($this->filters($request))]); }
    public function customers(Request $request, SalesReportService $reports): View { return $this->view('customers', ['rows' => $reports->byCustomer($this->filters($request))]); }
    public function items(Request $request, SalesReportService $reports): View { return $this->view('items', ['rows' => $reports->byItem($this->filters($request))]); }
    public function cropCycles(Request $request, SalesReportService $reports): View { return $this->view('crop-cycles', ['rows' => $reports->byCropCycle($this->filters($request))]); }
    public function livestock(Request $request, SalesReportService $reports): View { return $this->view('livestock', ['rows' => $reports->byLivestock($this->filters($request))]); }
    public function grossMargin(Request $request, SalesReportService $reports): View { return $this->view('gross-margin', ['rows' => $reports->grossMargin($this->filters($request))]); }

    private function view(string $name, array $data): View
    {
        return view('sales::reports.'.$name, $data + ['farms' => Farm::orderBy('name')->get(), 'customers' => SalesCustomer::orderBy('name')->get()]);
    }

    private function filters(Request $request): array
    {
        return $request->only(['organization_id', 'farm_id', 'date_from', 'date_to', 'customer_id', 'category', 'status', 'payment_status', 'crop_cycle_id', 'animal_id', 'animal_group_id']);
    }
}
