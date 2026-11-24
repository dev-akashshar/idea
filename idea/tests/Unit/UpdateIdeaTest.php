<?php

use App\Actions\UpdateIdea;
use App\IdeaStatus;
use App\Models\Idea;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('it updates an idea attributes', function () {
    $user = User::factory()->create();
    $idea = Idea::factory()->for($user)->create([
        'title' => 'Old Title',
        'description' => 'Old description',
        'status' => IdeaStatus::PENDING,
    ]);

    $action = new UpdateIdea();

    $updated = $action->handle($idea, [
        'title' => 'New Title',
        'description' => 'New description',
        'status' => IdeaStatus::COMPLETED->value,
    ]);

    expect($updated->title)->toBe('New Title');
    expect($updated->description)->toBe('New description');
    expect($updated->status)->toBe(IdeaStatus::COMPLETED);

    $this->assertDatabaseHas('ideas', [
        'id' => $idea->id,
        'title' => 'New Title',
        'description' => 'New description',
        'status' => IdeaStatus::COMPLETED->value,
    ]);
});

test('it updates an idea with steps and an image', function () {
    Storage::fake();

    $user = User::factory()->create();
    $idea = Idea::factory()->for($user)->create([
        'title' => 'Idea with Steps',
    ]);

    $idea->steps()->createMany([
        ['description' => 'Old Step 1'],
        ['description' => 'Old Step 2'],
    ]);

    $file = UploadedFile::fake()->image('updated.jpg');

    $action = new UpdateIdea();

    $updated = $action->handle($idea, [
        'title' => 'Updated Idea Title',
        'status' => IdeaStatus::IN_PROGRESS->value,
        'steps' => ['New Step 1', 'New Step 2', 'New Step 3'],
    ], $file);

    expect($updated->steps)->toHaveCount(3);
    expect($updated->steps->pluck('description')->all())->toBe(['New Step 1', 'New Step 2', 'New Step 3']);
    expect($updated->image_path)->not->toBeNull();
    Storage::assertExists($updated->image_path);
});

test('it removes old image when a new image is uploaded', function () {
    Storage::fake();

    $user = User::factory()->create();
    $oldFile = UploadedFile::fake()->image('old.jpg');
    $oldPath = $oldFile->store('ideas');

    $idea = Idea::factory()->for($user)->create([
        'image_path' => $oldPath,
    ]);

    Storage::assertExists($oldPath);

    $newFile = UploadedFile::fake()->image('new.jpg');

    $action = new UpdateIdea();
    $updated = $action->handle($idea, [
        'title' => 'New Title',
        'status' => IdeaStatus::PENDING->value,
    ], $newFile);

    Storage::assertMissing($oldPath);
    Storage::assertExists($updated->image_path);
});
