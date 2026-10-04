const search = document.querySelector('#job-search');
const typeFilter = document.querySelector('#job-type-filter');
const jobCards = [...document.querySelectorAll('.job-card')];
const filterEmpty = document.querySelector('#job-filter-empty');

function filterJobs() {
    const term = search.value.trim().toLocaleLowerCase();
    const type = typeFilter.value;
    let visibleCount = 0;

    for (const card of jobCards) {
        const visible = card.dataset.title.includes(term) && (type === 'all' || card.dataset.type === type);
        card.hidden = !visible;
        if (visible) visibleCount++;
    }

    if (filterEmpty) filterEmpty.hidden = visibleCount !== 0 || jobCards.length === 0;
}

search?.addEventListener('input', filterJobs);
typeFilter?.addEventListener('change', filterJobs);
document.querySelector('#reset-job-filters')?.addEventListener('click', () => {
    search.value = '';
    typeFilter.value = 'all';
    filterJobs();
    document.querySelector('#open-positions')?.scrollIntoView({ behavior: 'smooth' });
});

const gallery = document.querySelector('[data-gallery]');
const galleryStep = () => gallery.querySelector('.gallery-item')?.getBoundingClientRect().width + 14 || 0;

document.querySelector('.gallery-prev')?.addEventListener('click', () => {
    gallery.scrollBy({ left: -galleryStep(), behavior: 'smooth' });
});

document.querySelector('.gallery-next')?.addEventListener('click', () => {
    gallery.scrollBy({ left: galleryStep(), behavior: 'smooth' });
});
