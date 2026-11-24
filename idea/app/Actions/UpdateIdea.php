<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Idea;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UpdateIdea
{
    public function handle(Idea $idea, array $attributes, ?UploadedFile $image = null): Idea
    {
        $image ??= $attributes['image'] ?? null;

        $ideaAttributes = collect($attributes)->except(['steps', 'image'])->all();

        if ($image instanceof UploadedFile) {
            if ($idea->image_path) {
                Storage::delete($idea->image_path);
            }

            $ideaAttributes['image_path'] = $image->store('ideas');
        }

        $idea->update($ideaAttributes);

        if (array_key_exists('steps', $attributes)) {
            $idea->steps()->delete();

            if (! empty($attributes['steps'])) {
                $idea->steps()->createMany(
                    collect($attributes['steps'])->map(fn ($step) => [
                        'description' => is_array($step) ? ($step['description'] ?? '') : (string) $step,
                    ])->all()
                );
            }
        }

        return $idea;
    }
}
