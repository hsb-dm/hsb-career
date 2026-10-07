<x-filament-widgets::widget>
    <section class="recruitment-intro" aria-labelledby="recruitment-intro-title">
        <div class="recruitment-intro-copy">
            <p class="recruitment-intro-eyebrow">Recruitment workspace <span aria-hidden="true">/</span> {{ now()->format('d M Y') }}</p>
            <h2 id="recruitment-intro-title">A clear view of your hiring pipeline.</h2>
            <p>Review applications, move candidates through each stage, and keep open roles on track.</p>
        </div>
        <nav class="recruitment-intro-actions" aria-label="Recruitment shortcuts">
            <a class="recruitment-intro-primary" href="{{ \App\Filament\Resources\Applicants\ApplicantResource::getUrl('index') }}">Review applicants <span aria-hidden="true">↗</span></a>
            <a class="recruitment-intro-secondary" href="{{ \App\Filament\Resources\Jobs\JobResource::getUrl('index') }}">Manage jobs <span aria-hidden="true">↗</span></a>
        </nav>
    </section>
</x-filament-widgets::widget>
