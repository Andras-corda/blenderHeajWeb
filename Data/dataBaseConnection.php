<?php
/**
 * Configuration de la base de données
 * Support : Heroku (production) et Local (développement)
 */

try {
    $host = "shortline.proxy.rlwy.net";
    $port = 48479;
    $dbname = "railway";
    $username = "root";
    $password = "gYUBDNceJNvzxGUstHoHbQndRLVUeTZL";
    $strConnection = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    

    // Créer la connexion PDO
    $pdo = new PDO($strConnection, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    
} catch (PDOException $e) {
    $msg = 'ERREUR DE CONNEXION À LA BASE DE DONNÉES' . PHP_EOL;
    $msg .= 'Message : ' . $e->getMessage() . PHP_EOL;
    $msg .= 'Fichier : ' . $e->getFile() . ':' . $e->getLine() . PHP_EOL;
    error_log($msg);
}
