document.addEventListener('DOMContentLoaded', function () {
    const endpoint = 'src/xhr/get-images.php';
    const galleryContainer = document.querySelector('.row.row-cols-xl-5');
    const sentinel = document.getElementById('scroll-sentinel');
    const headerFilters = document.querySelectorAll('header .nav input[type="checkbox"]');
    const filtersForm = document.getElementById('offcanvas-filters');
    const resetButton = filtersForm?.querySelector('button[type="reset"]');
    const imageViewerModalEl = document.getElementById('modal-image-viewer');
    const imageViewerModalInstance = new bootstrap.Modal(imageViewerModalEl);
    const fullscreenBtn = imageViewerModalEl.querySelector('#toggle-fullscreen');
    const imagePropsContainer = imageViewerModalEl.querySelector('#image-props-container');
    const imgEl = imageViewerModalEl.querySelector('img');

    let currentFilters = getFiltersFromUrl();
    let isLoading = false;
    let offset = 0;
    let limit = 30;

    if (!galleryContainer || !sentinel) return;

    function getColumnCount() {
        const width = window.innerWidth;
        if (width >= 1400) return 6;
        if (width >= 1200) return 5;
        if (width >= 768) return 4;
        if (width >= 576) return 3;
        return 2;
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
                if (value.length > 0) {
                    params.set(key, value.join(','));
                } else {
                    params.delete(key);
                }
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
            });
    }

    function renderImages(images) {
        images.forEach(img => {
            const col = document.createElement('div');
            col.className = 'col';
            const anchor = document.createElement('a');
            anchor.href = '#modal-image-viewer';
            anchor.className = 'd-block ratio ratio-1x1';
            anchor.setAttribute('data-bs-toggle', 'modal');
            anchor.setAttribute('data-image-id', img.id);
            anchor.addEventListener('click', handleImageClick);
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

    function handleImageClick(event) {
        event.preventDefault();
        const imageId = event.currentTarget.getAttribute('data-image-id');
        if (!imageId) return;
        fetch(`src/xhr/get-image-info.php?imageId=${encodeURIComponent(imageId)}`)
            .then(res => res.json())
            .then(imageData => {
                showImageWithProps(imageData);
            })
            .catch(console.error);
    }

    function showImageWithProps(imageData) {
        imgEl.src = imageData.url;
        imgEl.alt = imageData.alt || 'Image';

        imagePropsContainer.innerHTML = '';
        if (Array.isArray(imageData.sections)) {
            const ul = document.createElement('ul');
            ul.className = 'list-unstyled gap-4 ms-xl-2';
            imagePropsContainer.appendChild(ul);
            imageData.sections.forEach(section => {
                const sectionContainer = document.createElement('li');
                sectionContainer.className = 'pb-4 border-bottom';

                const sectionTitle = document.createElement('h3');
                sectionTitle.className = 'h6 fs-lg';
                sectionTitle.textContent = section.title;
                sectionContainer.appendChild(sectionTitle);

                if (Array.isArray(section.fields)) {
                    const fieldsContainer = document.createElement('ul');
                    fieldsContainer.className = 'list-unstyled gap-xl-2 gap-3';
                    section.fields.forEach(field => {
                        const fieldItem = document.createElement('li');
                        fieldItem.className = 'd-flex flex-wrap align-items-center gap-1';

                        const fieldTitle = document.createElement('h4');
                        fieldTitle.className = 'flex-shrink-0 mb-0 me-lg-2 me-1 fs-sm text-body-secondary';
                        fieldTitle.textContent = field.title;
                        fieldItem.appendChild(fieldTitle);

                        if (Array.isArray(field.values)) {
                            field.values.forEach(value => {
                                const valueButton = document.createElement('a');
                                valueButton.className = 'btn btn-sm btn-props rounded-pill';
                                valueButton.href = '#';
                                valueButton.textContent = value.display_title;
                                fieldItem.appendChild(valueButton);
                            });
                        }
                        if (field.note) {
                            const fieldNote = document.createElement('p');
                            fieldNote.className = 'mt-1 mb-0 ff-extra fs-sm fst-italic text-dark w-100';
                            fieldNote.textContent = field.note;
                            fieldItem.appendChild(fieldNote);
                        }

                        fieldsContainer.appendChild(fieldItem);
                    });
                    sectionContainer.appendChild(fieldsContainer);
                }
                ul.appendChild(sectionContainer);
            });
        }

        imageViewerModalInstance.show();
    }

    fullscreenBtn?.addEventListener('click', () => {
        imageViewerModalEl.classList.toggle('fullscreen-mode');
    });

    imgEl.addEventListener('click', () => {
        imageViewerModalEl.classList.toggle('fullscreen-mode');
    });

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

    function fillToFullViewport() {
        const placeholder = document.createElement('div');
        placeholder.className = 'col';
        placeholder.innerHTML = `<div class="d-block ratio ratio-1x1"><img src="https://placehold.co/100x100" class="w-100 h-100" style="object-fit:cover;"></div>`;
        galleryContainer.appendChild(placeholder);

        requestAnimationFrame(() => {
            const rowHeight = placeholder.getBoundingClientRect().height;
            if (rowHeight < 10) {
                // fallback for mobile when placeholder has no height yet
                galleryContainer.removeChild(placeholder);
                loadImages(limit, true);
                return;
            }

            const rowTop = galleryContainer.getBoundingClientRect().top;
            const availableHeight = window.innerHeight - rowTop;
            const rows = Math.max(20, Math.floor(availableHeight / rowHeight));
            const cols = getColumnCount();
            const total = rows * cols;
            galleryContainer.removeChild(placeholder);
            loadImages(total, true);
        });
    }

    function isMobile() {
        return /Mobi|Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
    }

    if (isMobile()) {
        window.addEventListener('orientationchange', () => location.reload());
    } else {
        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => location.reload(), 200);
        });
    }

    fillToFullViewport();
});