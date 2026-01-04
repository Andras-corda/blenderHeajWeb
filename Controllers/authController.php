<?php

require_once __DIR__ . '/../Models/discordAuthModel.php';
require_once __DIR__ . '/../Config/discordConfig.php';

/**
 * Initie le processus d'authentification OAuth2 avec Discord
 */
function initiateDiscordAuth() {
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
}

/**
 * Gère le callback de Discord après authentification
 */
function handleDiscordCallback($pdo) {
    // Vérification CSRF
    if (!verifyDiscordState()) {
        return;
    }
    
    // Vérification du code d'autorisation
    if (!isset($_GET['code'])) {
        error_log("Aucun code d'autorisation reçu de Discord");
        $_SESSION['error'] = 'Aucun code d\'autorisation reçu.';
        header("Location: /");
        exit;
    }
    
    $code = $_GET['code'];
    
    // Échange du code contre un token
    $tokenInfo = exchangeCodeForToken($code);
    if (!$tokenInfo) {
        header("Location: /");
        exit;
    }
    
    // Récupération des infos utilisateur
    $discordUserInfo = fetchDiscordUserInfo($tokenInfo['access_token']);
    if (!$discordUserInfo) {
        header("Location: /");
        exit;
    }
    
    // Création/mise à jour de l'utilisateur dans la BDD
    $user = createOrUpdateDiscordUser($pdo, $discordUserInfo, $tokenInfo);
    
    if (!$user) {
        error_log("Échec de createOrUpdateDiscordUser pour Discord ID: " . $discordUserInfo['id']);
        $_SESSION['error'] = 'Erreur lors de la création/mise à jour de l\'utilisateur.';
        header("Location: /");
        exit;
    }
    
    // Stockage des informations en session
    storeUserInSession($user);
    
    $_SESSION['success'] = "Connexion réussie ! Bienvenue " . htmlspecialchars($user['nom']);
    error_log("Connexion Discord réussie pour l'utilisateur ID: " . $user['id']);
    
    header("Location: /");
    exit;
}

/**
 * Vérifie le state CSRF
 */
function verifyDiscordState() {
    if (!isset($_GET['state']) || !isset($_SESSION['discord_oauth_state']) || 
        $_GET['state'] !== $_SESSION['discord_oauth_state']) {
        error_log("State CSRF invalide lors du callback Discord");
        $_SESSION['error'] = 'State invalide. Tentative de connexion suspecte.';
        header("Location: /");
        exit;
    }
    
    unset($_SESSION['discord_oauth_state']);
    return true;
}

/**
 * Échange le code d'autorisation contre un token d'accès
 */
function exchangeCodeForToken($code) {
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
        return null;
    }
    
    $tokenInfo = json_decode($response, true);
    
    if (!isset($tokenInfo['access_token'])) {
        error_log("Token d'accès manquant dans la réponse Discord");
        $_SESSION['error'] = 'Token d\'accès invalide.';
        return null;
    }
    
    return $tokenInfo;
}

/**
 * Récupère les informations utilisateur depuis l'API Discord
 */
function fetchDiscordUserInfo($accessToken) {
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
        return null;
    }
    
    $discordUserInfo = json_decode($response, true);
    
    if (!isset($discordUserInfo['id'])) {
        error_log("ID utilisateur manquant dans la réponse Discord");
        $_SESSION['error'] = 'Informations utilisateur invalides.';
        return null;
    }
    
    return $discordUserInfo;
}

/**
 * Stocke les informations utilisateur en session
 */
function storeUserInSession($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['nom'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['discord_id'] = $user['discord_id'];
    $_SESSION['discord_username'] = $user['discord_username'];
    $_SESSION['discord_avatar'] = getDiscordAvatarUrl($user['discord_id'], $user['discord_avatar']);
}