function initMobileSidebar() {
    const sidebar = document.getElementById('appSidebar');
    const backdrop = document.querySelector('.sidebar-backdrop');
    const toggle = document.querySelector('.menu-toggle');
    if (!sidebar || !backdrop || !toggle) return;

    const setOpen = (isOpen) => {
        sidebar.classList.toggle('open', isOpen);
        backdrop.classList.toggle('show', isOpen);
        toggle.setAttribute('aria-expanded', String(isOpen));
    };

    setOpen(sidebar.classList.contains('open'));
    toggle.addEventListener('click', () => setOpen(!sidebar.classList.contains('open')));
    backdrop.addEventListener('click', () => setOpen(false));
}

function restoreSubmitState() {
    document.querySelectorAll('form.is-submitting').forEach((form) => {
        form.classList.remove('is-submitting');
        form.querySelectorAll('button[type="submit"][data-label]').forEach((button) => {
            button.innerHTML = button.dataset.label;
            button.disabled = false;
            button.removeAttribute('aria-busy');
            delete button.dataset.label;
        });
    });
}

function initSubmitState() {
    document.querySelectorAll('form[method="POST"]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (event.defaultPrevented) return;

            const button = event.submitter instanceof HTMLButtonElement
                ? event.submitter
                : form.querySelector('button[type="submit"]');

            if (!button || form.classList.contains('is-submitting')) {
                event.preventDefault();
                return;
            }

            form.classList.add('is-submitting');
            button.dataset.label = button.innerHTML;
            button.innerHTML = '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span><span>Mengirim...</span>';
            button.setAttribute('aria-busy', 'true');
            button.disabled = true;
        });
    });

    window.addEventListener('pageshow', restoreSubmitState);
}

async function writeToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
        try {
            await navigator.clipboard.writeText(text);
            return;
        } catch (error) {
            // Fall through for browsers that expose but deny the async API.
        }
    }

    const previousFocus = document.activeElement;
    const helper = document.createElement('textarea');
    helper.value = text;
    helper.setAttribute('readonly', '');
    helper.setAttribute('aria-hidden', 'true');
    helper.setAttribute('tabindex', '-1');
    helper.style.position = 'fixed';
    helper.style.left = '-9999px';
    helper.style.opacity = '0';
    helper.style.pointerEvents = 'none';
    document.body.appendChild(helper);

    let copied = false;
    try {
        helper.select();
        copied = document.execCommand('copy');
    } finally {
        helper.remove();
        if (previousFocus instanceof HTMLElement) previousFocus.focus({ preventScroll: true });
    }

    if (!copied) throw new Error('Clipboard API tidak tersedia.');
}

function replayLabelAnimation(label, text) {
    label.textContent = text;
    label.classList.remove('is-changing');
    void label.offsetWidth;
    label.classList.add('is-changing');
}

function initCopyFeedback() {
    document.querySelectorAll('[data-copy-target]').forEach((button) => {
        button.addEventListener('click', async () => {
            const target = document.getElementById(button.dataset.copyTarget);
            const label = button.querySelector('.copy-label');
            if (!target || !label) return;

            try {
                await writeToClipboard(target.textContent);
                window.clearTimeout(button.copyResetTimer);
                replayLabelAnimation(label, '✓ Copied');
                button.copyResetTimer = window.setTimeout(() => {
                    replayLabelAnimation(label, 'Copy');
                }, 1500);
            } catch (error) {
                console.warn('Payload tidak dapat disalin.', error);
            }
        });
    });
}

