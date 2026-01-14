/**
 * Bingoware-ng Modern UI JavaScript
 * Handles theme switching, animations, and enhanced UX
 */

// Theme Management
const ThemeManager = {
  THEME_KEY: 'bingoware-theme',
  
  init() {
    this.loadTheme();
    this.attachToggleListener();
  },
  
  loadTheme() {
    const savedTheme = localStorage.getItem(this.THEME_KEY);
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const theme = savedTheme || (prefersDark ? 'dark' : 'light');
    this.setTheme(theme);
  },
  
  setTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem(this.THEME_KEY, theme);
    
    // Update toggle switch if it exists
    const toggle = document.getElementById('theme-toggle');
    if (toggle) {
      toggle.checked = theme === 'dark';
    }
  },
  
  toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    this.setTheme(newTheme);
  },
  
  attachToggleListener() {
    const toggle = document.getElementById('theme-toggle');
    if (toggle) {
      toggle.addEventListener('change', () => this.toggleTheme());
    }
  }
};

// Enhanced Form Validation
const FormValidator = {
  init() {
    this.attachValidationListeners();
  },
  
  attachValidationListeners() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
      const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
      inputs.forEach(input => {
        input.addEventListener('blur', () => this.validateField(input));
        input.addEventListener('input', () => this.clearError(input));
      });
    });
  },
  
  validateField(field) {
    if (!field.value.trim() && field.hasAttribute('required')) {
      this.showError(field, 'This field is required');
      return false;
    }
    return true;
  },
  
  showError(field, message) {
    this.clearError(field);
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.style.color = 'var(--color-error)';
    errorDiv.style.fontSize = '0.875rem';
    errorDiv.style.marginTop = '0.25rem';
    errorDiv.textContent = message;
    field.parentNode.appendChild(errorDiv);
    field.style.borderColor = 'var(--color-error)';
  },
  
  clearError(field) {
    const errorDiv = field.parentNode.querySelector('.field-error');
    if (errorDiv) {
      errorDiv.remove();
    }
    field.style.borderColor = '';
  }
};

// Modern Confirmation Dialogs
const ConfirmDialog = {
  show(message, onConfirm, onCancel) {
    const overlay = document.createElement('div');
    overlay.className = 'modal-overlay';
    overlay.style.cssText = `
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.6);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 10000;
      backdrop-filter: blur(4px);
      animation: fadeIn 0.2s ease;
    `;
    
    const dialog = document.createElement('div');
    dialog.className = 'confirm-dialog';
    dialog.style.cssText = `
      background: var(--bg-secondary);
      padding: 2rem;
      border-radius: 12px;
      box-shadow: var(--shadow-lg);
      max-width: 400px;
      width: 90%;
      animation: slideUp 0.3s ease;
    `;
    
    dialog.innerHTML = `
      <div style="margin-bottom: 1.5rem;">
        <h3 style="color: var(--text-primary); margin-bottom: 0.5rem; font-size: 1.25rem;">Confirm Action</h3>
        <p style="color: var(--text-secondary);">${message}</p>
      </div>
      <div style="display: flex; gap: 1rem; justify-content: flex-end;">
        <button class="btn btn-secondary cancel-btn">Cancel</button>
        <button class="btn btn-primary confirm-btn">Confirm</button>
      </div>
    `;
    
    overlay.appendChild(dialog);
    document.body.appendChild(overlay);
    
    // Add animations
    const style = document.createElement('style');
    style.textContent = `
      @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
      }
      @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
      }
    `;
    document.head.appendChild(style);
    
    const confirmBtn = dialog.querySelector('.confirm-btn');
    const cancelBtn = dialog.querySelector('.cancel-btn');
    
    confirmBtn.addEventListener('click', () => {
      overlay.remove();
      if (onConfirm) onConfirm();
    });
    
    cancelBtn.addEventListener('click', () => {
      overlay.remove();
      if (onCancel) onCancel();
    });
    
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) {
        overlay.remove();
        if (onCancel) onCancel();
      }
    });
  }
};

