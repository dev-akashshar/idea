<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Notifications\EmailChangedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the user profile.
     */
    public function edit(): View
    {
        return view('profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Update the user profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'max:16'],
        ]);

        $attributes = collect($validated)->filter()->all();

        $emailChanged = array_key_exists('email', $attributes) && $attributes['email'] !== $user->email;

        $user->update($attributes);

        if ($emailChanged) {
            $user->notify(new EmailChangedNotification());
        }

        return to_route('profile.edit')->with('success', 'Profile updated successfully!');
    }
}
