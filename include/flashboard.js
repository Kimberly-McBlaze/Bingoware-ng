/**
 * Bingo Flashboard JavaScript
 * Handles display logic and communication with the main play window
 */

(function() {
  'use strict';

  // State management
  let state = {
    draws: [],
    latestNumber: null,
    currentPattern: 'No pattern selected',
    maxNumber: 75
  };

  /**
   * Initialize the flashboard
   */
  function init() {
    console.log('Flashboard initializing...');
    buildBoard();
    setupMessageListener();
    updateDisplay();
    
    // Request initial state from parent
    if (window.opener && !window.opener.closed) {
      window.opener.postMessage({ type: 'flashboard_ready' }, '*');
    }
  }

  /**
   * Build the bingo board HTML
   */
  function buildBoard() {
    const boardContainer = document.getElementById('bingo-board');
    if (!boardContainer) {
      console.error('Board container not found');
      return;
    }

    const letters = ['B', 'I', 'N', 'G', 'O'];
    const numbersPerLetter = 15;

    letters.forEach((letter, index) => {
      const row = document.createElement('div');
      row.className = 'board-row';

      // Letter label
      const label = document.createElement('div');
      label.className = 'letter-label';
      label.textContent = letter;
      row.appendChild(label);

      // Number cells
      const startNum = (index * numbersPerLetter) + 1;
      for (let i = 0; i < numbersPerLetter; i++) {
        const num = startNum + i;
        const cell = document.createElement('div');
        cell.className = 'number-cell';
        cell.dataset.number = num;
        cell.textContent = num;
        row.appendChild(cell);
      }

      boardContainer.appendChild(row);
    });

    console.log('Board built successfully');
  }

  /**
   * Setup message listener for communication with parent window
   */
  function setupMessageListener() {
    window.addEventListener('message', (event) => {
      // Security: In production, you might want to check event.origin
      if (!event.data || !event.data.type) return;

      console.log('Flashboard received message:', event.data);

      switch (event.data.type) {
        case 'draw_update':
          handleDrawUpdate(event.data);
          break;
        case 'pattern_update':
          handlePatternUpdate(event.data);
          break;
        case 'restart':
          handleRestart();
          break;
        case 'initial_state':
          handleInitialState(event.data);
          break;
      }
    });

    console.log('Message listener setup complete');
  }

  /**
   * Handle draw update from parent window
   */
  function handleDrawUpdate(data) {
    if (data.draws) {
      state.draws = data.draws;
    }
    if (data.latestNumber !== undefined) {
      state.latestNumber = data.latestNumber;
    }
    updateDisplay();
  }

  /**
   * Handle pattern update from parent window
   */
  function handlePatternUpdate(data) {
    if (data.pattern) {
      state.currentPattern = data.pattern;
    }
    updateDisplay();
  }

  /**
   * Handle restart event
   */
  function handleRestart() {
    state.draws = [];
    state.latestNumber = null;
    updateDisplay();
  }

  /**
   * Handle initial state from parent window
   */
  function handleInitialState(data) {
    if (data.draws) {
      state.draws = data.draws;
    }
    if (data.latestNumber !== undefined) {
      state.latestNumber = data.latestNumber;
    }
    if (data.pattern) {
      state.currentPattern = data.pattern;
    }
    updateDisplay();
  }

  /**
   * Update the display based on current state
   */
  function updateDisplay() {
    updateCurrentNumber();
    updatePattern();
    updateBoard();
  }

  /**
   * Update the current number display
   */
  function updateCurrentNumber() {
    const element = document.getElementById('current-number');
    if (!element) return;

    if (state.latestNumber !== null) {
      const letter = getLetterForNumber(state.latestNumber);
      element.textContent = letter + state.latestNumber;
    } else {
      element.textContent = '---';
    }
  }

  /**
   * Update the pattern display
   */
  function updatePattern() {
    const element = document.getElementById('current-pattern');
    if (!element) return;
    element.textContent = state.currentPattern;
  }

  /**
   * Update the board cells based on drawn numbers
   */
  function updateBoard() {
    // Reset all cells
    const cells = document.querySelectorAll('.number-cell');
    cells.forEach(cell => {
      cell.classList.remove('called', 'latest');
    });

    // Mark called numbers
    state.draws.forEach(num => {
      const cell = document.querySelector(`[data-number="${num}"]`);
      if (cell) {
        cell.classList.add('called');
      }
    });

    // Mark latest number with blinking
    if (state.latestNumber !== null) {
      const latestCell = document.querySelector(`[data-number="${state.latestNumber}"]`);
      if (latestCell) {
        latestCell.classList.add('latest');
      }
    }
  }

  /**
   * Get the letter for a given number
   */
  function getLetterForNumber(num) {
    if (num >= 1 && num <= 15) return 'B';
    if (num >= 16 && num <= 30) return 'I';
    if (num >= 31 && num <= 45) return 'N';
    if (num >= 46 && num <= 60) return 'G';
    if (num >= 61 && num <= 75) return 'O';
    return '?';
  }

  /**
   * Handle window close - notify parent
   */
  window.addEventListener('beforeunload', () => {
    if (window.opener && !window.opener.closed) {
      window.opener.postMessage({ type: 'flashboard_closed' }, '*');
    }
  });

  // Initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
