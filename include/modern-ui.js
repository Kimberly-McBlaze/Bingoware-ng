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
  ConfigConfirmation
};
