<?php
header('Content-Type: text/plain; charset=utf-8');

echo "=== TEST CONNEXION PDO ===\n\n";

try {
    $host = "shortline.proxy.rlwy.net";
    $port = 48479;
    $dbname = "railway";
    $username = "root";
    $password = "gYUBDNceJNvzxGUstHoHbQndRLVUeTZL";
    
    $strConnection = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    
    echo "📡 Tentative de connexion...\n";
    echo "DSN: $strConnection\n";
    echo "User: $username\n\n";

    $pdo = new PDO($strConnection, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    echo "CONNEXION RÉUSSIE !\n\n";
    
    // Test 1 : Version MySQL
    $version = $pdo->query('SELECT VERSION()')->fetchColumn();
    echo "MySQL version: $version\n\n";
    
    // Test 2 : Liste des tables
    $stmt = $pdo->query('SHOW TABLES');
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Tables trouvées (" . count($tables) . ") :\n";
    foreach ($tables as $table) {
        echo "  - $table\n";
    }
    
    // Test 3 : Requête simple
    if (in_array('users', $tables)) {
        echo "\n🔍 Test requête sur 'users' :\n";
        $stmt = $pdo->query('SELECT COUNT(*) as total FROM users');
        $count = $stmt->fetch();
        echo "  Nombre d'utilisateurs: " . $count['total'] . "\n";
    }
    
    echo "\n TOUS LES TESTS SONT OK !\n";

} catch (PDOException $e) {
    echo "❌ ERREUR PDO :\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getCode() . "\n";
    echo "Ligne: " . $e->getLine() . "\n";
}
