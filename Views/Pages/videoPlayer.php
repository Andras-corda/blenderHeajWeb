<?php
function getYouTubeVideoId($url) {
    if (preg_match('/[?&]v=([a-zA-Z0-9_-]{11})/', $url, $matches)) {
        return $matches[1];
    }
    if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]{11})/', $url, $matches)) {
        return $matches[1];
    }
    if (preg_match('/\/shorts\/([a-zA-Z0-9_-]{11})/', $url, $matches)) {
        return $matches[1];
    }
    if (preg_match('/\/embed\/([a-zA-Z0-9_-]{11})/', $url, $matches)) {
        return $matches[1];
    }
    return null;
}

function isYouTubeShort($url) {
    return preg_match('/\/shorts\//', $url) === 1;
}

function getYouTubeEmbedUrl($url) {
    $videoId = getYouTubeVideoId($url);
    if ($videoId) {
        return "https://www.youtube.com/embed/" . $videoId;
    }
    return $url;
}

function getYouTubeThumbnail($url) {
    $videoId = getYouTubeVideoId($url);
    if ($videoId) {
        return "https://img.youtube.com/vi/" . $videoId . "/mqdefault.jpg";
    }
    return "https://via.placeholder.com/320x180?text=Video";
}

function getYouTubeVideoBadge($url) {
    if (isYouTubeShort($url)) {
        return '<span class="video-badge video-badge--short" translate="no">
                    <span class="material-icons" translate="no">bolt</span>
                    Short
                </span>';
    }
    return '';
}

$totalVideos = count($videos);
$currentVideo = $videos[0] ?? null;
$currentVideoIsShort = $currentVideo ? isYouTubeShort($currentVideo['url']) : false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_video'])) {
    if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'admin') {
        $videoUrl = trim($_POST['videoUrl'] ?? '');
        $videoName = trim($_POST['videoName'] ?? '');
        $videoDescription = trim($_POST['videoDescription'] ?? '');
        
        if (!empty($videoUrl)) {
            $added = addVideoToPlaylist($pdo, $playlistId, $videoUrl, $videoName, $videoDescription);
            
            if ($added) {
                $_SESSION['success'] = "Vidéo ajoutée avec succès !";
            } else {
                $_SESSION['error'] = "Erreur lors de l'ajout de la vidéo.";
            }
            
            header("Location: /playlist?playlistId=" . $playlistId);
            exit;
        }
    }
}
?>

