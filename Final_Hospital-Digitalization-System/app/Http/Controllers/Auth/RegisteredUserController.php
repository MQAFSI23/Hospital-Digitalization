<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Dokter;
use App\Models\JadwalTugas;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the patient registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function storePasien(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:127'],
            'tanggal_lahir' => ['required', 'date', 'before_or_equal:today'],
            'jenis_kelamin' => ['required', 'in:pria,wanita'],
            'username' => ['required', 'string', 'max:15', 'unique:users,username', 'alpha_num'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:127', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'pasien',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('pasien.dashboard'));
    }

    /**
     * Display the admin registration view.
     */
    public function createAdmin(): View
    {
        return view('auth.register-admin');
    }

    /**
     * Handle an incoming admin registration request.
     */
    public function storeAdmin(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:127'],
            'tanggal_lahir' => ['required', 'date', 'before_or_equal:today'],
            'jenis_kelamin' => ['required', 'in:pria,wanita'],
            'username' => ['required', 'string', 'max:15', 'unique:users,username', 'alpha_num'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:127', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,dokter,pasien'],
            'jadwal_tugas' => 'required_if:role,dokter|array',
            'jadwal_tugas.*' => 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jenis_dokter' => 'nullable|required_if:role,dokter|in:umum,spesialis',
            'spesialisasi' => 'nullable|required_if:jenis_dokter,spesialis|in:kardiologi,neurologi,gastroenterologi,pediatri,pulmonologi',
            'admin_password' => 'required',
        ]);

        $admin = Auth::user();

        if (!Hash::check($request->admin_password, $admin->password)) {

            return redirect()->route('admin.dashboard')->with('error', 'Password admin tidak valid. Pengguna gagal didaftar.');
        }

        $user = User::create([
            'name' => $request->name,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        if ($request->role === 'dokter') {
            $dokter = Dokter::create([
                'dokter_id' => $user->id,
                'jenis_dokter' => $request->jenis_dokter,
                'spesialisasi' => $request->jenis_dokter === 'spesialis' ? $request->spesialisasi : null,
            ]);

            foreach ($request->jadwal_tugas as $hari) {
                JadwalTugas::create([
                    'dokter_id' => $dokter->id,
                    'hari_tugas' => $hari,
                ]);
            }
        }

        event(new Registered($user));

        return redirect()->route('admin.dashboard')->with('success', 'Pengguna baru berhasil ditambahkan.');
    }
}
