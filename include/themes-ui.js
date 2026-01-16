/**
 * Themes UI JavaScript
 * Handles theme management, color picker sync, and CRUD operations
 */

(function() {
  'use strict';

  let themes = [];

  /**
   * Load all themes
   */
  async function loadThemes() {
    try {
      const response = await fetch('api/themes.php');
      const data = await response.json();
      
      if (data.success) {
        themes = data.themes;
        renderThemes();
      } else {
        alert('Error loading themes: ' + data.error);
      }
    } catch (error) {
      alert('Error: ' + error.message);
    }
  }

  /**
   * Render themes to the page
   */
  function renderThemes() {
    const container = document.getElementById('themeList');
    container.innerHTML = '';
    
    themes.forEach(theme => {
      const card = document.createElement('div');
      card.className = 'theme-card' + (theme.is_active ? ' active' : '');
      card.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
          <div>
            <strong>${escapeHtml(theme.name)}</strong>
            ${theme.is_default ? '<span class="badge badge-default" style="margin-left: 0.5rem;">DEFAULT</span>' : ''}
            ${theme.is_active ? '<span class="badge badge-success" style="margin-left: 0.5rem;">ACTIVE</span>' : ''}
          </div>
        </div>
        ${theme.description ? `<p style="color: var(--text-secondary); font-size: 0.875rem; margin: 0.5rem 0;">${escapeHtml(theme.description)}</p>` : ''}
        <div class="theme-colors">
          <div class="color-swatch" style="background-color: ${theme.colors.primary};" title="Primary"></div>
          <div class="color-swatch" style="background-color: ${theme.colors.secondary};" title="Secondary"></div>
          <div class="color-swatch" style="background-color: ${theme.colors.success};" title="Success"></div>
          <div class="color-swatch" style="background-color: ${theme.colors.warning};" title="Warning"></div>
          <div class="color-swatch" style="background-color: ${theme.colors['bg-primary']};" title="BG Primary"></div>
          <div class="color-swatch" style="background-color: ${theme.colors['bg-secondary']};" title="BG Secondary"></div>
          <div class="color-swatch" style="background-color: ${theme.colors['text-primary']};" title="Text Primary"></div>
          <div class="color-swatch" style="background-color: ${theme.colors['border-color']};" title="Border"></div>
        </div>
        <div class="theme-actions">
          ${!theme.is_active ? `<button class="btn btn-sm btn-primary" onclick="activateTheme('${theme.id}')">✓ Activate</button>` : ''}
          ${!theme.is_default ? `<button class="btn btn-sm btn-secondary" onclick="openEditModal('${theme.id}')">✏️ Edit</button>` : ''}
          ${!theme.is_default ? `<button class="btn btn-sm btn-error" onclick="deleteTheme('${theme.id}', '${escapeHtml(theme.name)}')">🗑️ Delete</button>` : ''}
        </div>
      `;
      container.appendChild(card);
    });
  }

  /**
   * Escape HTML to prevent XSS
   */
  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  /**
   * Activate a theme
   */
  window.activateTheme = async function(themeId) {
    try {
      const formData = new FormData();
      formData.set('activate', themeId);
      
      const response = await fetch('api/themes.php', {
        method: 'POST',
        body: formData
      });
      
      const data = await response.json();
      
      if (data.success) {
        // Apply theme immediately
        applyTheme(data.theme);
        alert('Theme activated successfully!');
        loadThemes();
      } else {
        alert('Error: ' + data.error);
      }
    } catch (error) {
      alert('Error: ' + error.message);
    }
  };

  /**
   * Apply theme to the page
   */
  function applyTheme(theme) {
    const root = document.documentElement;
    
    for (const [key, value] of Object.entries(theme.colors)) {
      root.style.setProperty('--color-' + key, value);
      // Also set without prefix for compatibility
      if (!key.startsWith('color-')) {
        root.style.setProperty('--' + key, value);
      }
    }
  }

  /**
   * Open create theme modal
   */
  window.openCreateModal = function() {
    document.getElementById('modalTitle').textContent = 'Create Custom Theme';
    document.getElementById('themeForm').reset();
    document.getElementById('themeId').value = '';
    
    // Set default colors
    const defaultColors = {
      'primary': '#667eea',
      'secondary': '#764ba2',
      'success': '#10b981',
      'warning': '#f59e0b',
      'error': '#ef4444',
      'bg-primary': '#ffffff',
      'bg-secondary': '#f3f4f6',
      'bg-tertiary': '#e5e7eb',
      'text-primary': '#1f2937',
      'text-secondary': '#6b7280',
      'text-muted': '#9ca3af',
      'border-color': '#d1d5db'
    };
    
    setFormColors(defaultColors);
    setupColorPickers();
    
    document.getElementById('themeModal').classList.add('active');
  };

  /**
   * Open edit theme modal
   */
  window.openEditModal = async function(themeId) {
    try {
      const response = await fetch(`api/themes.php?id=${themeId}`);
      const data = await response.json();
      
      if (data.success) {
        const theme = data.theme;
        document.getElementById('modalTitle').textContent = 'Edit Theme';
        document.getElementById('themeId').value = theme.id;
        document.getElementById('themeName').value = theme.name;
        document.getElementById('themeDescription').value = theme.description || '';
        
        setFormColors(theme.colors);
        setupColorPickers();
        
        document.getElementById('themeModal').classList.add('active');
      } else {
        alert('Error loading theme: ' + data.error);
      }
    } catch (error) {
      alert('Error: ' + error.message);
    }
  };

  /**
   * Set colors in the form
   */
  function setFormColors(colors) {
    for (const [key, value] of Object.entries(colors)) {
      const picker = document.getElementById('color-' + key);
      const hex = document.getElementById('color-' + key + '-hex');
      if (picker && hex) {
        picker.value = value;
        hex.value = value;
      }
    }
  }

  /**
   * Setup color picker sync
   */
  function setupColorPickers() {
    const colorKeys = ['primary', 'secondary', 'success', 'warning', 'error', 
                       'bg-primary', 'bg-secondary', 'bg-tertiary', 
                       'text-primary', 'text-secondary', 'text-muted', 'border-color'];
    
    colorKeys.forEach(key => {
      const picker = document.getElementById('color-' + key);
      const hex = document.getElementById('color-' + key + '-hex');
      
      if (picker && hex) {
        // Sync picker -> hex
        picker.addEventListener('input', function() {
          hex.value = this.value;
        });
        
        // Sync hex -> picker
        hex.addEventListener('input', function() {
          if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
            picker.value = this.value;
          }
        });
      }
    });
  }

  /**
   * Close modal
   */
  window.closeModal = function() {
    document.getElementById('themeModal').classList.remove('active');
  };

  /**
   * Save theme
   */
  window.saveTheme = async function(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    
    // Collect colors
    const colors = {
      'primary': document.getElementById('color-primary-hex').value,
      'secondary': document.getElementById('color-secondary-hex').value,
      'success': document.getElementById('color-success-hex').value,
      'warning': document.getElementById('color-warning-hex').value,
      'error': document.getElementById('color-error-hex').value,
      'bg-primary': document.getElementById('color-bg-primary-hex').value,
      'bg-secondary': document.getElementById('color-bg-secondary-hex').value,
      'bg-tertiary': document.getElementById('color-bg-tertiary-hex').value,
      'text-primary': document.getElementById('color-text-primary-hex').value,
      'text-secondary': document.getElementById('color-text-secondary-hex').value,
      'text-muted': document.getElementById('color-text-muted-hex').value,
      'border-color': document.getElementById('color-border-color-hex').value
    };
    
    formData.set('colors', JSON.stringify(colors));
    
    try {
      const response = await fetch('api/themes.php', {
        method: 'POST',
        body: formData
      });
      
      const data = await response.json();
      
      if (data.success) {
        alert('Theme saved successfully!');
        closeModal();
        loadThemes();
      } else {
        alert('Error: ' + data.error);
      }
    } catch (error) {
      alert('Error: ' + error.message);
    }
  };

  /**
   * Delete theme
   */
  window.deleteTheme = async function(themeId, themeName) {
    if (!confirm(`Are you sure you want to delete the theme "${themeName}"? This action cannot be undone.`)) {
      return;
    }
    
    const formData = new FormData();
    formData.set('delete_id', themeId);
    
    try {
      const response = await fetch('api/themes.php', {
        method: 'POST',
        body: formData
      });
      
      const data = await response.json();
      
      if (data.success) {
        alert('Theme deleted successfully!');
        loadThemes();
      } else {
        alert('Error: ' + data.error);
      }
    } catch (error) {
      alert('Error: ' + error.message);
    }
  };

  /**
   * Export themes
   */
  window.exportThemes = function() {
    window.location.href = 'api/themes.php?export=1';
  };

  /**
   * Import themes
   */
  window.importThemes = async function(input) {
    if (!input.files || input.files.length === 0) {
      return;
    }
    
    const formData = new FormData();
    formData.append('import_file', input.files[0]);
    
    try {
      const response = await fetch('api/themes.php', {
        method: 'POST',
        body: formData
      });
      
      const data = await response.json();
      
      if (data.success) {
        alert(data.message || 'Themes imported successfully!');
        loadThemes();
      } else {
        alert('Error: ' + data.error);
      }
    } catch (error) {
      alert('Error: ' + error.message);
    }
    
    // Reset file input
    input.value = '';
  };

  /**
   * Load active theme on page load
   */
  async function loadActiveTheme() {
    try {
      const response = await fetch('api/themes.php?active=1');
      const data = await response.json();
      
      if (data.success && data.theme) {
        applyTheme(data.theme);
      }
    } catch (error) {
      console.error('Error loading active theme:', error);
    }
  }

  /**
   * Initialize
   */
  function init() {
    loadThemes();
    loadActiveTheme();
  }

  // Initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
