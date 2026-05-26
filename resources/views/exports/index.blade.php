{{-- Export hub – tactics: 3 columns (CSV | PDF | Import) --}}
@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            <div class="mb-4">
                <h2><i class="fas fa-file-export text-primary"></i> Import &amp; Export</h2>
                <p class="text-muted">Download farm data as <strong>CSV / XLSX</strong>, generate <strong>PDF</strong> reports, or import records from <strong>CSV</strong> files.</p>
            </div>

            {{-- Outcome alerts --}}
            @if(session('import_success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>{{ session('import_success') }}
                @if($importErrors = session('import_errors', []))
                    @foreach($importErrors as $msg)
                        <div class="text-danger small ms-4 mt-1">⚠ {{ $msg }}</div>
                    @endforeach
                @endif
                <button type="button" class="btn-close dismiss" data-dismiss-dismissible hidden></button>
            </div>
            @endif

            @if(session('import_error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('import_error') }}
                <button type="button" class="btn-close dismiss" data-dismiss-dismissible hidden></button>
            </div>
            @endif

            {{-- ================================================================== --}}
            {{--  SECTION 1 : CSV / XLSX EXPORT                                   --}}
            {{-- ================================================================== --}}
            <h4 class="mt-4 mb-3"><i class="fas fa-table text-success"></i> CSV / Excel Export</h4>
            <div class="row g-4">

                {{-- Sensor Readings --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-thermometer-half"></i> Sensor Readings</h5>
                        </div>
                        <div class="card-body">
                            <p>Temperature, humidity, soil moisture, and light data.</p>
                            <form method="GET" action="{{ route('exports.sensorReadings') }}">
                                <select name="farm_id" class="form-control mb-2">
                                    <option value="">All Farms</option>
                                    @foreach ($farms as $f)
                                        <option value="{{ $f->id }}">{{ $f->name }}</option>
                                    @endforeach
                                </select>
                                <div class="row g-2">
                                    <input type="date" name="from_date" class="form-control mb-2" placeholder="From">
                                    <input type="date" name="to_date" class="form-control mb-2" placeholder="To">
                                </div>
                                <select name="format" class="form-control mb-2">
                                    <option value="csv">CSV</option>
                                    <option value="xlsx">Excel (XLSX)</option>
                                </select>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-download"></i> Export
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Tasks --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-tasks"></i> Tasks &amp; Activities</h5>
                        </div>
                        <div class="card-body">
                            <p>All farm tasks, schedules, and progress records.</p>
                            <form method="GET" action="{{ route('exports.tasks') }}">
                                <select name="farm_id" class="form-control mb-2">
                                    <option value="">All Farms</option>
                                    @foreach ($farms as $f)
                                        <option value="{{ $f->id }}">{{ $f->name }}</option>
                                    @endforeach
                                </select>
                                <select name="status" class="form-control mb-2">
                                    <option value="">All Statuses</option>
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                </select>
                                <div class="row g-2">
                                    <input type="date" name="from_date" class="form-control mb-2" placeholder="From">
                                    <input type="date" name="to_date" class="form-control mb-2" placeholder="To">
                                </div>
                                <select name="format" class="form-control mb-2">
                                    <option value="csv">CSV</option>
                                    <option value="xlsx">Excel (XLSX)</option>
                                </select>
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-download"></i> Export
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Irrigation Logs --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-tint"></i> Irrigation Logs</h5>
                        </div>
                        <div class="card-body">
                            <p>Watering sessions, water usage, and soil moisture data.</p>
                            <form method="GET" action="{{ route('exports.irrigationLogs') }}">
                                <select name="farm_id" class="form-control mb-2">
                                    <option value="">All Farms</option>
                                    @foreach ($farms as $f)
                                        <option value="{{ $f->id }}">{{ $f->name }}</option>
                                    @endforeach
                                </select>
                                <div class="row g-2">
                                    <input type="date" name="from_date" class="form-control mb-2" placeholder="From">
                                    <input type="date" name="to_date" class="form-control mb-2" placeholder="To">
                                </div>
                                <select name="format" class="form-control mb-2">
                                    <option value="csv">CSV</option>
                                    <option value="xlsx">Excel (XLSX)</option>
                                </select>
                                <button type="submit" class="btn btn-info w-100">
                                    <i class="fas fa-download"></i> Export
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Weather Data --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-header bg-warning text-white">
                            <h5 class="mb-0"><i class="fas fa-cloud"></i> Weather Data</h5>
                        </div>
                        <div class="card-body">
                            <p>Temperature, rainfall, humidity, wind, and forecasts.</p>
                            <form method="GET" action="{{ route('exports.weatherData') }}">
                                <select name="farm_id" class="form-control mb-2" required>
                                    <option value="">Select Farm</option>
                                    @foreach ($farms as $f)
                                        <option value="{{ $f->id }}">{{ $f->name }}</option>
                                    @endforeach
                                </select>
                                <div class="row g-2">
                                    <input type="date" name="from_date" class="form-control mb-2" placeholder="From">
                                    <input type="date" name="to_date" class="form-control mb-2" placeholder="To">
                                </div>
                                <select name="format" class="form-control mb-2">
                                    <option value="csv">CSV</option>
                                    <option value="xlsx">Excel (XLSX)</option>
                                </select>
                                <button type="submit" class="btn btn-warning w-100">
                                    <i class="fas fa-download"></i> Export
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Crops --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-seedling"></i> Crops</h5>
                        </div>
                        <div class="card-body">
                            <p>Planting records, varieties, and harvest estimates.</p>
                            <form method="GET" action="{{ route('exports.crops') }}">
                                <select name="farm_id" class="form-control mb-2">
                                    <option value="">All Farms</option>
                                    @foreach ($farms as $f)
                                        <option value="{{ $f->id }}">{{ $f->name }}</option>
                                    @endforeach
                                </select>
                                <select name="format" class="form-control mb-2">
                                    <option value="csv">CSV</option>
                                    <option value="xlsx">Excel (XLSX)</option>
                                </select>
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-download"></i> Export
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Crop Analyses --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-header" style="background-color:#6f42c1;color:white">
                            <h5 class="mb-0"><i class="fas fa-microscope"></i> Crop Analyses</h5>
                        </div>
                        <div class="card-body">
                            <p>AI disease detection and crop health results.</p>
                            <form method="GET" action="{{ route('exports.cropAnalyses') }}">
                                <select name="farm_id" class="form-control mb-2">
                                    <option value="">All Farms</option>
                                    @foreach ($farms as $f)
                                        <option value="{{ $f->id }}">{{ $f->name }}</option>
                                    @endforeach
                                </select>
                                <div class="row g-2">
                                    <input type="date" name="from_date" class="form-control mb-2" placeholder="From">
                                    <input type="date" name="to_date" class="form-control mb-2" placeholder="To">
                                </div>
                                <select name="format" class="form-control mb-2">
                                    <option value="csv">CSV</option>
                                    <option value="xlsx">Excel (XLSX)</option>
                                </select>
                                <button type="submit" class="btn w-100" style="background-color:#6f42c1;color:white">
                                    <i class="fas fa-download"></i> Export
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Livestock --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0"><i class="fas fa-cow"></i> Livestock</h5>
                        </div>
                        <div class="card-body">
                            <p>Animal tag numbers, health status, weight, and breeding.</p>
                            <form method="GET" action="{{ route('exports.livestock') }}">
                                <select name="farm_id" class="form-control mb-2">
                                    <option value="">All Farms</option>
                                    @foreach ($farms as $f)
                                        <option value="{{ $f->id }}">{{ $f->name }}</option>
                                    @endforeach
                                </select>
                                <select name="format" class="form-control mb-2">
                                    <option value="csv">CSV</option>
                                    <option value="xlsx">Excel (XLSX)</option>
                                </select>
                                <button type="submit" class="btn btn-secondary w-100">
                                    <i class="fas fa-download"></i> Export
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Equipment --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0"><i class="fas fa-tools"></i> Equipment</h5>
                        </div>
                        <div class="card-body">
                            <p>Machinery, tooling, purchase price, and service schedule.</p>
                            <form method="GET" action="{{ route('exports.equipment') }}">
                                <select name="farm_id" class="form-control mb-2">
                                    <option value="">All Farms</option>
                                    @foreach ($farms as $f)
                                        <option value="{{ $f->id }}">{{ $f->name }}</option>
                                    @endforeach
                                </select>
                                <select name="format" class="form-control mb-2">
                                    <option value="csv">CSV</option>
                                    <option value="xlsx">Excel (XLSX)</option>
                                </select>
                                <button type="submit" class="btn btn-dark w-100">
                                    <i class="fas fa-download"></i> Export
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Fields --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-header bg-teal text-white" style="background-color:#008080">
                            <h5 class="mb-0"><i class="fas fa-map"></i> Fields</h5>
                        </div>
                        <div class="card-body">
                            <p>Field boundaries, soil type, water source, GPS coordinates.</p>
                            <form method="GET" action="{{ route('exports.fields') }}">
                                <select name="farm_id" class="form-control mb-2">
                                    <option value="">All Farms</option>
                                    @foreach ($farms as $f)
                                        <option value="{{ $f->id }}">{{ $f->name }}</option>
                                    @endforeach
                                </select>
                                <select name="format" class="form-control mb-2">
                                    <option value="csv">CSV</option>
                                    <option value="xlsx">Excel (XLSX)</option>
                                </select>
                                <button type="submit" class="btn w-100" style="background-color:#008080;color:white">
                                    <i class="fas fa-download"></i> Export
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Crop Cycles --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-header bg-indigo text-white" style="background-color:#4b0082">
                            <h5 class="mb-0"><i class="fas fa-layer-group"></i> Crop Cycles</h5>
                        </div>
                        <div class="card-body">
                            <p>Seasonal crop plans, seed varieties, and growth timelines.</p>
                            <form method="GET" action="{{ route('exports.cropCycles') }}">
                                <select name="farm_id" class="form-control mb-2">
                                    <option value="">All Farms</option>
                                    @foreach ($farms as $f)
                                        <option value="{{ $f->id }}">{{ $f->name }}</option>
                                    @endforeach
                                </select>
                                <div class="row g-2">
                                    <input type="date" name="from_date" class="form-control mb-2" placeholder="From">
                                    <input type="date" name="to_date" class="form-control mb-2" placeholder="To">
                                </div>
                                <select name="format" class="form-control mb-2">
                                    <option value="csv">CSV</option>
                                    <option value="xlsx">Excel (XLSX)</option>
                                </select>
                                <button type="submit" class="btn w-100" style="background-color:#4b0082;color:white">
                                    <i class="fas fa-download"></i> Export
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
            {{-- end CSV --}}

            {{-- ================================================================== --}}
            {{--  SECTION 2 : PDF REPORTS                                         --}}
            {{-- ================================================================== --}}
            <h4 class="mt-5 mb-3"><i class="fas fa-file-pdf text-danger"></i> PDF Reports</h4>
            <div class="row g-4">

                {{-- Livestock PDF --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0"><i class="fas fa-cow"></i> Livestock Report</h5>
                        </div>
                        <div class="card-body">
                            <p>Full inventory with type, health status, weight, and value.</p>
                            <form method="GET" action="{{ route('exports.pdf.livestock') }}">
                                <select name="farm_id" class="form-control mb-3">
                                    <option value="">All Farms</option>
                                    @foreach ($farms as $f)
                                        <option value="{{ $f->id }}">{{ $f->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="fas fa-file-pdf"></i> View PDF</button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Harvests PDF --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-seedling"></i> Harvest Report</h5>
                        </div>
                        <div class="card-body">
                            <p>Harvest records by grade, crop, and quantity with loss stats.</p>
                            <form method="GET" action="{{ route('exports.pdf.harvests') }}">
                                <select name="farm_id" class="form-control mb-3">
                                    <option value="">All Farms</option>
                                    @foreach ($farms as $f)
                                        <option value="{{ $f->id }}">{{ $f->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-file-pdf"></i> View PDF</button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Farm Summary PDF --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-book"></i> Farm Summary</h5>
                        </div>
                        <div class="card-body">
                            <p>Single-page overview of one farm: fields, livestock, crops &amp; equipment.</p>
                            <form method="GET" action="#"
                                  id="farmPdfForm"
                                  onsubmit="submitFarmPdf(event)">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Select farm <span class="text-danger">*</span></label>
                                    <select name="farm_id" class="form-control" id="farmPdfSelect" required>
                                        <option value="">Choose a farm…</option>
                                        @foreach ($farms as $f)
                                            <option value="{{ $f->id }}">
                                                {{ $f->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary w-100" id="farmPdfBtn">
                                    <i class="fas fa-file-pdf"></i> View PDF</button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
            {{-- end PDF --}}

            {{-- ================================================================== --}}
            {{--  SECTION 3 : CSV IMPORT                                          --}}
            {{-- ================================================================== --}}
            <h4 class="mt-5 mb-3"><i class="fas fa-file-import text-warning"></i> CSV Import</h4>
            <p class="text-muted mb-3">Upload a CSV to add or update records in
                <strong>Livestock</strong>, <strong>Crops</strong>, or <strong>Equipment</strong>.
                <a href="{{ route('exports.sample.livestock') }}">Download livestock template&rarr;</a>
                <a href="{{ route('exports.sample.crops') }}" class="ms-3">Download crops template&rarr;</a>
                <a href="{{ route('exports.sample.equipment') }}" class="ms-3">Download equipment template&rarr;</a>
            </p>

            <div class="row g-4">

                {{-- Livestock Import --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0"><i class="fas fa-cow"></i> Import Livestock</h5>
                        </div>
                        <div class="card-body">
                            <p>Upload a <code>.csv</code> with <code>tag_number</code>, <code>name</code>, <code>type</code>, <code>status</code>, and more.</p>
                            <form method="POST"
                                  action="{{ route('exports.import.livestock') }}"
                                  enctype="multipart/form-data">
                                @csrf
                                <div class="mb-2">
                                    <label class="form-label small fw-bold">CSV File <span class="text-danger">*</span></label>
                                    <input type="file" name="csv_file" accept=".csv,.txt"
                                           class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-secondary w-100">
                                    <i class="fas fa-upload"></i> Import CSV
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Crops Import --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-seedling"></i> Import Crops</h5>
                        </div>
                        <div class="card-body">
                            <p>Upload a <code>.csv</code> with <code>name</code>, <code>field</code>, <code>planting_date</code>, and more.</p>
                            <form method="POST"
                                  action="{{ route('exports.import.crops') }}"
                                  enctype="multipart/form-data">
                                @csrf
                                <div class="mb-2">
                                    <label class="form-label small fw-bold">CSV File <span class="text-danger">*</span></label>
                                    <input type="file" name="csv_file" accept=".csv,.txt"
                                           class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-upload"></i> Import CSV
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Equipment Import --}}
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0"><i class="fas fa-tools"></i> Import Equipment</h5>
                        </div>
                        <div class="card-body">
                            <p>Upload a <code>.csv</code> with <code>name</code>, <code>type</code>, <code>serial_number</code>, and more.</p>
                            <form method="POST"
                                  action="{{ route('exports.import.equipment') }}"
                                  enctype="multipart/form-data">
                                @csrf
                                <div class="mb-2">
                                    <label class="form-label small fw-bold">CSV File <span class="text-danger">*</span></label>
                                    <input type="file" name="csv_file" accept=".csv,.txt"
                                           class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-dark w-100">
                                    <i class="fas fa-upload"></i> Import CSV
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
            {{-- end Import --}}

        </div>
    </div>
</div>

{{-- Farm Summary PDF: redirect form action after validation --}}
<script>
function submitFarmPdf(e) {
    var select = document.getElementById('farmPdfSelect');
    var val    = select.value;
    if (!val) { e.preventDefault(); select.focus(); return; }
    var form   = e.target;
    form.action = '{{ route('exports.pdf.farm', ':id') }}'.replace(':id', val);
}
</script>
@endsection
