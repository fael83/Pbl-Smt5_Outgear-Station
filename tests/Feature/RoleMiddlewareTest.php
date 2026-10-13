<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::middleware(['web', 'role:admin'])
        ->get('/_test/admin-only', fn () => 'admin area');

    Route::middleware(['web', 'role:admin,petugas'])
        ->get('/_test/staff-only', fn () => 'staff area');
});

it('allows a pengguna whose role is listed', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get('/_test/admin-only')
        ->assertOk()
        ->assertSee('admin area');

    $this->actingAs(User::factory()->petugas()->create())
        ->get('/_test/staff-only')
        ->assertOk();

    $this->actingAs(User::factory()->admin()->create())
        ->get('/_test/staff-only')
        ->assertOk();
});

it('forbids a pengguna whose role is not listed', function () {
    $this->actingAs(User::factory()->petugas()->create())
        ->get('/_test/admin-only')
        ->assertForbidden();

    $this->actingAs(User::factory()->owner()->create())
        ->get('/_test/admin-only')
        ->assertForbidden();

    $this->actingAs(User::factory()->customer()->create())
        ->get('/_test/admin-only')
        ->assertForbidden();

    $this->actingAs(User::factory()->customer()->create())
        ->get('/_test/staff-only')
        ->assertForbidden();
});

it('redirects a guest to the login page', function () {
    $this->get('/_test/admin-only')->assertRedirect('/login');
});

it('rejects an unknown role in the route definition', function () {
    Route::middleware(['web', 'role:superadmin'])
        ->get('/_test/unknown-role', fn () => 'never');

    $this->actingAs(User::factory()->admin()->create())
        ->get('/_test/unknown-role')
        ->assertForbidden();
});
