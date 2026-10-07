<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#080909">
    <title>Apply for {{ $job->title }} | Careers at HSB Investasi</title>
    <meta name="robots" content="noindex, follow">
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
                <form class="application-form" action="{{ route('vacancies.apply.store', $job->slug) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if ($errors->any())
                        <div class="application-errors" role="alert">
                            <p>Please correct the following fields:</p>
                            <ul>
                                @foreach ($errors->messages() as $name => $messages)
                                    <li><a href="#{{ $name }}">{{ $messages[0] }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <p class="application-required">Fields marked * are required.</p>

                    @foreach (\App\Support\ScreeningForm::sections() as $section => $fields)
                        <fieldset class="application-section">
                            <legend>{{ $section }}</legend>
                            @foreach ($fields as $name => $field)
                                @include('partials.application-field', ['name' => $name, 'field' => $field])
                            @endforeach
                        </fieldset>
                    @endforeach

                    <button class="button button-lime application-submit" type="submit">Submit Application</button>
                </form>
            @endif
        </div>
    </main>

    @include('partials.footer', ['showAwards' => false])
</body>
</html>
