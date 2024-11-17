<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->redirectToRoleBasedDashboard($request->user());
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return $this->redirectToRoleBasedDashboard($request->user());
    }

    protected function redirectToRoleBasedDashboard($user)
    {
        if ($user->role == 'admin') {
            return redirect()->route('admin.dashboard')->with('verified', 1);
        }
    
        if ($user->role == 'dokter') {
            return redirect()->route('dokter.dashboard')->with('verified', 1);
        }
    
        if ($user->role == 'pasien') {
            return redirect()->route('pasien.dashboard')->with('verified', 1);
        }
    }
}