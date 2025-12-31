<div class="header__logo">
    <a href="/">
        <img src="/Assets/Pictures/IconBLENDER/icon_blender_red_and_white.png" alt="Blender Heaj Logo" class="logo">
    </a>
    <h1><a href="/">Blender Heaj</a></h1>
</div>

<nav class="header__nav">
    <ul class="nav__list">
        <li class="nav__item">
            <a href="/" class="<?= ($_SERVER['REQUEST_URI'] === '/' ? 'active' : '') ?>">
                <span class="material-icons hide-mobile" translate="no">home</span>
                Home
            </a>
        </li>
        <li class="nav__item">
            <a href="/Playlists" class="<?= (strpos($_SERVER['REQUEST_URI'], '/playlist') !== false ? 'active' : '') ?>">
                <span class="material-icons hide-mobile" >playlist_play</span>
                Playlists
            </a>
        </li>
        <li class="nav__item">
            <a href="/Forums">
                <span class="material-icons hide-mobile" translate="no">forum</span>
                Forum
            </a>
        </li>
        <?php if (isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === "admin") : ?>
            <li class="nav__item">
                <a href="/dashboard" class="<?= (strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false ? 'active' : '') ?>">
                    <span class="material-icons hide-mobile" translate="no">dashboard</span>
                    Dashboard
                </a>
            </li>
        <?php endif ?>
        <?php if (isset($_SESSION['user_id'])) : ?>
            <li class="nav__item">
                <a href="/profile" class="<?= ($_SERVER['REQUEST_URI'] === '/profile' ? 'active' : '') ?>">
                    <span class="material-icons hide-mobile" translate="no">person</span>
                    Profile
                </a>
            </li>
        <?php endif ?>
    </ul>
</nav>

<div class="header__user">
    <?php if (!isset($_SESSION['user_id'])) : ?>
        <a class="user__link" href="/auth/discord">
            <span class="material-icons" translate="no">login</span>
            <span class="hide-mobile" translate="no">Login with Discord</span>
        </a>
    <?php else: ?>
        <?php if (isset($_SESSION['discord_avatar'])) : ?>
            <img src="<?= htmlspecialchars($_SESSION['discord_avatar']) ?>" alt="Avatar" class="user__avatar">
        <?php endif ?>
        <a class="user__link" href="/logout">
            <span class="material-icons" translate="no">logout</span>
            <span class="hide-mobile" translate="no">Log out</span>
        </a>
    <?php endif ?>
</div>