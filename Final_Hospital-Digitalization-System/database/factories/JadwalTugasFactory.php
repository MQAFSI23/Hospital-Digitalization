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
        // Ambil dokter secara acak (untuk memastikan, kita bisa mengabaikan ini karena sudah diatur di seeder)
        $dokter = Dokter::inRandomOrder()->first();

        // Definisikan waktu slot yang tersedia
        $timeSlots = [
            ['start' => '08:00:00', 'end' => '10:00:00'],
            ['start' => '13:00:00', 'end' => '15:00:00'],
            ['start' => '16:00:00', 'end' => '18:00:00'],
        ];
        
        // Pilih waktu slot secara acak
        $selectedSlot = $this->faker->randomElement($timeSlots);

        return [
            // ID dokter yang ditetapkan untuk jadwal tugas ini
            'dokter_id' => $dokter->id,
            // Hari tugas secara acak
            'hari_tugas' => $this->faker->randomElement(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']),
            // Jam mulai dan jam selesai untuk jadwal tugas
            'jam_mulai' => $selectedSlot['start'],
            'jam_selesai' => $selectedSlot['end'],
        ];
    }
}