// Enhanced Restart Confirmation (replaces old function)
function RestartConfirmation(numberinplay) {
  ConfirmDialog.show(
    'Are you sure you want to restart the game? All progress will be lost.',
    () => {
      window.location.href = 'index.php?action=play&numberinplay=' + numberinplay + '&restart=1';
    }
  );
  return false;
}

// Enhanced Config Confirmation (replaces old function)
function ConfigConfirmation() {
  const bag = "0123456789ABCDEFGHIJKLMNOPQRSTVWXYZabcdefghijklmnopqrstuvwxyz-";
  let setID = stripCharsNotInBag(document.configForm.setidform.value, bag);
  
  if (setID === "") setID = "A";
  document.configForm.setidform.value = setID;
  
  // Use synchronous confirm for backward compatibility
  return confirm('Changing the configuration will restart the game. Do you want to proceed?');
}

// Utility function for input validation (from original scripts.js)
function stripCharsNotInBag(s, bag) {
  let returnString = "";
  for (let i = 0; i < s.length; i++) {
    const c = s.charAt(i);
    if (bag.indexOf(c) !== -1) returnString += c;
  }
  return returnString;
}

// Loading Indicator
const LoadingIndicator = {
  show(message = 'Loading...') {
    const existing = document.querySelector('.loading-overlay');
    if (existing) return;
    
    const overlay = document.createElement('div');
    overlay.className = 'loading-overlay';
    overlay.style.cssText = `
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.7);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      z-index: 9999;
      backdrop-filter: blur(4px);
    `;
    
    overlay.innerHTML = `
      <div class="loading-spinner"></div>
      <p style="color: white; margin-top: 1rem; font-size: 1.125rem;">${message}</p>
    `;
    
    document.body.appendChild(overlay);
  },
  
  hide() {
    const overlay = document.querySelector('.loading-overlay');
    if (overlay) {
      overlay.remove();
    }
  }
};