function initBackgroundParallax() {
    if (!window.matchMedia || !window.requestAnimationFrame) return;

    const layers = [
        { element: document.querySelector('.sphere-small'), maxX: 5, maxY: 4 },
        { element: document.querySelector('.sphere-large'), maxX: -3, maxY: -2 },
        { element: document.querySelector('.ambient-ribbon'), maxX: 2, maxY: 1.5 },
        { element: document.querySelector('.biometric-contour'), maxX: -1.5, maxY: -1 },
    ].filter((layer) => layer.element);
    if (!layers.length) return;

    const motionQuery = window.matchMedia('(min-width: 992px) and (hover: hover) and (pointer: fine) and (prefers-reduced-motion: no-preference)');
    const coarseQuery = window.matchMedia('(any-pointer: coarse)');
    let enabled = false;
    let frameId = null;
    let lastTime = 0;
    let targetX = 0;
    let targetY = 0;
    let currentX = 0;
    let currentY = 0;

    const paint = () => {
        layers.forEach(({ element, maxX, maxY }) => {
            element.style.setProperty('--parallax-x', `${(currentX * maxX).toFixed(3)}px`);
            element.style.setProperty('--parallax-y', `${(currentY * maxY).toFixed(3)}px`);
        });
    };

    const render = (time) => {
        const elapsed = lastTime ? Math.min(time - lastTime, 32) : 16.67;
        const blend = 1 - Math.exp(-elapsed / 140);
        lastTime = time;
        currentX += (targetX - currentX) * blend;
        currentY += (targetY - currentY) * blend;

        const settled = Math.abs(targetX - currentX) < .0005
            && Math.abs(targetY - currentY) < .0005;
        if (settled) {
            currentX = targetX;
            currentY = targetY;
        }
        paint();

        if (settled) {
            frameId = null;
            lastTime = 0;
        } else {
            frameId = window.requestAnimationFrame(render);
        }
    };

    const queueRender = () => {
        if (frameId === null) frameId = window.requestAnimationFrame(render);
    };

    const returnToCenter = () => {
        targetX = 0;
        targetY = 0;
        queueRender();
    };

    const handlePointerMove = (event) => {
        if (event.pointerType && event.pointerType !== 'mouse') return;
        targetX = Math.max(-1, Math.min(1, (event.clientX / Math.max(window.innerWidth, 1)) * 2 - 1));
        targetY = Math.max(-1, Math.min(1, (event.clientY / Math.max(window.innerHeight, 1)) * 2 - 1));
        queueRender();
    };

    const handlePointerOut = (event) => {
        if (event.relatedTarget === null) returnToCenter();
    };

    const clearPosition = () => {
        if (frameId !== null) window.cancelAnimationFrame(frameId);
        frameId = null;
        lastTime = 0;
        targetX = 0;
        targetY = 0;
        currentX = 0;
        currentY = 0;
        layers.forEach(({ element }) => {
            element.style.removeProperty('--parallax-x');
            element.style.removeProperty('--parallax-y');
        });
    };

    const enable = () => {
        if (enabled) return;
        enabled = true;
        window.addEventListener('pointermove', handlePointerMove, { passive: true });
        window.addEventListener('pointerout', handlePointerOut, { passive: true });
        window.addEventListener('blur', returnToCenter);
        window.addEventListener('resize', returnToCenter, { passive: true });
    };

    const disable = () => {
        if (!enabled) {
            clearPosition();
            return;
        }

        enabled = false;
        window.removeEventListener('pointermove', handlePointerMove);
        window.removeEventListener('pointerout', handlePointerOut);
        window.removeEventListener('blur', returnToCenter);
        window.removeEventListener('resize', returnToCenter);
        clearPosition();
    };

    const syncSafetyState = () => {
        const touchDevice = (navigator.maxTouchPoints || 0) > 0;
        if (motionQuery.matches && !coarseQuery.matches && !touchDevice) {
            enable();
        } else {
            disable();
        }
    };

    [motionQuery, coarseQuery].forEach((query) => {
        if (typeof query.addEventListener === 'function') {
            query.addEventListener('change', syncSafetyState);
        } else {
            query.addListener(syncSafetyState);
        }
    });
    syncSafetyState();
}

document.addEventListener('DOMContentLoaded', () => {
    initMobileSidebar();
    initSubmitState();
    initCopyFeedback();
    initBackgroundParallax();
});
