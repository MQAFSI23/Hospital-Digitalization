@extends('layouts.app2')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="py-10">
        <h1 class="text-3xl font-bold text-gray-800">Admin Dashboard</h1>
        <p class="mt-4 text-gray-600">Halo, {{ auth()->user()->name }}. Selamat datang di halaman Admin Dashboard!</p>

        <div class="mt-8">
            <a href="{{ route('register-admin') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                Register User Baru
            </a>
        </div>
    </div>
@endsection