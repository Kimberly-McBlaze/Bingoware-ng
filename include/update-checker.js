/**
 * Update Checker for Bingoware-ng
 * Checks for new versions and displays a non-intrusive notification
 */

(function() {
    'use strict';
    
    const UPDATE_CHECK_KEY = 'bingoware_update_check';
    const UPDATE_DISMISSED_KEY = 'bingoware_update_dismissed';
    const CHECK_INTERVAL = 24 * 60 * 60 * 1000; // 24 hours in milliseconds
    const CURRENT_VERSION = 'v2.6.4';
    const VERSION_URL = 'version.json';
    const REPO_URL = 'https://github.com/Kimberly-McBlaze/Bingoware-ng';
    
    /**
     * Compare version strings (semantic versioning)
     * Returns true if newVersion is greater than currentVersion
     */
    function isNewerVersion(currentVersion, newVersion) {
        // Remove 'v' prefix if present
        const current = currentVersion.replace(/^v/, '').split('.').map(Number);
        const latest = newVersion.replace(/^v/, '').split('.').map(Number);
        
        for (let i = 0; i < Math.max(current.length, latest.length); i++) {
            const currPart = current[i] || 0;
            const latestPart = latest[i] || 0;
            
            if (latestPart > currPart) return true;
            if (latestPart < currPart) return false;
        }
        
        return false;
    }
    
    /**
     * Check if we should run the update check
     */
    function shouldCheckForUpdates() {
        try {
            const lastCheck = localStorage.getItem(UPDATE_CHECK_KEY);
            const dismissedVersion = localStorage.getItem(UPDATE_DISMISSED_KEY);
            
            // If user dismissed this version, don't check again
            if (dismissedVersion === CURRENT_VERSION) {
                return false;
            }
            
            // Check if enough time has passed since last check
            if (lastCheck) {
                const lastCheckTime = parseInt(lastCheck, 10);
                const now = Date.now();
                
                if (now - lastCheckTime < CHECK_INTERVAL) {
                    return false;
                }
            }
            
            return true;
        } catch (e) {
            // localStorage might not be available
            return true;
        }
    }
    
    /**
     * Save the last check timestamp
     */
    function saveCheckTimestamp() {
        try {
            localStorage.setItem(UPDATE_CHECK_KEY, Date.now().toString());
        } catch (e) {
            // Ignore localStorage errors
        }
    }
    
    /**
     * Mark current version as dismissed
     */
    function dismissUpdate() {
        try {
            localStorage.setItem(UPDATE_DISMISSED_KEY, CURRENT_VERSION);
        } catch (e) {
            // Ignore localStorage errors
        }
    }
    
    /**
     * Display update notification
     */
    function showUpdateNotification(newVersion, releaseUrl) {
        // Create notification container
        const notification = document.createElement('div');
        notification.className = 'update-notification';
        notification.innerHTML = `
            <div class="update-content">
                <div class="update-icon">🎉</div>
                <div class="update-message">
                    <strong>Update Available!</strong>
                    <p>Bingoware-ng <strong>${newVersion}</strong> is now available. You're running ${CURRENT_VERSION}.</p>
                </div>
                <div class="update-actions">
                    <a href="${releaseUrl}" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-sm">
                        🔗 View Release
                    </a>
                    <button class="btn btn-secondary btn-sm" id="dismissUpdate">
                        ✕ Dismiss
                    </button>
                </div>
            </div>
        `;
        
        // Add styles
        const style = document.createElement('style');
        style.textContent = `
            .update-notification {
                position: fixed;
                top: 20px;
                right: 20px;
                max-width: 450px;
                background: var(--bg-secondary, #ffffff);
                border: 2px solid var(--accent-color, #4CAF50);
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                z-index: 10000;
                animation: slideIn 0.3s ease-out;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            }
            
            @keyframes slideIn {
                from {
                    transform: translateX(500px);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            .update-content {
                padding: 16px;
                display: flex;
                flex-direction: column;
                gap: 12px;
            }
            
            .update-icon {
                font-size: 32px;
                text-align: center;
            }
            
            .update-message {
                color: var(--text-primary, #333);
            }
            
            .update-message strong {
                color: var(--accent-color, #4CAF50);
            }
            
            .update-message p {
                margin: 8px 0 0 0;
                font-size: 14px;
                line-height: 1.5;
            }
            
            .update-actions {
                display: flex;
                gap: 8px;
                justify-content: flex-end;
            }
            
            .update-actions .btn {
                padding: 6px 12px;
                border-radius: 4px;
                text-decoration: none;
                font-size: 13px;
                cursor: pointer;
                border: none;
                display: inline-flex;
                align-items: center;
                gap: 4px;
                transition: all 0.2s;
            }
            
            .update-actions .btn-primary {
                background: var(--accent-color, #4CAF50);
                color: white;
            }
            
            .update-actions .btn-primary:hover {
                background: var(--accent-hover, #45a049);
            }
            
            .update-actions .btn-secondary {
                background: var(--bg-tertiary, #f0f0f0);
                color: var(--text-primary, #333);
            }
            
            .update-actions .btn-secondary:hover {
                background: var(--border-color, #ddd);
            }
            
            @media (max-width: 768px) {
                .update-notification {
                    top: 10px;
                    right: 10px;
                    left: 10px;
                    max-width: none;
                }
                
                .update-actions {
                    flex-direction: column;
                }
                
                .update-actions .btn {
                    width: 100%;
                    justify-content: center;
                }
            }
        `;
        
        document.head.appendChild(style);
        document.body.appendChild(notification);
        
        // Handle dismiss button
        const dismissBtn = notification.querySelector('#dismissUpdate');
        dismissBtn.addEventListener('click', function() {
            notification.style.animation = 'slideIn 0.3s ease-out reverse';
            setTimeout(function() {
                notification.remove();
            }, 300);
            dismissUpdate();
        });
    }
    
    /**
     * Check for updates
     */
    function checkForUpdates() {
        if (!shouldCheckForUpdates()) {
            return;
        }
        
        // Save timestamp
        saveCheckTimestamp();
        
        // Fetch version file with cache busting
        const url = VERSION_URL + '?t=' + Date.now();
        
        fetch(url)
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(function(data) {
                const latestVersion = data.version;
                
                if (isNewerVersion(CURRENT_VERSION, latestVersion)) {
                    const releaseUrl = data.releaseUrl || REPO_URL;
                    showUpdateNotification('v' + latestVersion, releaseUrl);
                }
            })
            .catch(function(error) {
                // Silently fail - don't bother the user with network errors
                console.debug('Update check failed:', error);
            });
    }
    
    // Run check when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', checkForUpdates);
    } else {
        checkForUpdates();
    }
})();
