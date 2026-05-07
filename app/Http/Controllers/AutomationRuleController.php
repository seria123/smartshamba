<?php

namespace App\Http\Controllers;

use App\Enums\SensorType;
use App\Models\AutomationRule;
use App\Models\Field;
use Illuminate\Http\Request;

class AutomationRuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rules = AutomationRule::with('field')->get();

        return view('automation_rules.index', compact('rules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $fields = Field::all();
        $sensorTypes = SensorType::values();
        $operators = ['<', '<=', '>', '>=', '==', '!='];
        $actions = [
            'irrigation_on' => 'Turn Irrigation ON 💧',
            'irrigation_off' => 'Turn Irrigation OFF 🚫',
            'cooling_on' => 'Activate Cooling ❄️',
            'cooling_off' => 'Deactivate Cooling 🔥',
            'shade_on' => 'Activate Shade ☂️',
            'shade_off' => 'Deactivate Shade ☀️',
            'alert' => 'Send Alert ⚠️',
        ];

        return view('automation_rules.create', compact('fields', 'sensorTypes', 'operators', 'actions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'field_id' => 'required|exists:fields,id',
            'name' => 'required|string|max:255',
            'sensor_type' => 'required|string|in:'.implode(',', SensorType::values()),
            'operator' => 'required|string|in:<,<=,>,>=,==,!=',
            'threshold' => 'required|numeric',
            'action' => 'required|string|in:irrigation_on,irrigation_off,cooling_on,cooling_off,shade_on,shade_off,alert',
            'is_active' => 'boolean',
            'cooldown_minutes' => 'integer|min:1|max:1440',
        ]);

        AutomationRule::create($validated);

        return redirect()->route('automation_rules.index')
            ->with('success', 'Automation rule created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AutomationRule $automationRule)
    {
        $automationRule->load('field');

        return view('automation_rules.show', compact('automationRule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AutomationRule $automationRule)
    {
        $fields = Field::all();
        $sensorTypes = SensorType::values();
        $operators = ['<', '<=', '>', '>=', '==', '!='];
        $actions = [
            'irrigation_on' => 'Turn Irrigation ON 💧',
            'irrigation_off' => 'Turn Irrigation OFF 🚫',
            'cooling_on' => 'Activate Cooling ❄️',
            'cooling_off' => 'Deactivate Cooling 🔥',
            'shade_on' => 'Activate Shade ☂️',
            'shade_off' => 'Deactivate Shade ☀️',
            'alert' => 'Send Alert ⚠️',
        ];

        return view('automation_rules.edit', compact('automationRule', 'fields', 'sensorTypes', 'operators', 'actions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AutomationRule $automationRule)
    {
        $validated = $request->validate([
            'field_id' => 'required|exists:fields,id',
            'name' => 'required|string|max:255',
            'sensor_type' => 'required|string|in:'.implode(',', SensorType::values()),
            'operator' => 'required|string|in:<,<=,>,>=,==,!=',
            'threshold' => 'required|numeric',
            'action' => 'required|string|in:irrigation_on,irrigation_off,cooling_on,cooling_off,shade_on,shade_off,alert',
            'is_active' => 'boolean',
            'cooldown_minutes' => 'integer|min:1|max:1440',
        ]);

        $automationRule->update($validated);

        return redirect()->route('automation_rules.index')
            ->with('success', 'Automation rule updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AutomationRule $automationRule)
    {
        $automationRule->delete();

        return redirect()->route('automation_rules.index')
            ->with('success', 'Automation rule deleted successfully.');
    }

    /**
     * Toggle the active status of a rule
     */
    public function toggle(AutomationRule $automationRule)
    {
        $automationRule->update(['is_active' => ! $automationRule->is_active]);

        $status = $automationRule->is_active ? 'enabled' : 'disabled';

        return redirect()->back()
            ->with('success', "Automation rule {$status}.");
    }

    /**
     * Manually trigger a rule for testing
     */
    public function trigger(AutomationRule $automationRule, Request $request)
    {
        $request->validate([
            'test_value' => 'required|numeric',
        ]);

        $value = $request->input('test_value');
        $result = $automationRule->evaluate($value);

        return redirect()->back()
            ->with('info', "Rule evaluation result for value {$value}: ".($result ? 'TRIGGERED' : 'NOT TRIGGERED'));
    }
}
