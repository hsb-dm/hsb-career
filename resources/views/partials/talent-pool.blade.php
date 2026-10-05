@php($talentReturnRoute = request()->routeIs('vacancies.index') ? 'vacancies.index' : 'home')
<section class="talent section-black" id="talent-pool" aria-labelledby="talent-title">
    <div class="talent-box {{ session('talent_pool_success') ? 'talent-box-success' : '' }}">
        @if (session('talent_pool_success'))
            <div class="talent-success" role="status">
                <h2 class="sr-only" id="talent-title">Talent Pool submission received</h2>
                <img src="{{ asset('images/home/success-talentpool.png') }}" alt="" width="300" height="300">
                <p>Thanks, we've got your details. We'll be in touch when a role opens up that fits.<br>
                    In the meantime, follow <a href="https://www.instagram.com/hsb.life/" target="_blank"
                        rel="noopener noreferrer">@hsb.life</a> on Instagram for a look at life inside HSB.</p>
                <a class="button button-lime talent-button" href="{{ route($talentReturnRoute) }}#talent-pool">Ok, Excellent
                    <svg class="button-arrow" width="28" height="28" viewBox="0 0 28 28" fill="none"
                        aria-hidden="true"><rect width="27.6398" height="27.6398" rx="4.60664" fill="#A7CB19" />
                        <path d="M6.89409 18.9865C6.4083 19.4723 6.4083 20.2599 6.89409 20.7457C7.37987 21.2315 8.16749 21.2315 8.65328 20.7457L7.77368 19.8661L6.89409 18.9865ZM21.11 7.77367C21.11 7.08667 20.5531 6.52974 19.8661 6.52974H8.67069C7.98369 6.52974 7.42676 7.08667 7.42676 7.77367C7.42676 8.46068 7.98369 9.01761 8.67069 9.01761H18.6222V18.9691C18.6222 19.6561 19.1791 20.213 19.8661 20.213C20.5531 20.213 21.11 19.6561 21.11 18.9691V7.77367ZM7.77368 19.8661L8.65328 20.7457L20.7457 8.65327L19.8661 7.77367L18.9865 6.89408L6.89409 18.9865L7.77368 19.8661Z" fill="#0F322C" />
                    </svg></a>
            </div>
        @else
            <h2 class="section-title" id="talent-title">DON’T SEE <span>YOUR ROLE</span> YET?</h2>
            <p class="talent-intro">We're growing fast and new roles open regularly.<br>Leave your details and we'll
                reach out when something that fits comes up.</p>
            <form class="talent-form" action="{{ route('talent-pool.store') }}" method="POST"
                enctype="multipart/form-data" aria-label="Join our talent pool" novalidate>
                @csrf
                <input type="hidden" name="return_to" value="{{ $talentReturnRoute }}">
                <div class="talent-field">
                    <input name="name" type="text" placeholder="Full Name" aria-label="Full Name"
                        autocomplete="name" value="{{ old('name') }}" required maxlength="150">
                    @error('name') <span class="talent-error">{{ $message }}</span> @enderror
                </div>
                <div class="talent-field">
                    <input name="email" type="email" placeholder="Email Address" aria-label="Email Address"
                        autocomplete="email" value="{{ old('email') }}" required maxlength="255">
                    @error('email') <span class="talent-error">{{ $message }}</span> @enderror
                </div>
                <div class="talent-field talent-select" data-talent-select>
                    <input type="hidden" name="area_of_interest" value="{{ old('area_of_interest') }}">
                    <button class="talent-select-trigger" type="button" aria-haspopup="listbox"
                        aria-expanded="false" aria-controls="talent-interest-options">
                        <span data-talent-selection>{{ old('area_of_interest', 'Area of interest') }}</span>
                        <span class="talent-chevron" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="m4 7 6 6 6-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" /></svg></span>
                    </button>
                    <div class="talent-options" id="talent-interest-options" role="listbox" aria-label="Area of interest" hidden>
                        <button type="button" role="option" aria-selected="{{ old('area_of_interest') ? 'false' : 'true' }}"
                            data-value="">Area of interest</button>
                        @foreach (\App\Models\TalentPoolEntry::AREAS_OF_INTEREST as $interest)
                            <button type="button" role="option" aria-selected="{{ old('area_of_interest') === $interest ? 'true' : 'false' }}"
                                data-value="{{ $interest }}">{{ $interest }}</button>
                        @endforeach
                    </div>
                    @error('area_of_interest') <span class="talent-error">{{ $message }}</span> @enderror
                </div>
                <div class="talent-field">
                    <label class="talent-upload">
                        <span data-talent-file-name>Upload CV</span><span class="talent-upload-button">Upload</span>
                        <input name="cv" type="file" accept=".pdf,.doc,.docx" required aria-label="Upload CV">
                    </label>
                    @error('cv') <span class="talent-error">{{ $message }}</span> @enderror
                </div>
                <button class="button button-lime talent-button" type="submit">Join Our Talent Pool
                    <svg class="button-arrow" width="28" height="28" viewBox="0 0 28 28" fill="none"
                        aria-hidden="true"><rect width="27.6398" height="27.6398" rx="4.60664" fill="#A7CB19" />
                        <path d="M6.89409 18.9865C6.4083 19.4723 6.4083 20.2599 6.89409 20.7457C7.37987 21.2315 8.16749 21.2315 8.65328 20.7457L7.77368 19.8661L6.89409 18.9865ZM21.11 7.77367C21.11 7.08667 20.5531 6.52974 19.8661 6.52974H8.67069C7.98369 6.52974 7.42676 7.08667 7.42676 7.77367C7.42676 8.46068 7.98369 9.01761 8.67069 9.01761H18.6222V18.9691C18.6222 19.6561 19.1791 20.213 19.8661 20.213C20.5531 20.213 21.11 19.6561 21.11 18.9691V7.77367ZM7.77368 19.8661L8.65328 20.7457L20.7457 8.65327L19.8661 7.77367L18.9865 6.89408L6.89409 18.9865L7.77368 19.8661Z" fill="#0F322C" />
                    </svg></button>
            </form>
        @endif
    </div>
</section>
