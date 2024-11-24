@extends('layouts.app2')

@section('title', 'Dashboard')

@section('content')
    <div class="py-10">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
        <p class="mt-4 text-gray-600">Halo, {{ auth()->user()->name }}👋</p>
    </div>
@endsection