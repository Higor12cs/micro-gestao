@props([
    'label' => '',
    'name' => '',
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'autocomplete' => 'off',
    'disabled' => false,
])

<div {{ $attributes->merge(['class' => 'form-group']) }}>
    @if ($label)
        <label for="{{ $name }}">{{ $label }}</label>
    @endif

    <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}" class="form-control {{ $errors->has($name) ? 'is-invalid' : '' }}"
        autocomplete="{{ $autocomplete }}" @disabled($disabled)>

    @if ($errors->has($name))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first($name) }}</strong>
        </span>
    @endif
</div>