<div class="player">
    <!-- Main Video Section -->
    <div class="player__main">
        <!-- Admin Controls -->
        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'admin'): ?>
        <div class="player__admin-bar">
            <a href="/dashboard/playlist/edit?playlistId=<?= $playlistId ?>" class="btn btn-secondary btn-sm">
                <span class="material-icons" translate="no">edit</span>
                <span translate="no">Éditer la playlist</span>
            </a>
        </div>
        <?php endif; ?>
        
        <!-- Video Frame -->
        <div class="player__video-wrapper <?= $currentVideoIsShort ? 'player__video-wrapper--short' : '' ?>" id="videoWrapper">
            <?php if ($currentVideo): ?>
                <iframe 
                    id="mainVideo"
                    src="<?= htmlspecialchars(getYouTubeEmbedUrl($currentVideo['url'])) ?>"
                    title="<?= htmlspecialchars($currentVideo['videoName'] ?? 'Vidéo') ?>"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
                </iframe>
            <?php else: ?>
                <div class="player__video-placeholder">
                    <span class="material-icons" translate="no">videocam_off</span>
                    <p><span translate="no">Aucune vidéo dans cette playlist</span></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Video Info -->
        <div class="player__info">
            <div class="player__title-row">
                <h1 class="player__title" id="mainTitle">
                    <span translate="no"><?= htmlspecialchars($currentVideo['videoName'] ?? 'Vidéo #' . ($currentVideo['order_position'] ?? 1)) ?></span>
                </h1>
                <div id="videoBadge">
                    <?= $currentVideo ? getYouTubeVideoBadge($currentVideo['url']) : '' ?>
                </div>
            </div>
            
            <?php if (!empty($currentVideo['videoDescription'])): ?>
            <p class="player__video-description" id="mainVideoDescription">
                <span translate="no"><?= htmlspecialchars($currentVideo['videoDescription']) ?></span>
            </p>
            <?php endif; ?>
            
            <div class="player__meta">
                <span class="player__counter" id="videoCounter" translate="no">
                    <span class="material-icons" translate="no">playlist_play</span>
                    <span translate="no">Vidéo <?= $currentVideo['order_position'] ?? 1 ?> / <?= $totalVideos ?></span>
                </span>
            </div>
        </div>

        <!-- Navigation Controls -->
        <?php if ($totalVideos > 1): ?>
        <div class="player__controls">
            <button class="player__nav-btn" id="prevBtn" <?= ($currentVideo['order_position'] ?? 1) <= 1 ? 'disabled' : '' ?>>
                <span class="material-icons" translate="no">skip_previous</span>
                <span translate="no">Précédent</span>
            </button>
            <button class="player__nav-btn" id="nextBtn" <?= ($currentVideo['order_position'] ?? 1) >= $totalVideos ? 'disabled' : '' ?>>
                <span translate="no">Suivant</span>
                <span class="material-icons" translate="no">skip_next</span>
            </button>
        </div>
        <?php endif; ?>

        <!-- Add Video Form (Admin only) -->
        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'admin'): ?>
        <div class="player__add-section">
            <h3 class="player__add-title">
                <span class="material-icons" translate="no">add_circle</span>
                <span translate="no">Ajouter une vidéo</span>
            </h3>
            <form method="POST" class="player__add-form">
                <input type="hidden" name="add_video" value="1">
                
                <div class="form-group">
                    <input 
                        type="url" 
                        name="videoUrl" 
                        required 
                        class="form-control"
                        placeholder="URL YouTube (vidéo ou short)"
                    >
                </div>
                
                <div class="form-group">
                    <input 
                        type="text" 
                        name="videoName" 
                        required 
                        class="form-control"
                        placeholder="Titre de la vidéo"
                    >
                </div>
                
                <div class="form-group">
                    <textarea 
                        name="videoDescription" 
                        class="form-control"
                        rows="2"
                        placeholder="Description (optionnel)"
                    ></textarea>
                </div>
                
                <small class="form-text" style="display: block; margin-bottom: 0.5rem;">
                    <span translate="no">💡 La vidéo sera ajoutée à la fin de la playlist</span>
                </small>
                
                <button type="submit" class="btn btn-success btn-block">
                    <span class="material-icons" translate="no">add</span>
                    <span translate="no">Ajouter</span>
                </button>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar: Playlist -->
    <div class="player__sidebar">
        <div class="player__playlist">
            <div class="player__playlist-header">
                <h2 class="player__playlist-title" translate="no">
                    <?= htmlspecialchars($playlist['playlistTitle']) ?>
                </h2>
                <span class="player__playlist-count" translate="no">
                    <?= $totalVideos ?> vidéo<?= $totalVideos > 1 ? 's' : '' ?>
                </span>
            </div>

            <?php if (!empty($playlist['playlistDescription'])): ?>
            <p class="player__playlist-description" translate="no">
                <?= htmlspecialchars($playlist['playlistDescription']) ?>
            </p>
            <?php endif; ?>

            <div class="player__video-list" id="videoList">
                <?php foreach ($videos as $index => $video): ?>
                <div 
                    class="player__video-item <?= $index === 0 ? 'player__video-item--active' : '' ?>"
                    data-index="<?= $index ?>"
                    data-url="<?= htmlspecialchars(getYouTubeEmbedUrl($video['url'])) ?>"
                    data-original-url="<?= htmlspecialchars($video['url']) ?>"
                    data-order="<?= $video['order_position'] ?>"
                    data-video-title="<?= htmlspecialchars($video['videoName'] ?? '') ?>"
                    data-video-description="<?= htmlspecialchars($video['videoDescription'] ?? '') ?>"
                    data-is-short="<?= isYouTubeShort($video['url']) ? '1' : '0' ?>"
                    onclick="loadVideo(this)"
                >
                    <div class="player__video-item-thumbnail">
                        <img 
                            src="<?= htmlspecialchars(getYouTubeThumbnail($video['url'])) ?>" 
                            alt="Thumbnail"
                            loading="lazy"
                        >
                        <div class="player__video-item-overlay">
                            <span class="material-icons" translate="no">play_arrow</span>
                        </div>
                    </div>
                    <div class="player__video-item-info">
                        <?= getYouTubeVideoBadge($video['url']) ?>
                        <span class="player__video-item-number" translate="no">#<?= $video['order_position'] ?></span>
                        <span class="player__video-item-title" translate="no">
                            <?= htmlspecialchars($video['videoName'] ?? 'Vidéo #' . $video['order_position']) ?>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
const videos = document.querySelectorAll('.player__video-item');
let currentIndex = 0;

function loadVideo(element) {
    const iframe = document.getElementById('mainVideo');
    const mainTitle = document.getElementById('mainTitle');
    const mainDescription = document.getElementById('mainVideoDescription');
    const videoCounter = document.getElementById('videoCounter');
    const videoBadge = document.getElementById('videoBadge');
    const videoWrapper = document.getElementById('videoWrapper');
    
    const url = element.dataset.url;
    const originalUrl = element.dataset.originalUrl;
    const order = element.dataset.order;
    const index = parseInt(element.dataset.index);
    const videoTitle = element.dataset.videoTitle || 'Vidéo #' + order;
    const videoDescription = element.dataset.videoDescription || '';
    const isShort = element.dataset.isShort === '1';

    iframe.src = url;
    
    mainTitle.querySelector('span').innerText = videoTitle;
    
    if (videoDescription && mainDescription) {
        mainDescription.querySelector('span').innerText = videoDescription;
        mainDescription.style.display = 'block';
    } else if (mainDescription) {
        mainDescription.style.display = 'none';
    }
    
    videoCounter.querySelector('span:last-child').innerText = 'Vidéo ' + order + ' / <?= $totalVideos ?>';
    
    if (isShort) {
        videoBadge.innerHTML = '<span class="video-badge video-badge--short" translate="no"><span class="material-icons" translate="no">bolt</span>Short</span>';
        videoWrapper.classList.add('player__video-wrapper--short');
    } else {
        videoBadge.innerHTML = '';
        videoWrapper.classList.remove('player__video-wrapper--short');
    }
    
    videos.forEach(v => v.classList.remove('player__video-item--active'));
    element.classList.add('player__video-item--active');
    
    currentIndex = index;
    updateButtons();

    element.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function updateButtons() {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    
    if (prevBtn) prevBtn.disabled = currentIndex <= 0;
    if (nextBtn) nextBtn.disabled = currentIndex >= videos.length - 1;
}

document.getElementById('prevBtn')?.addEventListener('click', () => {
    if (currentIndex > 0) {
        loadVideo(videos[currentIndex - 1]);
    }
});

document.getElementById('nextBtn')?.addEventListener('click', () => {
    if (currentIndex < videos.length - 1) {
        loadVideo(videos[currentIndex + 1]);
    }
});
</script>