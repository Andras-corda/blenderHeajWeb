<?php
// Pour éviter les notices
if (session_status() == PHP_SESSION_NONE) {
  // Démarrer la session de manière sécurisée
  session_start([
    'cookie_httponly' => true, // Empêche JavaScript d'accéder au cookie de session
    'cookie_secure' => true, // Ne transmet le cookie de session que via HTTPS
    'use_strict_mode' => true // Active le mode strict pour la gestion de session
  ]);
}

/*
// Validation et nettoyage des données d'entrée
function secure_input($data) {
  return htmlspecialchars(trim($data)); // Empêche les attaques XSS et nettoie les données
}
*/

//var_dump($_SESSION);

//Base de donnée
require_once("Data/dataBaseConnection.php");

//Controller
require_once("Controllers/mainController.php");
