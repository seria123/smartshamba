<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;

class AutomationController extends Controller
{
    /**
     * Redirect to automation_rules index for backwards compatibility.
     */
    public function index()
    {
        return Redirect::route('automation_rules.index');
    }
}
