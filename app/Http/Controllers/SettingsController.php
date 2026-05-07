<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        $user = Auth::user();
        $farm = $user->farm ?? null;

        return view('settings.index', compact('user', 'farm'));
    }

    /**
     * Update profile settings.
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $user->update($request->only([
            'first_name',
            'last_name',
            'email',
            'phone',
            'address',
        ]));

        return redirect()->route('settings.index')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Update farm settings.
     */
    public function updateFarm(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'subcounty' => 'nullable|string|max:100',
            'physical_address' => 'nullable|string|max:255',
            'farm_type' => 'nullable|string|max:100',
            'ownership_type' => 'nullable|string|max:100',
            'size_hectares' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'storage_facilities' => 'nullable|boolean',
            'estimated_budget' => 'nullable|numeric|min:0',
            'main_purpose' => 'nullable|string|max:100',
        ]);

        $user = Auth::user();
        $farm = $user->farm ?? Farm::create([
            'user_id' => $user->id,
        ]);

        $farm->update($request->only([
            'name',
            'location',
            'subcounty',
            'physical_address',
            'farm_type',
            'ownership_type',
            'size_hectares',
            'description',
            'storage_facilities',
            'estimated_budget',
            'main_purpose',
        ]));

        return redirect()->route('settings.index')
            ->with('success', 'Farm settings updated successfully.');
    }

    /**
     * Update notification settings.
     */
    public function updateNotifications(Request $request)
    {
        $request->validate([
            'email_alerts' => 'nullable|boolean',
            'sms_alerts' => 'nullable|boolean',
            'push_notifications' => 'nullable|boolean',
            'weather_alerts' => 'nullable|boolean',
            'disease_alerts' => 'nullable|boolean',
            'market_prices' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $preferences = $user->preferences ?? [];
        $preferences['notifications'] = $request->only([
            'email_alerts',
            'sms_alerts',
            'push_notifications',
            'weather_alerts',
            'disease_alerts',
            'market_prices',
        ]);

        $user->update(['preferences' => $preferences]);

        return redirect()->route('settings.index')
            ->with('success', 'Notification settings updated successfully.');
    }

    /**
     * Update system settings.
     */
    public function updateSystem(Request $request)
    {
        $request->validate([
            'date_format' => 'required|in:YYYY-MM-DD,DD/MM/MM,MM/DD/YYYY',
            'time_format' => 'required|in:24h,12h',
            'measurement_system' => 'required|in:metric,imperial',
            'currency' => 'required|string|max:10',
            'language' => 'required|string|max:10',
        ]);

        $user = Auth::user();
        $preferences = $user->preferences ?? [];
        $preferences['system'] = $request->only([
            'date_format',
            'time_format',
            'measurement_system',
            'currency',
            'language',
        ]);

        $user->update(['preferences' => $preferences]);

        return redirect()->route('settings.index')
            ->with('success', 'System settings updated successfully.');
    }

    /**
     * Update password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'The current password is incorrect.',
            ]);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('settings.index')
            ->with('success', 'Password updated successfully.');
    }
}