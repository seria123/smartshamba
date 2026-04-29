<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class VerificationController extends Controller
{
    /**
     * Show the email verification notice.
     */
    public function show(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('dashboard'))
                    : view('auth.verify-email');
    }

    /**
     * Mark the authenticated user's email address as verified.
     */
    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return redirect()->intended(route('dashboard'))
                        ->with('success', 'Your email has been verified.');
    }

    /**
     * Resend the email verification notification.
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard'));
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('resent', 'Verification link resent!');
    }
}