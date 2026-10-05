@php
    $required = $field['required'] ?? false;
    $invalid = $errors->has($name);
@endphp
<div class="application-field">
    <label for="{{ $name }}">{{ $field['label'] }} @if ($required)<span aria-hidden="true">*</span>@endif</label>

    @switch($field['type'])
        @case('select')
            <select id="{{ $name }}" name="{{ $name }}" @required($required) aria-invalid="{{ $invalid ? 'true' : 'false' }}">
                <option value="">Choose an option</option>
                @foreach ($field['options'] as $value => $label)
                    <option value="{{ $value }}" @selected(old($name) === $value)>{{ $label }}</option>
                @endforeach
                @if ($name !== 'serious_disease')
                    <option value="Other" @selected(old($name) === 'Other')>Other</option>
                @endif
            </select>
            @break

        @case('textarea')
            <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $name === 'serious_disease_details' ? 3 : 4 }}" @required($required) aria-invalid="{{ $invalid ? 'true' : 'false' }}">{{ old($name) }}</textarea>
            @break

        @default
            <input id="{{ $name }}" name="{{ $name }}" type="{{ $field['type'] }}" value="{{ old($name) }}" @if ($field['type'] === 'number') min="1" max="120" @endif @required($required) aria-invalid="{{ $invalid ? 'true' : 'false' }}">
    @endswitch

    @error($name)<p class="application-error">{{ $message }}</p>@enderror

    @if ($field['type'] === 'select' && $name !== 'serious_disease')
        <div data-other-for="{{ $name }}">
            <label class="application-other-label" for="{{ $name }}_other">If other, please specify</label>
            <input id="{{ $name }}_other" name="{{ $name }}_other" type="text" value="{{ old($name.'_other') }}" aria-invalid="{{ $errors->has($name.'_other') ? 'true' : 'false' }}">
            @error($name.'_other')<p class="application-error">{{ $message }}</p>@enderror
        </div>
    @endif
</div>
