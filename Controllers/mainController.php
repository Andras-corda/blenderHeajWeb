<?php

require_once __DIR__ . '/../Models/userModel.php';        // Model => Gestion des utilisateurs
require_once __DIR__ . '/../Models/videoModel.php';       // Model => Gestion des vidéos
require_once __DIR__ . '/../Models/playlistModel.php';    // Model => Gestion des playlists
require_once __DIR__ . '/../Models/discordAuthModel.php'; // Model => Gestion de connexion avec discord

require_once __DIR__ . '/../Config/discordConfig.php';      // Config => Discord
require_once __DIR__ . '/../Config/themeConfig.php';        // Config => Theme
require_once __DIR__ . '/../Config/iconConfig.php';         // Config => Icon
                                                                              
setThemeFromPost();                                       // Set theme   
$theme_preference = getCurrentTheme();                    // Get theme   
                                                          
$userId = $_SESSION['user_id'] ?? null;                   // If user are loged take his ID
$userRole = $_SESSION['user_role'] ?? null;               // If user are loged take his permission

$uri = strtolower(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));  // Get current URI && Parsing
$method = $_SERVER['REQUEST_METHOD'];                                 // Get request method

/// ================================================================
/// Rooting :
/// '/index.php' <=> '/' => Home page
/// '/playlists' => display all playlists
/// 'playlist' => display of a playlist and video player
/// USERS :
/// '/profile' => display user profile (discord)
/// '/auth/discord' => discord call api and auth + csrf token
/// '/auth/discord/callback' => csrf verification and login
/// '/logout' => log out user
/// DASHBOARD :
/// '/dashboard' => admin pannel view to manage playlist and users
/// 'dashboard/playlist' => view to manage one specific playlist and associated video
/// 'dashboard/user' => View to manage one specific user 
/// HTTP RESPONSE CODE : (3xx => redirection)(4xx => client side)(5xx => server side)
/// '302' => Found (temporary redirection)
/// '401' => error 401 => Unauthorized acess
/// '404' => error 404 => Page Not Found
/// '410' => error 410 => Gone (no ressources)
/// '418' => error 418 => I'm a teapot
/// '500' => error 500 => Internal Server Error
/// '503' => error 503 => Service Unavailable
/// ================================================================

