<?php

namespace Database\Factories;

use App\Models\JadwalTugas;
use App\Models\Dokter;
use Illuminate\Database\Eloquent\Factories\Factory;

class JadwalTugasFactory extends Factory
{
    protected $model = JadwalTugas::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $dokter = Dokter::inRandomOrder()->first();
        $timeSlots = [
            ['start' => '08:00:00', 'end' => '10:00:00'],
            ['start' => '13:00:00', 'end' => '15:00:00'],
            ['start' => '16:00:00', 'end' => '18:00:00'],
        ];
        $selectedSlot = $this->faker->randomElement($timeSlots);

        return [
            'dokter_id' => $dokter->id,
            'hari_tugas' => $this->faker->randomElement(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']),
            'jam_mulai' => $selectedSlot['start'],
            'jam_selesai' => $selectedSlot['end'],
        ];
    }
}