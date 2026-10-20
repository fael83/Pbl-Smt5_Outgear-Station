<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'no_telepon' => '081234567890',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect('/katalog');

    $user = User::query()->where('email', 'test@example.com')->firstOrFail();

    expect($user->role)->toBe(Role::Customer)
        ->and($user->status_akun)->toBe('aktif')
        ->and($user->no_telepon)->toBe('081234567890')
        ->and(Hash::check('password', $user->password))->toBeTrue();
});

test('registration rejects an invalid indonesian phone number', function () {
    $response = $this->from('/register')->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'no_telepon' => '12345',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response
        ->assertRedirect('/register')
        ->assertSessionHasErrors('no_telepon');

    $this->assertGuest();
    $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
});
