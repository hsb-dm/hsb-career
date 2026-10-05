import Swiper from 'swiper';
import { A11y, Autoplay, Keyboard, Navigation, Pagination } from 'swiper/modules';
import 'swiper/css';

const search = document.querySelector('#job-search');
const typeFilter = document.querySelector('#job-type-filter');
const jobFilters = document.querySelector('[data-job-filters]');
const isVacanciesPage = jobFilters?.dataset.page === 'vacancies';
const jobCards = [...document.querySelectorAll('.job-card')];
const filterEmpty = document.querySelector('#job-filter-empty');

function filterJobs() {
    if (!isVacanciesPage) return;
    const term = search.value.trim().toLocaleLowerCase();
    const type = typeFilter.value;
    let visibleCount = 0;

    for (const card of jobCards) {
        const visible = card.dataset.title.includes(term) && (type === 'all' || card.dataset.type === type);
        card.hidden = !visible;
        if (visible) visibleCount++;
    }

    if (filterEmpty) filterEmpty.hidden = visibleCount !== 0 || jobCards.length === 0;

    const url = new URL(window.location.href);
    if (search.value.trim()) url.searchParams.set('q', search.value.trim());
    else url.searchParams.delete('q');
    if (type !== 'all') url.searchParams.set('type', type);
    else url.searchParams.delete('type');
    window.history.replaceState(null, '', url);
}

search?.addEventListener('input', filterJobs);
if (isVacanciesPage) filterJobs();
const jobSelect = document.querySelector('[data-job-select]');
if (jobSelect) {
    const trigger = jobSelect.querySelector('.talent-select-trigger');
    const options = jobSelect.querySelector('.talent-options');
    const label = jobSelect.querySelector('[data-job-selection]');
    const choices = [...options.querySelectorAll('[role="option"]')];

    function closeJobOptions(focusTrigger = false) {
        options.hidden = true;
        trigger.setAttribute('aria-expanded', 'false');
        if (focusTrigger) trigger.focus();
    }

    function openJobOptions() {
        options.hidden = false;
        trigger.setAttribute('aria-expanded', 'true');
        choices.find((choice) => choice.dataset.value === typeFilter.value)?.focus();
    }

    trigger.addEventListener('click', () => {
        if (options.hidden) openJobOptions();
        else closeJobOptions();
    });

    trigger.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
            event.preventDefault();
            openJobOptions();
            choices[event.key === 'ArrowDown' ? 0 : choices.length - 1].focus();
        }
    });

    choices.forEach((choice, index) => {
        choice.addEventListener('click', () => {
            typeFilter.value = choice.dataset.value;
            label.textContent = choice.textContent;
            choices.forEach((item) => item.setAttribute('aria-selected', String(item === choice)));
            closeJobOptions(true);
            filterJobs();
        });

        choice.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') closeJobOptions(true);
            if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                event.preventDefault();
                choices[(index + (event.key === 'ArrowDown' ? 1 : -1) + choices.length) % choices.length].focus();
            }
        });
    });

    document.addEventListener('click', (event) => {
        if (!jobSelect.contains(event.target)) closeJobOptions();
    });
}

const gallery = document.querySelector('[data-gallery]');
if (gallery) {
    new Swiper(gallery, {
        modules: [A11y, Autoplay, Keyboard, Navigation, Pagination],
        initialSlide: 1,
        loop: true,
        speed: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 650,
        grabCursor: true,
        autoplay: {
            delay: 2500,
            disableOnInteraction: false,
            pauseOnMouseEnter: false,
            waitForTransition: true,
        },
        slidesPerView: 1,
        spaceBetween: 14,
        breakpoints: {
            601: { slidesPerView: 2 },
            901: { slidesPerView: 4 },
        },
        navigation: {
            prevEl: '.gallery-prev',
            nextEl: '.gallery-next',
        },
        pagination: {
            el: '.gallery-pagination',
            clickable: true,
            bulletElement: 'button',
            bulletClass: 'gallery-dot',
            bulletActiveClass: 'gallery-dot-active',
        },
        keyboard: { enabled: true, onlyInViewport: true },
        a11y: { enabled: true },
    });
}

const talentSelect = document.querySelector('[data-talent-select]');
if (talentSelect) {
    const trigger = talentSelect.querySelector('.talent-select-trigger');
    const options = talentSelect.querySelector('.talent-options');
    const value = talentSelect.querySelector('input[name="area_of_interest"]');
    const label = talentSelect.querySelector('[data-talent-selection]');
    const choices = [...options.querySelectorAll('[role="option"]')];

    function closeTalentOptions(focusTrigger = false) {
        options.hidden = true;
        trigger.setAttribute('aria-expanded', 'false');
        if (focusTrigger) trigger.focus();
    }

    function openTalentOptions() {
        options.hidden = false;
        trigger.setAttribute('aria-expanded', 'true');
        choices.find((choice) => choice.dataset.value === value.value)?.focus();
    }

    trigger.addEventListener('click', () => {
        if (options.hidden) openTalentOptions();
        else closeTalentOptions();
    });

    trigger.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
            event.preventDefault();
            openTalentOptions();
            choices[event.key === 'ArrowDown' ? 0 : choices.length - 1].focus();
        }
    });

    choices.forEach((choice, index) => {
        choice.addEventListener('click', () => {
            value.value = choice.dataset.value;
            label.textContent = choice.textContent;
            choices.forEach((item) => item.setAttribute('aria-selected', String(item === choice)));
            closeTalentOptions(true);
        });

        choice.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') closeTalentOptions(true);
            if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                event.preventDefault();
                choices[(index + (event.key === 'ArrowDown' ? 1 : -1) + choices.length) % choices.length].focus();
            }
        });
    });

    document.addEventListener('click', (event) => {
        if (!talentSelect.contains(event.target)) closeTalentOptions();
    });

    document.querySelector('.talent-form')?.addEventListener('submit', (event) => {
        if (!value.value) {
            event.preventDefault();
            trigger.focus();
            openTalentOptions();
        }
    });
}

const talentFile = document.querySelector('.talent-upload input[type="file"]');
talentFile?.addEventListener('change', () => {
    const fileName = document.querySelector('[data-talent-file-name]');
    if (fileName) fileName.textContent = talentFile.files?.[0]?.name || 'Upload CV';
});

const vacancyDescription = document.querySelector('[data-vacancy-description]');
const vacancyReadMore = document.querySelector('[data-vacancy-read-more]');
if (vacancyDescription && vacancyReadMore) {
    vacancyDescription.classList.add('is-collapsed');

    if (vacancyDescription.scrollHeight > vacancyDescription.clientHeight + 1) {
        vacancyReadMore.hidden = false;
        vacancyReadMore.addEventListener('click', () => {
            const expanded = vacancyReadMore.getAttribute('aria-expanded') === 'true';
            vacancyDescription.classList.toggle('is-collapsed', expanded);
            vacancyReadMore.setAttribute('aria-expanded', String(!expanded));
            vacancyReadMore.querySelector('[data-read-more-label]').textContent = expanded ? 'Read More' : 'Read Less';

            if (expanded) vacancyDescription.scrollIntoView({
                behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth',
                block: 'start',
            });
        });
    } else {
        vacancyDescription.classList.remove('is-collapsed');
    }
}
