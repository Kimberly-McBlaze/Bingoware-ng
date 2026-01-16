<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $pagetitle; ?></title>
<link rel="stylesheet" href="include/modern-styles.css">
<link rel="stylesheet" href="include/app.css">
<script src="include/scripts.js"></script>
<script src="include/colorpicker.js"></script>
<script src="include/modern-ui.js"></script>
<script src="include/update-checker.js"></script>
<script>
// Load and apply active theme
(async function() {
  try {
    const response = await fetch('api/themes.php?active=1');
    const data = await response.json();
    
    if (data.success && data.theme) {
      const root = document.documentElement;
      for (const [key, value] of Object.entries(data.theme.colors)) {
        root.style.setProperty('--color-' + key, value);
        // Also set without prefix for compatibility
        if (!key.startsWith('color-')) {
          root.style.setProperty('--' + key, value);
        }
      }
    }
  } catch (error) {
    console.error('Error loading theme:', error);
  }
})();
</script>
</head>
<body>
<header class="modern-header">
  <div class="header-container">
    <div class="header-logo">
      <div>
        <h1>🎱 Bingoware-ng</h1>
        <span class="logo-subtitle">Modern Bingo Card Management</span>
      </div>
    </div>
    <div class="theme-toggle">
      <span class="theme-toggle-label">🌙 Dark Mode</span>
      <label class="toggle-switch">
        <input type="checkbox" id="theme-toggle">
        <span class="toggle-slider"></span>
      </label>
    </div>
  </div>
</header>


