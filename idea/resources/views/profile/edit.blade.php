<x-layout>
    <x-form
        title="Edit Your Profile"
        description="Update your account information."
    >
        <form
            action="{{ route('profile.update') }}"
            method="POST"
            class="mt-10 space-y-4"
        >
            @csrf
            @method('PATCH')

            <x-form.field
                name="name"
                label="Name"
                :value="$user->name"
                required
            />

            <x-form.field
                name="email"
                label="Email"
                type="email"
                :value="$user->email"
                required
            />

            <x-form.field
                name="password"
                label="New Password"
                type="password"
            />

            <button
                type="submit"
                class="btn mt-2 h-10 w-full"
                data-test="update-profile-button"
            >
                Update Profile
            </button>
        </form>
    </x-form>
</x-layout>
