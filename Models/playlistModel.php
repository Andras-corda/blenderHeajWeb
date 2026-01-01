<?php

/* Fetch all playlists */
function getAllPlaylists($pdo) {
    try {
        $query = "SELECT * FROM playlist ORDER BY playlistCreatedAt DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error getAllPlaylists: " . $e->getMessage());
        return [];
    }
}

/* Fetch one playlist by its id */
function getPlaylistById($pdo, $id) {
    try {
        $query = "SELECT * FROM playlist WHERE playlistId = :id;";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error getPlaylistById: " . $e->getMessage());
        return null;
    }   
}

/* Create a new playlist */
function createPlaylist($pdo, $userId, $title, $description = '', $thumbnail = '') {
    try {
        $query = "INSERT INTO playlist (playlistUserId, playlistTitle, playlistDescription, playlistThumbnail, playlistCreatedAt) 
                  VALUES (:playlistUserId, :playlistTitle, :playlistDescription, :playlistThumbnail, NOW())";

        $stmt = $pdo->prepare($query);
        $result = $stmt->execute([
            'playlistUserId' => $userId,
            'playlistTitle' => $title,
            'playlistDescription' => $description,
            'playlistThumbnail' => $thumbnail ?: null
        ]);

        if ($result) {
            $lastId = $pdo->lastInsertId();
            error_log("✅ Playlist créée ! ID: $lastId, Titre: $title");
            return $lastId;
        }
        
        error_log("❌ L'insertion a échoué");
        return false;

    } catch (PDOException $e) {
        error_log("💥 Error createPlaylist: " . $e->getMessage());
        error_log("📍 Code erreur SQL: " . $e->getCode());
        return false;
    }
}

/* Update a playlist */
function updatePlaylist($pdo, $id, $title, $description, $thumbnail = null) {
    try {
        $query = "UPDATE playlist 
                  SET playlistTitle = :title, 
                      playlistDescription = :description, 
                      playlistThumbnail = :thumbnail 
                  WHERE playlistId = :id";
        
        $stmt = $pdo->prepare($query);
        return $stmt->execute([
            'id' => $id,
            'title' => $title,
            'description' => $description,
            'thumbnail' => $thumbnail
        ]);
    } catch (PDOException $e) {
        error_log("Error updatePlaylist: " . $e->getMessage());
        return false;
    }
}

/* Delete a playlist and all its videos */
function deletePlaylist($pdo, $id) {
    try {
        // Supprimer d'abord les vidéos associées
        $stmt = $pdo->prepare("DELETE FROM playlist_videos WHERE playlistId = :id");
        $stmt->execute(['id' => $id]);
        
        // Puis supprimer la playlist
        $stmt = $pdo->prepare("DELETE FROM playlist WHERE playlistId = :id");
        return $stmt->execute(['id' => $id]);
    } catch (PDOException $e) {
        error_log("Error deletePlaylist: " . $e->getMessage());
        return false;
    }
}

/* Fetch playlists by user ID */
function getPlaylistsByUserId($pdo, $userId) {
    try {
        $query = "SELECT * FROM playlist WHERE playlistUserId = :userId ORDER BY playlistId DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error getPlaylistsByUserId: " . $e->getMessage());
        return [];
    }
}
