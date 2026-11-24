<?php

use App\Models\User;
use App\Notifications\EmailChangedNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

test('guests cannot visit the edit profile page', function () {
    $this->get(route('profile.edit'))
        ->assertRedirect(route('login'));
});

test('authenticated users can visit the edit profile page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('profile.edit'))
        ->assertOk()
        ->assertSee($user->name)
        ->assertSee($user->email);
});

test('users can update their name and email', function () {
    $user = User::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@example.com',
    ]);

    $this->actingAs($user)
        ->patch(route('profile.update'), [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ])
        ->assertRedirect(route('profile.edit'));

    $user->refresh();

    expect($user->name)->toBe('Updated Name');
    expect($user->email)->toBe('updated@example.com');
});

test('users can update their password', function () {
    $user = User::factory()->create([
        'password' => 'oldpassword123',
    ]);

    $this->actingAs($user)
        ->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'newpassword123',
        ])
        ->assertRedirect(route('profile.edit'));

    $user->refresh();

    expect(Hash::check('newpassword123', $user->password))->toBeTrue();
});

test('email must be unique except for the current user', function () {
    $john = User::factory()->create(['email' => 'john@example.com']);
    $jane = User::factory()->create(['email' => 'jane@example.com']);

    $this->actingAs($jane)
        ->patch(route('profile.update'), [
            'name' => 'Jane',
            'email' => 'john@example.com',
        ])
        ->assertSessionHasErrors('email');

    $this->actingAs($jane)
        ->patch(route('profile.update'), [
            'name' => 'Jane Updated',
            'email' => 'jane@example.com',
        ])
        ->assertSessionHasNoErrors();
});

test('a notification is sent when email changes', function () {
    Notification::fake();

    $user = User::factory()->create(['email' => 'old@example.com']);

    $this->actingAs($user)
        ->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => 'new@example.com',
        ]);

    Notification::assertSentTo($user, EmailChangedNotification::class);
});
