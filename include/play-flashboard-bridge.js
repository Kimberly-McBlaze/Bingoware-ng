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
    const latestNumber = draws.length > 0 ? draws[draws.length - 1] : null;
    const pattern = extractPatternFromPage();

    return {
      draws: draws,
      latestNumber: latestNumber,
      pattern: pattern
    };
  }

  /**
   * Extract drawn numbers from the page
   */
  function extractDrawsFromPage() {
    const draws = [];
    
    // Try to find draws in the display grid
    const drawElements = document.querySelectorAll('div[style*="background: linear-gradient(135deg, #667eea"]');
    drawElements.forEach(el => {
      const text = el.textContent.trim();
      // Extract number from text like "B12"
      const match = text.match(/[BINGO](\d+)/);
      if (match) {
        draws.push(parseInt(match[1]));
      }
    });

    return draws;
  }

  /**
   * Extract current pattern from the page
   */
  function extractPatternFromPage() {
    // Default pattern info - in a real implementation, this would be extracted from the page
    // For now, we'll check if we can find pattern information in the page
    return 'Check Winning Patterns page';
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
    if (!event.data || !event.data.type) return;

    switch (event.data.type) {
      case 'flashboard_ready':
        console.log('Flashboard ready, sending initial state');
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

    // Intercept form submission
    form.addEventListener('submit', function(e) {
      // Allow form to submit normally, then check for updates
      setTimeout(() => {
        const state = getCurrentState();
        if (state.latestNumber) {
          sendDrawUpdate(state.latestNumber);
        }
      }, 100);
    });

    console.log('Game action monitoring initialized');
  }

  /**
   * Monitor restart button
   */
  function monitorRestartButton() {
    const restartBtn = document.querySelector('button[onClick*="RestartConfirmation"]');
    if (restartBtn) {
      // We'll hook into the restart via a global function override
      const originalRestart = window.RestartConfirmation;
      window.RestartConfirmation = function(...args) {
        if (originalRestart) {
          originalRestart.apply(this, args);
        }
        // Send restart to flashboard after a short delay
        setTimeout(() => {
          sendRestart();
        }, 100);
      };
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
