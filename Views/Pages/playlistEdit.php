<div class="page-header">
    <h1 class="page-header__title">
        <span class="material-icons" translate="no">edit</span>
        <span translate="no">Edit playlist</span>
    </h1>
    <div class="page-header__actions">
        <a href="/playlist?playlistId=<?= $playlist['playlistId'] ?>" class="btn btn-secondary">
            <span class="material-icons" translate="no">visibility</span>
            <span translate="no">View</span>
        </a>
        <a href="/dashboard" class="btn btn-secondary">
            <span class="material-icons" translate="no">arrow_back</span>
            <span translate="no">Back</span>
        </a>
    </div>
</div>

<div class="edit-layout">
    <div class="edit-sidebar">
        <div class="form-card">
            <h2 class="form-card__title">
                <span translate="no">Information</span>
            </h2>
            
            <form method="POST">
                <input type="hidden" name="update_playlist" value="1">
                
                <div class="form-group">
                    <label for="playlistTitle" class="form-label">
                        <span translate="no">Title *</span>
                    </label>
                    <input 
                        type="text" 
                        id="playlistTitle" 
                        name="playlistTitle" 
                        value="<?= htmlspecialchars($playlist['playlistTitle']) ?>"
                        required 
                        class="form-control"
                        maxlength="200"
                    >
                </div>
                
                <div class="form-group">
                    <label for="playlistDescription" class="form-label">
                        <span translate="no">Description</span>
                    </label>
                    <textarea 
                        id="playlistDescription" 
                        name="playlistDescription" 
                        class="form-control"
                        rows="5"
                    ><?= htmlspecialchars($playlist['playlistDescription'] ?? '') ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="playlistThumbnail" class="form-label">
                        <span translate="no">Thumbnail (URL or hex color)</span>
                    </label>
                    <input 
                        type="text" 
                        id="playlistThumbnail" 
                        name="playlistThumbnail" 
                        value="<?= htmlspecialchars($playlist['playlistThumbnail'] ?? '') ?>"
                        class="form-control"
                        placeholder="https://... or #FF5733"
                        onchange="previewThumbnail()"
                    >
                </div>
                
                <?php 
                $thumb = $playlist['playlistThumbnail'] ?? '';
                $isHexColor = preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', trim($thumb));
                if (!empty($thumb)): ?>
                <div class="thumbnail-preview">
                    <label class="form-label">
                        <span translate="no">Preview</span>
                    </label>
                    <div class="thumbnail-preview__wrapper" id="thumbnailWrapper" 
                         <?= $isHexColor ? 'style="background-color:' . htmlspecialchars(trim($thumb)) . '; display:flex; align-items:center; justify-content:center;"' : '' ?>>
                        <?php if ($isHexColor): ?>
                            <span class="material-icons" style="font-size:48px; color:rgba(255,255,255,0.6);" translate="no">playlist_play</span>
                        <?php else: ?>
                            <img id="thumbnailPreview" src="<?= htmlspecialchars($thumb) ?>" alt="Preview">
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <span class="material-icons" translate="no">save</span>
                    <span translate="no">Save</span>
                </button>
            </form>
            
            <div class="form-divider"></div>
            <a href="/dashboard/playlist/delete?playlistId=<?= $playlist['playlistId'] ?>" class="btn btn-danger btn-block">
                <span class="material-icons" translate="no">delete</span>
                <span translate="no">Delete playlist</span>
            </a>
        </div>
    </div>
    
    <div class="edit-main">
        <div class="form-card">
            <h2 class="form-card__title">
                <span class="material-icons" translate="no">video_library</span>
                <span translate="no">Videos (<?= count($videos) ?>)</span>
            </h2>
            
            <?php if (!empty($videos)): ?>
                <div class="video-list" id="videoList">
                    <?php foreach ($videos as $video): ?>
                        <div class="video-list-item" draggable="true" data-video-id="<?= $video['id'] ?>">
                            <div class="video-list-item__drag">
                                <span class="material-icons" translate="no">drag_indicator</span>
                            </div>
                            
                            <div class="video-list-item__order">
                                <span translate="no">#<?= $video['order_position'] ?></span>
                            </div>
                            
                            <div class="video-list-item__content">
                                <div class="video-list-item__title" translate="no">
                                    <?php 
                                    $displayName = !empty($video['videoName']) ? $video['videoName'] : $video['url'];
                                    echo htmlspecialchars($displayName);
                                    ?>
                                </div>
                                <?php if (!empty($video['videoDescription'])): ?>
                                <div class="video-list-item__description" translate="no">
                                    <?= htmlspecialchars($video['videoDescription']) ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="video-list-item__actions">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="move_video_up" value="1">
                                    <input type="hidden" name="videoId" value="<?= $video['id'] ?>">
                                    <button 
                                        type="submit" 
                                        class="btn btn-ghost btn-sm" 
                                        title="Move up"
                                        <?= $video['order_position'] <= 1 ? 'disabled' : '' ?>
                                    >
                                        <span class="material-icons" translate="no">arrow_upward</span>
                                    </button>
                                </form>
                                
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="move_video_down" value="1">
                                    <input type="hidden" name="videoId" value="<?= $video['id'] ?>">
                                    <button 
                                        type="submit" 
                                        class="btn btn-ghost btn-sm" 
                                        title="Move down"
                                        <?= $video['order_position'] >= count($videos) ? 'disabled' : '' ?>
                                    >
                                        <span class="material-icons" translate="no">arrow_downward</span>
                                    </button>
                                </form>
                                
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this video?');">
                                    <input type="hidden" name="delete_video" value="1">
                                    <input type="hidden" name="videoId" value="<?= $video['id'] ?>">
                                    <button type="submit" class="btn btn-ghost btn-sm btn-danger" title="Delete">
                                        <span class="material-icons" translate="no">delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state-small">
                    <span class="material-icons" translate="no">videocam_off</span>
                    <p><span translate="no">No videos in this playlist</span></p>
                </div>
            <?php endif; ?>
            
            <div class="form-divider"></div>
            <h3 class="form-card__subtitle">
                <span class="material-icons" translate="no">add_circle</span>
                <span translate="no">Add a video</span>
            </h3>
            
            <form method="POST">
                <input type="hidden" name="add_video" value="1">
                
                <div class="form-group">
                    <label for="videoUrl" class="form-label">
                        <span translate="no">Video URL *</span>
                    </label>
                    <input 
                        type="url" 
                        id="videoUrl" 
                        name="videoUrl" 
                        required 
                        class="form-control"
                        placeholder="https://www.youtube.com/watch?v=..."
                    >
                </div>
                
                <div class="form-group">
                    <label for="videoName" class="form-label">
                        <span translate="no">Video title *</span>
                    </label>
                    <input 
                        type="text" 
                        id="videoName" 
                        name="videoName" 
                        required 
                        class="form-control"
                        placeholder="Ex: Introduction to Blender"
                    >
                </div>
                
                <div class="form-group">
                    <label for="videoDescription" class="form-label">
                        <span translate="no">Description (optional)</span>
                    </label>
                    <textarea 
                        id="videoDescription" 
                        name="videoDescription" 
                        class="form-control"
                        rows="2"
                        placeholder="Video description..."
                    ></textarea>
                </div>
                
                <small class="form-text">
                    <span translate="no">The video will be added at the end</span>
                </small>
                
                <button type="submit" class="btn btn-success btn-block">
                    <span class="material-icons" translate="no">add</span>
                    <span translate="no">Add</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function previewThumbnail() {
    const val = document.getElementById('playlistThumbnail').value.trim();
    const wrapper = document.getElementById('thumbnailWrapper');
    const preview = document.getElementById('thumbnailPreview');
    const isHex = /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test(val);

    if (!wrapper) return;

    if (isHex) {
        wrapper.style.backgroundColor = val;
        wrapper.style.display = 'flex';
        wrapper.style.alignItems = 'center';
        wrapper.style.justifyContent = 'center';
        if (preview) preview.style.display = 'none';

        if (!wrapper.querySelector('.hex-icon')) {
            const icon = document.createElement('span');
            icon.className = 'material-icons hex-icon';
            icon.style.cssText = 'font-size:48px; color:rgba(255,255,255,0.6);';
            icon.translate = false;
            icon.textContent = 'playlist_play';
            wrapper.appendChild(icon);
        }
    } else if (val) {
        wrapper.style.backgroundColor = '';
        const hexIcon = wrapper.querySelector('.hex-icon');
        if (hexIcon) hexIcon.remove();
        if (preview) {
            preview.style.display = '';
            preview.src = val;
            preview.onerror = function() {
                this.src = 'https://via.placeholder.com/320x180?text=Invalid+URL';
            };
        }
    }
}

