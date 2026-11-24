@props(['idea' => null, 'name' => null, 'title' => null])

@php
    $isEdit = (bool) $idea;
    $name ??= $isEdit ? 'edit-idea' : 'create-idea';
    $title ??= $isEdit ? 'Edit Idea' : 'New Idea';
    $action = $isEdit ? route('idea.update', $idea) : route('idea.store');
    $submitText = $isEdit ? 'Update Idea' : 'Create Idea';
    $submitTest = $isEdit ? 'edit-idea-submit-button' : 'create-idea-submit-button';

    $initialStatus = old('status', $idea?->status->value ?? 'pending');
    $initialLinks = old('links', ($idea && count($idea->links)) ? $idea->links->toArray() : ['']);
    $initialSteps = old('steps', ($idea && $idea->steps->count()) ? $idea->steps->pluck('description')->all() : ['']);
@endphp

<x-modal :name="$name" :title="$title">
    <form
        x-data="{
            status: @js($initialStatus),
            links: @js($initialLinks),
            steps: @js($initialSteps)
        }"
        action="{{ $action }}"
        method="POST"
        enctype="multipart/form-data"
    >
        @csrf
        @if ($isEdit)
            @method('PATCH')
        @endif

        <input type="hidden" name="status" :value="status">

        <div class="space-y-6">
            <x-form.field
                label="Title"
                name="title"
                placeholder="Enter an idea for your title"
                :value="$idea?->title"
                autofocus
                required
            />

            <div class="space-y-2">
                <label for="status" class="label">Status</label>

                <div class="grid grid-cols-3 gap-2 sm:gap-3">
                    @foreach (App\IdeaStatus::cases() as $status)
                        <button
                            type="button"
                            @click="status = @js($status->value)"
                            class="btn h-10 text-xs sm:text-sm px-1 sm:px-3 truncate"
                            :class="status === @js($status->value) ? '' : 'btn-outlined'"
                            data-test="status-{{ $status->value }}"
                        >
                            {{ $status->label() }}
                        </button>
                    @endforeach
                </div>
            </div>

            <x-form.field
                label="Description"
                name="description"
                type="textarea"
                placeholder="Describe your idea..."
                :value="$idea?->description"
            />

            <x-form.field
                label="Featured Image"
                name="image"
                type="file"
            />

            <div class="space-y-2">
                <label class="label">Actionable Steps</label>

                <div class="space-y-2">
                    <template x-for="(step, index) in steps" :key="index">
                        <div class="flex items-center gap-x-2">
                            <input
                                type="text"
                                name="steps[]"
                                x-model="steps[index]"
                                placeholder="Describe a step..."
                                class="input flex-1"
                            >

                            <button
                                type="button"
                                @click="steps.splice(index, 1)"
                                x-show="steps.length > 1"
                                class="form-muted-icon cursor-pointer p-2"
                                title="Remove step"
                            >
                                <x-icons.close class="w-5 h-5" />
                            </button>
                        </div>
                    </template>
                </div>

                <button
                    type="button"
                    @click="steps.push('')"
                    data-test="add-step-button"
                    class="btn btn-outlined h-9 text-xs flex items-center gap-x-1"
                >
                    <x-icons.plus class="w-4 h-4" />
                    Add another step
                </button>
            </div>

            <div class="space-y-2">
                <label class="label">Links</label>

                <div class="space-y-2">
                    <template x-for="(link, index) in links" :key="index">
                        <div class="flex items-center gap-x-2">
                            <input
                                type="url"
                                name="links[]"
                                x-model="links[index]"
                                placeholder="https://example.com"
                                class="input flex-1"
                            >

                            <button
                                type="button"
                                @click="links.splice(index, 1)"
                                x-show="links.length > 1"
                                class="form-muted-icon cursor-pointer p-2"
                                title="Remove link"
                            >
                                <x-icons.close class="w-5 h-5" />
                            </button>
                        </div>
                    </template>
                </div>

                <button
                    type="button"
                    @click="links.push('')"
                    data-test="add-link-button"
                    class="btn btn-outlined h-9 text-xs flex items-center gap-x-1"
                >
                    <x-icons.plus class="w-4 h-4" />
                    Add another link
                </button>
            </div>

            <div class="flex justify-end gap-x-3 pt-2">
                <button
                    type="button"
                    @click="show = false"
                    class="btn btn-outlined h-10"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="btn h-10"
                    data-test="{{ $submitTest }}"
                >
                    {{ $submitText }}
                </button>
            </div>
        </div>
    </form>
</x-modal>