// Toast Notifications
const Toast = {
  show(message, type = 'info', duration = 3000) {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      background: var(--bg-secondary);
      color: var(--text-primary);
      padding: 1rem 1.5rem;
      border-radius: 8px;
      box-shadow: var(--shadow-lg);
      border-left: 4px solid;
      z-index: 10000;
      animation: slideInRight 0.3s ease;
      max-width: 400px;
    `;
    
    const colors = {
      success: 'var(--color-success)',
      warning: 'var(--color-warning)',
      error: 'var(--color-error)',
      info: 'var(--color-info)'
    };
    
    toast.style.borderColor = colors[type] || colors.info;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    const style = document.createElement('style');
    style.textContent = `
      @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
      }
      @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
      }
    `;
    document.head.appendChild(style);
    
    setTimeout(() => {
      toast.style.animation = 'slideOutRight 0.3s ease';
      setTimeout(() => toast.remove(), 300);
    }, duration);
  }
};

// Smooth Scroll
const SmoothScroll = {
  init() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href === '#') return;
        
        e.preventDefault();
        const target = document.querySelector(href);
        if (target) {
          target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
        }
      });
    });
  }
};

// Active Navigation Highlighting
const NavigationHighlighter = {
  init() {
    const currentPage = window.location.href;
    const navLinks = document.querySelectorAll('.nav-button');
    
    navLinks.forEach(link => {
      if (link.href === currentPage) {
        link.classList.add('active');
      }
    });
  }
};

// Initialize everything when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  ThemeManager.init();
  FormValidator.init();
  SmoothScroll.init();
  NavigationHighlighter.init();
  
  // Show a welcome toast on first load
  const hasVisited = localStorage.getItem('bingoware-visited');
  if (!hasVisited) {
    setTimeout(() => {
      Toast.show('Welcome to Bingoware-ng! Try the new dark mode toggle.', 'info', 4000);
      localStorage.setItem('bingoware-visited', 'true');
    }, 500);
  }
  
  // Auto-focus first input on configure page
  if (document.configForm && document.configForm.setidform) {
    document.configForm.setidform.focus();
  }
});

// Export for global use
window.BingowareUI = {
  ThemeManager,
  FormValidator,
  ConfirmDialog,
  LoadingIndicator,
  Toast,
  RestartConfirmation,
  ConfigConfirmation,
  // Legacy namespace for backward compatibility
  Legacy: {
    explain: null  // Will be set below
  }
};

// Legacy function wrappers for backward compatibility
// These ensure older pages that call global functions still work

/**
 * explain() - Context-sensitive help (from scripts.js)
 * Legacy function maintained for backward compatibility
 */
function explain(item) {
  let height = 280;
  let width = 350;
  let msg = '';
  
  if (item === "Set ID") {
    msg = "The set ID is a unique identifier given to your set of cards. " +
      " The set ID will always prefix the card numbers when displayed on screen or printed out.<br><br>"+
      " You can have several sets of cards saved on the computer, which will remain untouched, simply" +
      " by changing the set ID (in config mode) to a different letter or word.  For example, if you generate  "+
      " a set 'A' of 10 cards, the cards will be numbered A0001-A0010.  Once set 'A' is generated, you can change "+
      " the set ID to 'Freddy-' and generate a new set of 20 cards (numbered Freddy-0001 to Freddy-0020).  The "+
      " original set remains untouched.<br><br> This feature is very useful if you want to personalize several sets " +
      " of cards.  It also allows you to reload a previously generated set of cards.<br><br>"+
      " Only alphanumerical characters, or hyphens, will be retained for your set ID. Leaving the field blank will return to " +
      " the default letter A.";
    height = 540;
  } else if (item === "Winning Pattern") {
    msg = "The winning pattern tells Bingoware-ng what you want the winning cards to look like.<br><br> " +
      " Bingoware-ng lets you choose from 11 different styles of winning patterns, and lets you customize "+
      " 10 of them.  Of course, we have given you the normal winning pattern which most people will use.<br><br> " +
      " In the normal winning pattern, any row, column or diagonal wins! " +
      " The names given to the other winning patterns don't actually mean much, since you can customize " +
      " any of them the way you like.<br><br>"+
      " To customize a winning pattern, simply click on the customize link beside the winning pattern you " +
      " want to change, a window will pop up and let you color which squares you want the winning card to have. <br><br>" + 
      " Have fun! Make your Bingo special!";
    height = 530;
  } else if (item === "Draw Mode") {
    msg = "Most users will use the automatic draw mode, which means that Bingoware-ng will " +
      " draw the numbers for you.<br><br>"+
      " However, some users may already have a random number generating mechanism they would like" +
      " to keep using, such as a barrel with numbered balls.  Bingoware-ng will let you enter the"+
      " numbers that were drawn and still perform the card validation for you. <br><br>"+
      " The manual mode will ask you the numbers instead of giving you the numbers.  Note that"+
      " Bingoware-ng thoroughly checks the number you enter so that no mistakes are made!";
    height = 430;
  } else if (item === "Cards in play") {
    msg = "In game mode, the software will open the current set of cards (as indicated" +
      " by the setid variable in the config mode).  If you do not distribute all the cards you generated," +
      " because for instance you did not get the crowd you expected or are charging to much for you cards," +
      " then you can tell Bingoware-ng not to consider all the cards.  <br><br>The trick is to issue out your cards in"+ 
      " sequential order, and enter the number of the last card given away in the box.  You can"+
      " always change the number throughout the game if you gain or lose some people.  Bingoware-ng will simply" +
      " not announce winning cards numbers that are still in your hands.";
    height = 450;
  } else if (item === "Hint") {
    msg = "You can easily add a link to close the window in the header or footer, by inserting this line here: <br>" +
      " <pre>&lt;br&gt;&lt;p align=&quot;center&quot;&gt;&lt;a href=&quot;javascript:window.close();&quot;&gt;close window&lt;/a&gt;&lt;/p&gt;</pre>";
    height = 250;
    width = 600;
  } else if (item === "Free Squares") {
    msg = "Bingoware-ng gives you some flexibility when generating your set of cards." +
      " You can choose to have a free square in the center of every card (will not help for winning" +
      " patterns such as the perimeter of the Bingo card), no Free squares at all (slightly longer games)" +
      " or a randomly placed free square on all cards (all cards are different).";
    height = 300;
  } else if (item === "Name File") {
    msg = "Bingoware-ng allows you to customize each card by writing the name of" +
      " a person on each card.  Simply place a list of names in the file called" +
      " '<b>names.txt</b>' in the '<b>config</b>' folder, without any blank lines at the end," +
      " using Notepad, and check this box, Bingoware-ng will print a different name" +
      " at the bottom each card.";
  } else if (item === "Print Rules") {
    msg = "If your printer can print double-sided, this option will allow you to print" +
      " the rules of the game at the back of each card!  You can of course customize the rules" +
      " file (file called '<b>rules.html</b>' in the <b>'config'</b> folder) to suit your needs";
  } else if (item === "Four per page") {
    msg = "When printing the cards, you have the otion of printing only one card per page or printing" +
      " four cards per page.  The rest of the program is unaffected.  The only difference is seen" +
      " when printing the cards, that is when choosing  <b>show all</b>.";
  } else if (item === "Colours") {
    msg = "You now have the option of changing the colours of the cards from this configuration"+
      " screen.  The header colours are the colours used to show the B.I.N.G.O. letters on the top" +
      " top of the cards.  The selected and non-selected colours are the colours used to display the" +
      " numbers that have already been drawn and the free squares, or the numbers that have not been"+
      " drawn, respectively.  Use the colour chooser to find a colour that you like.";
    height = 320;
  } else if (item === "Border colour") {
    msg = "The table border colour, is not supported by the Opera browser, up to and including Opera 7.21."+
      " The table border will always be black when viewed with Opera";
    height = 230;
  }
  
  const newwin = window.open('', '', `top=30,left=70,width=${width},height=${height}`);
  if (!newwin.opener) newwin.opener = self;
  
  newwin.document.open();
  newwin.document.write('<html>');
  newwin.document.write(`<head><title>Help on ${item}</title></head>`);
  newwin.document.write(`<body><h1>${item}:</h1><br>${msg}<br>`);
  newwin.document.write('<br><p align="center"><a href="javascript:close()">close</a>');
  newwin.document.write('</body></html>');
  newwin.document.close();
}

// Make explain available in Legacy namespace
window.BingowareUI.Legacy.explain = explain;

/**
 * validate_number() - Validate manual number entry
 * Legacy function from scripts.js
 */
function validate_number(maxColumnNum) {
  const digits = "0123456789";
  const letters = document.random.letters.value;
  let temp, number, minlim, maxlim;

  if (document.random.enterednumber.value === "" || document.random.enterednumber.value.length > 3) {
    alert("Please enter the drawn number in the form N45\nYou can choose one of the following letters: " + letters);
    return false;
  }

  for (let i = 1; i < document.random.enterednumber.value.length; i++) {
    temp = document.random.enterednumber.value.substring(i, i + 1);
    if (digits.indexOf(temp) === -1) {
      alert("Please enter the drawn number in the form N45\nYou can choose one of the following letters: " + letters);
      return false;
    }
  }

  if (letters.indexOf(document.random.enterednumber.value.substring(0, 1).toUpperCase()) === -1) {
    alert("Please enter the drawn number in the form N45\nYou can choose one of the following letters: " + letters);
    return false;
  }

  number = document.random.enterednumber.value.substring(1, document.random.enterednumber.value.length);
  minlim = letters.indexOf(document.random.enterednumber.value.substring(0, 1).toUpperCase()) * maxColumnNum + 1;
  maxlim = (letters.indexOf(document.random.enterednumber.value.substring(0, 1).toUpperCase()) + 1) * maxColumnNum;

  if ((number < minlim) || (number > maxlim)) {
    alert("The range allowed for the letter " + document.random.enterednumber.value.substring(0, 1).toUpperCase() + " is between " + minlim + " and " + maxlim);
    return false;
  }

  return true;
}

// Make validate_number available globally
window.BingowareUI.Legacy.validate_number = validate_number;

