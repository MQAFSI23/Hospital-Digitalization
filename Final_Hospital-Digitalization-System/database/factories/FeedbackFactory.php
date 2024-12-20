<?php

namespace Database\Factories;

use App\Models\Feedback;
use App\Models\Pasien;
use App\Models\Dokter;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeedbackFactory extends Factory
{
    protected $model = Feedback::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $dokter = Dokter::inRandomOrder()->first();
        $pasien = Pasien::inRandomOrder()->first();

        return [
            'dokter_id' => $dokter->id,
            'pasien_id' => $pasien->id,
            'rating' => $this->faker->numberBetween(1, 5),
            'ulasan' => $this->faker->sentence(),
        ];
    }
}