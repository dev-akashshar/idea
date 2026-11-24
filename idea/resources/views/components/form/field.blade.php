@props(['label' => false, 'name', 'type' => 'text', 'value' => null])

<div class="space-y-2">
    @if ($label)
        <label for="{{ $name }}" class="label">{{ $label }}</label>
    @endif

    @if ($type === 'textarea')
        <textarea
            name="{{ $name }}"
            id="{{ $name }}"
            class="textarea"
            {{ $attributes }}
        >{{ old($name, $value) }}</textarea>
    @elseif ($type === 'file')
        <input
            type="file"
            id="{{ $name }}"
            name="{{ $name }}"
            {{ $attributes }}
        >
    @else
        <input
            type="{{ $type }}"
            class="input"
            id="{{ $name }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            {{ $attributes }}
        >
    @endif
    @error($name)
        <p class="error">{{ $message }}</p>
    @enderror
</div>
