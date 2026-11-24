<?php

use App\Models\Idea;
use App\Models\Step;
use App\Models\User;

test('a user cannot view another users idea', function () {
    $john = User::factory()->create();
    $jane = User::factory()->create();

    $idea = Idea::factory()->create(['user_id' => $john->id]);

    $this->actingAs($jane)
        ->get(route('idea.show', $idea))
        ->assertForbidden();
});

test('a user can view their own idea', function () {
    $user = User::factory()->create();
    $idea = Idea::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get(route('idea.show', $idea))
        ->assertOk();
});

test('a user cannot delete another users idea', function () {
    $john = User::factory()->create();
    $jane = User::factory()->create();

    $idea = Idea::factory()->create(['user_id' => $john->id]);

    $this->actingAs($jane)
        ->delete(route('idea.destroy', $idea))
        ->assertForbidden();

    $this->assertDatabaseHas('ideas', ['id' => $idea->id]);
});

test('a user can delete their own idea', function () {
    $user = User::factory()->create();
    $idea = Idea::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->delete(route('idea.destroy', $idea))
        ->assertRedirect(route('idea.index'));

    $this->assertDatabaseMissing('ideas', ['id' => $idea->id]);
});

test('a user cannot update another users step', function () {
    $john = User::factory()->create();
    $jane = User::factory()->create();

    $idea = Idea::factory()->create(['user_id' => $john->id]);
    $step = Step::factory()->create(['idea_id' => $idea->id]);

    $this->actingAs($jane)
        ->patch(route('step.update', $step), ['completed' => true])
        ->assertForbidden();
});

test('a user cannot update another users idea', function () {
    $john = User::factory()->create();
    $jane = User::factory()->create();

    $idea = Idea::factory()->create(['user_id' => $john->id]);

    $this->actingAs($jane)
        ->patch(route('idea.update', $idea), [
            'title' => 'Updated Title',
            'status' => 'pending',
        ])
        ->assertForbidden();

    $this->assertDatabaseHas('ideas', [
        'id' => $idea->id,
        'title' => $idea->title,
    ]);
});

test('a user can update their own idea', function () {
    $user = User::factory()->create();
    $idea = Idea::factory()->create([
        'user_id' => $user->id,
        'title' => 'Original Title',
    ]);

    $this->actingAs($user)
        ->patch(route('idea.update', $idea), [
            'title' => 'Updated Title',
            'status' => 'completed',
        ])
        ->assertRedirect(route('idea.show', $idea));

    $this->assertDatabaseHas('ideas', [
        'id' => $idea->id,
        'title' => 'Updated Title',
        'status' => 'completed',
    ]);
});
