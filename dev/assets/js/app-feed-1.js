document.addEventListener('DOMContentLoaded', function () {
    const endpoint = 'src/xhr/get-images.php';
    const galleryContainer = document.querySelector('.row.row-cols-xl-5');
    const sentinel = document.getElementById('scroll-sentinel');
    let offset = 0;
    let loading = false;

    if (!galleryContainer || !sentinel) return;

    function getColumnCount() {
        const width = window.innerWidth;
        if (width >= 1400) return 6; // xxl
        if (width >= 1200) return 5; // xl
        if (width >= 768) return 4;  // md
        if (width >= 576) return 3;  // sm
        return 2;                    // default
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

        return height || 200; // fallback to 200px
    }

    function loadImages(count) {
        if (loading) return;
        loading = true;

        return fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({ limit: count, offset }),
        })
            .then(response => response.json())
            .then(images => {
                offset += images.length;
                renderImages(images);
                loading = false;
            })
            .catch(err => {
                console.error('Error loading images:', err);
                loading = false;
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

            console.log({
                rowHeight,
                rowTop,
                availableHeight,
                rows,
                cols,
                total
            });

            galleryContainer.removeChild(placeholder);
            loadImages(total);
        });
    }

    function debounce(fn, delay) {
        let timeout;
        return function () {
            clearTimeout(timeout);
            timeout = setTimeout(fn, delay);
        };
    }

    // Infinite scroll observer
    const observer = new IntersectionObserver(entries => {
        if (entries[0].isIntersecting) {
            const cols = getColumnCount();
            const rowsToAdd = 5;
            const count = rowsToAdd * cols;
            loadImages(count);
        }
    }, {
        rootMargin: '100px',
    });

    observer.observe(sentinel);

    window.addEventListener('resize', debounce(() => {
        // Optional: on resize, reload everything from scratch
        offset = 0;
        galleryContainer.innerHTML = '';
        fillToFullViewport();
    }, 200));

    fillToFullViewport();
});
