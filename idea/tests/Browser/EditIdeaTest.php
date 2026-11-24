<?php

use App\IdeaStatus;
use App\Models\Idea;
use App\Models\User;

test('an authenticated user can edit an idea', function () {
    $user = User::factory()->create();
    $idea = Idea::factory()->for($user)->create([
        'title' => 'Initial Title',
        'description' => 'Initial description',
        'status' => IdeaStatus::PENDING,
    ]);

    $this->actingAs($user);

    visit(route('idea.show', $idea))
        ->click('@edit-idea-button')
        ->assertValue('title', 'Initial Title')
        ->assertValue('description', 'Initial description')
        ->fill('title', 'Updated Idea Title')
        ->fill('description', 'Updated idea description')
        ->click('@status-in_progress')
        ->click('@edit-idea-submit-button')
        ->assertSee('Updated Idea Title');

    $this->assertDatabaseHas('ideas', [
        'id' => $idea->id,
        'title' => 'Updated Idea Title',
        'description' => 'Updated idea description',
        'status' => IdeaStatus::IN_PROGRESS->value,
    ]);
});

test('an authenticated user can edit an idea with links and steps', function () {
    $user = User::factory()->create();
    $idea = Idea::factory()->for($user)->create([
        'title' => 'Idea With Links and Steps',
        'links' => ['https://laracasts.com'],
    ]);

    $idea->steps()->create([
        'description' => 'Step 1: Original',
        'completed' => false,
    ]);

    $this->actingAs($user);

    visit(route('idea.show', $idea))
        ->click('@edit-idea-button')
        ->assertValue('title', 'Idea With Links and Steps')
        ->fill('title', 'Updated Idea With Links and Steps')
        ->fill('links[]', 'https://laravel.com')
        ->fill('steps[]', 'Step 1: Updated')
        ->click('@edit-idea-submit-button')
        ->assertSee('Updated Idea With Links and Steps');

    $idea->refresh();

    expect($idea->title)->toBe('Updated Idea With Links and Steps');
    expect($idea->links[0])->toBe('https://laravel.com');
    expect($idea->steps->first()->description)->toBe('Step 1: Updated');
});
