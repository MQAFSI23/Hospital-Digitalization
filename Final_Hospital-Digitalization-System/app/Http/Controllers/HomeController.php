<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function adminDashboard()
    {
        return view('admin.dashboard');
    }

    public function dokterDashboard()
    {
        return view('dokter.dashboard');
    }

    public function pasienDashboard()
    {
        return view('pasien.dashboard');
    }
}