<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        return $request->user()->hasVerifiedEmail()
                    ? $this->redirectToRoleBasedDashboard($request->user())
                    : view('auth.verify-email');
    }

    protected function redirectToRoleBasedDashboard($user)
    {
        if ($user->role == 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role == 'dokter') {
            return redirect()->route('dokter.dashboard');
        }

        if ($user->role == 'pasien') {
            return redirect()->route('pasien.dashboard');
        }
    }
}
