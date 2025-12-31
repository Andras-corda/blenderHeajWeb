<?php
/**
 * Affiche une vidéo depuis playlist_videos
 * Convertit automatiquement les liens YouTube en embed
 */

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Affichage vidéo playlist</title>
    <style>
        body {
            font-family: monospace;
            background: #0d1117;
            color: #e6edf3;
            padding: 20px;
        }
        h1 { color: #58a6ff; }
        iframe {
            width: 100%;
            height: 75vh;
            border: 2px solid #30363d;
            border-radius: 10px;
            background: #161b22;
        }
        .url-box {
            background: #161b22;
            padding: 10px 15px;
            border-radius: 6px;
            color: #58a6ff;
            word-break: break-all;
            margin-bottom: 15px;
        }
        .error { color: #f85149; }
        select, button {
            padding: 6px 10px;
            border-radius: 6px;
            background: #21262d;
            color: #e6edf3;
            border: 1px solid #30363d;
        }
        button:hover {
            background: #58a6ff;
            color: #0d1117;
            cursor: pointer;
        }
    </style>
</head>
<body>";

echo "<h1>🎬 Vidéo depuis playlist_videos</h1>";

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

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Sélection vidéo
    if (!isset($_GET['videoId'])) {
        $videos = $pdo->query("SELECT videoId, playlistId, videoUrl FROM playlist_videos ORDER BY videoId ASC")->fetchAll(PDO::FETCH_ASSOC);

        if (empty($videos)) {
            echo "<p class='error'>❌ Aucune vidéo trouvée</p>";
        } else {
            echo "<form method='get'>";
            echo "<select name='videoId'>";
            foreach ($videos as $v) {
                $vid = (int)$v['videoId'];
                $pid = (int)$v['playlistId'];
                $url = htmlspecialchars($v['videoUrl']);
                echo "<option value='$vid'>#{$vid} (playlist $pid) - $url</option>";
            }
            echo "</select> ";
            echo "<button type='submit'>Afficher</button>";
            echo "</form>";
        }
        echo "</body></html>";
        exit;
    }

    // Récupération de la vidéo
    $videoId = (int)$_GET['videoId'];
    $stmt = $pdo->prepare("SELECT videoUrl, playlistId FROM playlist_videos WHERE videoId = ?");
    $stmt->execute([$videoId]);
    $video = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$video) {
        echo "<p class='error'>❌ Vidéo introuvable</p>";
        echo "</body></html>";
        exit;
    }

    $videoUrl = trim($video['videoUrl']);
    $playlistId = htmlspecialchars($video['playlistId']);
    $embedUrl = $videoUrl;

    // 🔄 Conversion YouTube → version embarquée
    if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $videoUrl, $matches)) {
        $embedUrl = "https://www.youtube.com/embed/" . $matches[1];
    } elseif (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $videoUrl, $matches)) {
        $embedUrl = "https://www.youtube.com/embed/" . $matches[1];
    }

    $encodedUrl = htmlspecialchars($embedUrl, ENT_QUOTES, 'UTF-8');

    echo "<h2>🎞️ Vidéo #$videoId (Playlist $playlistId)</h2>";
    echo "<div class='url-box'>🔗 URL affichée : <b>$encodedUrl</b></div>";
    echo "<iframe src='$encodedUrl' allowfullscreen title='Vidéo Playlist'></iframe>";

} catch (PDOException $e) {
    echo "<p class='error'>❌ Erreur base de données : " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "</body></html>";
?>
