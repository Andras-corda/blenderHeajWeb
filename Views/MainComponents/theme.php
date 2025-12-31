<!-- Theme Switcher Dropdown -->
<div class="theme-dropdown">
    <button type="button" class="theme-dropdown__trigger" aria-label="Changer le thème">
        <span class="material-icons">palette</span>
        <span class="hide-mobile"></span>
    </button>
    <div class="theme-dropdown__menu">
        <form method="POST">
            <button type="submit" name="theme" value="dark" class="theme-dropdown__item <?= ($theme_preference ?? 'dark') === 'dark' ? 'active' : '' ?>">
                <span class="theme-dropdown__icon">🌙</span>
                <span>Dark</span>
                <span class="material-icons theme-dropdown__check">check</span>
            </button>
            <button type="submit" name="theme" value="light" class="theme-dropdown__item <?= ($theme_preference ?? '') === 'light' ? 'active' : '' ?>">
                <span class="theme-dropdown__icon">☀️</span>
                <span>Light</span>
                <span class="material-icons theme-dropdown__check">check</span>
            </button>
            <button type="submit" name="theme" value="CAN" class="theme-dropdown__item <?= ($theme_preference ?? '') === 'CAN' ? 'active' : '' ?>">
                <span class="theme-dropdown__icon">🔮</span>
                <span>Crimson Amethyst</span>
                <span class="material-icons theme-dropdown__check">check</span>
            </button>
            <button type="submit" name="theme" value="OM" class="theme-dropdown__item <?= ($theme_preference ?? '') === 'OM' ? 'active' : '' ?>">
                <span class="theme-dropdown__icon">🌕</span>
                <span>Orange Moon</span>
                <span class="material-icons theme-dropdown__check">check</span>
            </button>
        </form>
    </div>
</div>