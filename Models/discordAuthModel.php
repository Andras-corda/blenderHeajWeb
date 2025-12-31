<?php

function createOrUpdateDiscordUser($pdo, $discordData, $tokenData) {
    try {
        // Vérifier si l'utilisateur existe déjà 
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE discord_id = :discord_id");
        $stmt->execute(['discord_id' => $discordData['id']]);
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Calculer la date d'expiration du token
        $tokenExpiresAt = time() + $tokenData['expires_in'];
        
        if ($existingUser) {
            // Mise à jour de l'utilisateur existant
            $stmt = $pdo->prepare("
                UPDATE utilisateurs 
                SET discord_username = :username,
                    discord_discriminator = :discriminator,
                    discord_avatar = :avatar,
                    discord_access_token = :access_token,
                    discord_refresh_token = :refresh_token,
                    discord_token_expires_at = :token_expires_at
                WHERE discord_id = :discord_id
            ");
            
            $result = $stmt->execute([
                'username' => $discordData['username'],
                'discriminator' => $discordData['discriminator'] ?? '0',
                'avatar' => $discordData['avatar'] ?? '',
                'access_token' => $tokenData['access_token'],
                'refresh_token' => $tokenData['refresh_token'],
                'token_expires_at' => $tokenExpiresAt,
                'discord_id' => $discordData['id']
            ]);
            
            if (!$result) {
                error_log("Échec de la mise à jour de l'utilisateur Discord ID: " . $discordData['id']);
                return false;
            }
            
            $userId = $existingUser['id'];
        } else {
            // Création d'un nouvel utilisateur
            $stmt = $pdo->prepare("
                INSERT INTO utilisateurs (
                    nom,
                    role,
                    discord_id,
                    discord_username,
                    discord_discriminator,
                    discord_avatar,
                    discord_access_token,
                    discord_refresh_token,
                    discord_token_expires_at
                ) VALUES (
                    :nom,
                    'user',
                    :discord_id,
                    :discord_username,
                    :discord_discriminator,
                    :discord_avatar,
                    :discord_access_token,
                    :discord_refresh_token,
                    :discord_token_expires_at
                )
            ");
            
            $nom = $discordData['global_name'] ?? $discordData['username'];
            
            $result = $stmt->execute([
                'nom' => $nom,
                'discord_id' => $discordData['id'],
                'discord_username' => $discordData['username'],
                'discord_discriminator' => $discordData['discriminator'] ?? '0',
                'discord_avatar' => $discordData['avatar'] ?? '',
                'discord_access_token' => $tokenData['access_token'],
                'discord_refresh_token' => $tokenData['refresh_token'],
                'discord_token_expires_at' => $tokenExpiresAt
            ]);
            
            if (!$result) {
                error_log("Échec de la création de l'utilisateur Discord ID: " . $discordData['id']);
                return false;
            }
            
            $userId = $pdo->lastInsertId();
        }
        
        // Récupérer les informations complètes de l'utilisateur
        return getUserById($pdo, $userId);
        
    } catch (PDOException $e) {
        error_log("Erreur PDO lors de la création/mise à jour de l'utilisateur Discord: " . $e->getMessage());
        error_log("Code d'erreur: " . $e->getCode());
        error_log("Discord data: " . print_r($discordData, true));
        return false;
    }
}

function getUserById($pdo, $userId) {
    try {
        $stmt = $pdo->prepare("
            SELECT id, nom, role, discord_id, discord_username, 
                   discord_discriminator, discord_avatar, date_inscription
            FROM utilisateurs 
            WHERE id = :id
        ");
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            error_log("Aucun utilisateur trouvé avec l'ID: " . $userId);
            return false;
        }
        
        return $user;
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération de l'utilisateur: " . $e->getMessage());
        return false;
    }
}

function getUserByDiscordId($pdo, $discordId) {
    try {
        $stmt = $pdo->prepare("
            SELECT id, nom, role, discord_id, discord_username, 
                   discord_discriminator, discord_avatar, date_inscription
            FROM utilisateurs 
            WHERE discord_id = :discord_id
        ");
        $stmt->execute(['discord_id' => $discordId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération de l'utilisateur par Discord ID: " . $e->getMessage());
        return false;
    }
}

function getDiscordAvatarUrl($discordId, $avatar, $size = 128) {
    if (empty($avatar)) {
        // Avatar par défaut
        return "https://cdn.discordapp.com/embed/avatars/0.png";
    }
    return "https://cdn.discordapp.com/avatars/{$discordId}/{$avatar}.png?size={$size}";
}

function refreshDiscordToken($pdo, $userId) {
    try {
        // Récupérer le refresh token
        $stmt = $pdo->prepare("
            SELECT discord_refresh_token 
            FROM utilisateurs 
            WHERE id = :id
        ");
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user || empty($user['discord_refresh_token'])) {
            return false;
        }
        
        require_once __DIR__ . '/../Config/discordConfig.php';
        
        // Données pour rafraîchir le token
        $tokenData = [
            'client_id' => DISCORD_CLIENT_ID,
            'client_secret' => DISCORD_CLIENT_SECRET,
            'grant_type' => 'refresh_token',
            'refresh_token' => $user['discord_refresh_token']
        ];
        
        // Requête pour rafraîchir le token
        $ch = curl_init(DISCORD_TOKEN_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            error_log("Échec du rafraîchissement du token Discord: " . $response);
            return false;
        }
        
        $tokenInfo = json_decode($response, true);
        $tokenExpiresAt = time() + $tokenInfo['expires_in'];
        
        // Mettre à jour les tokens
        $stmt = $pdo->prepare("
            UPDATE utilisateurs 
            SET discord_access_token = :access_token,
                discord_refresh_token = :refresh_token,
                discord_token_expires_at = :token_expires_at
            WHERE id = :id
        ");
        
        return $stmt->execute([
            'access_token' => $tokenInfo['access_token'],
            'refresh_token' => $tokenInfo['refresh_token'],
            'token_expires_at' => $tokenExpiresAt,
            'id' => $userId
        ]);
        
    } catch (Exception $e) {
        error_log("Erreur lors du rafraîchissement du token: " . $e->getMessage());
        return false;
    }
}
?>