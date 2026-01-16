<?php 
include_once("include/bootstrap.php");

// Regular page display
$themes = [];
$themes_file = __DIR__ . "/data/themes.json";
if (file_exists($themes_file)) {
    $data = json_decode(file_get_contents($themes_file), true);
    $themes = $data['themes'] ?? [];
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Theme Manager - Bingoware-ng</title>
<link rel="stylesheet" href="include/modern-styles.css">
<link rel="stylesheet" href="include/app.css">
<style>
.theme-preview {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 1rem;
  margin-top: 1rem;
}

.theme-card {
  border: 2px solid var(--border-color);
  border-radius: 8px;
  padding: 1rem;
  cursor: pointer;
  transition: all 0.2s;
  background: var(--bg-primary);
}

.theme-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.theme-card.active {
  border-color: var(--color-success);
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
}

.theme-colors {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 0.5rem;
  margin: 1rem 0;
}

.color-swatch {
  height: 40px;
  border-radius: 4px;
  border: 1px solid rgba(0,0,0,0.1);
}

.theme-actions {
  display: flex;
  gap: 0.5rem;
  margin-top: 1rem;
}

.modal {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.5);
  z-index: 1000;
  align-items: center;
  justify-content: center;
}

.modal.active {
  display: flex;
}

.modal-content {
  background: var(--bg-primary);
  border-radius: 12px;
  padding: 2rem;
  max-width: 600px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: var(--text-secondary);
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: var(--text-primary);
}

.form-input, .form-textarea {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid var(--border-color);
  border-radius: 8px;
  font-size: 1rem;
  background: var(--bg-secondary);
  color: var(--text-primary);
}

.color-input-group {
  display: grid;
  grid-template-columns: 1fr auto;
  gap: 0.5rem;
  align-items: center;
}

.color-input {
  width: 60px;
  height: 40px;
  border: 2px solid var(--border-color);
  border-radius: 4px;
  cursor: pointer;
}

.import-export-section {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
}

#import-file-input {
  display: none;
}
</style>
</head>
<body>
<?php include_once("header.php"); ?>

<div class="main-container">
  <?php include_once("menu.php"); ?>
  <main class="content-area">
    <div class="content-header">
      <h2 class="content-title">🎨 Theme Manager</h2>
      <p class="content-subtitle">Customize the look and feel of Bingoware-ng</p>
    </div>
    
    <div class="card mb-3">
      <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 class="card-title">Available Themes</h3>
        <div class="import-export-section">
          <button class="btn btn-secondary" onclick="exportThemes()">📥 Export Themes</button>
          <input type="file" id="import-file-input" accept=".json" onchange="importThemes(this)">
          <button class="btn btn-secondary" onclick="document.getElementById('import-file-input').click()">📤 Import Themes</button>
          <button class="btn btn-primary" onclick="openCreateModal()">➕ Create Custom Theme</button>
        </div>
      </div>
      <div class="card-body">
        <div class="theme-preview" id="themeList">
          <!-- Themes will be loaded here by JavaScript -->
        </div>
      </div>
    </div>
    
  </main>
</div>

