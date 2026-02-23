<div class="page-header">
    <h1 class="page-header__title">
        <span class="material-icons" translate="no">add_circle</span>
        <span translate="no">Create a playlist</span>
    </h1>
    <a href="/dashboard" class="btn btn-secondary">
        <span class="material-icons" translate="no">arrow_back</span>
        <span translate="no">Back</span>
    </a>
</div>

<div class="form-container">
    <form method="POST" class="form-card">
        <input type="hidden" name="create_playlist" value="1">
        
        <div class="form-group">
            <label for="title" class="form-label">
                <span translate="no">Playlist title *</span>
            </label>
            <input 
                type="text" 
                id="title" 
                name="title" 
                required 
                class="form-control"
                placeholder="Ex: Blender Basics"
                maxlength="200"
            >
        </div>
        
        <div class="form-group">
            <label for="description" class="form-label">
                <span translate="no">Description</span>
            </label>
            <textarea 
                id="description" 
                name="description" 
                class="form-control"
                rows="5"
                placeholder="Describe the content of this playlist..."
            ></textarea>
        </div>
        
        <div class="form-group">
            <label for="picture" class="form-label">
                <span translate="no">Thumbnail (URL or hex color)</span>
            </label>
            <input 
                type="text" 
                id="picture" 
                name="picture" 
                class="form-control"
                placeholder="#FF5733 or https://example.com/image.jpg"
                onchange="previewThumbnail()"
            >
            <small class="form-text">
                <span translate="no">Leave empty to use the default thumbnail</span>
            </small>
        </div>
        
        <div class="thumbnail-preview" id="thumbnailPreview" style="display: none;">
            <label class="form-label">
                <span translate="no">Preview</span>
            </label>
            <div class="thumbnail-preview__wrapper" id="thumbnailWrapper">
                <img id="thumbnailImage" src="" alt="Preview">
            </div>
        </div>
        
        <div class="form-actions">
            <a href="/dashboard" class="btn btn-outline">
                <span translate="no">Cancel</span>
            </a>
            <button type="submit" class="btn btn-primary">
                <span class="material-icons" translate="no">check</span>
                <span translate="no">Create playlist</span>
            </button>
        </div>
    </form>
</div>

<script>
function previewThumbnail() {
    const val = document.getElementById('picture').value.trim();
    const previewSection = document.getElementById('thumbnailPreview');
    const wrapper = document.getElementById('thumbnailWrapper');
    const img = document.getElementById('thumbnailImage');
    const isHex = /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test(val);

    if (isHex) {
        previewSection.style.display = 'block';
        wrapper.style.backgroundColor = val;
        wrapper.style.minHeight = '120px';
        wrapper.style.display = 'flex';
        wrapper.style.alignItems = 'center';
        wrapper.style.justifyContent = 'center';
        img.style.display = 'none';

        if (!wrapper.querySelector('.hex-icon')) {
            const icon = document.createElement('span');
            icon.className = 'material-icons hex-icon';
            icon.style.cssText = 'font-size:48px; color:rgba(255,255,255,0.6);';
            icon.textContent = 'playlist_play';
            wrapper.appendChild(icon);
        }
    } else if (val) {
        previewSection.style.display = 'block';
        wrapper.style.backgroundColor = '';
        const hexIcon = wrapper.querySelector('.hex-icon');
        if (hexIcon) hexIcon.remove();
        img.style.display = '';
        img.src = val;
        img.onerror = function() {
            previewSection.style.display = 'none';
        };
    } else {
        previewSection.style.display = 'none';
        wrapper.style.backgroundColor = '';
        const hexIcon = wrapper.querySelector('.hex-icon');
        if (hexIcon) hexIcon.remove();
        img.style.display = '';
    }
}
</script>