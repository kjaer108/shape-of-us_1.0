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
    let currentImageId = null;

    if (!galleryContainer || !sentinel) return;

    imageViewerModalEl.addEventListener('hidden.bs.modal', () => {
        imageViewerModalEl.classList.remove('fullscreen-mode');
    });

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

        // Remove all keys we're going to update
        Object.keys(partialFilters).forEach(key => {
            params.delete(`${key}[]`);
            params.delete(key); // Handle both array and single keys
        });

        // Only re-add keys that have non-empty values
        Object.entries(partialFilters).forEach(([key, value]) => {
            if (Array.isArray(value)) {
                if (value.length > 0) {
                    value.forEach(v => params.append(`${key}[]`, v));
                }
            } else if (value) {
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
        updateFilterCountDisplay();

        if (filtersForm) {
            const offcanvasFilters = filtersForm.querySelectorAll(`[name="${filterName}[]"]`);
            offcanvasFilters.forEach(input => {
                input.checked = values.includes(input.value);
            });
        }
    }

    function updateFilterCountDisplay() {
        const allInputs = filtersForm.querySelectorAll('input[type="checkbox"]');
        const checkedCount = Array.from(allInputs).filter(input => input.checked).length;

        const filterButtons = document.querySelectorAll('[data-filter-button]');
        filterButtons.forEach(button => {
            const baseLabel = button.dataset.filterText || "Filters";
            button.innerHTML = `
            ${baseLabel}${checkedCount > 0 ? ` (${checkedCount})` : ""}
            <svg xmlns="http://www.w3.org/2000/svg" class="ms-2" width="16" height="16" fill="none">
                <path d="M12.956 5.766c-.101-.244-.272-.452-.491-.599s-.477-.225-.741-.225H4.276c-.264 0-.521.078-.741.225s-.39.355-.491.598-.127.512-.076.77.178.496.365.683l3.724 3.724c.25.25.589.39.943.39s.693-.14.943-.39l3.724-3.724c.186-.186.313-.424.365-.682s.025-.527-.076-.77z" fill="currentColor"/>
            </svg>`;
        });
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
        currentImageId = imageId; // ✅ Track current ID
        fetch(`src/xhr/get-image-info.php?imageId=${encodeURIComponent(imageId)}`)
            .then(res => res.json())
            .then(imageData => {
                showImageWithProps(imageData);
            })
            .catch(console.error);
    }

    function openSiblingImage(direction) {
        const allAnchors = [...document.querySelectorAll('[data-image-id]')];
        const index = allAnchors.findIndex(a => a.getAttribute('data-image-id') === currentImageId);
        if (index === -1) return;

        const newIndex = direction === 'next' ? index + 1 : index - 1;
        const targetAnchor = allAnchors[newIndex];
        if (!targetAnchor) return;

        // Simulate a click event to reuse handleImageClick logic without navigating
        const fakeEvent = new Event('click');
        Object.defineProperty(fakeEvent, 'currentTarget', { value: targetAnchor });
        handleImageClick(fakeEvent);
    }

    function showImageWithProps(imageData) {
        // Clear props immediately
        imagePropsContainer.innerHTML = '';

        // Populate props while image is loading
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

        // Swap image + trigger modal only if it's not open yet
        const isAlreadyShown = imageViewerModalEl.classList.contains('show');

        imgEl.onload = null; // clear previous listener
        imgEl.src = imageData.url;
        imgEl.alt = imageData.alt || 'Image';

        if (!isAlreadyShown) {
            imageViewerModalInstance.show();
        }
    }

    if (!isMobile()) {
        fullscreenBtn?.addEventListener('click', () => {
            imageViewerModalEl.classList.toggle('fullscreen-mode');
        });

        imgEl.addEventListener('click', () => {
            imageViewerModalEl.classList.toggle('fullscreen-mode');
        });
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
            updateFilterCountDisplay();
        });
    });

    if (filtersForm) {
        filtersForm.addEventListener('change', () => {
            const formData = new FormData(filtersForm);
            const newFilters = {};

            for (const [key, value] of formData.entries()) {
                if (key.endsWith('[]')) {
                    const name = key.slice(0, -2);
                    if (!newFilters[name]) newFilters[name] = [];
                    newFilters[name].push(value);
                } else {
                    newFilters[key] = value;
                }
            }

            currentFilters = newFilters;
            updateUrlFilters(currentFilters);
            syncFilterControls('body-parts', currentFilters['body-parts'] || []);
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

    imageViewerModalEl.addEventListener('shown.bs.modal', () => {
        document.addEventListener('keydown', handleKeyNavigation);
    });
    imageViewerModalEl.addEventListener('hidden.bs.modal', () => {
        document.removeEventListener('keydown', handleKeyNavigation);
    });

    function handleKeyNavigation(e) {
        if (e.key === 'ArrowLeft') {
            openSiblingImage('prev');
        } else if (e.key === 'ArrowRight') {
            openSiblingImage('next');
        }
    }


    /*
    (function setupLogOverlay() {
        const logBox = document.createElement('pre');
        logBox.style.position = 'fixed';
        logBox.style.bottom = '0';
        logBox.style.left = '0';
        logBox.style.right = '0';
        logBox.style.maxHeight = '50vh';
        logBox.style.overflow = 'auto';
        logBox.style.background = 'rgba(0,0,0,0.8)';
        logBox.style.color = '#0f0';
        logBox.style.fontSize = '12px';
        logBox.style.padding = '10px';
        logBox.style.zIndex = '9999';
        document.body.appendChild(logBox);

        const originalLog = console.log;
        console.log = (...args) => {
            originalLog(...args);
            logBox.textContent += args.map(String).join(' ') + '\n';
        };
    })();
    */

    if (isMobile()) {
        let startX = 0;

        const modalContent = imageViewerModalEl.querySelector('.modal-content');

        modalContent.addEventListener('touchstart', e => {
            if (e.touches.length === 1) {
                startX = e.touches[0].clientX;
            }
        });

        modalContent.addEventListener('touchend', e => {
            if (e.changedTouches.length === 1) {
                const endX = e.changedTouches[0].clientX;
                const deltaX = endX - startX;

                if (Math.abs(deltaX) > 50) {
                    if (deltaX > 0) {
                        openSiblingImage('prev');
                    } else {
                        openSiblingImage('next');
                    }
                }
            }
        });

    }


    fillToFullViewport();
});