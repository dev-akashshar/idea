<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Idea;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\UploadedFile;

class CreateIdea
{
    public function __construct(
        #[CurrentUser] protected ?User $user = null
    ) {}

    public function handle(array $attributes, ?UploadedFile $image = null, ?User $user = null): Idea
    {
        $user ??= $this->user;
        $image ??= $attributes['image'] ?? null;

        $ideaAttributes = collect($attributes)->except(['steps', 'image'])->all();

        if ($image instanceof UploadedFile) {
            $ideaAttributes['image_path'] = $image->store('ideas');
        }

        $idea = $user->ideas()->create($ideaAttributes);

        if (! empty($attributes['steps'])) {
            $idea->steps()->createMany(
                collect($attributes['steps'])->map(fn ($step) => ['description' => $step])->all()
            );
        }

        return $idea;
    }
}
