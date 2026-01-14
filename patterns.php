<?php 
include_once("include/bootstrap.php");

// Regular page display
$patterns = load_patterns();
?>
<!DOCTYPE html>
<html>
<head>
<title>Manage Winning Patterns - Bingoware-ng</title>
<link rel="stylesheet" href="include/modern-styles.css">
<link rel="stylesheet" href="include/app.css">
</head>
<body>
<?php include_once("header.php"); ?>

<div class="main-container">
  <?php include_once("menu.php"); ?>
  <main class="content-area">
    <div class="content-header">
      <h2 class="content-title">🎯 Manage Winning Patterns</h2>
      <p class="content-subtitle">Add, edit, and delete custom winning patterns <a href="javascript:explain('Winning Pattern')" class="help-icon">help?</a></p>
    </div>
    
    <div class="card mb-3">
      <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 class="card-title">Winning Patterns</h3>
        <div style="display: flex; gap: 0.5rem;">
          <button class="btn btn-secondary" onclick="resetToDefault()" title="Remove all custom patterns and restore defaults">
            🔄 Reset to Default
          </button>
          <button class="btn btn-primary" onclick="openAddModal()">➕ Add New Pattern</button>
        </div>
      </div>
      <div class="card-body">
        <div class="pattern-list" id="patternList">
          <?php foreach ($patterns as $pattern): ?>
          <div class="pattern-item" data-id="<?= htmlspecialchars($pattern['id']); ?>" data-enabled="<?= $pattern['enabled'] ? '1' : '0'; ?>">
            <div class="pattern-info">
              <div class="pattern-name">
                <?= htmlspecialchars($pattern['name']); ?>
                <?php if ($pattern['enabled']): ?>
                  <span class="badge badge-success">ENABLED</span>
                <?php endif; ?>
                <?php if ($pattern['is_default']): ?>
                  <span class="badge badge-default">DEFAULT</span>
                <?php endif; ?>
              </div>
              <?php if ($pattern['description']): ?>
              <div class="pattern-desc"><?= htmlspecialchars($pattern['description']); ?></div>
              <?php endif; ?>
            </div>
            <div class="pattern-actions">
              <label class="checkbox-option" style="margin: 0;">
                <input type="checkbox" 
                       class="pattern-enable-checkbox"
                       data-pattern-id="<?= htmlspecialchars($pattern['id']); ?>"
                       <?= $pattern['enabled'] ? 'checked' : ''; ?>
                       onchange="markPatternChanged('<?= htmlspecialchars($pattern['id']); ?>', this.checked)">
                <span>Enable</span>
              </label>
              <?php if (!$pattern['is_special']): ?>
              <button class="btn btn-sm btn-secondary" 
                      onclick="openEditModal('<?= htmlspecialchars($pattern['id']); ?>')">
                ✏️ Edit
              </button>
              <?php endif; ?>
              <?php if (!$pattern['is_default']): ?>
              <button class="btn btn-sm btn-error" 
                      onclick="deletePattern('<?= htmlspecialchars($pattern['id']); ?>', '<?= htmlspecialchars($pattern['name']); ?>')">
                🗑️ Delete
              </button>
              <?php endif; ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <div id="saveButtonContainer" style="margin-top: 1rem; text-align: right; display: none;">
          <button class="btn btn-primary" onclick="savePatternChanges(event)">💾 Save Changes</button>
          <button class="btn btn-secondary" onclick="cancelPatternChanges()">Cancel</button>
        </div>
      </div>
    </div>
    
  </main>
</div>

<!-- Add/Edit Pattern Modal -->
<div id="patternModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3 class="modal-title" id="modalTitle">Add New Pattern</h3>
      <button class="close-btn" onclick="closeModal()">&times;</button>
    </div>
    
    <form id="patternForm" onsubmit="savePattern(event)">
      <input type="hidden" id="patternId" name="id">
      
      <div class="form-group">
        <label class="form-label">Pattern Name *</label>
        <input type="text" id="patternName" name="name" class="form-input" required maxlength="50">
      </div>
      
      <div class="form-group">
        <label class="form-label">Description</label>
        <textarea id="patternDesc" name="description" class="form-input" rows="2" maxlength="200"></textarea>
      </div>
      
      <div class="form-group">
        <label class="form-label">Pattern Grid (click squares to select) *</label>
        <div style="text-align: center;">
          <div class="pattern-grid" id="editGrid">
            <div class="pattern-cell header">B</div>
            <div class="pattern-cell header">I</div>
            <div class="pattern-cell header">N</div>
            <div class="pattern-cell header">G</div>
            <div class="pattern-cell header">O</div>
          </div>
        </div>
      </div>
      
      <div class="form-group">
        <label class="checkbox-option">
          <input type="checkbox" id="patternEnabled" name="enabled">
          <span>Enable this pattern</span>
        </label>
      </div>
      
      <div style="display: flex; gap: 1rem; justify-content: flex-end;">
        <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Pattern</button>
      </div>
    </form>
  </div>
</div>

<?php include_once("footer.php"); ?>

<script src="include/patterns-ui.js"></script>

</body>
</html>
