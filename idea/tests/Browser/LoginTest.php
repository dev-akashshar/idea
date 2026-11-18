
<?php

use App\Models\User;

test('login a user', function () {

    $user = User::factory()->create([
        'password' => bcrypt('password123'),
    ]);

    visit('/login')
        ->fill('email', $user->email)
        ->fill('password', 'password123')
        ->click('@login-button')
        ->assertPathIs('/');

    $this->assertAuthenticated();
});

test('logs out a user', function () {

    $user = User::factory()->create();

    $this->actingAs($user);

    visit('/')
        ->click('@logout-button');

    $this->assertGuest();
});
