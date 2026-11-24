<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Step;
use App\Models\User;

class StepPolicy
{
    /**
     * Determine whether the user can update the step.
     */
    public function update(User $user, Step $step): bool
    {
        return $step->idea->user->is($user);
    }
}
