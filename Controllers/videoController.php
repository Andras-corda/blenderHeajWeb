<?php

require_once __DIR__ . '/../Models/videoModel.php';

/**
 * Affiche le lecteur vidéo avec une vidéo spécifique
 */
function showVideoPlayer($pdo, $userId) {
    if (!$userId) {
        $_SESSION['error'] = "You must be logged in to watch videos.";
        header("Location: /");
        exit;
    }
    
    $videoId = (int) ($_GET['videoId'] ?? 0);
    
    if ($videoId <= 0) {
        $_SESSION['error'] = "Invalid video ID.";
        header("Location: /playlists");
        exit;
    }
    
    $video = getVideoById($pdo, $videoId);
    
    if (!$video) {
        $_SESSION['error'] = "Video not found.";
        header("Location: /playlists");
        exit;
    }
    
    // Récupérer la playlist associée
    require_once __DIR__ . '/../Models/playlistModel.php';
    $playlist = getPlaylistById($pdo, $video['playlist_id']);
    
    // Récupérer toutes les vidéos de la playlist pour la navigation
    $playlistVideos = getVideosByPlaylistId($pdo, $video['playlist_id']);
    
    $title = $video['nom'] . " - Video Player";
    $template = __DIR__ . "/../Views/Pages/videoPlayer.php";
    require_once(__DIR__ . "/../Views/base.php");
}
