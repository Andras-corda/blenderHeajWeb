<?php
// Configuration Discord OAuth2
define('DISCORD_CLIENT_ID', '1431177167605207070');
define('DISCORD_CLIENT_SECRET', 'TjwWFMe0MCrEorh3vNOeYmMWAsr1uTiF');

// Déterminer l'URL de redirection selon l'environnement
define('DISCORD_REDIRECT_URI', "https://blenderheajweb-production.up.railway.app/auth/discord/callback");

// URLs de l'API Discord
define('DISCORD_AUTH_URL', 'https://discord.com/api/oauth2/authorize');
define('DISCORD_TOKEN_URL', 'https://discord.com/api/oauth2/token');
define('DISCORD_API_URL', 'https://discord.com/api/users/@me');

// Scopes Discord (permissions demandées)
define('DISCORD_SCOPES', 'identify email');
?>