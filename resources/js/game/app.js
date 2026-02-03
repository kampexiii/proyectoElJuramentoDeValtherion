import '../echo';
import './settings';
import './profile';
import './chat';

// JS general zona logueada (migrado desde layouts si aplica)
(() => {
    const key = 'valtherion_theme';
    const root = document.documentElement;
    const saved = localStorage.getItem(key);

    if (saved === 'light' || saved === 'dark') {
        root.dataset.theme = saved;
    }

    const buttons = document.querySelectorAll('#themeToggle');
    if (!buttons.length) return;

    buttons.forEach((btn) => {
        btn.addEventListener('click', () => {
            const next = (root.dataset.theme === 'dark') ? 'light' : 'dark';
            root.dataset.theme = next;
            localStorage.setItem(key, next);
        });
    });
})();

// Medición segura de navbars para fijar el alto del main sin scroll.
(() => {
    const root = document.documentElement;

    const setNavbarHeights = () => {
        const bars = document.querySelectorAll('.navbar-game');
        if (!bars.length) return;

        const topBar = bars[0];
        const bottomBar = bars[bars.length - 1];

        const topHeight = topBar ? Math.round(topBar.getBoundingClientRect().height) : 0;
        const bottomHeight = bottomBar ? Math.round(bottomBar.getBoundingClientRect().height) : 0;

        root.style.setProperty('--topbar-h', `${topHeight}px`);
        root.style.setProperty('--bottombar-h', `${bottomHeight}px`);
    };

    const scheduleMeasure = () => {
        window.requestAnimationFrame(setNavbarHeights);
    };

    window.addEventListener('load', scheduleMeasure);
    window.addEventListener('resize', scheduleMeasure);
    scheduleMeasure();
})();
