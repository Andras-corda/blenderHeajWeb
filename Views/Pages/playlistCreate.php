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
                <span translate="no">Thumbnail URL</span>
            </label>
            <input 
                type="url" 
                id="picture" 
                name="picture" 
                class="form-control"
                placeholder="https://example.com/image.jpg"
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
            <div class="thumbnail-preview__wrapper">
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
    const url = document.getElementById('picture').value;
    const preview = document.getElementById('thumbnailPreview');
    const img = document.getElementById('thumbnailImage');
    
    if (url) {
        img.src = url;
        preview.style.display = 'block';
        
        img.onerror = function() {
            preview.style.display = 'none';
        };
    } else {
        preview.style.display = 'none';
    }
}
</script>