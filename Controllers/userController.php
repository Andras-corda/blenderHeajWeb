<?php

require_once __DIR__ . '/../Models/userModel.php';

/**
 * Affiche le profil de l'utilisateur connecté
 */
function handleUserProfile($pdo, $userId) {
    if (!$userId) {
        $_SESSION['error'] = "You must be logged in to access your profile.";
        header("Location: /");
        exit;
    }
    
    $title = "My profile";
    $template = __DIR__ . "/../Views/Pages/profile.php";
    require_once(__DIR__ . "/../Views/base.php");
}