const videoList = document.getElementById('videoList');
let draggedElement = null;

if (videoList) {
    const items = videoList.querySelectorAll('.video-list-item');
    
    items.forEach(item => {
        item.addEventListener('dragstart', function(e) {
            draggedElement = this;
            this.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
        });
        
        item.addEventListener('dragend', function(e) {
            this.classList.remove('dragging');
            items.forEach(i => i.classList.remove('drag-over'));
        });
        
        item.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            
            if (this !== draggedElement) {
                this.classList.add('drag-over');
            }
        });
        
        item.addEventListener('dragleave', function(e) {
            this.classList.remove('drag-over');
        });
        
        item.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            
            if (this !== draggedElement) {
                const allItems = [...videoList.querySelectorAll('.video-list-item')];
                const draggedIndex = allItems.indexOf(draggedElement);
                const targetIndex = allItems.indexOf(this);
                
                if (draggedIndex < targetIndex) {
                    this.parentNode.insertBefore(draggedElement, this.nextSibling);
                } else {
                    this.parentNode.insertBefore(draggedElement, this);
                }
                
                updateVideoOrder();
            }
        });
    });
}

function updateVideoOrder() {
    const items = videoList.querySelectorAll('.video-list-item');
    const videoIds = Array.from(items).map(item => item.dataset.videoId);
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.style.display = 'none';
    
    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'reorder_videos';
    actionInput.value = '1';
    form.appendChild(actionInput);
    
    const orderInput = document.createElement('input');
    orderInput.type = 'hidden';
    orderInput.name = 'video_order';
    orderInput.value = JSON.stringify(videoIds);
    form.appendChild(orderInput);
    
    document.body.appendChild(form);
    form.submit();
}
</script>