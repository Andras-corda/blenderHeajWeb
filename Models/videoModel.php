<?php

/**
 * Récupérer toutes les vidéos d'une playlist (ordonnées)
 */
function getVideosByPlaylistId($pdo, $playlistId) {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                videoId as id,
                playlistId as playlist_id,
                videoUrl as url,
                videoName,
                videoDescription,
                videoOrderInPlaylist as order_position
            FROM playlist_videos
            WHERE playlistId = ?
            ORDER BY videoOrderInPlaylist ASC, videoId ASC
        ");
        $stmt->execute([$playlistId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur getVideosByPlaylistId: " . $e->getMessage());
        return [];
    }
}

/**
 * Récupérer une vidéo par son ID
 */
function getVideoById($pdo, $videoId) {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                videoId as id,
                playlistId as playlist_id,
                videoUrl as url,
                videoOrderInPlaylist as order_position
            FROM playlist_videos
            WHERE videoId = ?
        ");
        $stmt->execute([$videoId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur getVideoById: " . $e->getMessage());
        return null;
    }
}

/**
 * Ajouter une vidéo à une playlist (à la fin)
 */
function addVideoToPlaylist($pdo, $playlistId, $videoUrl, $videoName = '', $videoDescription = '') {
    try {
        // Récupérer le prochain ordre disponible
        $stmt = $pdo->prepare("
            SELECT COALESCE(MAX(videoOrderInPlaylist), 0) + 1 as next_order
            FROM playlist_videos
            WHERE playlistId = ?
        ");
        $stmt->execute([$playlistId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $nextOrder = $result['next_order'];
        
        // Insérer la vidéo avec l'ordre
        $stmt = $pdo->prepare("
            INSERT INTO playlist_videos (playlistId, videoUrl, videoName, videoDescription, videoOrderInPlaylist)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$playlistId, $videoUrl, $videoName, $videoDescription, $nextOrder]);
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        error_log("Erreur addVideoToPlaylist: " . $e->getMessage());
        return false;
    }
}

/**
 * Ajouter une vidéo avec un ordre spécifique
 */
function addVideoToPlaylistAtPosition($pdo, $playlistId, $videoUrl, $position) {
    try {
        // Décaler les vidéos existantes
        $stmt = $pdo->prepare("
            UPDATE playlist_videos
            SET videoOrderInPlaylist = videoOrderInPlaylist + 1
            WHERE playlistId = ? AND videoOrderInPlaylist >= ?
        ");
        $stmt->execute([$playlistId, $position]);
        
        // Insérer la nouvelle vidéo
        $stmt = $pdo->prepare("
            INSERT INTO playlist_videos (playlistId, videoUrl, videoOrderInPlaylist)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$playlistId, $videoUrl, $position]);
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        error_log("Erreur addVideoToPlaylistAtPosition: " . $e->getMessage());
        return false;
    }
}

/**
 * Mettre à jour l'URL d'une vidéo
 */
function updateVideo($pdo, $videoId, $videoUrl) {
    try {
        $stmt = $pdo->prepare("
            UPDATE playlist_videos
            SET videoUrl = ?
            WHERE videoId = ?
        ");
        return $stmt->execute([$videoUrl, $videoId]);
    } catch (PDOException $e) {
        error_log("Erreur updateVideo: " . $e->getMessage());
        return false;
    }
}

/**
 * Changer l'ordre d'une vidéo dans la playlist
 */
function updateVideoOrder($pdo, $videoId, $newOrder) {
    try {
        // Récupérer l'ancien ordre et la playlist
        $stmt = $pdo->prepare("
            SELECT videoOrderInPlaylist, playlistId 
            FROM playlist_videos 
            WHERE videoId = ?
        ");
        $stmt->execute([$videoId]);
        $video = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$video) {
            return false;
        }
        
        $oldOrder = $video['videoOrderInPlaylist'];
        $playlistId = $video['playlistId'];
        
        if ($oldOrder == $newOrder) {
            return true; // Rien à faire
        }
        
        // Décaler les autres vidéos
        if ($newOrder < $oldOrder) {
            // Déplacer vers le haut : décaler les vidéos entre newOrder et oldOrder vers le bas
            $stmt = $pdo->prepare("
                UPDATE playlist_videos
                SET videoOrderInPlaylist = videoOrderInPlaylist + 1
                WHERE playlistId = ? 
                AND videoOrderInPlaylist >= ? 
                AND videoOrderInPlaylist < ?
            ");
            $stmt->execute([$playlistId, $newOrder, $oldOrder]);
        } else {
            // Déplacer vers le bas : décaler les vidéos entre oldOrder et newOrder vers le haut
            $stmt = $pdo->prepare("
                UPDATE playlist_videos
                SET videoOrderInPlaylist = videoOrderInPlaylist - 1
                WHERE playlistId = ? 
                AND videoOrderInPlaylist > ? 
                AND videoOrderInPlaylist <= ?
            ");
            $stmt->execute([$playlistId, $oldOrder, $newOrder]);
        }
        
        // Mettre à jour la vidéo elle-même
        $stmt = $pdo->prepare("
            UPDATE playlist_videos
            SET videoOrderInPlaylist = ?
            WHERE videoId = ?
        ");
        return $stmt->execute([$newOrder, $videoId]);
    } catch (PDOException $e) {
        error_log("Erreur updateVideoOrder: " . $e->getMessage());
        return false;
    }
}

/**
 * Déplacer une vidéo vers le haut (ordre - 1)
 */
function moveVideoUp($pdo, $videoId) {
    try {
        $stmt = $pdo->prepare("
            SELECT videoOrderInPlaylist, playlistId 
            FROM playlist_videos 
            WHERE videoId = ?
        ");
        $stmt->execute([$videoId]);
        $video = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$video || $video['videoOrderInPlaylist'] <= 1) {
            return false; // Déjà en première position
        }
        
        return updateVideoOrder($pdo, $videoId, $video['videoOrderInPlaylist'] - 1);
    } catch (PDOException $e) {
        error_log("Erreur moveVideoUp: " . $e->getMessage());
        return false;
    }
}

/**
 * Déplacer une vidéo vers le bas (ordre + 1)
 */
function moveVideoDown($pdo, $videoId) {
    try {
        $stmt = $pdo->prepare("
            SELECT videoOrderInPlaylist, playlistId 
            FROM playlist_videos 
            WHERE videoId = ?
        ");
        $stmt->execute([$videoId]);
        $video = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$video) {
            return false;
        }
        
        // Vérifier qu'on n'est pas déjà à la fin
        $stmt = $pdo->prepare("
            SELECT MAX(videoOrderInPlaylist) as max_order
            FROM playlist_videos
            WHERE playlistId = ?
        ");
        $stmt->execute([$video['playlistId']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($video['videoOrderInPlaylist'] >= $result['max_order']) {
            return false; // Déjà en dernière position
        }
        
        return updateVideoOrder($pdo, $videoId, $video['videoOrderInPlaylist'] + 1);
    } catch (PDOException $e) {
        error_log("Erreur moveVideoDown: " . $e->getMessage());
        return false;
    }
}

/**
 * Supprimer une vidéo (réorganise l'ordre automatiquement)
 */
function deleteVideo($pdo, $videoId) {
    try {
        // Récupérer l'ordre et la playlist avant suppression
        $stmt = $pdo->prepare("
            SELECT videoOrderInPlaylist, playlistId 
            FROM playlist_videos 
            WHERE videoId = ?
        ");
        $stmt->execute([$videoId]);
        $video = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$video) {
            return false;
        }
        
        // Supprimer la vidéo
        $stmt = $pdo->prepare("DELETE FROM playlist_videos WHERE videoId = ?");
        $result = $stmt->execute([$videoId]);
        
        if ($result) {
            // Réorganiser les ordres des vidéos suivantes
            $stmt = $pdo->prepare("
                UPDATE playlist_videos
                SET videoOrderInPlaylist = videoOrderInPlaylist - 1
                WHERE playlistId = ? AND videoOrderInPlaylist > ?
            ");
            $stmt->execute([$video['playlistId'], $video['videoOrderInPlaylist']]);
        }
        
        return $result;
    } catch (PDOException $e) {
        error_log("Erreur deleteVideo: " . $e->getMessage());
        return false;
    }
}

/**
 * Supprimer toutes les vidéos d'une playlist
 */
function deleteVideosByPlaylistId($pdo, $playlistId) {
    try {
        $stmt = $pdo->prepare("DELETE FROM playlist_videos WHERE playlistId = ?");
        return $stmt->execute([$playlistId]);
    } catch (PDOException $e) {
        error_log("Erreur deleteVideosByPlaylistId: " . $e->getMessage());
        return false;
    }
}

/**
 * Compter le nombre de vidéos dans une playlist
 */
function countVideosByPlaylistId($pdo, $playlistId) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM playlist_videos WHERE playlistId = ?");
        $stmt->execute([$playlistId]);
        return (int) $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Erreur countVideosByPlaylistId: " . $e->getMessage());
        return 0;
    }
}

/**
 * Récupérer toutes les vidéos (pour admin ou debug)
 */
function getAllVideos($pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                videoId as id,
                playlistId as playlist_id,
                videoUrl as url,
                videoOrderInPlaylist as order_position
            FROM playlist_videos
            ORDER BY playlistId ASC, videoOrderInPlaylist ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur getAllVideos: " . $e->getMessage());
        return [];
    }
}

/**
 * Réorganiser complètement les ordres d'une playlist (nettoyage)
 * Utile si les ordres sont désynchronisés
 */
function reorderPlaylistVideos($pdo, $playlistId) {
    try {
        $stmt = $pdo->prepare("
            SELECT videoId 
            FROM playlist_videos 
            WHERE playlistId = ? 
            ORDER BY videoOrderInPlaylist ASC, videoId ASC
        ");
        $stmt->execute([$playlistId]);
        $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $order = 1;
        foreach ($videos as $video) {
            $stmt = $pdo->prepare("
                UPDATE playlist_videos 
                SET videoOrderInPlaylist = ? 
                WHERE videoId = ?
            ");
            $stmt->execute([$order, $video['videoId']]);
            $order++;
        }
        
        return true;
    } catch (PDOException $e) {
        error_log("Erreur reorderPlaylistVideos: " . $e->getMessage());
        return false;
    }
}