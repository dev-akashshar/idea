<?php

use App\IdeaStatus;
use App\Models\Idea;
use App\Models\User;

test('an authenticated user can create an idea', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    visit('/')
        ->click('@create-idea-button')
        ->fill('title', 'My First Idea')
        ->fill('description', 'This is a description of my first idea.')
        ->click('@create-idea-submit-button')
        ->assertSee('My First Idea');

    $this->assertDatabaseHas('ideas', [
        'title' => 'My First Idea',
        'description' => 'This is a description of my first idea.',
        'user_id' => $user->id,
        'status' => IdeaStatus::PENDING->value,
    ]);
});

test('an authenticated user can create an idea with a specific status', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    visit('/')
        ->click('@create-idea-button')
        ->fill('title', 'Idea In Progress')
        ->click('@status-in_progress')
        ->fill('description', 'This idea is in progress.')
        ->click('@create-idea-submit-button')
        ->assertSee('Idea In Progress');

    $this->assertDatabaseHas('ideas', [
        'title' => 'Idea In Progress',
        'description' => 'This idea is in progress.',
        'user_id' => $user->id,
        'status' => IdeaStatus::IN_PROGRESS->value,
    ]);
});

test('an authenticated user can create an idea with one or many links', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    visit('/')
        ->click('@create-idea-button')
        ->fill('title', 'Idea With Multiple Links')
        ->fill('description', 'Testing multiple links on an idea.')
        ->fill('links[]', 'https://laracasts.com')
        ->click('@add-link-button')
        ->fill('links[]', 'https://laravel.com')
        ->click('@create-idea-submit-button')
        ->assertSee('Idea With Multiple Links');

    $idea = Idea::where('title', 'Idea With Multiple Links')->first();

    expect($idea)->not->toBeNull();
    expect($idea->links)->toHaveCount(2);
    expect($idea->links[0])->toBe('https://laracasts.com');
    expect($idea->links[1])->toBe('https://laravel.com');
});

test('an authenticated user can create an idea with actionable steps', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    visit('/')
        ->click('@create-idea-button')
        ->fill('title', 'Idea With Steps')
        ->fill('description', 'Testing actionable steps on an idea.')
        ->fill('steps[]', 'Step 1: Planning')
        ->click('@add-step-button')
        ->fill('steps[]', 'Step 2: Execution')
        ->click('@create-idea-submit-button')
        ->assertSee('Idea With Steps');

    $idea = Idea::where('title', 'Idea With Steps')->first();

    expect($idea)->not->toBeNull();
    expect($idea->steps)->toHaveCount(2);
    expect($idea->steps[0]->description)->toBe('Step 1: Planning');
    expect($idea->steps[1]->description)->toBe('Step 2: Execution');
});

test('an idea requires a title', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    visit('/')
        ->click('@create-idea-button')
        ->click('@create-idea-submit-button');

    $this->assertDatabaseEmpty('ideas');
});
