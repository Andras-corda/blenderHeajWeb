<?php

require_once __DIR__ . '/../Models/playlistModel.php';

/**
 * Affiche toutes les playlists
 */
function showAllPlaylists($pdo, $userId) {
    if (!$userId) {
        $_SESSION['error'] = "You must be logged in to access playlists.";
        header("Location: /");
        exit;
    }

    $title = "Playlists";
    $playlists = getAllPlaylists($pdo);
    $template = __DIR__ . "/../Views/Pages/playlists.php";
    require_once(__DIR__ . "/../Views/base.php");
}

/**
 * Affiche une playlist spécifique avec ses vidéos
 */
function showPlaylistWithVideos($pdo, $userId) {
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

    require_once __DIR__ . '/../Models/videoModel.php';
    $videos = getVideosByPlaylistId($pdo, $playlistId);

    $title = "Video player";
    $template = __DIR__ . "/../Views/Pages/videoPlayer.php";
    require_once(__DIR__ . "/../Views/base.php");
}