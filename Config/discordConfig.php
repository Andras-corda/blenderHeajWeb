<?php
// Configuration Discord OAuth2
define('DISCORD_CLIENT_ID', '1431177167605207070');
define('DISCORD_CLIENT_SECRET', 'TjwWFMe0MCrEorh3vNOeYmMWAsr1uTiF');

// Déterminer l'URL de redirection selon l'environnement
if (getenv('HEROKU_APP_NAME')) {
    // Sur Heroku
    $appName = getenv('HEROKU_APP_NAME');
    define('DISCORD_REDIRECT_URI', "https://{$appName}.herokuapp.com/auth/discord/callback");
} else {
    // En local - Utilisez votre configuration locale
    define('DISCORD_REDIRECT_URI', 'http://localhost:3000/index.php?callback=discord');
}

// URLs de l'API Discord
define('DISCORD_AUTH_URL', 'https://discord.com/api/oauth2/authorize');
define('DISCORD_TOKEN_URL', 'https://discord.com/api/oauth2/token');
define('DISCORD_API_URL', 'https://discord.com/api/users/@me');

// Scopes Discord (permissions demandées)
define('DISCORD_SCOPES', 'identify email');
?>