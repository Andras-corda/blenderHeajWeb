console.log('=== DEBUG VIDEOPLAYER ===');
console.log('1. Preference: NO AUTO PLAYING');
console.log('2. Playlist:', playlists);
console.log('3. Video number', playlists.count());
console.log('4. HTML element:', document.documentElement);

let playlists = [];
let currentPlaylistIndex = 0;
let currentVideoIndex = null;

// Charger les playlists depuis 
async function loadPlaylists() {
    try {
        const response = await fetch('./jsonDB/videos.json');
        if (!response.ok) {
            throw new Error('Impossible de charger les playlists');
        }
        const data = await response.json();
        playlists = data.playlists;
        
        createPlaylistSelector();
        loadPlaylist(0);
    } catch (error) {
        console.error('Erreur lors du chargement des playlists:', error);
        document.getElementById('currentTitle').textContent = 'Erreur de chargement';
        document.getElementById('currentDesc').textContent = 'Impossible de charger les playlists. Vérifiez que le fichier videos.json existe.';
    }
}

// Créer le sélecteur de playlists
function createPlaylistSelector() {
    const sidebar = document.querySelector('.sidebar');
    
    const playlistTabs = document.createElement('div');
    playlistTabs.className = 'playlist-tabs';
    playlistTabs.id = 'playlistTabs';
    
    playlists.forEach((playlist, index) => {
        const tab = document.createElement('button');
        tab.className = 'playlist-tab';
        tab.textContent = playlist.name;
        tab.onclick = () => loadPlaylist(index);
        playlistTabs.appendChild(tab);
    });
    
    sidebar.insertBefore(playlistTabs, sidebar.firstChild);
}

// Charger une playlist spécifique
function loadPlaylist(playlistIndex) {
    currentPlaylistIndex = playlistIndex;
    const playlist = playlists[playlistIndex];
    
    document.querySelectorAll('.playlist-tab').forEach((tab, index) => {
        tab.classList.toggle('active', index === playlistIndex);
    });
    
    document.getElementById('playlistTitle').textContent = playlist.name;
    
    createVideoList(playlist.videos);
    
    if (playlist.videos.length > 0) {
        loadVideo(0);
    }
}

// Créer la liste des vidéos d'une playlist
function createVideoList(videos) {
    const list = document.getElementById('videoList');
    list.innerHTML = '';
    
    videos.forEach((video, index) => {
        const item = document.createElement('div');
        item.className = 'video-item';
        item.onclick = () => loadVideo(index);
        
        item.innerHTML = `
            <img src="${video.thumbnail}" alt="${video.title}" class="video-thumbnail">
            <div class="video-item-title">${video.title}</div>
            <div class="video-item-desc">${video.description}</div>
        `;
        
        list.appendChild(item);
    });
}

// Charger une vidéo spécifique
function loadVideo(videoIndex) {
    const playlist = playlists[currentPlaylistIndex];
    const video = playlist.videos[videoIndex];
    const videoPlayer = document.getElementById('videoPlayer');
    const currentTitle = document.getElementById('currentTitle');
    const currentDesc = document.getElementById('currentDesc');
    
    // Mise à jour du lecteur
    videoPlayer.src = `https://www.youtube.com/embed/${video.id}?autoplay=1&rel=0`;
    
    currentTitle.textContent = video.title;
    currentDesc.textContent = video.description;
    
    document.querySelectorAll('.video-item').forEach((item, i) => {
        item.classList.toggle('active', i === videoIndex);
    });
    
    currentVideoIndex = videoIndex;
}

// Initialisation au chargement de la page
window.addEventListener('DOMContentLoaded', loadPlaylists);