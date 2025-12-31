console.log('=== DEBUG THEME ===');
console.log('1. Preference:', document.documentElement.getAttribute('data-theme-preference'));
console.log('2. Theme actuel:', document.documentElement.getAttribute('data-theme'));
console.log('3. Système dark?', window.matchMedia('(prefers-color-scheme: dark)').matches);
console.log('4. HTML element:', document.documentElement);

// Détecter et appliquer le thème système
function applyTheme() {
    const preference = document.documentElement.getAttribute('data-theme-preference');

    if (preference === 'system') {
        // Détecter le thème système
        const isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        document.documentElement.setAttribute('data-theme', isDark ? 'dark' : 'light');
    } else {
        // Appliquer le thème choisi manuellement
        document.documentElement.setAttribute('data-theme', preference);
    }
}

// Appliquer au chargement
applyTheme();

// Écouter les changements du thème système
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', applyTheme);