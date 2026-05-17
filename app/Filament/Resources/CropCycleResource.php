<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CropCycleResource\Pages;
use App\Models\CropCycle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CropCycleResource extends Resource
{
    protected static ?string $model = CropCycle::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationGroup = 'Crop Management';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Crop Cycle';

    protected static ?string $pluralModelLabel = 'Crop Cycles';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make('crop_management_wizard')
                    ->steps([
                        // Step 1: Basic Crop Identity
                        Forms\Components\Wizard\Step::make('Basic Crop Identity')
                            ->icon('heroicon-o-flower')
                            ->schema([
                                Forms\Components\Select::make('crop_id')
                                    ->label('Crop Variety')
                                    ->relationship('crop', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->crop) {
                                            $component->state($record->crop->id);
                                        }
                                    }),
Forms\Components\TextInput::make('crop_name')
                                     ->label('Crop Name')
                                     ->required()
                                     ->maxLength(255)
                                     ->default(fn ($record) => $record?->crop?->name),
                                 Forms\Components\TextInput::make('code')
                                     ->label('Cycle Code')
                                     ->required()
                                     ->maxLength(50)
                                     ->unique(ignorable: fn ($record) => $record)
                                     ->default(fn ($record) => $record?->code ?? strtoupper(substr($record?->crop?->name ?? 'CRP', 0, 3)) . '-' . now()->format('ym')),
                                 Forms\Components\TextInput::make('variety')
                                    ->label('Variety')
                                    ->maxLength(255)
                                    ->default(fn ($record) => $record?->crop?->variety),
                                Forms\Components\Select::make('category')
                                    ->label('Category')
                                    ->options([
                                        'cereal' => 'Cereal',
                                        'legume' => 'Legume',
                                        'vegetable' => 'Vegetable',
                                        'fruit' => 'Fruit',
                                        'root_crop' => 'Root Crop',
                                        'cash_crop' => 'Cash Crop',
                                        'other' => 'Other',
                                    ])
                                    ->default(fn ($record) => $record?->crop?->category)
                                    ->required(),
                                Forms\Components\Select::make('season')
                                    ->label('Season')
                                    ->options([
                                        'spring' => 'Spring',
                                        'summer' => 'Summer',
                                        'fall' => 'Fall',
                                        'winter' => 'Winter',
                                        'year_round' => 'Year Round',
                                    ])
                                    ->required(),
Forms\Components\DatePicker::make('planned_start_date')
                                     ->label('Planned Start Date'),
                                 Forms\Components\DatePicker::make('start_date')
                                     ->label('Actual Planting Date')
                                     ->required(),
                                 Forms\Components\DatePicker::make('expected_harvest_date')
                                     ->label('Expected Harvest Date')
                                     ->required(),
                                 Forms\Components\Select::make('status')
                                     ->label('Status')
                                     ->options([
                                         'planned' => 'Planned',
                                         'active' => 'Active',
                                         'completed' => 'Completed',
                                         'cancelled' => 'Cancelled',
                                     ])
                                     ->default('planned')
                                     ->required(),
                                 Forms\Components\Select::make('staff_id')
                                     ->label('Responsible Person')
                                     ->relationship('staff', 'fullName')
                                     ->searchable()
                                     ->preload(),
                             ]),

                        // Step 2: Land & Soil Requirements
                        Forms\Components\Wizard\Step::make('Land & Soil')
                            ->icon('heroicon-o-map')
                            ->schema([
                                Forms\Components\Select::make('field_id')
                                    ->label('Field / Plot')
                                    ->relationship('field', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\Select::make('farm_id')
                                    ->label('Farm')
                                    ->relationship('farm', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\TextInput::make('soil_type_override')
                                    ->label('Soil Type')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('ph_level')
                                    ->label('pH Level')
                                    ->numeric()
                                    ->step(0.1),
Forms\Components\TextInput::make('land_size_hectares')
                                     ->label('Land Size (Hectares)')
                                     ->numeric()
                                     ->default(fn ($record) => $record?->field?->size_hectares)
                                     ->disabled(),
                                 Forms\Components\TextInput::make('area_planted')
                                     ->label('Area Planted (Hectares)')
                                     ->numeric()
                                     ->step(0.01),
                                 Forms\Components\Select::make('planting_method')
                                     ->label('Planting Method')
                                     ->options([
                                         'direct_seeding' => 'Direct Seeding',
                                         'transplanting' => 'Transplanting',
                                         'cuttings' => 'Cuttings',
                                         'other' => 'Other',
                                     ]),
Forms\Components\Select::make('previous_crop_cycle_id')
                                     ->label('Previous Crop')
                                     ->relationship('previousCycle', 'crop_name')
                                     ->placeholder('Select previous crop (optional)')
                                     ->searchable()
                                     ->preload(),
                             ]),

                         // Step 2.5: Land/Plot Preparation Details
                         Forms\Components\Wizard\Step::make('Land Preparation')
                             ->icon('heroicon-o-wrench')
                             ->schema([
                                 Forms\Components\CheckboxList::make('preparation_activities')
                                     ->label('Land Preparation Activities')
                                     ->options([
                                         'ploughing' => 'Ploughing',
                                         'harrowing' => 'Harrowing',
                                         'bed_preparation' => 'Bed Preparation',
                                         'ridging' => 'Ridging',
                                         'levelling' => 'Levelling',
                                         'drainage_digging' => 'Drainage Digging',
                                         'other' => 'Other',
                                     ])
                                     ->columns(3),
                                 Forms\Components\TextInput::make('soil_test_ph')
                                     ->label('Soil Test pH')
                                     ->numeric()
                                     ->step(0.1),
                                 Forms\Components\TextInput::make('soil_test_nitrogen')
                                     ->label('Nitrogen (ppm)')
                                     ->numeric(),
                                 Forms\Components\TextInput::make('soil_test_phosphorus')
                                     ->label('Phosphorus (ppm)')
                                     ->numeric(),
                                 Forms\Components\TextInput::make('soil_test_potassium')
                                     ->label('Potassium (ppm)')
                                     ->numeric(),
                                 Forms\Components\Textarea::make('soil_test_recommendations')
                                     ->label('Soil Test Recommendations')
                                     ->rows(2),
                                 Forms\Components\TextInput::make('manure_quantity')
                                     ->label('Manure/Compost Quantity')
                                     ->numeric()
                                     ->suffix('tons'),
                                 Forms\Components\TextInput::make('manure_cost')
                                     ->label('Manure Cost')
                                     ->numeric()
                                     ->prefix('$'),
                                 Forms\Components\TextInput::make('manure_source')
                                     ->label('Manure Source')
                                     ->maxLength(255),
                                 Forms\Components\TextInput::make('basal_fertilizer_dap')
                                     ->label('DAP (kg)')
                                     ->numeric(),
                                 Forms\Components\TextInput::make('basal_fertilizer_npk')
                                     ->label('NPK (kg)')
                                     ->numeric(),
                                 Forms\Components\TextInput::make('basal_fertilizer_lime')
                                     ->label('Lime (kg)')
                                     ->numeric(),
                                 Forms\Components\Select::make('irrigation_setup')
                                     ->label('Irrigation Setup')
                                     ->options([
                                         'drip_installed' => 'Drip Irrigation Installed',
                                         'sprinkler_installed' => 'Sprinkler Installed',
                                         'manual' => 'Manual/Baseline',
                                         'existing' => 'Existing System',
                                         'none' => 'No Irrigation',
                                     ]),
                                 Forms\Components\TextInput::make('prep_labor_workers')
                                     ->label('Preparation Workers')
                                     ->numeric(),
                                 Forms\Components\TextInput::make('prep_labor_hours')
                                     ->label('Preparation Hours')
                                     ->numeric(),
                                 Forms\Components\TextInput::make('prep_labor_cost')
                                     ->label('Preparation Labor Cost')
                                     ->numeric()
                                     ->prefix('$'),
                                 Forms\Components\TextInput::make('machinery_tractor')
                                     ->label('Tractor Hours')
                                     ->numeric(),
                                 Forms\Components\TextInput::make('machinery_pump')
                                     ->label('Pump Hours')
                                     ->numeric(),
                                 Forms\Components\TextInput::make('machinery_sprayer')
                                     ->label('Sprayer Hours')
                                     ->numeric(),
                                 Forms\Components\Textarea::make('machinery_notes')
                                     ->label('Machinery Notes')
                                     ->rows(2),
                             ]),

                         // Step 3: Planting Details
                         Forms\Components\Wizard\Step::make('Planting Details')
                             ->icon('heroicon-o-sun')
                             ->schema([
                                 Forms\Components\TextInput::make('seed_batch_number')
                                     ->label('Seed/Seedling Batch Number')
                                     ->maxLength(255)
                                     ->placeholder('Seed lot number or nursery batch'),
                                 Forms\Components\Grid::make(2)
                                     ->schema([
                                         Forms\Components\TextInput::make('seed_quantity')
                                             ->label('Seed Quantity')
                                             ->numeric()
                                             ->suffix('kg/kg'),
                                         Forms\Components\TextInput::make('seedling_quantity')
                                             ->label('Seedling Quantity')
                                             ->numeric()
                                             ->suffix('plants'),
                                     ]),
                                 Forms\Components\Grid::make(2)
                                     ->schema([
                                         Forms\Components\TextInput::make('spacing_row')
                                             ->label('Row Spacing (cm)')
                                             ->numeric(),
                                         Forms\Components\TextInput::make('spacing_plant')
                                             ->label('Plant Spacing (cm)')
                                             ->numeric(),
                                     ]),
                                 Forms\Components\TextInput::make('plant_population')
                                     ->label('Plant Population')
                                     ->numeric()
                                     ->helperText('Expected number of plants'),
                                 Forms\Components\Grid::make(2)
                                     ->schema([
                                         Forms\Components\TextInput::make('germination_rate')
                                             ->label('Germination Rate (%)')
                                             ->numeric()
                                             ->maxValue(100),
                                         Forms\Components\TextInput::make('survival_rate')
                                             ->label('Survival Rate (%)')
                                             ->numeric()
                                             ->maxValue(100),
                                     ]),
                                 Forms\Components\Grid::make(2)
                                     ->schema([
                                         Forms\Components\TextInput::make('planting_labor_workers')
                                             ->label('Planting Workers')
                                             ->numeric(),
                                         Forms\Components\TextInput::make('planting_labor_cost')
                                             ->label('Planting Labor Cost')
                                             ->numeric()
                                             ->prefix('$'),
                                     ]),
                                 Forms\Components\Textarea::make('planting_notes')
                                     ->label('Planting Notes')
                                     ->rows(2)
                                     ->placeholder('Poor rainfall, delayed transplanting...'),
                             ]),

                         // Step 4: Water & Irrigation
                        Forms\Components\Wizard\Step::make('Water & Irrigation')
                            ->icon('heroicon-o-water')
                            ->schema([
                                Forms\Components\Select::make('irrigation_type')
                                    ->label('Irrigation Type')
                                    ->options([
                                        'drip' => 'Drip Irrigation',
                                        'sprinkler' => 'Sprinkler',
                                        'furrow' => 'Furrow Irrigation',
                                        'flood' => 'Flood Irrigation',
                                        'rainfed' => 'Rainfed',
                                        'other' => 'Other',
                                    ]),
                                Forms\Components\TextInput::make('water_source_override')
                                    ->label('Water Source')
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('irrigation_schedule')
                                    ->label('Irrigation Schedule')
                                    ->rows(3),
                                Forms\Components\Textarea::make('drainage')
                                    ->label('Drainage System')
                                    ->rows(3),
                            ]),

                        // Step 4: Inputs (Seeds, Fertilizers, Agrochemicals)
                        Forms\Components\Wizard\Step::make('Inputs')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Forms\Components\Repeater::make('inputs')
                                    ->label('Inputs')
                                    ->schema([
                                        Forms\Components\Select::make('input_type')
                                            ->label('Type')
                                            ->options([
                                                'seed' => 'Seed',
                                                'fertilizer' => 'Fertilizer',
                                                'pesticide' => 'Pesticide',
                                                'herbicide' => 'Herbicide',
                                                'fungicide' => 'Fungicide',
                                                'organic_amendment' => 'Organic Amendment',
                                                'other' => 'Other',
                                            ])
                                            ->required(),
                                        Forms\Components\TextInput::make('name')
                                            ->label('Name / Product')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('quantity')
                                            ->label('Quantity')
                                            ->numeric()
                                            ->required(),
                                        Forms\Components\TextInput::make('unit')
                                            ->label('Unit')
                                            ->maxLength(50),
                                        Forms\Components\TextInput::make('cost')
                                            ->label('Cost')
                                            ->numeric()
                                            ->prefix('$'),
                                        Forms\Components\DatePicker::make('application_date')
                                            ->label('Application Date'),
                                        Forms\Components\TextInput::make('application_method')
                                            ->label('Application Method')
                                            ->maxLength(255),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(0)
                                    ->addable()
                                    ->deletable(),
                            ]),

                        // Step 5: Pest & Disease Management
                        Forms\Components\Wizard\Step::make('Pest & Disease')
                            ->icon('heroicon-o-exclamation-triangle')
                            ->schema([
                                Forms\Components\Repeater::make('pest_disease_treatments')
                                    ->label('Treatments')
                                    ->schema([
                                        Forms\Components\Select::make('issue_type')
                                            ->label('Issue Type')
                                            ->options([
                                                'pest' => 'Pest',
                                                'disease' => 'Disease',
                                                'other' => 'Other',
                                            ]),
                                        Forms\Components\TextInput::make('title')
                                            ->label('Title')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('description')
                                            ->label('Description')
                                            ->rows(2),
                                        Forms\Components\Select::make('severity')
                                            ->label('Severity')
                                            ->options([
                                                'low' => 'Low',
                                                'medium' => 'Medium',
                                                'high' => 'High',
                                                'critical' => 'Critical',
                                            ]),
                                        Forms\Components\TextInput::make('affected_area')
                                            ->label('Affected Area')
                                            ->maxLength(255),
                                        Forms\Components\DatePicker::make('treatment_date')
                                            ->label('Treatment Date'),
                                        Forms\Components\TextInput::make('treatment_method')
                                            ->label('Treatment Method')
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('chemicals_used')
                                            ->label('Chemicals Used')
                                            ->rows(2),
                                        Forms\Components\Select::make('outcome')
                                            ->label('Outcome')
                                            ->options([
                                                'resolved' => 'Resolved',
                                                'ongoing' => 'Ongoing',
                                                'unresolved' => 'Unresolved',
                                            ]),
                                        Forms\Components\TextInput::make('cost')
                                            ->label('Cost')
                                            ->numeric()
                                            ->prefix('$'),
                                        Forms\Components\Textarea::make('notes')
                                            ->label('Notes')
                                            ->rows(2),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(0)
                                    ->addable()
                                    ->deletable(),
                            ]),

                        // Step 6: Labor & Activities
                        Forms\Components\Wizard\Step::make('Labor & Activities')
                            ->icon('heroicon-o-users')
                            ->schema([
                                Forms\Components\Repeater::make('activities')
                                    ->label('Activities')
                                    ->schema([
                                        Forms\Components\TextInput::make('activity_name')
                                            ->label('Activity Name')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('description')
                                            ->label('Description')
                                            ->rows(2),
                                        Forms\Components\DatePicker::make('activity_date')
                                            ->label('Date')
                                            ->required(),
                                        Forms\Components\TextInput::make('cost')
                                            ->label('Cost')
                                            ->numeric()
                                            ->prefix('$'),
                                        Forms\Components\Select::make('labor_type')
                                            ->label('Labor Type')
                                            ->options([
                                                'manual' => 'Manual',
                                                'mechanized' => 'Mechanized',
                                                'contract' => 'Contract',
                                                'other' => 'Other',
                                            ]),
                                        Forms\Components\Select::make('staff_id')
                                            ->label('Staff')
                                            ->relationship('staff', 'fullName')
                                            ->searchable()
                                            ->preload(),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(0)
                                    ->addable()
                                    ->deletable(),
                            ]),

                        // Step 7: Weather & Conditions
                        Forms\Components\Wizard\Step::make('Weather & Conditions')
                            ->icon('heroicon-o-cloud')
                            ->schema([
                                Forms\Components\Repeater::make('weather')
                                    ->label('Weather Records')
                                    ->schema([
                                        Forms\Components\DateTimePicker::make('recorded_at')
                                            ->label('Recorded At')
                                            ->required(),
                                        Forms\Components\TextInput::make('temperature')
                                            ->label('Temperature (°C)')
                                            ->numeric(),
                                        Forms\Components\TextInput::make('humidity')
                                            ->label('Humidity (%)')
                                            ->numeric(),
                                        Forms\Components\TextInput::make('precipitation')
                                            ->label('Precipitation (mm)')
                                            ->numeric(),
                                        Forms\Components\TextInput::make('wind_speed')
                                            ->label('Wind Speed (m/s)')
                                            ->numeric(),
                                        Forms\Components\TextInput::make('wind_direction')
                                            ->label('Wind Direction (degrees)')
                                            ->numeric(),
                                        Forms\Components\TextInput::make('pressure')
                                            ->label('Pressure (hPa)')
                                            ->numeric(),
                                        Forms\Components\TextInput::make('uv_index')
                                            ->label('UV Index')
                                            ->numeric(),
                                        Forms\Components\TextInput::make('cloud_cover')
                                            ->label('Cloud Cover (%)')
                                            ->numeric(),
                                        Forms\Components\Textarea::make('weather_condition')
                                            ->label('Weather Condition')
                                            ->rows(1),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(0)
                                    ->addable()
                                    ->deletable(),
                            ]),

                        // Step 8: Growth Tracking
                        Forms\Components\Wizard\Step::make('Growth Tracking')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                Forms\Components\Repeater::make('stages')
                                    ->label('Growth Stages')
                                    ->schema([
                                        Forms\Components\TextInput::make('stage_name')
                                            ->label('Stage Name')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\DatePicker::make('start_date')
                                            ->label('Start Date'),
                                        Forms\Components\DatePicker::make('end_date')
                                            ->label('End Date'),
                                        Forms\Components\Select::make('health_status')
                                            ->label('Health Status')
                                            ->options([
                                                'excellent' => 'Excellent',
                                                'good' => 'Good',
                                                'fair' => 'Fair',
                                                'poor' => 'Poor',
                                            ]),
                                        Forms\Components\TextInput::make('germination_rate')
                                            ->label('Germination Rate (%)')
                                            ->numeric()
                                            ->maxValue(100),
                                        Forms\Components\Repeater::make('measurements')
                                            ->label('Measurements')
                                            ->schema([
                                                Forms\Components\DatePicker::make('measurement_date')
                                                    ->label('Date')
                                                    ->required(),
                                                Forms\Components\TextInput::make('height_cm')
                                                    ->label('Height (cm)')
                                                    ->numeric(),
                                                Forms\Components\TextInput::make('leaf_count')
                                                    ->label('Leaf Count')
                                                    ->numeric(),
                                                Forms\Components\TextInput::make('fruit_count')
                                                    ->label('Fruit Count')
                                                    ->numeric(),
                                                Forms\Components\Textarea::make('health_notes')
                                                    ->label('Health Notes')
                                                    ->rows(2),
                                            ])
                                            ->columns(2)
                                            ->defaultItems(0)
                                            ->addable()
                                            ->deletable(),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(0)
                                    ->addable()
                                    ->deletable(),
                            ]),

// Step 9: Harvest & Yield
                         Forms\Components\Wizard\Step::make('Harvest & Yield')
                             ->icon('heroicon-o-currency-dollar')
                             ->schema([
                                 Forms\Components\Repeater::make('harvests')
                                     ->label('Harvest Records')
                                     ->schema([
                                         Forms\Components\DatePicker::make('harvest_date')
                                             ->label('Harvest Date')
                                             ->required(),
                                         Forms\Components\Select::make('harvest_number')
                                             ->label('Harvest Number')
                                             ->options([
                                                 '1st' => '1st Harvest',
                                                 '2nd' => '2nd Harvest',
                                                 '3rd' => '3rd Harvest',
                                                 '4th' => '4th Harvest',
                                                 '5th' => '5th Harvest',
                                             ])
                                             ->required(),
                                         Forms\Components\TextInput::make('quantity_harvested')
                                             ->label('Total Quantity Harvested')
                                             ->numeric()
                                             ->required(),
                                         Forms\Components\Select::make('unit')
                                             ->label('Unit')
                                             ->options([
                                                 'kg' => 'Kilograms (kg)',
                                                 'crates' => 'Crates',
                                                 'bags' => 'Bags',
                                                 'bunches' => 'Bunches',
                                             ])
                                             ->default('kg')
                                             ->required(),
                                         Forms\Components\Select::make('quality_grade')
                                             ->label('Overall Grade')
                                             ->options([
                                                 'grade_1' => 'Grade 1',
                                                 'grade_2' => 'Grade 2',
                                                 'grade_3' => 'Grade 3',
                                                 'rejects' => 'Rejects',
                                             ])
                                             ->default('grade_2'),
                                         Forms\Components\Grid::make(3)
                                             ->schema([
                                                 Forms\Components\TextInput::make('grade_1_quantity')
                                                     ->label('Grade 1')
                                                     ->numeric()
                                                     ->default(0),
                                                 Forms\Components\TextInput::make('grade_2_quantity')
                                                     ->label('Grade 2')
                                                     ->numeric()
                                                     ->default(0),
                                                 Forms\Components\TextInput::make('rejects_quantity')
                                                     ->label('Rejects')
                                                     ->numeric()
                                                     ->default(0),
                                             ]),
                                         Forms\Components\Select::make('destination')
                                             ->label('Destination')
                                             ->options([
                                                 'store' => 'Store',
                                                 'sold_directly' => 'Sold Directly',
                                                 'processing' => 'Processing',
                                             ])
                                             ->default('store'),
                                         Forms\Components\TextInput::make('buyer_reference')
                                             ->label('Buyer/Sales Reference')
                                             ->maxLength(255),
                                         Forms\Components\Select::make('staff_id')
                                             ->label('Harvested By')
                                             ->relationship('staff', 'fullName')
                                             ->searchable()
                                             ->preload(),
                                         Forms\Components\TextInput::make('loss_quantity')
                                             ->label('Wastage/Rejects')
                                             ->numeric(),
                                         Forms\Components\Textarea::make('notes')
                                             ->label('Notes')
                                             ->rows(2),
                                     ])
                                     ->columns(2)
                                     ->defaultItems(0)
                                     ->addable()
                                     ->deletable(),
                             ]),

                        // Step 10: Sales & Profitability
                        Forms\Components\Wizard\Step::make('Sales & Profitability')
                            ->icon('heroicon-o-trending-up')
                            ->schema([
                                Forms\Components\Repeater::make('revenues')
                                    ->label('Sales Records')
                                    ->schema([
                                        Forms\Components\DatePicker::make('sale_date')
                                            ->label('Sale Date')
                                            ->required(),
                                        Forms\Components\Select::make('buyer_id')
                                            ->label('Buyer')
                                            ->relationship('buyer', 'name')
                                            ->searchable()
                                            ->preload(),
                                        Forms\Components\TextInput::make('quantity_sold')
                                            ->label('Quantity Sold')
                                            ->numeric()
                                            ->required(),
                                        Forms\Components\TextInput::make('unit')
                                            ->label('Unit')
                                            ->default('kg')
                                            ->maxLength(50),
                                        Forms\Components\TextInput::make('price_per_unit')
                                            ->label('Price per Unit')
                                            ->numeric()
                                            ->prefix('$'),
                                        Forms\Components\TextInput::make('amount')
                                            ->label('Total Amount')
                                            ->numeric()
                                            ->prefix('$')
                                            ->helperText('Auto-calculated if quantity and price are set'),
                                        Forms\Components\Select::make('payment_status')
                                            ->label('Payment Status')
                                            ->options([
                                                'pending' => 'Pending',
                                                'partial' => 'Partial',
                                                'paid' => 'Paid',
                                                'overdue' => 'Overdue',
                                            ])
                                            ->default('pending'),
                                        Forms\Components\DatePicker::make('payment_date')
                                            ->label('Payment Date'),
                                        Forms\Components\TextInput::make('payment_method')
                                            ->label('Payment Method')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('invoice_number')
                                            ->label('Invoice Number')
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('notes')
                                            ->label('Notes')
                                            ->rows(2),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(0)
                                    ->addable()
                                    ->deletable(),
                            ]),

                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('crop_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('field.name')
                    ->label('Field')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'planned' => 'gray',
                        'active' => 'success',
                        'completed' => 'info',
                        'cancelled' => 'danger',
                    ]),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expected_harvest_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('staff.fullName')
                    ->label('Responsible')
                    ->sortable(),
                Tables\Columns\TextColumn::make('variety')
                    ->searchable(),
                Tables\Columns\TextColumn::make('season')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCropCycles::route('/'),
            'create' => Pages\CreateCropCycle::route('/create'),
            'edit' => Pages\EditCropCycle::route('/{record}/edit'),
        ];
    }
}
