<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Obat;
use App\Models\Resep;
use App\Models\Dokter;
use App\Models\Pasien;
use App\Models\LogObat;
use App\Models\Feedback;
use App\Models\RekamMedis;
use App\Models\JadwalTugas;
use App\Models\TindakanMedis;
use Illuminate\Support\Facades\Hash;
use App\Models\PenjadwalanKonsultasi;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory() // Admint
            ->create([
                'name' => 'Admintz',
                'email' => 'admin@example.com',
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now()
            ]);

        User::factory() // Dokter
            ->create([
                'name' => 'Jamal',
                'email' => 'dokter@example.com',
                'username' => 'dokter',
                'password' => Hash::make('dokter123'),
                'role' => 'dokter',
                'email_verified_at' => now()
            ])
            ->each(function ($user) {
                
                if ($user->role === 'dokter') {
                    $dokter = Dokter::factory()->create(['user_id' => $user->id]);
            
                    JadwalTugas::factory()->create([
                        'dokter_id' => $dokter->id,
                    ]);
                }
            });

        User::factory() // Pasien
            ->create([
                'name' => 'Asep',
                'email' => 'pasien@example.com',
                'username' => 'pasien',
                'password' => Hash::make('pasien123'),
                'role' => 'pasien',
                'email_verified_at' => now()
            ])
            ->each(function ($user) {
                Pasien::create([
                    'user_id' => $user->id,
                    'berat_badan' => fake()->randomFloat(1, 40, 120), // Berat badan antara 40-120 kg
                    'tinggi_badan' => fake()->randomFloat(1, 140, 200), // Tinggi badan antara 140-200 cm
                ]);
            });

        User::factory() // Random Tester
            ->count(30)
            ->create()
            ->each(function ($user) {
                $user->password = Hash::make('tester123');
                $user->email_verified_at = now();
                $user->save();
                
                if ($user->role === 'dokter') {
                    $dokter = Dokter::factory()->create(['user_id' => $user->id]);
            
                    JadwalTugas::factory()->create([
                        'dokter_id' => $dokter->id,
                    ]);
                } elseif ($user->role === 'pasien') {
                    Pasien::create([
                        'user_id' => $user->id,
                        'berat_badan' => fake()->randomFloat(1, 40, 120), // Berat badan antara 40-120 kg
                        'tinggi_badan' => fake()->randomFloat(1, 140, 200), // Tinggi badan antara 140-200 cm
                    ]);
                }
            });

        User::factory() // Random
            ->count(10)
            ->create()
            ->each(function ($user) {

                if ($user->role === 'dokter') {
                    $dokter = Dokter::factory()->create(['user_id' => $user->id]);
            
                    JadwalTugas::factory()->create([
                        'dokter_id' => $dokter->id,
                    ]);
                } elseif ($user->role === 'pasien') {
                    Pasien::create([
                        'user_id' => $user->id,
                        'berat_badan' => fake()->randomFloat(1, 40, 120), // Berat badan antara 40-120 kg
                        'tinggi_badan' => fake()->randomFloat(1, 140, 200), // Tinggi badan antara 140-200 cm
                    ]);
                }
            });

        Obat::factory()
            ->count(30)
            ->create()
            ->each(function ($obat) {
                LogObat::factory()->create([
                    'obat_id' => $obat->id,
                    'jumlah' => $obat->stok,
                    'status' => 'terisi',
                ]);
            });
        Feedback::factory()->count(20)->create();

        RekamMedis::factory()
            ->count(15)
            ->has(
                Resep::factory()
                    ->count(3)
                    ->state(function (array $attributes, RekamMedis $rekamMedis) {
                        return [
                            'rekam_medis_id' => $rekamMedis->id,
                            'obat_id' => Obat::all()->random()->id, // Ambil obat secara acak
                        ];
                    }),
                'resep'
            )
            ->create();

        PenjadwalanKonsultasi::factory()->count(15)->create();
    }
}