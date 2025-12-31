<div class="dashboard-header">
    <div class="dashboard-header__content">
        <h1 class="dashboard-header__title">
            <span class="material-icons" translate="no">dashboard</span>
            <span translate="no">Dashboard</span>
        </h1>
        <p class="dashboard-header__description">
            <span translate="no">Manage your playlists and content</span>
        </p>
    </div>
    <a href="/dashboard/playlist/create" class="btn btn-primary">
        <span class="material-icons" translate="no">add</span>
        <span translate="no">New playlist</span>
    </a>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-card__icon stat-card__icon--primary">
            <span class="material-icons" translate="no">playlist_play</span>
        </div>
        <div class="stat-card__content">
            <span class="stat-card__value" translate="no"><?= count($allPlaylists ?? []) ?></span>
            <span class="stat-card__label" translate="no">Playlist<?= count($allPlaylists ?? []) > 1 ? 's' : '' ?></span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-card__icon stat-card__icon--success">
            <span class="material-icons" translate="no">video_library</span>
        </div>
        <div class="stat-card__content">
            <?php
            $totalVideos = 0;
            foreach ($allPlaylists ?? [] as $playlist) {
                $totalVideos += countVideosByPlaylistId($pdo, $playlist['playlistId']);
            }
            ?>
            <span class="stat-card__value" translate="no"><?= $totalVideos ?></span>
            <span class="stat-card__label" translate="no">Video<?= $totalVideos > 1 ? 's' : '' ?></span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-card__icon stat-card__icon--info">
            <span class="material-icons" translate="no">person</span>
        </div>
        <div class="stat-card__content">
            <span class="stat-card__value" translate="no"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></span>
            <span class="stat-card__label" translate="no">Connected</span>
        </div>
    </div>
</div>

<div class="dashboard-section">
    <h2 class="dashboard-section__title">
        <span translate="no">My Playlists</span>
    </h2>
    
    <?php if (!empty($allPlaylists)): ?>
        <div class="dashboard-playlists">
            <?php foreach ($allPlaylists as $playlist): ?>
                <div class="dashboard-playlist-card">
                    <div class="dashboard-playlist-card__thumbnail">
                        <?php if (!empty($playlist['playlistThumbnail'])): ?>
                            <img 
                                src="<?= htmlspecialchars($playlist['playlistThumbnail']) ?>" 
                                alt="<?= htmlspecialchars($playlist['playlistTitle']) ?>"
                                loading="lazy"
                            >
                        <?php else: ?>
                            <div class="dashboard-playlist-card__thumbnail-placeholder">
                                <span class="material-icons" translate="no">playlist_play</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="dashboard-playlist-card__content">
                        <h3 class="dashboard-playlist-card__title" translate="no">
                            <?= htmlspecialchars($playlist['playlistTitle']) ?>
                        </h3>
                        <p class="dashboard-playlist-card__description" translate="no">
                            <?= htmlspecialchars($playlist['playlistDescription'] ?: 'No description') ?>
                        </p>
                        <div class="dashboard-playlist-card__meta">
                            <span class="dashboard-playlist-card__meta-item" translate="no">
                                <span class="material-icons" translate="no">video_library</span>
                                <span translate="no"><?= countVideosByPlaylistId($pdo, $playlist['playlistId']) ?> videos</span>
                            </span>
                            <span class="dashboard-playlist-card__meta-item" translate="no">
                                <span class="material-icons" translate="no">tag</span>
                                <span translate="no">ID: <?= $playlist['playlistId'] ?></span>
                            </span>
                        </div>
                    </div>
                    
                    <div class="dashboard-playlist-card__actions">
                        <a href="/playlist?playlistId=<?= $playlist['playlistId'] ?>" class="btn btn-ghost btn-sm" title="View">
                            <span class="material-icons" translate="no">visibility</span>
                        </a>
                        <a href="/dashboard/playlist/edit?playlistId=<?= $playlist['playlistId'] ?>" class="btn btn-ghost btn-sm" title="Edit">
                            <span class="material-icons" translate="no">edit</span>
                        </a>
                        <a href="/dashboard/playlist/delete?playlistId=<?= $playlist['playlistId'] ?>" class="btn btn-ghost btn-sm btn-danger" title="Delete">
                            <span class="material-icons" translate="no">delete</span>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-state__icon">
                <span class="material-icons" translate="no">playlist_remove</span>
            </div>
            <h3 class="empty-state__title">
                <span translate="no">No playlists</span>
            </h3>
            <p class="empty-state__description">
                <span translate="no">Create your first playlist to get started</span>
            </p>
            <a href="/dashboard/playlist/create" class="btn btn-primary">
                <span class="material-icons" translate="no">add</span>
                <span translate="no">Create a playlist</span>
            </a>
        </div>
    <?php endif; ?>
</div>