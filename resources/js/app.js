const search = document.querySelector('#job-search');
const typeFilter = document.querySelector('#job-type-filter');
const jobFilters = document.querySelector('[data-job-filters]');
const isVacanciesPage = jobFilters?.dataset.page === 'vacancies';
const jobCards = [...document.querySelectorAll('.job-card')];
const filterEmpty = document.querySelector('#job-filter-empty');
const seeMoreJobs = document.querySelector('[data-see-more-jobs]');
const jobsPerPage = 9;
let shownJobs = jobsPerPage;

const mobileNav = document.querySelector('[data-mobile-nav]');
const mobileNavOpen = document.querySelector('[data-mobile-nav-open]');
const mobileNavClose = document.querySelector('[data-mobile-nav-close]');

if (mobileNav && mobileNavOpen && mobileNavClose) {
    const mobileViewport = window.matchMedia('(max-width: 600px)');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const firstMobileLink = mobileNav.querySelector('a');
    let closeTimer;

    function closeNavigation({ restoreFocus = true, immediate = false } = {}) {
        if (!mobileNav.open) return;

        window.clearTimeout(closeTimer);
        mobileNav.classList.remove('is-open');
        mobileNavOpen.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('mobile-nav-open');

        const finishClose = () => {
            if (mobileNav.open) mobileNav.close();
            if (restoreFocus) mobileNavOpen.focus();
        };

        if (immediate || reducedMotion.matches) finishClose();
        else closeTimer = window.setTimeout(finishClose, 240);
    }

    mobileNavOpen.addEventListener('click', () => {
        if (mobileNav.open) {
            closeNavigation();
            return;
        }

        window.clearTimeout(closeTimer);
        mobileNav.showModal();
        mobileNavOpen.setAttribute('aria-expanded', 'true');
        document.body.classList.add('mobile-nav-open');
        window.requestAnimationFrame(() => mobileNav.classList.add('is-open'));
        firstMobileLink?.focus();
    });

    mobileNavClose.addEventListener('click', () => closeNavigation());
    mobileNav.addEventListener('cancel', (event) => {
        event.preventDefault();
        closeNavigation();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape' || !mobileNav.open) return;
        event.preventDefault();
        closeNavigation();
    });
    mobileNav.addEventListener('click', (event) => {
        if (event.target === mobileNav) closeNavigation();
    });
    mobileNav.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => closeNavigation({ restoreFocus: false }));
    });
    mobileViewport.addEventListener('change', (event) => {
        if (!event.matches) closeNavigation({ restoreFocus: false, immediate: true });
    });
}

function filterJobs() {
    if (!isVacanciesPage) return;
    const term = search.value.trim().toLocaleLowerCase();
    const type = typeFilter.value;
    let matchingCount = 0;

    for (const card of jobCards) {
        const matches = card.dataset.title.includes(term) && (type === 'all' || card.dataset.type === type);
        if (matches) matchingCount++;
        card.hidden = !matches || matchingCount > shownJobs;
    }

    if (filterEmpty) filterEmpty.hidden = matchingCount !== 0 || jobCards.length === 0;
    if (seeMoreJobs) seeMoreJobs.hidden = matchingCount <= shownJobs;

    const url = new URL(window.location.href);
    if (search.value.trim()) url.searchParams.set('q', search.value.trim());
    else url.searchParams.delete('q');
    if (type !== 'all') url.searchParams.set('type', type);
    else url.searchParams.delete('type');
    window.history.replaceState(null, '', url);
}

search?.addEventListener('input', () => {
    shownJobs = jobsPerPage;
    filterJobs();
});
seeMoreJobs?.addEventListener('click', () => {
    shownJobs += jobsPerPage;
    filterJobs();
});
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
    () => {
        shownJobs = jobsPerPage;
        filterJobs();
    },
);