if ($uri === "/index.php" || $uri === "/") {
    $title = "Home page";
    $playlists = getAllPlaylists($pdo);
    $template = __DIR__ . "/../Views/Pages/home.php";
    require_once(__DIR__ . "/../Views/base.php");
} elseif ($uri === "/playlists") {
    if (!$userId) {
        $_SESSION['error'] = "You must be logged in to access playlists.";
        header("Location: /");
        exit;
    }

    $title = "Playlists";
    $playlists = getAllPlaylists($pdo);
    $template = __DIR__ . "/../Views/Pages/playlists.php";
    require_once(__DIR__ . "/../Views/base.php");
} elseif (isset($_GET["playlistId"]) && $uri === "/playlist") {
    if (!$userId) {
        $_SESSION['error'] = "You must be logged in to access playlists.";
        header("Location: /");
        exit;
    }

    $playlistId = (int) $_GET["playlistId"];
    if ($playlistId <= 0) {
        $_SESSION['error'] = "Invalid playlist ID.";
        header("Location: /");
        exit;
    }

    $playlist = getPlaylistById($pdo, $playlistId);
    if (!$playlist) {
        $_SESSION['error'] = "Playlist unreachable.";
        header("Location: /");
        exit;
    }

    $videos = getVideosByPlaylistId($pdo, $playlistId);

    $title = "Video player";
    $template = __DIR__ . "/../Views/Pages/videoPlayer.php";
    require_once(__DIR__ . "/../Views/base.php");
} elseif ($uri === '/profile') {
    if (!$userId) {
        $_SESSION['error'] = "You must be logged in to access your profile.";
        header("Location: /");
        exit;
    }
    
    $title = "My profile";
    $template = __DIR__ . "/../Views/Pages/profile.php";
    require_once(__DIR__ . "/../Views/base.php");
} elseif ($uri === "/auth/discord") {
    $state = bin2hex(random_bytes(16));
    $_SESSION['discord_oauth_state'] = $state;
    
    $params = [
        'client_id' => DISCORD_CLIENT_ID,
        'redirect_uri' => DISCORD_REDIRECT_URI,
        'response_type' => 'code',
        'scope' => DISCORD_SCOPES,
        'state' => $state
    ];
    
    $discordLoginUrl = DISCORD_AUTH_URL . '?' . http_build_query($params);
    header('Location: ' . $discordLoginUrl);
    exit;
} elseif ($uri === "/auth/discord/callback") {
    if (!isset($_GET['state']) || !isset($_SESSION['discord_oauth_state']) || 
        $_GET['state'] !== $_SESSION['discord_oauth_state']) {
        error_log("State CSRF invalide lors du callback Discord");
        $_SESSION['error'] = 'State invalide. Tentative de connexion suspecte.';
        header("Location: /");
        exit;
    }
    
    unset($_SESSION['discord_oauth_state']);
    
    if (!isset($_GET['code'])) {
        error_log("Aucun code d'autorisation reçu de Discord");
        $_SESSION['error'] = 'Aucun code d\'autorisation reçu.';
        header("Location: /");
        exit;
    }
    
    $code = $_GET['code'];
    
    $tokenData = [
        'client_id' => DISCORD_CLIENT_ID,
        'client_secret' => DISCORD_CLIENT_SECRET,
        'grant_type' => 'authorization_code',
        'code' => $code,
        'redirect_uri' => DISCORD_REDIRECT_URI
    ];
    
    $ch = curl_init(DISCORD_TOKEN_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        error_log("Erreur Discord Token (HTTP $httpCode): " . $response);
        if ($curlError) {
            error_log("Erreur CURL: " . $curlError);
        }
        $_SESSION['error'] = 'Erreur lors de l\'obtention du token Discord.';
        header("Location: /");
        exit;
    }
    
    $tokenInfo = json_decode($response, true);
    
    if (!isset($tokenInfo['access_token'])) {
        error_log("Token d'accès manquant dans la réponse Discord: " . print_r($tokenInfo, true));
        $_SESSION['error'] = 'Token d\'accès invalide.';
        header("Location: /");
        exit;
    }
    
    $accessToken = $tokenInfo['access_token'];
    
    // Récupérer les infos utilisateur Discord
    $ch = curl_init(DISCORD_API_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        error_log("Erreur Discord User Info (HTTP $httpCode): " . $response);
        if ($curlError) {
            error_log("Erreur CURL: " . $curlError);
        }
        $_SESSION['error'] = 'Erreur lors de la récupération des informations utilisateur.';
        header("Location: /");
        exit;
    }
    
    $discordUserInfo = json_decode($response, true);
    
    if (!isset($discordUserInfo['id'])) {
        error_log("ID utilisateur manquant dans la réponse Discord: " . print_r($discordUserInfo, true));
        $_SESSION['error'] = 'Informations utilisateur invalides.';
        header("Location: /");
        exit;
    }
    
    // Log des données reçues pour le débogage
    error_log("Discord User Info reçu: " . print_r($discordUserInfo, true));
    error_log("Discord Token Info reçu: " . print_r([
        'access_token' => substr($tokenInfo['access_token'], 0, 10) . '...',
        'expires_in' => $tokenInfo['expires_in'],
        'has_refresh_token' => isset($tokenInfo['refresh_token'])
    ], true));
    
    // Créer ou mettre à jour l'utilisateur dans la BDD
    $user = createOrUpdateDiscordUser($pdo, $discordUserInfo, $tokenInfo);
    
    if (!$user) {
        error_log("Échec de createOrUpdateDiscordUser pour Discord ID: " . $discordUserInfo['id']);
        error_log("Données Discord: " . print_r($discordUserInfo, true));
        $_SESSION['error'] = 'Erreur lors de la création/mise à jour de l\'utilisateur.';
        header("Location: /");
        exit;
    }
    
    // Stocker les informations en session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['nom'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['discord_id'] = $user['discord_id'];
    $_SESSION['discord_username'] = $user['discord_username'];
    $_SESSION['discord_avatar'] = getDiscordAvatarUrl($user['discord_id'], $user['discord_avatar']);
    
    $_SESSION['success'] = "Connexion réussie ! Bienvenue " . htmlspecialchars($user['nom']);
    
    error_log("Connexion Discord réussie pour l'utilisateur ID: " . $user['id']);
    
    header("Location: /");
    exit;
} elseif ($uri === "/logout") {
    session_unset();
    session_destroy();
    header("Location: /");
    exit;
} elseif ($uri === "/dashboard") {
    if (!$userId || $userRole !== "admin") {
        $_SESSION['error'] = "Unauthorized access";
        header("Location: /");
        exit;
    }
    
    $title = "Dashboard";
    $allPlaylists = getAllPlaylists($pdo);
    
    $template = __DIR__ . "/../Views/Pages/dashboard.php";
    require_once(__DIR__ . "/../Views/base.php");
} elseif ($uri === "/dashboard/playlist/create") {
    if (!$userId || $userRole !== "admin") {
        $_SESSION['error'] = "Unauthorized access";
        header("Location: /");
        exit;
    }
    
    if ($method === 'POST' && isset($_POST['create_playlist'])) {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $picture = trim($_POST['picture'] ?? '');
        
        if (empty($title)) {
            $_SESSION['error'] = "The title is required";
        } else {
            $newPlaylistId = createPlaylist($pdo, $userId, $title, $description, $picture);
            
            if ($newPlaylistId) {
                $_SESSION['success'] = "Playlist created";
                header("Location: /dashboard/playlist/edit?playlistId=" . $newPlaylistId);
                exit;
            } else {
                $_SESSION['error'] = "Error during creation";
            }
        }
    }
    
    $title = "Créer une playlist";
    $template = __DIR__ . "/../Views/Pages/playlistCreate.php";
    require_once(__DIR__ . "/../Views/base.php");
} elseif (isset($_GET['playlistId']) && $uri === "/dashboard/playlist/edit") {
    if (!$userId || $userRole !== "admin") {
        $_SESSION['error'] = "Accès non autorisé.";
        header("Location: /");
        exit;
    }
    
    $playlistId = (int) $_GET['playlistId'];
    
    if ($playlistId <= 0) {
        $_SESSION['error'] = "ID invalide.";
        header("Location: /dashboard");
        exit;
    }
    
    $playlist = getPlaylistById($pdo, $playlistId);
    
    if (!$playlist) {
        $_SESSION['error'] = "Playlist introuvable.";
        header("Location: /dashboard");
        exit;
    }
    
    $videos = getVideosByPlaylistId($pdo, $playlistId);
    
    // UPDATE playlist
    if ($method === 'POST' && isset($_POST['update_playlist'])) {
        $newTitle = trim($_POST['playlistTitle'] ?? '');
        $newDescription = trim($_POST['playlistDescription'] ?? '');
        $newThumbnail = trim($_POST['playlistThumbnail'] ?? '');
        
        if (empty($newTitle)) {
            $_SESSION['error'] = "Le titre est obligatoire.";
        } else {
            $updated = updatePlaylist($pdo, $playlistId, $newTitle, $newDescription, $newThumbnail);
            
            if ($updated) {
                $_SESSION['success'] = "Playlist mise à jour !";
                header("Location: /dashboard/playlist/edit?playlistId=" . $playlistId);
                exit;
            } else {
                $_SESSION['error'] = "Erreur lors de la mise à jour.";
            }
        }
    }
    
    // ADD video
    if ($method === 'POST' && isset($_POST['add_video'])) {
        $videoUrl = trim($_POST['videoUrl'] ?? '');
        $videoName = trim($_POST['videoName'] ?? '');
        $videoDescription = trim($_POST['videoDescription'] ?? '');
        
        if (empty($videoUrl)) {
            $_SESSION['error'] = "L'URL est obligatoire.";
        } elseif (empty($videoName)) {
            $_SESSION['error'] = "Le titre est obligatoire.";
        } else {
            $added = addVideoToPlaylist($pdo, $playlistId, $videoUrl, $videoName, $videoDescription);
            
            if ($added) {
                $_SESSION['success'] = "Vidéo ajoutée !";
                header("Location: /dashboard/playlist/edit?playlistId=" . $playlistId);
                exit;
            } else {
                $_SESSION['error'] = "Erreur lors de l'ajout.";
            }
        }
    }
    
    // DELETE video
    if ($method === 'POST' && isset($_POST['delete_video'])) {
        $videoId = (int) ($_POST['videoId'] ?? 0);
        
        if ($videoId > 0) {
            $deleted = deleteVideo($pdo, $videoId);
            
            if ($deleted) {
                $_SESSION['success'] = "Vidéo supprimée !";
            } else {
                $_SESSION['error'] = "Erreur lors de la suppression.";
            }
        }
        
        header("Location: /dashboard/playlist/edit?playlistId=" . $playlistId);
        exit;
    }
    
    // MOVE UP video
    if ($method === 'POST' && isset($_POST['move_video_up'])) {
        $videoId = (int) ($_POST['videoId'] ?? 0);
        
        if ($videoId > 0) {
            moveVideoUp($pdo, $videoId);
            $_SESSION['success'] = "Vidéo déplacée !";
        }
        
        header("Location: /dashboard/playlist/edit?playlistId=" . $playlistId);
        exit;
    }
    
    // MOVE DOWN video
    if ($method === 'POST' && isset($_POST['move_video_down'])) {
        $videoId = (int) ($_POST['videoId'] ?? 0);
        
        if ($videoId > 0) {
            moveVideoDown($pdo, $videoId);
            $_SESSION['success'] = "Vidéo déplacée !";
        }
        
        header("Location: /dashboard/playlist/edit?playlistId=" . $playlistId);
        exit;
    }
    
    // REORDER videos (drag and drop)
    if ($method === 'POST' && isset($_POST['reorder_videos'])) {
        $videoOrder = json_decode($_POST['video_order'] ?? '[]', true);
        
        if (!empty($videoOrder)) {
            // Réorganiser l'ordre des vidéos
            foreach ($videoOrder as $index => $videoId) {
                $newOrder = $index + 1;
                updateVideoOrder($pdo, (int)$videoId, $newOrder);
            }
            $_SESSION['success'] = "Ordre mis à jour !";
        }
        
        header("Location: /dashboard/playlist/edit?playlistId=" . $playlistId);
        exit;
    }
    
    $title = "Éditer la playlist";
    $template = __DIR__ . "/../Views/Pages/playlistEdit.php";
    require_once(__DIR__ . "/../Views/base.php");
} elseif ($uri === "/dashboard/playlist/delete" && isset($_GET['playlistId'])) {
    if (!$userId || $userRole !== "admin") {
        $_SESSION['error'] = "Accès non autorisé.";
        header("Location: /");
        exit;
    }
    
    $playlistId = (int) $_GET['playlistId'];
    
    if ($playlistId <= 0) {
        $_SESSION['error'] = "ID invalide.";
        header("Location: /dashboard");
        exit;
    }
    
    $playlist = getPlaylistById($pdo, $playlistId);
    
    if (!$playlist) {
        $_SESSION['error'] = "Playlist introuvable.";
        header("Location: /dashboard");
        exit;
    }
    
    // Confirmation de suppression
    if ($method === 'POST' && isset($_POST['confirm_delete'])) {
        $deleted = deletePlaylist($pdo, $playlistId);
        
        if ($deleted) {
            $_SESSION['success'] = "Playlist supprimée avec succès !";
            header("Location: /dashboard");
            exit;
        } else {
            $_SESSION['error'] = "Erreur lors de la suppression.";
        }
    }
    
    $title = "Supprimer la playlist";
    $template = __DIR__ . "/../Views/Pages/playlistDeleteConfirm.php";
    require_once(__DIR__ . "/../Views/base.php");
} else {
    http_response_code(404);
    $title = "Page non trouvée - 404";
    
    if (file_exists("Views/Pages/404.php")) {
        $template = "Views/Pages/404.php";
        require_once("Views/base.php");
    } else {
        echo "<h1>404 - Page non trouvée</h1>";
        exit;
    }
}