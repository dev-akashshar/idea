<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Step;
use Illuminate\Http\Request;

class StepController extends Controller
{
    public function update(Step $step)
    {
        $step->update([
            'completed' => request()->boolean('completed'),
        ]);

        return back();
    }
}
