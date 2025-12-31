<div class="page-header">
    <h1 class="page-header__title">
        <span class="material-icons" translate="no">playlist_play</span>
        <span translate="no">Playlists</span>
    </h1>
    
    <div class="search-bar">
        <div class="search-bar__input-container">
            <input 
                type="text" 
                id="playlistSearch" 
                class="search-bar__input" 
                placeholder="Search"
                onkeyup="filterPlaylists()"
            >
            <button class="search-bar__clear" id="clearSearch" onclick="clearSearch()" style="display: none;">
                <span class="material-icons" translate="no">close</span>
            </button>
        </div>
        <button class="search-bar__button" onclick="filterPlaylists()" title="Search">
            <span class="material-icons" translate="no">search</span>
        </button>
    </div>
</div>

<div class="results-info" id="resultsInfo">
    <span translate="no"><?= count($playlists ?? []) ?> playlist<?= count($playlists ?? []) > 1 ? 's' : '' ?></span>
</div>

<?php if (!empty($playlists)) : ?>
    <div class="playlists-grid">
        <?php foreach ($playlists as $playlist) : ?>
            <div class="playlist-card" data-title="<?= strtolower(htmlspecialchars($playlist['playlistTitle'])) ?>">
                <a href="/playlist?playlistId=<?= htmlspecialchars($playlist['playlistId']) ?>" class="playlist-card__link">
                    <div class="playlist-card__thumbnail">
                        <?php if (!empty($playlist['playlistThumbnail'])) : ?>
                            <img 
                                src="<?= htmlspecialchars($playlist['playlistThumbnail']) ?>" 
                                alt="<?= htmlspecialchars($playlist['playlistTitle']) ?>"
                                loading="lazy"
                            >
                        <?php else: ?>
                            <div class="playlist-card__thumbnail-placeholder">
                                <span class="material-icons" translate="no">playlist_play</span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="playlist-card__overlay">
                            <div class="playlist-card__play-icon">
                                <span class="material-icons" translate="no">play_arrow</span>
                            </div>
                            <div class="playlist-card__count">
                                <?php 
                                $videoCount = isset($pdo) ? countVideosByPlaylistId($pdo, $playlist['playlistId']) : 0;
                                ?>
                                <span class="material-icons" translate="no">playlist_play</span>
                                <span translate="no"><?= $videoCount ?></span>
                            </div>
                        </div>
                        
                        <div class="playlist-card__title-overlay">
                            <h3 class="playlist-card__title" translate="no">
                                <?= htmlspecialchars($playlist['playlistTitle']) ?>
                            </h3>
                        </div>
                    </div>
                    
                    <div class="playlist-card__info">
                        <p class="playlist-card__meta">
                            <span translate="no">View full playlist</span>
                        </p>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="no-results" id="noResults" style="display: none;">
        <span class="material-icons" translate="no">search_off</span>
        <p><span translate="no">No playlists match your search.</span></p>
    </div>
<?php else: ?>
    <div class="empty-state">
        <div class="empty-state__icon">
            <span class="material-icons" translate="no">playlist_remove</span>
        </div>
        <h3 class="empty-state__title">
            <span translate="no">No playlists available</span>
        </h3>
        <p class="empty-state__description">
            <span translate="no">Come back soon, new playlists will be added!</span>
        </p>
        <a href="/" class="btn btn-primary">
            <span class="material-icons" translate="no">home</span>
            <span translate="no">Back to home</span>
        </a>
    </div>
<?php endif; ?>

<script>
function filterPlaylists() {
    const searchInput = document.getElementById('playlistSearch');
    const searchValue = searchInput.value.toLowerCase().trim();
    const playlists = document.querySelectorAll('.playlist-card');
    const noResults = document.getElementById('noResults');
    const resultsInfo = document.getElementById('resultsInfo');
    const clearBtn = document.getElementById('clearSearch');
    let visibleCount = 0;
    
    clearBtn.style.display = searchValue ? 'flex' : 'none';
    
    playlists.forEach(playlist => {
        const title = playlist.dataset.title;
        const isVisible = title.includes(searchValue);
        playlist.style.display = isVisible ? '' : 'none';
        if (isVisible) visibleCount++;
    });
    
    if (searchValue) {
        resultsInfo.innerHTML = `<span translate="no">${visibleCount} result${visibleCount > 1 ? 's' : ''}</span>`;
    } else {
        resultsInfo.innerHTML = `<span translate="no"><?= count($playlists ?? []) ?> playlist<?= count($playlists ?? []) > 1 ? 's' : '' ?></span>`;
    }
    
    noResults.style.display = (visibleCount === 0 && searchValue) ? 'flex' : 'none';
}

function clearSearch() {
    const searchInput = document.getElementById('playlistSearch');
    searchInput.value = '';
    filterPlaylists();
    searchInput.focus();
}

document.getElementById('playlistSearch')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
    }
});
</script>