document.addEventListener('DOMContentLoaded', function () {
    const endpoint = 'src/xhr/get-images.php';
    const galleryContainer = document.querySelector('.row.row-cols-xl-5');
    const sentinel = document.getElementById('scroll-sentinel');
    const headerFilters = document.querySelectorAll('header .nav input[type="checkbox"]');
    const filtersForm = document.getElementById('offcanvas-filters');
    const resetButton = filtersForm?.querySelector('button[type="reset"]');
    const pullToRefreshSpinner = document.getElementById('pull-to-refresh-spinner');

    let currentFilters = getFiltersFromUrl();
    let isLoading = false;
    let offset = 0;
    let limit = 30;

    let isPulling = false;
    let startY = 0;

    if (!galleryContainer || !sentinel) return;

    function getColumnCount() {
        const width = window.innerWidth;
        if (width >= 1400) return 6;
        if (width >= 1200) return 5;
        if (width >= 768) return 4;
        if (width >= 576) return 3;
        return 2;
    }

    function getRowHeight() {
        const temp = document.createElement('div');
        temp.className = 'col';
        temp.innerHTML = `
      <div class="d-block ratio ratio-1x1">
        <img src="https://placehold.co/100x100" class="w-100 h-100" style="object-fit:cover;">
      </div>
    `;
        galleryContainer.appendChild(temp);
        const height = temp.getBoundingClientRect().height;
        galleryContainer.removeChild(temp);
        return height || 200;
    }

    function getFiltersFromUrl() {
        const params = new URLSearchParams(window.location.search);
        const filters = {};
        for (const [key, value] of params.entries()) {
            if (key.endsWith('[]')) {
                const name = key.slice(0, -2);
                if (!filters[name]) filters[name] = [];
                filters[name].push(value);
            } else {
                filters[key] = value;
            }
        }
        return filters;
    }

    function updateUrlFilters(partialFilters) {
        const url = new URL(window.location);
        const params = new URLSearchParams(url.search);

        Object.keys(partialFilters).forEach(key => {
            params.delete(`${key}[]`);
        });

        Object.entries(partialFilters).forEach(([key, value]) => {
            if (Array.isArray(value)) {
                value.forEach(v => params.append(`${key}[]`, v));
            } else {
                params.set(key, value);
            }
        });

        history.pushState(null, '', `${url.pathname}?${params.toString()}`);
    }

    function getCurrentBodyPartFilters() {
        const values = [];
        headerFilters.forEach(input => {
            if (input.checked) values.push(input.value);
        });
        return values;
    }

    function syncFilterControls(filterName, values) {
        headerFilters.forEach(input => {
            if (input.name === `${filterName}[]`) {
                input.checked = values.includes(input.value);
            }
        });
        if (filtersForm) {
            const offcanvasFilters = filtersForm.querySelectorAll(`[name="${filterName}[]"]`);
            offcanvasFilters.forEach(input => {
                input.checked = values.includes(input.value);
            });
        }
    }

    function loadImages(count, reset = false) {
        if (isLoading) return;
        isLoading = true;
        const requestFilters = { ...currentFilters };
        requestFilters.limit = count;
        requestFilters.offset = reset ? 0 : offset;

        const body = new URLSearchParams();
        Object.entries(requestFilters).forEach(([key, value]) => {
            if (Array.isArray(value)) {
                value.forEach(v => body.append(`${key}[]`, v));
            } else {
                body.append(key, value);
            }
        });

        fetch(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body
        })
            .then(res => res.json())
            .then(images => {
                if (reset) {
                    galleryContainer.innerHTML = '';
                    offset = 0;
                }
                renderImages(images);
                offset += images.length;
            })
            .catch(console.error)
            .finally(() => {
                isLoading = false;
                pullToRefreshSpinner.classList.remove('active');
                isPulling = false;
            });
    }

    function renderImages(images) {
        images.forEach(img => {
            const col = document.createElement('div');
            col.className = 'col';
            const anchor = document.createElement('a');
            anchor.href = '#modal-image-props';
            anchor.className = 'd-block ratio ratio-1x1';
            anchor.setAttribute('data-bs-toggle', 'modal');
            const image = document.createElement('img');
            image.src = img.url;
            image.alt = 'Gallery image';
            image.className = 'w-100 h-100';
            image.style.objectFit = 'cover';
            anchor.appendChild(image);
            col.appendChild(anchor);
            galleryContainer.appendChild(col);
        });
    }

    function fillToFullViewport() {
        const placeholder = document.createElement('div');
        placeholder.className = 'col';
        placeholder.innerHTML = `
      <div class="d-block ratio ratio-1x1">
        <img src="https://placehold.co/100x100" class="w-100 h-100" style="object-fit:cover;">
      </div>`;
        galleryContainer.appendChild(placeholder);

        requestAnimationFrame(() => {
            const rowHeight = placeholder.getBoundingClientRect().height;
            const rowTop = galleryContainer.getBoundingClientRect().top;
            const availableHeight = window.innerHeight - rowTop;
            const rows = Math.max(20, Math.floor(availableHeight / rowHeight));
            const cols = getColumnCount();
            const total = rows * cols;
            galleryContainer.removeChild(placeholder);
            loadImages(total, true);
        });
    }

    function debounce(fn, delay) {
        let timeout;
        return function () {
            clearTimeout(timeout);
            timeout = setTimeout(fn, delay);
        };
    }

    const observer = new IntersectionObserver(entries => {
        if (entries[0].isIntersecting) {
            loadImages(limit);
        }
    }, {
        root: null,
        rootMargin: '500px',
        threshold: 0.1
    });
    observer.observe(sentinel);

    headerFilters.forEach(input => {
        input.addEventListener('change', () => {
            const values = getCurrentBodyPartFilters();
            currentFilters['body-parts'] = values;
            updateUrlFilters({ 'body-parts': values });
            syncFilterControls('body-parts', values);
            loadImages(limit, true);
        });
    });

    if (filtersForm) {
        filtersForm.addEventListener('change', () => {
            const formData = new FormData(filtersForm);
            const bodyPartValues = formData.getAll('body-parts[]');
            currentFilters['body-parts'] = bodyPartValues;
            updateUrlFilters({ 'body-parts': bodyPartValues });
            syncFilterControls('body-parts', bodyPartValues);
            loadImages(limit, true);
        });
    }

    if (resetButton) {
        resetButton.addEventListener('click', () => {
            currentFilters = {};
            updateUrlFilters({ 'body-parts': [] });
            syncFilterControls('body-parts', []);
            loadImages(limit, true);
        });
    }

    /* Pull to Refresh */
    let isTouching = false;
    let startY = 0;
    let pullDistance = 0;
    let isThresholdPassed = false;
    const PULL_THRESHOLD = 80;

    document.addEventListener('touchstart', (e) => {
        if (window.scrollY === 0 && !isLoading) {
            isTouching = true;
            startY = e.touches[0].clientY;
            pullDistance = 0;
            isThresholdPassed = false;
            pullToRefreshSpinner.classList.remove('active');
        }
    }, { passive: true });

    document.addEventListener('touchmove', (e) => {
        if (!isTouching) return;
        const currentY = e.touches[0].clientY;
        pullDistance = currentY - startY;

        if (pullDistance > 0) {
            e.preventDefault(); // block native bounce
            pullToRefreshSpinner.style.transform = `translate(-50%, ${Math.min(pullDistance, PULL_THRESHOLD)}px)`;
            pullToRefreshSpinner.style.opacity = Math.min(pullDistance / PULL_THRESHOLD, 1);

            if (pullDistance > PULL_THRESHOLD) {
                isThresholdPassed = true;
            } else {
                isThresholdPassed = false;
            }
        }
    }, { passive: false });

    document.addEventListener('touchend', () => {
        if (isTouching && isThresholdPassed && !isLoading) {
            isPulling = true;
            pullToRefreshSpinner.classList.add('active');
            loadImages(limit, true);
        } else {
            // animate back to hidden
            pullToRefreshSpinner.style.transform = `translate(-50%, -100%)`;
            pullToRefreshSpinner.style.opacity = 0;
        }

        isTouching = false;
        isThresholdPassed = false;
        pullDistance = 0;
    }, { passive: true });

    window.addEventListener('resize', debounce(() => fillToFullViewport(), 200));
    fillToFullViewport();
});
