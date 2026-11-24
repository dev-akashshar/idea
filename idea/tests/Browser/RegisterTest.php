
<?php

use Illuminate\Support\Str;

test('register a user', function () {

    $email = 'akash'.Str::random(8).'@example.com';

    visit('/register')
        ->fill('name', 'Akash Sharma')
        ->fill('email', $email)
        ->fill('password', 'password123')
        ->click('Create Account')
        ->assertPathIs('/');

    $this->assertDatabaseHas('users', [
        'name' => 'Akash Sharma',
        'email' => $email,
    ]);
});
test('requires a valid email', function () {

    $email = 'akash'.Str::random(8).'@example.com';

    visit('/register')
        ->fill('name', 'Akash Sharma')
        ->fill('email', $email)
        ->fill('password', 'error')
        ->debug();
});