const lifeGallery = document.querySelector('[data-life-gallery]');
if (lifeGallery) {
    const tabs = [...lifeGallery.querySelectorAll('[role="tab"]')];
    const panels = tabs.map((tab) => document.getElementById(tab.getAttribute('aria-controls')));
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    let activeIndex = 0;
    let timerId;

    panels.forEach((panel, index) => {
        panel.inert = index !== activeIndex;
        panel.setAttribute('aria-hidden', String(index !== activeIndex));
    });

    function selectCategory(index) {
        activeIndex = index;
        tabs.forEach((tab, tabIndex) => {
            const selected = tabIndex === index;
            const panel = panels[tabIndex];
            tab.setAttribute('aria-selected', String(selected));
            tab.tabIndex = selected ? 0 : -1;

            if (selected && panel.hidden) {
                panel.hidden = false;
                void panel.offsetWidth;
            }

            panel.classList.toggle('is-active', selected);
            panel.inert = !selected;
            panel.setAttribute('aria-hidden', String(!selected));
        });
    }

    function restartRotation() {
        clearInterval(timerId);
        if (!reducedMotion.matches && !document.hidden && !lifeGallery.matches(':hover') && !lifeGallery.contains(document.activeElement)) {
            timerId = window.setInterval(() => selectCategory((activeIndex + 1) % tabs.length), 5000);
        }
    }

    tabs.forEach((tab, index) => {
        tab.addEventListener('click', () => {
            selectCategory(index);
            restartRotation();
        });

        tab.addEventListener('keydown', (event) => {
            const nextIndex = {
                ArrowRight: (index + 1) % tabs.length,
                ArrowLeft: (index - 1 + tabs.length) % tabs.length,
                Home: 0,
                End: tabs.length - 1,
            }[event.key];

            if (nextIndex === undefined) return;
            event.preventDefault();
            selectCategory(nextIndex);
            tabs[nextIndex].focus();
            restartRotation();
        });
    });

    document.addEventListener('visibilitychange', restartRotation);
    reducedMotion.addEventListener('change', restartRotation);
    lifeGallery.addEventListener('mouseenter', () => clearInterval(timerId));
    lifeGallery.addEventListener('mouseleave', restartRotation);
    lifeGallery.addEventListener('focusin', () => clearInterval(timerId));
    lifeGallery.addEventListener('focusout', () => window.setTimeout(restartRotation, 0));
    restartRotation();
}

const talentSelect = document.querySelector('[data-talent-select]');
if (talentSelect) {
    const value = talentSelect.querySelector('input[name="area_of_interest"]');
    const interestError = talentSelect.querySelector('[data-talent-interest-error]');
    const showInterestError = (show) => {
        interestError.hidden = !show;
        listbox.trigger.setAttribute('aria-invalid', String(show));
    };
    const listbox = setupListbox(
        talentSelect,
        value,
        talentSelect.querySelector('[data-talent-selection]'),
        () => showInterestError(false),
    );

    document.querySelector('.talent-form')?.addEventListener('submit', (event) => {
        if (!value.value) {
            event.preventDefault();
            showInterestError(true);
            listbox.trigger.focus();
            listbox.open();
        }
    });
}

const talentFile = document.querySelector('.talent-upload input[type="file"]');
talentFile?.addEventListener('change', () => {
    const fileName = document.querySelector('[data-talent-file-name]');
    const file = talentFile.files?.[0];
    if (fileName) fileName.textContent = file?.name || 'Upload CV';

    let error = '';
    if (file && !/\.(pdf|doc|docx)$/i.test(file.name)) {
        error = 'Upload a PDF, DOC, or DOCX file.';
    } else if (file && file.size > 5 * 1024 * 1024) {
        error = 'CV file must be no larger than 5 MB.';
    }

    talentFile.setCustomValidity(error);
    talentFile.setAttribute('aria-invalid', String(Boolean(error)));
    const errorElement = document.querySelector('[data-talent-cv-error]');
    if (errorElement) {
        errorElement.textContent = error;
        errorElement.hidden = !error;
    }
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
