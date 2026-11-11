<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable(['name', 'email', 'password', 'role', 'no_telepon', 'status_akun'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected static function booted(): void
    {
        $lupakan = static fn (self $user) => \Illuminate\Support\Facades\Cache::forget(
            \App\Auth\CachedEloquentUserProvider::kunci($user->getKey())
        );

        static::saved($lupakan);
        static::deleted($lupakan);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
        ];
    }

    /**
     * Cek apakah pengguna memiliki salah satu peran yang diberikan.
     */
    public function hasRole(Role ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }

    public function isPetugas(): bool
    {
        return $this->role === Role::Petugas;
    }

    public function isOwner(): bool
    {
        return $this->role === Role::Owner;
    }

    public function isCustomer(): bool
    {
        return $this->role === Role::Customer;
    }

    /**
     * Cek apakah pengguna termasuk tim internal (bukan pelanggan).
     */
    public function isStaff(): bool
    {
        return $this->hasRole(Role::Admin, Role::Petugas, Role::Owner);
    }

    /**
     * Cek apakah akun aktif (ERD: PENGGUNA.status_akun).
     */
    public function isAktif(): bool
    {
        return $this->status_akun === 'aktif';
    }

    /**
     * Inisial nama untuk avatar (maksimal dua huruf kapital).
     */
    public function inisial(): string
    {
        $inisial = Str::of($this->name)
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(static fn (string $kata): string => Str::upper(Str::substr($kata, 0, 1)))
            ->implode('');

        return $inisial !== '' ? $inisial : 'AD';
    }

    /**
     * Label peran untuk ditampilkan di sidebar/topbar panel.
     */
    public function labelPeran(): string
    {
        return $this->role?->label() ?? 'Staf Internal';
    }

    /**
     * Pemesanan yang dibuat oleh pengguna ini (ERD: pelanggan_id).
     *
     * @return HasMany<Pemesanan, $this>
     */
    public function pemesanan(): HasMany
    {
        return $this->hasMany(Pemesanan::class);
    }

    /**
     * Pengembalian yang diproses pengguna ini (ERD: petugas_id).
     *
     * @return HasMany<Pengembalian, $this>
     */
    public function pengembalian(): HasMany
    {
        return $this->hasMany(Pengembalian::class);
    }

    /**
     * Serah terima yang diproses pengguna ini (ERD: petugas_id).
     *
     * @return HasMany<SerahTerima, $this>
     */
    public function serahTerima(): HasMany
    {
        return $this->hasMany(SerahTerima::class);
    }

    /**
     * Aturan denda yang terakhir diubah pengguna ini (ERD: diubah_oleh).
     *
     * @return HasMany<AturanDenda, $this>
     */
    public function aturanDenda(): HasMany
    {
        return $this->hasMany(AturanDenda::class);
    }
}
