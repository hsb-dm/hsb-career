@php
    $required = $field['required'] ?? false;
    $invalid = $errors->has($name);
@endphp
<div class="application-field">
    <label for="{{ $name }}">{{ $field['label'] }} @if ($required)<span aria-hidden="true">*</span>@endif</label>

    @switch($field['type'])
        @case('file')
            <input id="{{ $name }}" name="{{ $name }}" type="file" accept=".pdf,application/pdf" @required($required) aria-invalid="{{ $invalid ? 'true' : 'false' }}" @if ($invalid) aria-describedby="{{ $name }}-error" @endif>
            @break

        @case('select')
            <div class="application-select-wrap">
                <select id="{{ $name }}" name="{{ $name }}" @required($required) aria-invalid="{{ $invalid ? 'true' : 'false' }}" @if ($invalid) aria-describedby="{{ $name }}-error" @endif>
                    <option value="">Choose an option</option>
                    @foreach ($field['options'] as $value => $label)
                        <option value="{{ $value }}" @selected(old($name) === $value)>{{ $label }}</option>
                    @endforeach
                    @if ($name !== 'serious_disease')
                        <option value="Other" @selected(old($name) === 'Other')>Other</option>
                    @endif
                </select>
                <svg viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path d="m4 7 6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            @break

        @case('textarea')
            <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $name === 'serious_disease_details' ? 3 : 4 }}" maxlength="5000" @required($required) aria-invalid="{{ $invalid ? 'true' : 'false' }}" @if ($invalid) aria-describedby="{{ $name }}-error" @endif>{{ old($name) }}</textarea>
            @break

        @default
            <input id="{{ $name }}" name="{{ $name }}" type="{{ $field['type'] }}" value="{{ old($name) }}" @if ($field['type'] === 'number') min="1" max="120" @else maxlength="255" @endif @required($required) aria-invalid="{{ $invalid ? 'true' : 'false' }}" @if ($invalid) aria-describedby="{{ $name }}-error" @endif>
    @endswitch

    @error($name)<p class="application-error" id="{{ $name }}-error">{{ $message }}</p>@enderror

    @if ($field['type'] === 'select' && $name !== 'serious_disease')
        <div data-other-for="{{ $name }}">
            <label class="application-other-label" for="{{ $name }}_other">If other, please specify</label>
            <input id="{{ $name }}_other" name="{{ $name }}_other" type="text" value="{{ old($name.'_other') }}" maxlength="255" aria-invalid="{{ $errors->has($name.'_other') ? 'true' : 'false' }}" @if ($errors->has($name.'_other')) aria-describedby="{{ $name }}_other-error" @endif>
            @error($name.'_other')<p class="application-error" id="{{ $name }}_other-error">{{ $message }}</p>@enderror
        </div>
    @endif
</div>
