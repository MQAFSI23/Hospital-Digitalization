<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {   
        $username = $this->faker->unique()->userName;
        $name = $this->faker->name;
        $role = $this->faker->randomElement(['dokter', 'pasien']);

        if (strpos($username, '.') !== false) {
            $username = preg_replace('/\.(?=\S)/', '', $username);
        }

        if ($role === 'dokter') {
            if (strpos($name, 'Prof.') !== false) {
                $name = preg_replace('/^Prof\.\s*/', '', $name);
                $name = 'Prof. Dr. dr. ' . $name;
            } elseif (strpos($name, 'Dr.') !== false) {
                $name = preg_replace('/^Dr\.\s*/', '', $name);
                $name = 'Dr. dr. ' . $name;
            } else {
                $name = 'dr. ' . $name;
            }
        }

        return [
            'name' => $name,
            'username' => $username,
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make('password'),
            'role' => $role,
            'tanggal_lahir' => $this->faker->dateTimeBetween('-80 years', '-20 years')->format('Y-m-d'),
            'jenis_kelamin' => $this->faker->randomElement(['pria', 'wanita']),
        ];
    }
}