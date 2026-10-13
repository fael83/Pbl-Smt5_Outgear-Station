<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => Role::Customer,
            'no_telepon' => fake()->numerify('08##########'),
            'status_akun' => 'aktif',
        ];
    }

    /**
     * Pengguna dengan peran admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => ['role' => Role::Admin]);
    }

    /**
     * Pengguna dengan peran petugas (ERD: PENGGUNA.peran).
     */
    public function petugas(): static
    {
        return $this->state(fn (array $attributes) => ['role' => Role::Petugas]);
    }

    /**
     * Pengguna dengan peran owner.
     */
    public function owner(): static
    {
        return $this->state(fn (array $attributes) => ['role' => Role::Owner]);
    }

    /**
     * Pengguna dengan peran pelanggan.
     */
    public function customer(): static
    {
        return $this->state(fn (array $attributes) => ['role' => Role::Customer]);
    }

    /**
     * Akun yang dinonaktifkan.
     */
    public function nonaktif(): static
    {
        return $this->state(fn (array $attributes) => ['status_akun' => 'nonaktif']);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
