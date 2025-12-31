<?php
$files = [
    'Views/Pages/home.php',
    'Views/Pages/videoPlayer.php',
    'Views/Users/profile.php',
    'Views/Pages/dashboard.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Chercher les ->
        if (preg_match_all('/\$\w+->(\w+)/', $content, $matches)) {
            echo "<h3>📄 $file</h3>";
            echo "<ul>";
            foreach (array_unique($matches[0]) as $match) {
                echo "<li style='color:red;'>$match</li>";
            }
            echo "</ul>";
        } else {
            echo "<h3 style='color:green;'>✅ $file - OK</h3>";
        }
    }
}
?>