/**
 * Unified Theme Management for Enrollment System
 */

function updateThemeIcon(theme) {
    const iconElement = document.getElementById('theme-icon');
    if (iconElement) {
        iconElement.innerText = theme === 'dark' ? '🌙' : '☀️';
    }
}

function applyTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem('theme', theme);
    updateThemeIcon(theme);
}

function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    applyTheme(newTheme);
}

// Global initialization
document.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem('theme') || 'light';
    applyTheme(savedTheme);

    // Initialize Lucide Icons if available
    if (window.lucide) {
        window.lucide.createIcons();
    }
});

window.toggleTheme = toggleTheme;
