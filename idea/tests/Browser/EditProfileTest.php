<?php

use App\Models\User;

test('an authenticated user can navigate to and update their profile', function () {
    $user = User::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@example.com',
    ]);

    $this->actingAs($user);

    visit('/')
        ->click('@edit-profile-link')
        ->assertPathIs('/profile/edit')
        ->assertValue('name', 'Original Name')
        ->assertValue('email', 'original@example.com')
        ->fill('name', 'Jane Updated')
        ->fill('email', 'jane.updated@example.com')
        ->click('@update-profile-button')
        ->assertSee('Profile updated successfully!');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Jane Updated',
        'email' => 'jane.updated@example.com',
    ]);
});
