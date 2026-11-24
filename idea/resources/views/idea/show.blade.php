<x-layout>
    <div class="py-8 max-w-4xl mx-auto">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <a href="{{ route('idea.index') }}" class="flex items-center gap-x-2 text-sm font-medium">Back to Ideas</a>

            <div class="gap-x-3 flex items-center">
                @can('update', $idea)
                    <button
                        type="button"
                        x-data
                        @click="$dispatch('open-modal', 'edit-idea')"
                        class="btn btn-outlined"
                        data-test="edit-idea-button"
                    >
                        <x-icons.external />
                        Edit Idea
                    </button>
                @endcan

                @can('delete', $idea)
                    <form method="POST" action="{{ route('idea.destroy', $idea) }}">
                        @csrf
                        @method('DELETE')

                        <button class="btn btn-outlined text-red-500">Delete</button>
                    </form>
                @endcan
            </div>
        </div>
        <div class="mt-8 space-y-6">
            <h1 class="font-bold text-4xl">{{ $idea->title }}</h1>

            <div class="mt-2 flex gap-x-3 items-center">
                <x-idea.status-label :status="$idea->status->value">{{ $idea->status->label() }}</x-idea.status-label>

                <div class="text-muted-foreground text-sm">{{ $idea->created_at->diffForHumans() }}</div>
            </div>

            @if ($idea->image_path)
                <div>
                    <img src="{{ Storage::url($idea->image_path) }}" alt="{{ $idea->title }}" class="rounded-xl w-full max-h-96 object-cover">
                </div>
            @endif

            <x-card class="mt-6">
                <div class="prose dark:prose-invert text-foreground max-w-none">
                    {!! $idea->formatted_description !!}
                </div>
            </x-card>

            @if ($idea->steps->count())
                <div>
                    <h3 class="font-bold text-xl mt-6">Actionable Steps</h3>

                    <div class="mt-3 space-y-2">
                        @foreach ($idea->steps as $step)
                            <x-card class="flex items-center gap-x-3">
                                <form method="POST" action="{{ route('step.update', $step) }}">
                                    @csrf
                                    @method('PATCH')

                                    <input
                                        type="checkbox"
                                        name="completed"
                                        value="1"
                                        onchange="this.form.submit()"
                                        {{ $step->completed ? 'checked' : '' }}
                                        class="cursor-pointer"
                                    >
                                </form>

                                <span class="{{ $step->completed ? 'line-through text-muted-foreground' : 'text-foreground' }}">
                                    {{ $step->description }}
                                </span>
                            </x-card>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($idea->links->count())
                <div>
                    <h3 class="font-bold text-xl mt-6">Links</h3>

                    <div class="mt-3 space-y-2">
                        @foreach ($idea->links as $link)
                            <x-card :href="$link" class="text-primary font-medium flex gap-x-3 items-center">
                                <x-icons.external />
                                {{ $link }}</x-card>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    @can('update', $idea)
        <x-idea.modal :idea="$idea" />
    @endcan
</x-layout>
