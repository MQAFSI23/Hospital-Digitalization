<?php

namespace Database\Factories;

use App\Models\Resep;
use App\Models\RekamMedis;
use App\Models\Obat;
use App\Models\Dokter;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResepFactory extends Factory
{
    protected $model = Resep::class;

    public function definition()
    {
        return [
            'rekam_medis_id' => RekamMedis::factory(),
            'obat_id' => Obat::factory(),
            'dosis' => '3x sehari',
            'jumlah' => 7,
            'aturan_pakai' => 'Setelah makan',
            'created_by' => Dokter::factory(),
        ];
    }
}