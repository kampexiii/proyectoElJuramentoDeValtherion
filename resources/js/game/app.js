import './settings';

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
