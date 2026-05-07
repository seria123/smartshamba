<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function updatePreferences(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'preferred_language' => 'nullable|string|in:en,sw,fr,lu',
            'notification_preferences' => 'nullable|array',
            'notification_preferences.*' => 'boolean',
            'weather_alerts' => 'boolean',
            'ai_recommendations' => 'boolean',
        ]);

        // Convert checkboxes: when unchecked, they are not sent; ensure all keys exist as booleans
        $preferences = $validated['notification_preferences'] ?? [];
        // Ensure all possible keys have boolean values
        $allKeys = ['email', 'weather', 'crop', 'livestock', 'finance', 'marketing'];
        foreach ($allKeys as $key) {
            if (! array_key_exists($key, $preferences)) {
                $preferences[$key] = false;
            } else {
                $preferences[$key] = (bool) $preferences[$key];
            }
        }
        $validated['notification_preferences'] = $preferences;

        $user = $request->user();
        $user->fill($validated);
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'preferences-updated');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $request->user()->update([
            'password' => bcrypt($request->password),
        ]);

        return Redirect::route('profile.edit')->with('status', 'password-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
