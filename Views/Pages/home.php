<!-- Connection Warning -->
<?php if (!isset($_SESSION['user_id'])): ?>
    <div class="alert alert-warning mb-6">
        <span class="material-icons alert__icon" translate="no">warning</span>
        <div class="alert__content">
            <strong>Attention :</strong> a connection is required to access the entire site.
        </div>
    </div>
<?php endif; ?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero__content">
        <h1 class="hero__title">
            Learning <span translate="no"> Blender </span> easily
        </h1>
        <p class="hero__description">
            Discover Blender at your own rhythm with simple, structured, and accessible resources. No matter if you're a beginner or already familiar with Blender, this site shows you step by step how to learn 3D modeling, animation, texturing, and much more.
        </p>
        <div class="hero__actions">
            <a href="/Playlists" class="btn btn-primary btn-lg">
                <span class="material-icons" translate="no">play_circle</span>
                View playlists
            </a>
            <a href="/forum" class="btn btn-outline btn-lg">
                <span class="material-icons" translate="no">groups</span>
                Join the forum
            </a>
        </div>
    </div>
    <div class="hero__image">
        <!--<div class="hero__image-placeholder"> -->
        <img src="Assets/Pictures/modeling_meshes_primitives_all.png" alt="">
        <!-- <span class="material-icons" style="font-size: 64px;" translate="no">view_in_ar</span> -->
        <!--</div> -->
    </div>
</section>

<!-- Playlists Section -->
<section class="section playlists-section">
    <div class="section__header">
        <h2 class="section__title">Playlists</h2>
        <a href="/Playlists" class="section__link">
            see all
            <span class="material-icons" translate="no">arrow_forward</span>
        </a>
    </div>

    <?php if (!empty($playlists)) : ?>
        <div class="playlists-section__grid">
            <?php foreach ($playlists as $playlist) : ?>
                <a href="/playlist?playlistId=<?= htmlspecialchars($playlist['playlistId']) ?>" class="home-playlist-card">
                    <div class="home-playlist-card__thumbnail">
                        <?php if (!empty($playlist['playlistThumbnail'])) : ?>
                            <img
                                src="<?= htmlspecialchars($playlist['playlistThumbnail']) ?>"
                                alt="<?= htmlspecialchars($playlist['playlistTitle']) ?>"
                                onerror="this.style.display='none'">
                        <?php endif; ?>
                        <div class="home-playlist-card__overlay"></div>
                        <div class="home-playlist-card__play">
                            <span class="material-icons" translate="no">play_arrow</span>
                        </div>
                    </div>
                    <div class="home-playlist-card__body">
                        <h3 class="home-playlist-card__title">
                            <?= htmlspecialchars($playlist['playlistTitle']) ?>
                        </h3>
                        <p class="home-playlist-card__description">
                            <?= htmlspecialchars($playlist['playlistDescription']) ?>
                        </p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-playlists">
            <p>No playlists available at this time.</p>
        </div>
    <?php endif; ?>
</section>

<!-- Forum Section -->
<section class="section forum-section">
    <div class="section__header">
        <h2 class="section__title">Forums</h2>
    </div>
    
    <?php require_once("Views/MainComponents/comingSoon.php"); ?>
</section>