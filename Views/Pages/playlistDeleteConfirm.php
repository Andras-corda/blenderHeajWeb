<div class="confirmation-page">
    <div class="confirmation-card">
        <div class="confirmation-card__icon">
            <span class="material-icons" translate="no">warning</span>
        </div>
        
        <h1 class="confirmation-card__title">
            <span translate="no">Delete this playlist?</span>
        </h1>
        
        <p class="confirmation-card__message">
            <span translate="no">You are about to delete the playlist:</span>
        </p>
        
        <div class="confirmation-card__info">
            <h2 translate="no"><?= htmlspecialchars($playlist['playlistTitle']) ?></h2>
            <?php if (!empty($playlist['playlistDescription'])): ?>
                <p translate="no"><?= htmlspecialchars($playlist['playlistDescription']) ?></p>
            <?php endif; ?>
        </div>
        
        <?php
        $videoCount = countVideosByPlaylistId($pdo, $playlist['playlistId']);
        if ($videoCount > 0):
        ?>
        <div class="confirmation-card__warning">
            <span class="material-icons" translate="no">info</span>
            <span translate="no">
                This playlist contains <?= $videoCount ?> video<?= $videoCount > 1 ? 's' : '' ?>. 
                All videos will also be deleted.
            </span>
        </div>
        <?php endif; ?>
        
        <p class="confirmation-card__caution">
            <span translate="no">This action is irreversible.</span>
        </p>
        
        <div class="confirmation-card__actions">
            <a href="/dashboard/playlist/edit?playlistId=<?= $playlist['playlistId'] ?>" class="btn btn-outline btn-lg">
                <span class="material-icons" translate="no">close</span>
                <span translate="no">Cancel</span>
            </a>
            
            <form method="POST" style="display: inline;">
                <input type="hidden" name="confirm_delete" value="1">
                <button type="submit" class="btn btn-danger btn-lg">
                    <span class="material-icons" translate="no">delete_forever</span>
                    <span translate="no">Delete permanently</span>
                </button>
            </form>
        </div>
    </div>
</div>