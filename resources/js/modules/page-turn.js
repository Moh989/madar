// A chain of narrow, hinged strips lets the sheet bend as it turns.
export function createPageTurn(book, fromImage, toImage, backward = false) {
    const stripCount = 24;
    const width = book.clientWidth;
    const height = book.clientHeight;
    const layer = document.createElement('div');
    layer.className = 'paper-turn';
    layer.setAttribute('aria-hidden', 'true');
    layer.inert = true;
    const underneath = document.createElement('div');
    underneath.className = 'paper-underneath';
    underneath.style.backgroundImage = `url(${JSON.stringify(backward ? fromImage : toImage)})`;
    const shadow = document.createElement('div');
    shadow.className = 'paper-cast-shadow';
    const sheet = document.createElement('div');
    sheet.className = 'paper-sheet';
    layer.append(underneath, shadow, sheet);
    const strips = [];
    const shades = [];
    let parent = sheet;
    for (let index = 0; index < stripCount; index++) {
        const strip = document.createElement('div');
        strip.className = 'paper-strip';
        const front = document.createElement('div');
        front.className = 'paper-face paper-front';
        front.style.backgroundImage = `url(${JSON.stringify(backward ? toImage : fromImage)})`;
        front.style.backgroundSize = `${width}px ${height}px`;
        front.style.backgroundPosition = `${-index * width / stripCount}px 0`;
        const shade = document.createElement('div');
        shade.className = 'paper-shading';
        front.append(shade);
        const back = document.createElement('div');
        back.className = 'paper-face paper-back';
        strip.append(front, back);
        parent.append(strip);
        strips.push(strip);
        shades.push(shade);
        parent = strip;
    }
    strips[0].style.width = `${100 / stripCount}%`;
    book.append(layer);
    book.classList.add('is-turning');
    let progress = 0;
    let frame = null;
    let finishAnimation = null;
    let removed = false;

    const render = value => {
        progress = Math.min(1, Math.max(0, value));
        const fold = backward ? 1 - progress : progress;
        const bend = Math.sin(fold * Math.PI);
        strips.forEach((strip, index) => {
            const angle = index === 0 ? -180 * fold : bend * 44 / stripCount;
            strip.style.transform = `rotateY(${angle}deg)`;
            shades[index].style.opacity = String(bend * (.08 + index / stripCount * .22));
        });
        // At the end the turned sheet settles out of the single-page reading area.
        sheet.style.opacity = String(Math.min(1, (1 - fold) * 8));
        shadow.style.opacity = String(bend * .3);
        shadow.style.transform = `scaleX(${Math.max(.05, 1 - fold)})`;
    };
    const stop = () => {
        cancelAnimationFrame(frame);
        finishAnimation?.(false);
        finishAnimation = null;
    };
    render(0);
    return {
        setProgress: render,
        animateTo(target, duration = 850) {
            stop();
            if (removed) return Promise.resolve(false);
            const startProgress = progress;
            const startTime = performance.now();
            return new Promise(resolve => {
                finishAnimation = resolve;
                const tick = now => {
                    const elapsed = Math.min(1, (now - startTime) / Math.max(1, duration));
                    const eased = elapsed < .5 ? 4 * elapsed ** 3 : 1 - (-2 * elapsed + 2) ** 3 / 2;
                    render(startProgress + (target - startProgress) * eased);
                    if (elapsed < 1) frame = requestAnimationFrame(tick);
                    else { finishAnimation = null; resolve(true); }
                };
                frame = requestAnimationFrame(tick);
            });
        },
        remove() {
            stop();
            removed = true;
            layer.remove();
            book.classList.remove('is-turning');
        }
    };
}
