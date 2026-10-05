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
function setupListbox(root, value, label, onChange = () => {}) {
    if (!root) return null;

    const trigger = root.querySelector('.talent-select-trigger');
    const options = root.querySelector('.talent-options');
    const choices = [...options.querySelectorAll('[role="option"]')];

    function close(focusTrigger = false) {
        options.hidden = true;
        trigger.setAttribute('aria-expanded', 'false');
        if (focusTrigger) trigger.focus();
    }

    function open() {
        options.hidden = false;
        trigger.setAttribute('aria-expanded', 'true');
        choices.find((choice) => choice.dataset.value === value.value)?.focus();
    }

    trigger.addEventListener('click', () => {
        if (options.hidden) open();
        else close();
    });

    trigger.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
            event.preventDefault();
            open();
            choices[event.key === 'ArrowDown' ? 0 : choices.length - 1]?.focus();
        }
    });

    choices.forEach((choice, index) => {
        choice.addEventListener('click', () => {
            value.value = choice.dataset.value;
            label.textContent = choice.textContent;
            choices.forEach((item) => item.setAttribute('aria-selected', String(item === choice)));
            close(true);
            onChange();
        });

        choice.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') close(true);
            if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                event.preventDefault();
                choices[(index + (event.key === 'ArrowDown' ? 1 : -1) + choices.length) % choices.length].focus();
            }
        });
    });

    document.addEventListener('click', (event) => {
        if (!root.contains(event.target)) close();
    });

    return { trigger, open };
}

setupListbox(
    document.querySelector('[data-job-select]'),
    typeFilter,
    document.querySelector('[data-job-selection]'),
    filterJobs,
);

const gallery = document.querySelector('[data-gallery]');
async function initGallery(gallery) {
    const [{ default: Swiper }, { A11y, Autoplay, Keyboard, Navigation, Pagination }] = await Promise.all([
        import('swiper'),
        import('swiper/modules'),
    ]);

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

if (gallery) initGallery(gallery);

const talentSelect = document.querySelector('[data-talent-select]');
if (talentSelect) {
    const value = talentSelect.querySelector('input[name="area_of_interest"]');
    const listbox = setupListbox(talentSelect, value, talentSelect.querySelector('[data-talent-selection]'));

    document.querySelector('.talent-form')?.addEventListener('submit', (event) => {
        if (!value.value) {
            event.preventDefault();
            listbox.trigger.focus();
            listbox.open();
        }
    });
}

const talentFile = document.querySelector('.talent-upload input[type="file"]');
talentFile?.addEventListener('change', () => {
    const fileName = document.querySelector('[data-talent-file-name]');
    if (fileName) fileName.textContent = talentFile.files?.[0]?.name || 'Upload CV';
});

const applicationForm = document.querySelector('.application-form');
if (applicationForm) {
    for (const otherField of applicationForm.querySelectorAll('[data-other-for]')) {
        const select = applicationForm.elements.namedItem(otherField.dataset.otherFor);
        const input = otherField.querySelector('input');
        const sync = () => {
            otherField.hidden = select.value !== 'Other';
            input.required = !otherField.hidden;
        };

        select.addEventListener('change', sync);
        sync();
    }

    const healthAnswer = applicationForm.elements.namedItem('serious_disease');
    const healthDetails = applicationForm.elements.namedItem('serious_disease_details');
    const syncHealth = () => { healthDetails.required = healthAnswer.value === 'yes'; };
    healthAnswer.addEventListener('change', syncHealth);
    syncHealth();
}

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
