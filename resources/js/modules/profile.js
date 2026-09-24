import {createPageTurn} from './page-turn';

const magazine = document.querySelector('[data-magazine]');
if (magazine) {
    const pages = JSON.parse(magazine.querySelector('[data-profile-pages]').textContent);
    const book = magazine.querySelector('[data-book]');
    const stage = magazine.querySelector('[data-stage]');
    const previous = magazine.querySelector('[data-previous]');
    const next = magazine.querySelector('[data-next]');
    const count = magazine.querySelector('[data-page-count]');
    const input = magazine.querySelector('#profile-page-number');
    const thumbnails = magazine.querySelector('#profile-thumbnails');
    const pageText = document.querySelector('[data-page-text]');
    const error = magazine.querySelector('[data-page-error]');
    const reducedMotion = matchMedia('(prefers-reduced-motion: reduce)');
    const rtl = document.documentElement.dir === 'rtl';
    const loaded = new Map();
    let current = Number(magazine.dataset.current);
    let sequence = 0;
    let requested = current;
    let activeTurn = null;
    let gesture = null;
    let suppressClickUntil = 0;

    const visiblePages = number => [number];
    const previousPage = () => requested - 1;
    const nextPage = () => requested + 1;
    const label = number => `${magazine.dataset.pageLabel} ${number} — ${magazine.dataset.zoomLabel}`;
    const imageReady = number => {
        if (number < 1 || number > pages.length) return Promise.resolve();
        if (!loaded.has(number)) {
            const image = new Image();
            const promise = new Promise((resolve, reject) => {
                image.onload = resolve;
                image.onerror = () => { loaded.delete(number); reject(new Error('Page image unavailable')); };
            });
            loaded.set(number, promise);
            image.src = pages[number - 1].image;
        }
        return loaded.get(number);
    };
    const setBoundary = (link, disabled, number) => {
        link.setAttribute('aria-disabled', String(disabled));
        link.tabIndex = disabled ? -1 : 0;
        const url = new URL(location.href);
        url.searchParams.set('page', String(Math.min(pages.length, Math.max(1, number))));
        link.href = url;
    };
    const updateState = () => {
        const visible = visiblePages(current);
        count.textContent = visible.map(number => String(number).padStart(2, '0')).join('–');
        input.value = current;
        magazine.dataset.current = current;
        magazine.querySelector('[data-page-progress]').style.width = `${visible.at(-1) / pages.length * 100}%`;
        setBoundary(previous, current === 1, previousPage());
        setBoundary(next, visible.at(-1) === pages.length, nextPage());
        magazine.querySelector('[data-stage-previous]').disabled = current === 1;
        magazine.querySelector('[data-stage-next]').disabled = current === pages.length;
        thumbnails.querySelectorAll('[data-thumbnail]').forEach(link => {
            if (visible.includes(Number(link.dataset.thumbnail))) link.setAttribute('aria-current', 'page');
            else link.removeAttribute('aria-current');
        });
        pageText.replaceChildren();
        visible.forEach(number => {
            const heading = document.createElement('h2');
            heading.textContent = `${magazine.dataset.pageLabel} ${number}`;
            const paragraph = document.createElement('p');
            paragraph.dir = 'auto';
            paragraph.textContent = pages[number - 1].text;
            pageText.append(heading, paragraph);
        });
        const url = new URL(location.href);
        if (current === 1) url.searchParams.delete('page'); else url.searchParams.set('page', String(current));
        history.replaceState(null, '', url);
        const switcher = document.querySelector('.language');
        if (switcher) {
            const other = new URL(switcher.href);
            if (current === 1) other.searchParams.delete('page'); else other.searchParams.set('page', String(current));
            switcher.href = other;
        }
    };
    const makePage = number => {
        const data = pages[number - 1];
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'magazine-page';
        button.dataset.pageNumber = number;
        button.setAttribute('aria-label', label(number));
        const image = document.createElement('img');
        image.src = data.image;
        image.width = data.width;
        image.height = data.height;
        image.alt = `${magazine.dataset.pageLabel} ${number}`;
        image.draggable = false;
        button.append(image);
        button.addEventListener('click', () => { if (performance.now() > suppressClickUntil && !gesture?.moving) openZoom(number); });
        return button;
    };
    const clearTurn = () => {
        activeTurn?.remove();
        activeTurn = null;
        book.setAttribute('aria-busy', 'false');
    };
    const commit = (number, restoreFocus = false) => {
        const focusWasInBook = restoreFocus || book.contains(document.activeElement);
        clearTurn();
        current = requested = number;
        book.replaceChildren(makePage(current));
        updateState();
        if (focusWasInBook) stage.focus({preventScroll: true});
        [current - 1, current + 1].forEach(number => imageReady(number).catch(() => {}));
    };
    const turn = async (number, animate = true) => {
        if (!Number.isInteger(number) || number < 1 || number > pages.length) return;
        const ticket = ++sequence;
        const restoreFocus = book.contains(document.activeElement);
        gesture = null;
        clearTurn();
        requested = number;
        error.hidden = true;
        book.setAttribute('aria-busy', 'true');
        try { await imageReady(number); }
        catch {
            if (ticket === sequence) {
                requested = current;
                error.textContent = magazine.dataset.errorLabel;
                error.hidden = false;
                book.setAttribute('aria-busy', 'false');
            }
            return;
        }
        if (ticket !== sequence) return;
        if (animate && !reducedMotion.matches && number !== current) {
            activeTurn = createPageTurn(book, pages[current - 1].image, pages[number - 1].image, number < current);
            const finished = await activeTurn.animateTo(1);
            if (!finished || ticket !== sequence) return;
        }
        commit(number, restoreFocus);
    };
    magazine.querySelector('[data-stage-previous]').addEventListener('click', () => turn(previousPage()));
    magazine.querySelector('[data-stage-next]').addEventListener('click', () => turn(nextPage()));
    previous.addEventListener('click', event => { event.preventDefault(); if (current > 1) turn(previousPage()); });
    next.addEventListener('click', event => { event.preventDefault(); if (nextPage() <= pages.length) turn(nextPage()); });
    magazine.querySelector('[data-page-form]').addEventListener('submit', event => {
        event.preventDefault();
        if (input.checkValidity()) turn(Number(input.value));
    });
    thumbnails.querySelectorAll('a').forEach(link => link.addEventListener('click', event => {
        event.preventDefault(); turn(Number(link.dataset.thumbnail));
    }));
    magazine.querySelector('[data-toggle-thumbnails]').addEventListener('click', event => {
        thumbnails.hidden = !thumbnails.hidden;
        event.currentTarget.setAttribute('aria-expanded', String(!thumbnails.hidden));
        if (!thumbnails.hidden) thumbnails.querySelector('[aria-current]')?.scrollIntoView({block: 'nearest', inline: 'center'});
    });
    magazine.addEventListener('keydown', event => {
        if (event.target.matches('input, select, textarea') || zoomDialog.open) return;
        const nextKey = rtl ? 'ArrowLeft' : 'ArrowRight';
        const previousKey = rtl ? 'ArrowRight' : 'ArrowLeft';
        if ([nextKey, previousKey, 'Home', 'End', 'PageDown', 'PageUp'].includes(event.key)) {
            event.preventDefault();
            const number = event.key === 'Home' ? 1 : event.key === 'End' ? pages.length : [nextKey, 'PageDown'].includes(event.key) ? nextPage() : previousPage();
            turn(number);
        }
    });
    book.addEventListener('dragstart', event => event.preventDefault());
    book.addEventListener('pointerdown', event => {
        if (!event.isPrimary || event.button !== 0 || activeTurn || book.getAttribute('aria-busy') === 'true') return;
        gesture = {id: event.pointerId, x: event.clientX, y: event.clientY, dx: 0, moving: false, progress: 0, renderer: null};
    });
    window.addEventListener('pointermove', async event => {
        const drag = gesture;
        if (!drag || drag.id !== event.pointerId) return;
        drag.dx = event.clientX - drag.x;
        const dy = event.clientY - drag.y;
        if (!drag.moving) {
            if (Math.abs(dy) > 14 && Math.abs(dy) > Math.abs(drag.dx)) { gesture = null; return; }
            if (Math.abs(drag.dx) < 12 || Math.abs(drag.dx) < Math.abs(dy) * 1.4) return;
            suppressClickUntil = performance.now() + 1000;
            drag.target = current + (drag.dx < 0 ? 1 : -1);
            if (drag.target < 1 || drag.target > pages.length) { gesture = null; return; }
            drag.moving = true;
            drag.restoreFocus = book.contains(document.activeElement);
            book.setPointerCapture(event.pointerId);
            drag.ticket = ++sequence;
            error.hidden = true;
            try { await imageReady(drag.target); }
            catch {
                if (gesture === drag) {
                    error.textContent = magazine.dataset.errorLabel;
                    error.hidden = false;
                    gesture = null;
                }
                return;
            }
            if (gesture !== drag || drag.ticket !== sequence) return;
            if (!reducedMotion.matches) {
                activeTurn = drag.renderer = createPageTurn(book, pages[current - 1].image, pages[drag.target - 1].image, drag.target < current);
            }
        }
        const distance = drag.target > current ? -drag.dx : drag.dx;
        drag.progress = Math.min(.98, Math.max(0, distance / (book.clientWidth * .85)));
        drag.renderer?.setProgress(drag.progress);
    }, {passive: true});
    const releaseGesture = async (event, cancelled = false) => {
        const drag = gesture;
        if (!drag || drag.id !== event.pointerId) return;
        gesture = null;
        if (book.hasPointerCapture(event.pointerId)) book.releasePointerCapture(event.pointerId);
        if (!drag.moving) return;
        suppressClickUntil = performance.now() + 700;
        const distance = drag.target > current ? -drag.dx : drag.dx;
        const complete = !cancelled && distance > Math.min(95, book.clientWidth * .24);
        if (!drag.renderer) {
            if (complete) turn(drag.target);
            return;
        }
        const finished = await drag.renderer.animateTo(complete ? 1 : 0, 500);
        if (!finished || drag.ticket !== sequence) return;
        if (complete) commit(drag.target, drag.restoreFocus);
        else {
            clearTurn();
            if (drag.restoreFocus) stage.focus({preventScroll: true});
        }
    };
    window.addEventListener('pointerup', event => releaseGesture(event));
    window.addEventListener('pointercancel', event => releaseGesture(event, true));
    book.addEventListener('lostpointercapture', event => { if (event.target === book && gesture) releaseGesture(event, true); });
    reducedMotion.addEventListener('change', () => {
        if (activeTurn || gesture?.moving) turn(current, false);
    });
    window.addEventListener('resize', () => { if (activeTurn) turn(requested, false); });
    document.addEventListener('visibilitychange', () => {
        if (document.hidden && activeTurn) turn(requested, false);
    });

    const fullscreenButton = magazine.querySelector('[data-fullscreen]');
    if (document.fullscreenEnabled && magazine.requestFullscreen) {
        fullscreenButton.hidden = false;
        fullscreenButton.addEventListener('click', async () => {
            try {
                if (document.fullscreenElement) await document.exitFullscreen();
                else await magazine.requestFullscreen();
            } catch { fullscreenButton.hidden = true; }
        });
        document.addEventListener('fullscreenchange', () => {
            fullscreenButton.querySelector('[data-fullscreen-label]').textContent = document.fullscreenElement ? magazine.dataset.exitFullscreenLabel : magazine.dataset.fullscreenLabel;
        });
    }
    const zoomDialog = magazine.querySelector('[data-zoom-dialog]');
    const zoomImage = magazine.querySelector('[data-zoom-image]');
    const zoomScroll = magazine.querySelector('[data-zoom-scroll]');
    const zoomLevel = magazine.querySelector('[data-zoom-level]');
    let zoom = 1;
    let zoomOpener = null;
    const setZoom = level => {
        zoom = Math.min(3, Math.max(1, level));
        zoomImage.style.width = `${zoom * 100}%`;
        zoomImage.style.maxWidth = zoom === 1 ? 'min(100%, 62vh)' : 'none';
        zoomLevel.textContent = `${Math.round(zoom * 100)}%`;
        magazine.querySelector('[data-zoom-out]').disabled = zoom === 1;
        magazine.querySelector('[data-zoom-in]').disabled = zoom === 3;
    };
    const openZoom = number => {
        zoomOpener = document.activeElement;
        zoomImage.src = pages[number - 1].image;
        zoomImage.alt = `${magazine.dataset.pageLabel} ${number}`;
        magazine.querySelector('[data-zoom-title]').textContent = `${magazine.dataset.pageLabel} ${number} ${magazine.dataset.ofLabel} ${pages.length}`;
        setZoom(1);
        zoomDialog.showModal();
        document.body.style.overflow = 'hidden';
        zoomScroll.scrollTo(0, 0);
    };
    magazine.querySelector('[data-reader-zoom]').addEventListener('click', () => openZoom(current));
    magazine.querySelector('[data-zoom-in]').addEventListener('click', () => setZoom(zoom + .5));
    magazine.querySelector('[data-zoom-out]').addEventListener('click', () => setZoom(zoom - .5));
    magazine.querySelector('[data-zoom-close]').addEventListener('click', () => zoomDialog.close());
    zoomDialog.addEventListener('close', () => { document.body.style.overflow = ''; zoomOpener?.focus({preventScroll: true}); });
    zoomDialog.addEventListener('click', event => { if (event.target === zoomDialog) zoomDialog.close(); });
    document.querySelectorAll('[data-enhanced-control]').forEach(element => { element.hidden = false; });
    thumbnails.hidden = true;
    magazine.classList.add('is-enhanced');
    turn(current, false);
}
