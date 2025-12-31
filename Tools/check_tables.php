<?php
/**
 * Script de vérification détaillée des tables MySQL
 * Accès : https://blender-heaj-540530c6e70e.herokuapp.com/check_tables.php
 */

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Vérification détaillée des tables</title>
    <style>
        body { 
            font-family: monospace; 
            background: #0d1117; 
            color: #e6edf3; 
            padding: 20px;
        }
        .success { color: #3fb950; }
        .error { color: #f85149; }
        .info { color: #58a6ff; }
        pre { background: #161b22; padding: 15px; border-radius: 6px; overflow-x: auto; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #30363d; padding: 6px 10px; }
        th { background: #21262d; color: #58a6ff; }
        tr:nth-child(even) { background: #161b22; }
    </style>
</head>
<body>";

echo "<h1>🔍 Vérification détaillée de la base de données</h1>";

$url = getenv('DATABASE_URL') ?: getenv('JAWSDB_URL');

if (!$url) {
    die("<p class='error'>❌ DATABASE_URL non trouvée</p></body></html>");
}

$dbopts = parse_url($url);
$host = $dbopts["host"];
$port = $dbopts["port"] ?? 3306;
$dbname = ltrim($dbopts["path"], '/');
$username = $dbopts["user"];
$password = $dbopts["pass"];

echo "<p class='info'>📡 Base de données : <b>$dbname</b></p>";

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p class='success'>✅ Connexion réussie !</p>";
    
    echo "<h2>📋 Tables existantes :</h2>";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tables)) {
        echo "<p class='error'>❌ AUCUNE TABLE trouvée !</p>";
        echo "<p>Vous pouvez créer les tables via :</p>";
        echo "<pre>https://blender-heaj-540530c6e70e.herokuapp.com/setup_database.php?password=setup123</pre>";
    } else {
        echo "<pre>";
        foreach ($tables as $table) {
            echo "  ✓ $table\n";
        }
        echo "</pre>";
        
        // Pour chaque table, afficher les détails
        foreach ($tables as $table) {
            echo "<h3>📄 Table : <span class='info'>$table</span></h3>";
            
            // Structure des colonnes
            echo "<h4>📑 Structure :</h4>";
            $columns = $pdo->query("DESCRIBE `$table`")->fetchAll(PDO::FETCH_ASSOC);
            echo "<table><tr>";
            foreach (array_keys($columns[0]) as $colName) {
                echo "<th>$colName</th>";
            }
            echo "</tr>";
            foreach ($columns as $col) {
                echo "<tr>";
                foreach ($col as $val) {
                    echo "<td>" . htmlspecialchars($val) . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
            
            // Nombre de lignes
            $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
            echo "<p class='info'>📊 Nombre d’enregistrements : <b>$count</b></p>";
            
            // Aperçu des 5 premières lignes
            if ($count > 0) {
                echo "<h4>🔎 Aperçu des 5 premières lignes :</h4>";
                $rows = $pdo->query("SELECT * FROM `$table` LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
                echo "<table><tr>";
                foreach (array_keys($rows[0]) as $header) {
                    echo "<th>$header</th>";
                }
                echo "</tr>";
                foreach ($rows as $row) {
                    echo "<tr>";
                    foreach ($row as $cell) {
                        $val = is_null($cell) ? "<i>null</i>" : htmlspecialchars($cell);
                        echo "<td>$val</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p class='error'>⚠️ Table vide.</p>";
            }
        }
    }
    
} catch (PDOException $e) {
    echo "<p class='error'>❌ Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "</body></html>";
?>
