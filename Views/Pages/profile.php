<div class="profile-page">
    <div class="profile-header">
        <div class="profile-avatar">
            <?php if (isset($_SESSION['discord_avatar']) && !empty($_SESSION['discord_avatar'])): ?>
                <img src="<?= htmlspecialchars($_SESSION['discord_avatar']) ?>" alt="Avatar">
            <?php else: ?>
                <div class="profile-avatar__placeholder">
                    <span class="material-icons" translate="no">person</span>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="profile-info">
            <h1 class="profile-name" translate="no">
                <?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?>
            </h1>
            <span class="profile-role profile-role--<?= $_SESSION['user_role'] ?? 'user' ?>" translate="no">
                <?= ucfirst($_SESSION['user_role'] ?? 'user') ?>
            </span>
        </div>
    </div>
    
    <div class="profile-content">
        <div class="profile-card">
            <h2 class="profile-card__title">
                <span class="material-icons" translate="no">person</span>
                <span translate="no">Account information</span>
            </h2>
            
            <div class="profile-details">
                <div class="profile-detail">
                    <span class="profile-detail__label" translate="no">Username</span>
                    <span class="profile-detail__value" translate="no">
                        <?= htmlspecialchars($_SESSION['user_name'] ?? 'N/A') ?>
                    </span>
                </div>
                
                <div class="profile-detail">
                    <span class="profile-detail__label" translate="no">Role</span>
                    <span class="profile-detail__value" translate="no">
                        <?= htmlspecialchars($_SESSION['user_role'] ?? 'user') ?>
                    </span>
                </div>
                
                <div class="profile-detail">
                    <span class="profile-detail__label" translate="no">User ID</span>
                    <span class="profile-detail__value" translate="no">
                        <?= htmlspecialchars($_SESSION['user_id'] ?? 'N/A') ?>
                    </span>
                </div>
            </div>
        </div>
        
        <?php if (isset($_SESSION['discord_id']) && !empty($_SESSION['discord_id'])): ?>
        <div class="profile-card">
            <h2 class="profile-card__title">
                <svg class="discord-icon" width="24" height="24" viewBox="0 0 127.14 96.36" fill="currentColor">
                    <path d="M107.7,8.07A105.15,105.15,0,0,0,81.47,0a72.06,72.06,0,0,0-3.36,6.83A97.68,97.68,0,0,0,49,6.83,72.37,72.37,0,0,0,45.64,0,105.89,105.89,0,0,0,19.39,8.09C2.79,32.65-1.71,56.6.54,80.21h0A105.73,105.73,0,0,0,32.71,96.36,77.7,77.7,0,0,0,39.6,85.25a68.42,68.42,0,0,1-10.85-5.18c.91-.66,1.8-1.34,2.66-2a75.57,75.57,0,0,0,64.32,0c.87.71,1.76,1.39,2.66,2a68.68,68.68,0,0,1-10.87,5.19,77,77,0,0,0,6.89,11.1A105.25,105.25,0,0,0,126.6,80.22h0C129.24,52.84,122.09,29.11,107.7,8.07ZM42.45,65.69C36.18,65.69,31,60,31,53s5-12.74,11.43-12.74S54,46,53.89,53,48.84,65.69,42.45,65.69Zm42.24,0C78.41,65.69,73.25,60,73.25,53s5-12.74,11.44-12.74S96.23,46,96.12,53,91.08,65.69,84.69,65.69Z"/>
                </svg>
                <span translate="no">Discord Connection</span>
            </h2>
            
            <div class="connection-card">
                <div class="connection-card__icon">
                    <svg width="48" height="48" viewBox="0 0 127.14 96.36" fill="currentColor">
                        <path d="M107.7,8.07A105.15,105.15,0,0,0,81.47,0a72.06,72.06,0,0,0-3.36,6.83A97.68,97.68,0,0,0,49,6.83,72.37,72.37,0,0,0,45.64,0,105.89,105.89,0,0,0,19.39,8.09C2.79,32.65-1.71,56.6.54,80.21h0A105.73,105.73,0,0,0,32.71,96.36,77.7,77.7,0,0,0,39.6,85.25a68.42,68.42,0,0,1-10.85-5.18c.91-.66,1.8-1.34,2.66-2a75.57,75.57,0,0,0,64.32,0c.87.71,1.76,1.39,2.66,2a68.68,68.68,0,0,1-10.87,5.19,77,77,0,0,0,6.89,11.1A105.25,105.25,0,0,0,126.6,80.22h0C129.24,52.84,122.09,29.11,107.7,8.07ZM42.45,65.69C36.18,65.69,31,60,31,53s5-12.74,11.43-12.74S54,46,53.89,53,48.84,65.69,42.45,65.69Zm42.24,0C78.41,65.69,73.25,60,73.25,53s5-12.74,11.44-12.74S96.23,46,96.12,53,91.08,65.69,84.69,65.69Z"/>
                    </svg>
                </div>
                <div class="connection-card__content">
                    <span class="connection-card__label" translate="no">Connected as</span>
                    <span class="connection-card__value" translate="no">
                        <?= htmlspecialchars($_SESSION['discord_username'] ?? 'Discord User') ?>
                    </span>
                    <span class="connection-card__id" translate="no">
                        ID: <?= htmlspecialchars($_SESSION['discord_id']) ?>
                    </span>
                </div>
                <div class="connection-card__badge">
                    <span class="material-icons" translate="no">check_circle</span>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="profile-actions">
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
            <a href="/dashboard" class="btn btn-primary btn-lg">
                <span class="material-icons" translate="no">dashboard</span>
                <span translate="no">Access dashboard</span>
            </a>
            <?php endif; ?>
            
            <a href="/logout" class="btn btn-danger btn-lg">
                <span class="material-icons" translate="no">logout</span>
                <span translate="no">Log out</span>
            </a>
        </div>
    </div>
</div>