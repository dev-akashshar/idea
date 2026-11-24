<?php

use App\Actions\CreateIdea;
use App\IdeaStatus;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('it creates an idea for an authenticated user', function () {
    $user = User::factory()->create();

    $action = new CreateIdea($user);

    $idea = $action->handle([
        'title' => 'New Action Idea',
        'description' => 'Created via action class.',
        'status' => IdeaStatus::PENDING->value,
    ]);

    expect($idea->title)->toBe('New Action Idea');
    expect($idea->user_id)->toBe($user->id);
});

test('it creates an idea with steps and an image', function () {
    Storage::fake();

    $user = User::factory()->create();
    $file = UploadedFile::fake()->image('test.jpg');

    $action = new CreateIdea($user);

    $idea = $action->handle([
        'title' => 'Action Idea with Steps',
        'description' => 'Action idea description',
        'status' => IdeaStatus::IN_PROGRESS->value,
        'steps' => ['Step 1', 'Step 2'],
    ], $file);

    expect($idea->steps)->toHaveCount(2);
    expect($idea->image_path)->not->toBeNull();
    Storage::assertExists($idea->image_path);
});
