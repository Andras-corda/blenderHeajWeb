
<!DOCTYPE html>
<html lang="en" data-theme-preference="<?php echo $theme_preference; ?>">
<!-- <= $homePageAnimation; > -->
<head>
    <!-- meta -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Material Icon CDN -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <!-- Google Fonts - Roboto -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Liens css -->
    <link rel="stylesheet" href="/Assets/Css/main.css">
    <link rel="stylesheet" href="/Assets/Css/theme-switcher.css">
    <link rel="stylesheet" href="/Assets/Css/hamster.css">
    
    <!-- Titre et icon-->
    <link rel="icon" href="/Assets/Pictures/pictoHEAJ/rouge/pictos-rouge-16.png">
    <title><?= htmlspecialchars($title ?? 'Blender Heaj'); ?></title>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <?php require_once(__DIR__ . "/MainComponents/header.php"); ?>
        
        <?php require_once(__DIR__ . "/MainComponents/theme.php"); ?>
    </header>

    <!-- Main Content -->
    <main class="container py-6">
        <?php require_once($template); ?>
    </main>

    <!-- Footer -->
    <footer class="footer__simple">
        <?php require_once(__DIR__ . "/MainComponents/footer.php"); ?>
    </footer>

    <!-- Scripts -->
    <script src="/Assets/Scripts/themeChanger.js"></script>
</body>
</html>