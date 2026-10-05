<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#080909">
    <title>Apply for {{ $job->title }} | Careers at HSB Investasi</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.header')

    <main class="application-page section-deep">
        <div class="container application-container">
            <a class="application-back" href="{{ route('vacancies.show', $job->slug) }}">← Back to {{ $job->title }}</a>
            <div class="application-heading">
                <p class="application-eyebrow">Application Screening Interview</p>
                <h1>Apply for {{ $job->title }}</h1>
                <p>Your application will be reviewed by the HSB recruitment team.</p>
            </div>

            @if (session('application_success'))
                <div class="application-success" role="status">
                    <h2>Application submitted</h2>
                    <p>Thank you for applying for {{ $job->title }}. Our team will review your answers.</p>
                    <a href="{{ route('vacancies.index') }}">View other vacancies</a>
                </div>
            @else
                <form class="application-form" action="{{ route('vacancies.apply.store', $job->slug) }}" method="POST">
                    @csrf
                    @if ($errors->any())
                        <div class="application-errors" role="alert">Please correct the highlighted fields below.</div>
                    @endif
                    <p class="application-required">Fields marked * are required.</p>

                    @php
                        $sections = [
                            'Personal details' => [
                                ['name', 'Full Name', 'text', true],
                                ['current_age', 'Current Age', 'number', true],
                            ],
                            'Career and compensation' => [
                                ['foreign_language_fluency', 'Foreign Language Fluency (Other than English)', 'text', false],
                                ['current_salary_answer', 'Current/Last Salary', 'text', true],
                                ['additional_benefits', 'Additional Benefits (Private Insurance, Bonus, Allowance, etc)', 'textarea', true],
                                ['expected_salary_answer', 'Expected Salary', 'text', true],
                                ['motivation', 'Motivation for Apply', 'textarea', true],
                                ['reason_for_leaving', 'Reason of Leaving Last Company', 'textarea', true],
                            ],
                            'Reference checks' => [
                                ['latest_company_reference', 'Reference Check Contact Details From Latest Company (Name, Position, Phone/Email)', 'textarea', true],
                                ['second_latest_company_reference', 'Reference Check Contact Details From 2nd Latest Company (Name, Position, Phone/Email) (If any)', 'textarea', false],
                                ['third_latest_company_reference', 'Reference Check Contact Details From 3rd Latest Company (Name, Position, Phone/Email) (If any)', 'textarea', false],
                            ],
                        ];
                        $selects = [
                            ['marital_status', 'Marital Status', ['Single', 'Married']],
                            ['current_status', 'Current Status', ['Still Working / employed', 'Available / free / unemployed']],
                            ['current_domicile', 'Current Domicile', ['Jakarta Area', 'Bogor Area', 'Depok Area', 'Tangerang Area', 'Bekasi Area']],
                            ['english_fluency', 'English Fluency', ['Professional', 'Conversation', 'Intermediate', 'Beginner']],
                            ['notice_period', 'Join Notification', ['As soon as Possible (ASAP)', '2 weeks notice', '1 month notice', '2 months notice']],
                        ];
                    @endphp

                    <fieldset class="application-section">
                        <legend>Personal details</legend>
                        @foreach ($sections['Personal details'] as [$field, $label, $type, $required])
                            <div class="application-field">
                                <label for="{{ $field }}">{{ $label }} @if ($required)<span aria-hidden="true">*</span>@endif</label>
                                <input id="{{ $field }}" name="{{ $field }}" type="{{ $type }}" value="{{ old($field) }}" @if ($field === 'current_age') min="1" max="120" @endif @if ($required) required @endif aria-invalid="{{ $errors->has($field) ? 'true' : 'false' }}">
                                @error($field)<p class="application-error">{{ $message }}</p>@enderror
                            </div>
                        @endforeach
                        @foreach (array_slice($selects, 0, 4) as [$field, $label, $options])
                            <div class="application-field">
                                <label for="{{ $field }}">{{ $label }} <span aria-hidden="true">*</span></label>
                                <select id="{{ $field }}" name="{{ $field }}" required aria-invalid="{{ $errors->has($field) ? 'true' : 'false' }}">
                                    <option value="">Choose an option</option>
                                    @foreach ($options as $option)<option value="{{ $option }}" @selected(old($field) === $option)>{{ $option }}</option>@endforeach
                                    <option value="Other" @selected(old($field) === 'Other')>Other</option>
                                </select>
                                @error($field)<p class="application-error">{{ $message }}</p>@enderror
                                <label class="application-other-label" for="{{ $field }}_other">If other, please specify</label>
                                <input id="{{ $field }}_other" name="{{ $field }}_other" type="text" value="{{ old($field.'_other') }}">
                                @error($field.'_other')<p class="application-error">{{ $message }}</p>@enderror
                            </div>
                        @endforeach
                    </fieldset>

                    <fieldset class="application-section">
                        <legend>Career and compensation</legend>
                        @foreach ($sections['Career and compensation'] as [$field, $label, $type, $required])
                            <div class="application-field">
                                <label for="{{ $field }}">{{ $label }} @if ($required)<span aria-hidden="true">*</span>@endif</label>
                                @if ($type === 'textarea')
                                    <textarea id="{{ $field }}" name="{{ $field }}" rows="4" @if ($required) required @endif aria-invalid="{{ $errors->has($field) ? 'true' : 'false' }}">{{ old($field) }}</textarea>
                                @else
                                    <input id="{{ $field }}" name="{{ $field }}" type="text" value="{{ old($field) }}" @if ($required) required @endif aria-invalid="{{ $errors->has($field) ? 'true' : 'false' }}">
                                @endif
                                @error($field)<p class="application-error">{{ $message }}</p>@enderror
                            </div>
                        @endforeach
                        @php([$field, $label, $options] = $selects[4])
                        <div class="application-field">
                            <label for="{{ $field }}">{{ $label }} <span aria-hidden="true">*</span></label>
                            <select id="{{ $field }}" name="{{ $field }}" required aria-invalid="{{ $errors->has($field) ? 'true' : 'false' }}">
                                <option value="">Choose an option</option>
                                @foreach ($options as $option)<option value="{{ $option }}" @selected(old($field) === $option)>{{ $option }}</option>@endforeach
                                <option value="Other" @selected(old($field) === 'Other')>Other</option>
                            </select>
                            @error($field)<p class="application-error">{{ $message }}</p>@enderror
                            <label class="application-other-label" for="{{ $field }}_other">If other, please specify</label>
                            <input id="{{ $field }}_other" name="{{ $field }}_other" type="text" value="{{ old($field.'_other') }}">
                            @error($field.'_other')<p class="application-error">{{ $message }}</p>@enderror
                        </div>
                    </fieldset>

                    <fieldset class="application-section">
                        <legend>Reference checks</legend>
                        @foreach ($sections['Reference checks'] as [$field, $label, $type, $required])
                            <div class="application-field">
                                <label for="{{ $field }}">{{ $label }} @if ($required)<span aria-hidden="true">*</span>@endif</label>
                                <textarea id="{{ $field }}" name="{{ $field }}" rows="3" @if ($required) required @endif aria-invalid="{{ $errors->has($field) ? 'true' : 'false' }}">{{ old($field) }}</textarea>
                                @error($field)<p class="application-error">{{ $message }}</p>@enderror
                            </div>
                        @endforeach
                    </fieldset>

                    <fieldset class="application-section">
                        <legend>Health</legend>
                        <div class="application-field">
                            <label for="serious_disease">Have you ever been diagnosed with any serious disease? <span aria-hidden="true">*</span></label>
                            <select id="serious_disease" name="serious_disease" required aria-invalid="{{ $errors->has('serious_disease') ? 'true' : 'false' }}">
                                <option value="">Choose an option</option>
                                <option value="yes" @selected(old('serious_disease') === 'yes')>Yes</option>
                                <option value="no" @selected(old('serious_disease') === 'no')>No</option>
                            </select>
                            @error('serious_disease')<p class="application-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="application-field">
                            <label for="serious_disease_details">If yes, please describe the illness/disease.</label>
                            <textarea id="serious_disease_details" name="serious_disease_details" rows="3" aria-invalid="{{ $errors->has('serious_disease_details') ? 'true' : 'false' }}">{{ old('serious_disease_details') }}</textarea>
                            @error('serious_disease_details')<p class="application-error">{{ $message }}</p>@enderror
                        </div>
                    </fieldset>

                    <button class="button button-lime application-submit" type="submit">Submit Application</button>
                </form>
            @endif
        </div>
    </main>

    @include('partials.footer')
</body>
</html>
