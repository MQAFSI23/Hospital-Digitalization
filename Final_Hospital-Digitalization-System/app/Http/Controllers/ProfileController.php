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
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user()->load('pasien');
        return view('partials.edit-profile', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user()->load('pasien');
        $user->fill($request->validated());
    
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
    
        $user->save();
    
        if ($user->role === 'pasien') {
            $validated = $request->validated();
            $pasien = $user->pasien;
            
            if ($pasien) {
                $pasien->update([
                    'berat_badan' => $validated['berat_badan'] ?? $pasien->berat_badan,
                    'tinggi_badan' => $validated['tinggi_badan'] ?? $pasien->tinggi_badan,
                ]);
            } else {
                $user->pasien()->create([
                    'berat_badan' => $validated['berat_badan'],
                    'tinggi_badan' => $validated['tinggi_badan'],
                ]);
            }
        }
    
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }    

    /**
     * Delete the user's account.
     */
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
