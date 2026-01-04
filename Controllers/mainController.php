<?php

require_once __DIR__ . '/../Config/discordConfig.php';
require_once __DIR__ . '/../Config/themeConfig.php';
require_once __DIR__ . '/../Config/iconConfig.php';

// Chargement des contrôleurs
require_once __DIR__ . '/authController.php';
require_once __DIR__ . '/playlistController.php';
require_once __DIR__ . '/videoController.php';
require_once __DIR__ . '/dashboardController.php';
require_once __DIR__ . '/userController.php';
require_once __DIR__ . '/forumController.php';

// Configuration du thème
setThemeFromPost();
$theme_preference = getCurrentTheme();

// Récupération des informations utilisateur
$userId = $_SESSION['user_id'] ?? null;
$userRole = $_SESSION['user_role'] ?? null;

// Parsing de l'URI et méthode HTTP
$uri = strtolower(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$method = $_SERVER['REQUEST_METHOD'];

// ================================================================
// ROUTAGE PRINCIPAL
// ================================================================

// Page d'accueil
if ($uri === "/index.php" || $uri === "/") {
    homeController($pdo);
}
// Authentification Discord
elseif (str_starts_with($uri, '/auth/')) {
    handleAuthRoutes($uri, $pdo);
}
// Gestion des playlists (public)
elseif (str_starts_with($uri, '/playlist')) {
    handlePlaylistRoutes($uri, $method, $pdo, $userId);
}
// Profil utilisateur
elseif ($uri === '/profile') {
    handleUserProfile($pdo, $userId);
}
// Dashboard admin
elseif (str_starts_with($uri, '/dashboard')) {
    handleDashboardRoutes($uri, $method, $pdo, $userId, $userRole);
}
// Forum (nouveau)
elseif (str_starts_with($uri, '/forum')) {
    handleForumRoutes($uri, $method, $pdo, $userId);
}
// Déconnexion
elseif ($uri === '/logout') {
    handleLogout();
}
// 404
else {
    handle404();
}

// ================================================================
// FONCTIONS PRINCIPALES
// ================================================================

function homeController($pdo) {
    require_once __DIR__ . '/../Models/playlistModel.php';
    
    $title = "Home page";
    $playlists = getAllPlaylists($pdo);
    $template = __DIR__ . "/../Views/Pages/home.php";
    require_once(__DIR__ . "/../Views/base.php");
}

function handleAuthRoutes($uri, $pdo) {
    if ($uri === '/auth/discord') {
        initiateDiscordAuth();
    } elseif ($uri === '/auth/discord/callback') {
        handleDiscordCallback($pdo);
    } else {
        handle404();
    }
}

function handlePlaylistRoutes($uri, $method, $pdo, $userId) {
    if ($uri === '/playlists') {
        showAllPlaylists($pdo, $userId);
    } elseif ($uri === '/playlist' && isset($_GET['playlistId'])) {
        showPlaylistWithVideos($pdo, $userId);
    } else {
        handle404();
    }
}

function handleDashboardRoutes($uri, $method, $pdo, $userId, $userRole) {
    // Vérification des permissions admin
    if (!$userId || $userRole !== "admin") {
        $_SESSION['error'] = "Unauthorized access";
        header("Location: /");
        exit;
    }
    
    if ($uri === '/dashboard') {
        showDashboard($pdo);
    } elseif (str_starts_with($uri, '/dashboard/playlist')) {
        handleDashboardPlaylistRoutes($uri, $method, $pdo, $userId);
    //} 
    // elseif (str_starts_with($uri, '/dashboard/user')) {
    //     handleDashboardUserRoutes($uri, $method, $pdo);
    } else {
        handle404();
    }
}

function handleForumRoutes($uri, $method, $pdo, $userId) {
    if ($uri === '/forum') {
        showForumIndex($pdo, $userId);
    } elseif ($uri === '/forum/topic' && isset($_GET['topicId'])) {
        showForumTopic($pdo, $userId);
    } elseif ($uri === '/forum/create') {
        createForumTopic($pdo, $userId, $method);
    } else {
        handle404();
    }
}

function handleLogout() {
    session_unset();
    session_destroy();
    header("Location: /");
    exit;
}

function handle404() {
    http_response_code(404);
    $title = "Page non trouvée - 404";
    
    if (file_exists(__DIR__ . "/../Views/Pages/404.php")) {
        $template = __DIR__ . "/../Views/Pages/404.php";
        require_once(__DIR__ . "/../Views/base.php");
    } else {
        echo "<h1>404 - Page non trouvée</h1>";
        exit;
    }
}