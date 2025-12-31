```sql
-- Table des utilisateurs
CREATE TABLE utilisateurs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    role VARCHAR(255) NOT NULL DEFAULT 'user',
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    discord_id VARCHAR(64) UNIQUE NOT NULL,
    discord_username VARCHAR(100) NOT NULL,
    discord_discriminator VARCHAR(10) NOT NULL,
    discord_avatar VARCHAR(255) NOT NULL,
    discord_access_token VARCHAR(2000) NOT NULL,
    discord_refresh_token VARCHAR(2000) NOT NULL,
    discord_token_expires_at INT NOT NULL,
    
    INDEX idx_discord_id (discord_id)
);

-- Table des playlists
CREATE TABLE playlist (
  playlistId INT AUTO_INCREMENT PRIMARY KEY,
  playlistUserId INT NOT NULL,
  playlistTitle VARCHAR(200) NOT NULL,
  playlistThumbnail VARCHAR(200),
  playlistDescription TEXT,
  playlistCreatedAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Table des vidéos (hébergées sur YouTube en non répertorié)
CREATE TABLE playlist_videos (
  videoId INT AUTO_INCREMENT PRIMARY KEY,
  videoPlaylistId INT NOT NULL,
  videoUrl VARCHAR(255) NOT NULL
);

```

```sql
-- Insertion de deux utilisateurs administrateurs
INSERT INTO utilisateurs (nom, email, mot_de_passe, image, role) VALUES
(
    'Administrateur Principal',
    'admin@example.com',
    'chucknorris',
    'https://ui-avatars.com/api/?name=Admin+Principal&size=200',
    'admin'
),
(
    'Administrateur Système',
    'sysadmin@example.com',
    'chucknorris',
    'https://ui-avatars.com/api/?name=Sys+Admin&size=200',
    'admin'
);
```