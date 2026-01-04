<?php

require_once __DIR__ . '/../Models/playlistModel.php';
require_once __DIR__ . '/../Models/videoModel.php';
require_once __DIR__ . '/../Models/userModel.php';

/**
 * Affiche le dashboard principal
 */
function showDashboard($pdo) {
    $title = "Dashboard";
    $allPlaylists = getAllPlaylists($pdo);
    
    $template = __DIR__ . "/../Views/Pages/dashboard.php";
    require_once(__DIR__ . "/../Views/base.php");
}

/**
 * Gère les routes liées aux playlists dans le dashboard
 */
function handleDashboardPlaylistRoutes($uri, $method, $pdo, $userId) {
    if ($uri === '/dashboard/playlist/create') {
        handlePlaylistCreate($pdo, $userId, $method);
    } elseif ($uri === '/dashboard/playlist/edit' && isset($_GET['playlistId'])) {
        handlePlaylistEdit($pdo, $userId, $method);
    } elseif ($uri === '/dashboard/playlist/delete' && isset($_GET['playlistId'])) {
        handlePlaylistDelete($pdo, $method);
    } else {
        handle404();
    }
}

/**
 * Gère les routes liées aux utilisateurs dans le dashboard
 */

// function handleDashboardUserRoutes($uri, $method, $pdo) {
//     // TODO: Implémenter la gestion des utilisateurs
//     if ($uri === '/dashboard/users') {
//         showAllUsers($pdo);
//     } elseif ($uri === '/dashboard/user/edit' && isset($_GET['userId'])) {
//         editUser($pdo, $method);
//     } else {
//         handle404();
//     }
// }

/**
 * Création d'une nouvelle playlist
 */
function handlePlaylistCreate($pdo, $userId, $method) {
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
}

/**
 * Édition d'une playlist existante
 */
function handlePlaylistEdit($pdo, $userId, $method) {
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
    
    // Gestion des différentes actions POST
    if ($method === 'POST') {
        if (isset($_POST['update_playlist'])) {
            handleUpdatePlaylist($pdo, $playlistId);
        } elseif (isset($_POST['add_video'])) {
            handleAddVideo($pdo, $playlistId);
        } elseif (isset($_POST['delete_video'])) {
            handleDeleteVideo($pdo, $playlistId);
        } elseif (isset($_POST['move_video_up'])) {
            handleMoveVideoUp($pdo, $playlistId);
        } elseif (isset($_POST['move_video_down'])) {
            handleMoveVideoDown($pdo, $playlistId);
        } elseif (isset($_POST['reorder_videos'])) {
            handleReorderVideos($pdo, $playlistId);
        }
    }
    
    $title = "Éditer la playlist";
    $template = __DIR__ . "/../Views/Pages/playlistEdit.php";
    require_once(__DIR__ . "/../Views/base.php");
}

/**
 * Suppression d'une playlist
 */
function handlePlaylistDelete($pdo, $method) {
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
}

// ================================================================
// ACTIONS SUR LES PLAYLISTS ET VIDÉOS
// ================================================================

function handleUpdatePlaylist($pdo, $playlistId) {
    $newTitle = trim($_POST['playlistTitle'] ?? '');
    $newDescription = trim($_POST['playlistDescription'] ?? '');
    $newThumbnail = trim($_POST['playlistThumbnail'] ?? '');
    
    if (empty($newTitle)) {
        $_SESSION['error'] = "Le titre est obligatoire.";
    } else {
        $updated = updatePlaylist($pdo, $playlistId, $newTitle, $newDescription, $newThumbnail);
        
        if ($updated) {
            $_SESSION['success'] = "Playlist mise à jour !";
        } else {
            $_SESSION['error'] = "Erreur lors de la mise à jour.";
        }
    }
    
    header("Location: /dashboard/playlist/edit?playlistId=" . $playlistId);
    exit;
}

function handleAddVideo($pdo, $playlistId) {
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
        } else {
            $_SESSION['error'] = "Erreur lors de l'ajout.";
        }
    }
    
    header("Location: /dashboard/playlist/edit?playlistId=" . $playlistId);
    exit;
}

function handleDeleteVideo($pdo, $playlistId) {
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

function handleMoveVideoUp($pdo, $playlistId) {
    $videoId = (int) ($_POST['videoId'] ?? 0);
    
    if ($videoId > 0) {
        moveVideoUp($pdo, $videoId);
        $_SESSION['success'] = "Vidéo déplacée !";
    }
    
    header("Location: /dashboard/playlist/edit?playlistId=" . $playlistId);
    exit;
}

function handleMoveVideoDown($pdo, $playlistId) {
    $videoId = (int) ($_POST['videoId'] ?? 0);
    
    if ($videoId > 0) {
        moveVideoDown($pdo, $videoId);
        $_SESSION['success'] = "Vidéo déplacée !";
    }
    
    header("Location: /dashboard/playlist/edit?playlistId=" . $playlistId);
    exit;
}

function handleReorderVideos($pdo, $playlistId) {
    $videoOrder = json_decode($_POST['video_order'] ?? '[]', true);
    
    if (!empty($videoOrder)) {
        foreach ($videoOrder as $index => $videoId) {
            $newOrder = $index + 1;
            updateVideoOrder($pdo, (int)$videoId, $newOrder);
        }
        $_SESSION['success'] = "Ordre mis à jour !";
    }
    
    header("Location: /dashboard/playlist/edit?playlistId=" . $playlistId);
    exit;
}

// ================================================================
// GESTION DES UTILISATEURS (à implémenter)
// ================================================================

// function showAllUsers($pdo) {
//     // TODO: Implémenter l'affichage de tous les utilisateurs
//     $title = "Gestion des utilisateurs";
//     $users = getAllUsers($pdo);
//     $template = __DIR__ . "/../Views/Pages/userManagement.php";
//     require_once(__DIR__ . "/../Views/base.php");
// }

// function editUser($pdo, $method) {
//     // TODO: Implémenter l'édition d'un utilisateur
//     $userId = (int) $_GET['userId'];
    
//     if ($method === 'POST') {
//         // Traiter la mise à jour
//     }
    
//     $title = "Éditer un utilisateur";
//     $user = getUserById($pdo, $userId);
//     $template = __DIR__ . "/../Views/Pages/userEdit.php";
//     require_once(__DIR__ . "/../Views/base.php");
// }