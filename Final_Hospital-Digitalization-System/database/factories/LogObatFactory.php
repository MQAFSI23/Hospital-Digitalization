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
        return [
            'obat_id' => Obat::inRandomOrder()->first()->id ?? Obat::factory(),
            'status' => $this->faker->randomElement(['terisi', 'terjual']),
            'jumlah' => $this->faker->numberBetween(1, 50),
            'tanggal_log' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}