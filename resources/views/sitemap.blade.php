@php echo '<?xml version="1.0" encoding="UTF-8"?>'; @endphp
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url><loc>{{ route('home') }}</loc></url>
    <url><loc>{{ route('vacancies.index') }}</loc></url>
    @foreach ($jobs as $job)
        <url>
            <loc>{{ route('vacancies.show', $job->slug) }}</loc>
            @if ($job->updated_at)
                <lastmod>{{ $job->updated_at->toAtomString() }}</lastmod>
            @endif
        </url>
    @endforeach
</urlset>
