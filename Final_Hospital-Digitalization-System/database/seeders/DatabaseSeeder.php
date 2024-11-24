<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Obat;
use App\Models\Dokter;
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
                    $dokter = Dokter::factory()->create(['dokter_id' => $user->id]);
            
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
            ]);

        User::factory() // Random Tester
            ->count(5)
            ->create()
            ->each(function ($user) {
                $user->password = Hash::make('tester123');
                $user->email_verified_at = now();
                $user->save();
                
                if ($user->role === 'dokter') {
                    $dokter = Dokter::factory()->create(['dokter_id' => $user->id]);
            
                    JadwalTugas::factory()->create([
                        'dokter_id' => $dokter->id,
                    ]);
                }
            });

        User::factory() // Random
            ->count(50)
            ->create()
            ->each(function ($user) {

                if ($user->role === 'dokter') {
                    $dokter = Dokter::factory()->create(['dokter_id' => $user->id]);
            
                    JadwalTugas::factory()->create([
                        'dokter_id' => $dokter->id,
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
            ->hasAttached(Obat::inRandomOrder()->limit(3)->get())
            ->create();
        PenjadwalanKonsultasi::factory()->count(15)->create();
    }
}