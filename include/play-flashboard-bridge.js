/**
 * Play Page - Flashboard Bridge
 * Handles communication between the play page and the flashboard window
 */

(function() {
  'use strict';

  let flashboardWindow = null;

  /**
   * Open the flashboard window
   */
  window.openFlashboard = function() {
    // Check if window is already open
    if (flashboardWindow && !flashboardWindow.closed) {
      flashboardWindow.focus();
      return;
    }

    // Open new window
    const width = 1400;
    const height = 900;
    const left = window.screen.width - width - 50;
    const top = 50;
    
    flashboardWindow = window.open(
      'flashboard.php',
      'bingoFlashboard',
      `width=${width},height=${height},left=${left},top=${top},resizable=yes,scrollbars=yes`
    );

    if (!flashboardWindow) {
      alert('Popup blocked! Please allow popups for this site to use the flashboard feature.');
      return;
    }

    console.log('Flashboard window opened');
    
    // Send initial state once window is ready
    setTimeout(() => {
      sendInitialState();
    }, 500);
  };

  /**
   * Send initial state to flashboard
   */
  function sendInitialState() {
    if (!flashboardWindow || flashboardWindow.closed) return;

    const state = getCurrentState();
    flashboardWindow.postMessage({
      type: 'initial_state',
      ...state
    }, '*');
    
    console.log('Initial state sent to flashboard:', state);
  }

  /**
   * Get current game state from the page
   */
  function getCurrentState() {
    const draws = extractDrawsFromPage();
    const latestNumber = extractLatestNumberFromPage();
    const pattern = extractPatternFromPage();

    return {
      draws: draws,
      latestNumber: latestNumber,
      pattern: pattern
    };
  }

  /**
   * Extract drawn numbers from the page
   * Reads from stable data-draws attribute instead of scraping DOM styles
   */
  function extractDrawsFromPage() {
    // Read draws from data attribute - stable source of truth
    const gameStateData = document.getElementById('game-state-data');
    if (gameStateData) {
      const drawsAttr = gameStateData.getAttribute('data-draws');
      if (drawsAttr) {
        try {
          const draws = JSON.parse(drawsAttr);
          if (Array.isArray(draws)) {
            // Ensure all values are integers
            return draws.map(num => parseInt(num, 10)).filter(num => !isNaN(num));
          }
        } catch (e) {
          console.error('Error parsing draws:', e);
        }
      }
    }
    
    // Fallback: return empty array if no data found
    return [];
  }

  /**
   * Extract the latest drawn number from the page
   * Reads from stable data-latest attribute
   */
  function extractLatestNumberFromPage() {
    const gameStateData = document.getElementById('game-state-data');
    if (gameStateData) {
      const latestAttr = gameStateData.getAttribute('data-latest');
      if (latestAttr) {
        try {
          const latest = JSON.parse(latestAttr);
          if (latest !== null && latest !== undefined) {
            return parseInt(latest, 10);
          }
        } catch (e) {
          console.error('Error parsing latest number:', e);
        }
      }
    }
    
    // Fallback: return null if no data found
    return null;
  }

  /**
   * Extract current pattern from the page
   */
  function extractPatternFromPage() {
    // Read pattern information from data attribute
    const gameStateData = document.getElementById('game-state-data');
    if (gameStateData) {
      const patternsAttr = gameStateData.getAttribute('data-patterns');
      if (patternsAttr) {
        try {
          const patterns = JSON.parse(patternsAttr);
          if (Array.isArray(patterns)) {
            if (patterns.length === 1) {
              // Exactly one pattern selected - show its name
              return patterns[0];
            } else if (patterns.length > 1) {
              // Multiple patterns - show count
              return `${patterns.length} patterns selected`;
            }
          }
        } catch (e) {
          console.error('Error parsing patterns:', e);
        }
      }
    }
    // No patterns enabled
    return 'No pattern selected';
  }

  /**
   * Send draw update to flashboard
   */
  function sendDrawUpdate(number) {
    if (!flashboardWindow || flashboardWindow.closed) return;

    const state = getCurrentState();
    flashboardWindow.postMessage({
      type: 'draw_update',
      draws: state.draws,
      latestNumber: number
    }, '*');

    console.log('Draw update sent to flashboard:', number);
  }

  /**
   * Send pattern update to flashboard
   */
  function sendPatternUpdate(pattern) {
    if (!flashboardWindow || flashboardWindow.closed) return;

    flashboardWindow.postMessage({
      type: 'pattern_update',
      pattern: pattern
    }, '*');

    console.log('Pattern update sent to flashboard:', pattern);
  }

  /**
   * Send restart event to flashboard
   */
  function sendRestart() {
    if (!flashboardWindow || flashboardWindow.closed) return;

    flashboardWindow.postMessage({
      type: 'restart'
    }, '*');

    console.log('Restart sent to flashboard');
  }

  /**
   * Listen for messages from flashboard
   */
  window.addEventListener('message', (event) => {
    // Security: Validate origin
    const allowedOrigins = [
      window.location.origin,
      'http://localhost:8000',
      'http://localhost:8080',
      'http://127.0.0.1:8000'
    ];
    
    if (!allowedOrigins.includes(event.origin)) {
      return;
    }

    if (!event.data || !event.data.type) return;

    switch (event.data.type) {
      case 'flashboard_ready':
        console.log('Flashboard ready, sending initial state');
        // Re-establish flashboard window reference after page reload
        if (event.source && event.source !== window) {
          flashboardWindow = event.source;
        }
        sendInitialState();
        break;
      case 'flashboard_closed':
        console.log('Flashboard closed');
        flashboardWindow = null;
        break;
    }
  });

  /**
   * Monitor form submissions to detect number draws
   */
  function monitorGameActions() {
    const form = document.querySelector('form[name="random"]');
    if (!form) return;

    // On page load, try to find existing flashboard window and send current state
    // After a form submission that reloads the page, flashboardWindow is lost
    // but we can try to re-establish connection by checking for the named window
    checkAndUpdateFlashboard();

    console.log('Game action monitoring initialized');
  }

  /**
   * Check if flashboard is open and send current state
   */
  function checkAndUpdateFlashboard() {
    // Try to get reference to the flashboard window by name
    // This only works if the flashboard was opened by this window
    // After page reload, we lose the reference, so we rely on the flashboard
    // sending us a 'flashboard_ready' message to re-establish the connection
    
    // If we have a reference to the flashboard, send current state
    if (flashboardWindow && !flashboardWindow.closed) {
      setTimeout(() => {
        sendInitialState();
      }, 100);
    }
  }

  /**
   * Monitor restart button
   */
  function monitorRestartButton() {
    // Monitor for restart parameter in URL which indicates a game restart
    const checkRestart = () => {
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.has('restart')) {
        sendRestart();
      }
    };
    
    // Check on page load
    checkRestart();
    
    // Also add click listener to restart button to detect restarts
    const restartBtn = document.querySelector('button[name="restart"]');
    if (restartBtn) {
      restartBtn.addEventListener('click', function() {
        // Send restart to flashboard after user confirms and page reloads
        // The actual restart happens via navigation, so we'll detect it on next load
        setTimeout(() => {
          sendRestart();
        }, 500);
      });
    }
  }

  /**
   * Initialize the bridge
   */
  function init() {
    console.log('Flashboard bridge initializing...');
    monitorGameActions();
    monitorRestartButton();
  }

  // Initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  // Expose functions globally for external access
  window.FlashboardBridge = {
    open: window.openFlashboard,
    sendDrawUpdate: sendDrawUpdate,
    sendPatternUpdate: sendPatternUpdate,
    sendRestart: sendRestart,
    isOpen: function() {
      return flashboardWindow && !flashboardWindow.closed;
    }
  };

})();
