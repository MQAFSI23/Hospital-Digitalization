<?php

namespace Database\Factories;

use App\Models\PenjadwalanKonsultasi;
use App\Models\User;
use App\Models\Dokter;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class PenjadwalanKonsultasiFactory extends Factory
{
    protected $model = PenjadwalanKonsultasi::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $pasien = User::where('role', 'pasien')->inRandomOrder()->first();
        $dokter = Dokter::inRandomOrder()->first();

        return [
            'pasien_id' => $pasien->id,
            'dokter_id' => $dokter->id,
            'tanggal_konsultasi' => Carbon::now()->addDays(rand(3, 10)),
        ];
    }
}