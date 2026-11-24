<?php

use App\Models\Idea;
use App\Models\User;

test('it belongs to a user', function () {
    $idea = Idea::factory()->create();

    expect($idea->user)->toBeInstanceOf(User::class);
});

test('it can have steps', function () {
    $idea = Idea::factory()->create();

    expect($idea->steps)->toBeEmpty();

    $idea->steps()->create([
        'description' => 'Step 1',
        'completed' => false,
    ]);

    expect($idea->fresh()->steps)->toHaveCount(1);
});

test('it formats markdown in description', function () {
    $idea = new Idea([
        'description' => '# Hello World',
    ]);

    expect($idea->formatted_description)->toContain('<h1>Hello World</h1>');
});