<!-- Create/Edit Theme Modal -->
<div id="themeModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3 class="modal-title" id="modalTitle">Create Custom Theme</h3>
      <button class="close-btn" onclick="closeModal()">&times;</button>
    </div>
    
    <form id="themeForm" onsubmit="saveTheme(event)">
      <input type="hidden" id="themeId" name="id">
      
      <div class="form-group">
        <label class="form-label">Theme Name *</label>
        <input type="text" id="themeName" name="name" class="form-input" required maxlength="50" placeholder="My Custom Theme">
      </div>
      
      <div class="form-group">
        <label class="form-label">Description</label>
        <textarea id="themeDescription" name="description" class="form-textarea" rows="2" maxlength="200" placeholder="A brief description of your theme"></textarea>
      </div>
      
      <div class="form-group">
        <label class="form-label">Colors</label>
        <div style="display: grid; gap: 1rem;">
          <div class="color-input-group">
            <label>Primary:</label>
            <input type="color" id="color-primary" value="#667eea" class="color-input">
            <input type="text" id="color-primary-hex" value="#667eea" class="form-input" style="width: 100px;" pattern="^#[0-9A-Fa-f]{6}$">
          </div>
          <div class="color-input-group">
            <label>Secondary:</label>
            <input type="color" id="color-secondary" value="#764ba2" class="color-input">
            <input type="text" id="color-secondary-hex" value="#764ba2" class="form-input" style="width: 100px;" pattern="^#[0-9A-Fa-f]{6}$">
          </div>
          <div class="color-input-group">
            <label>Success:</label>
            <input type="color" id="color-success" value="#10b981" class="color-input">
            <input type="text" id="color-success-hex" value="#10b981" class="form-input" style="width: 100px;" pattern="^#[0-9A-Fa-f]{6}$">
          </div>
          <div class="color-input-group">
            <label>Warning:</label>
            <input type="color" id="color-warning" value="#f59e0b" class="color-input">
            <input type="text" id="color-warning-hex" value="#f59e0b" class="form-input" style="width: 100px;" pattern="^#[0-9A-Fa-f]{6}$">
          </div>
          <div class="color-input-group">
            <label>Error:</label>
            <input type="color" id="color-error" value="#ef4444" class="color-input">
            <input type="text" id="color-error-hex" value="#ef4444" class="form-input" style="width: 100px;" pattern="^#[0-9A-Fa-f]{6}$">
          </div>
          <div class="color-input-group">
            <label>Background Primary:</label>
            <input type="color" id="color-bg-primary" value="#ffffff" class="color-input">
            <input type="text" id="color-bg-primary-hex" value="#ffffff" class="form-input" style="width: 100px;" pattern="^#[0-9A-Fa-f]{6}$">
          </div>
          <div class="color-input-group">
            <label>Background Secondary:</label>
            <input type="color" id="color-bg-secondary" value="#f3f4f6" class="color-input">
            <input type="text" id="color-bg-secondary-hex" value="#f3f4f6" class="form-input" style="width: 100px;" pattern="^#[0-9A-Fa-f]{6}$">
          </div>
          <div class="color-input-group">
            <label>Background Tertiary:</label>
            <input type="color" id="color-bg-tertiary" value="#e5e7eb" class="color-input">
            <input type="text" id="color-bg-tertiary-hex" value="#e5e7eb" class="form-input" style="width: 100px;" pattern="^#[0-9A-Fa-f]{6}$">
          </div>
          <div class="color-input-group">
            <label>Text Primary:</label>
            <input type="color" id="color-text-primary" value="#1f2937" class="color-input">
            <input type="text" id="color-text-primary-hex" value="#1f2937" class="form-input" style="width: 100px;" pattern="^#[0-9A-Fa-f]{6}$">
          </div>
          <div class="color-input-group">
            <label>Text Secondary:</label>
            <input type="color" id="color-text-secondary" value="#6b7280" class="color-input">
            <input type="text" id="color-text-secondary-hex" value="#6b7280" class="form-input" style="width: 100px;" pattern="^#[0-9A-Fa-f]{6}$">
          </div>
          <div class="color-input-group">
            <label>Text Muted:</label>
            <input type="color" id="color-text-muted" value="#9ca3af" class="color-input">
            <input type="text" id="color-text-muted-hex" value="#9ca3af" class="form-input" style="width: 100px;" pattern="^#[0-9A-Fa-f]{6}$">
          </div>
          <div class="color-input-group">
            <label>Border Color:</label>
            <input type="color" id="color-border-color" value="#d1d5db" class="color-input">
            <input type="text" id="color-border-color-hex" value="#d1d5db" class="form-input" style="width: 100px;" pattern="^#[0-9A-Fa-f]{6}$">
          </div>
        </div>
      </div>
      
      <div style="display: flex; gap: 1rem; justify-content: flex-end;">
        <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
        <button type="submit" class="btn btn-primary">💾 Save Theme</button>
      </div>
    </form>
  </div>
</div>

<script src="include/themes-ui.js"></script>

<?php include_once("footer.php"); ?>
</body>
</html>
