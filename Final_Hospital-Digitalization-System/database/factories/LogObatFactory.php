<?php

namespace Database\Factories;

use App\Models\LogObat;
use App\Models\Obat;
use Illuminate\Database\Eloquent\Factories\Factory;

class LogObatFactory extends Factory
{
    protected $model = LogObat::class;

    public function definition()
    {
        $obat = $this->faker->randomElement(Obat::all());
        return [
            'obat_id' => $obat->id,  // Mengambil ID obat yang baru saja dibuat
            'status' => 'terisi',
            'jumlah' => $obat->stok,  // Menentukan jumlah log berdasarkan stok obat
            'tanggal_log' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}