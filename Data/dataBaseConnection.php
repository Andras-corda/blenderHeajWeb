<?php

try {
    $host = "shortline.proxy.rlwy.net";
    $port = 48479;
    $dbname = "railway";
    $username = "root";
    $password = "gYUBDNceJNvzxGUstHoHbQndRLVUeTZL";
    
    $strConnection = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

    $pdo = new PDO($strConnection, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_PERSISTENT => false,
    ]);

    // Confirmation de connexion (à retirer en prod)
    error_log("✅ Connexion BDD réussie sur $host:$port");

} catch (PDOException $e) {
    $msg = '❌ ERREUR DE CONNEXION À LA BASE DE DONNÉES' . PHP_EOL;
    $msg .= 'Message : ' . $e->getMessage() . PHP_EOL;
    $msg .= 'Code : ' . $e->getCode() . PHP_EOL;
    $msg .= 'Fichier : ' . $e->getFile() . ':' . $e->getLine() . PHP_EOL;
    
    error_log($msg);
    
    // En prod, ne pas afficher les détails
    http_response_code(500);
    die(json_encode([
        'success' => false,
        'error' => 'Erreur de connexion à la base de données'
    ]));
}
