<?php 
include_once("include/functions.php");

// Handle AJAX requests
if (isset($_GET['action']) && $_GET['action'] === 'api') {
    header('Content-Type: application/json');
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    // List patterns
    if ($method === 'GET' && !isset($_GET['id'])) {
        echo json_encode(['success' => true, 'patterns' => load_patterns()]);
        exit;
    }
    
    // Get single pattern
    if ($method === 'GET' && isset($_GET['id'])) {
        $pattern = get_pattern_by_id($_GET['id']);
        if ($pattern) {
            echo json_encode(['success' => true, 'pattern' => $pattern]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Pattern not found']);
        }
        exit;
    }
    
    // Create pattern
    if ($method === 'POST' && (!isset($_POST['id']) || empty($_POST['id']))) {
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $grid_json = $_POST['grid'] ?? '[]';
        $enabled = isset($_POST['enabled']) && $_POST['enabled'] === 'true';
        
        $grid = json_decode($grid_json, true);
        $result = create_pattern($name, $description, $grid, $enabled);
        echo json_encode($result);
        exit;
    }
    
    // Update pattern
    if ($method === 'POST' && isset($_POST['id']) && !empty($_POST['id'])) {
        $id = $_POST['id'];
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $grid_json = $_POST['grid'] ?? null;
        $enabled = isset($_POST['enabled']) ? ($_POST['enabled'] === 'true') : null;
        
        $grid = $grid_json ? json_decode($grid_json, true) : null;
        $result = update_pattern($id, $name, $description, $grid, $enabled);
        echo json_encode($result);
        exit;
    }
    
    // Delete pattern
    if ($method === 'POST' && isset($_POST['delete_id'])) {
        $result = delete_pattern($_POST['delete_id']);
        echo json_encode($result);
        exit;
    }
    
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

// Regular page display
$patterns = load_patterns();
?>
<!DOCTYPE html>
<html>
<head>
<title>Manage Winning Patterns - Bingoware-ng</title>
<link rel="stylesheet" href="include/modern-styles.css">
<style>
.pattern-grid {
    display: grid;
    grid-template-columns: repeat(5, 60px);
    gap: 4px;
    margin: 1rem 0;
    justify-content: center;
}

.pattern-cell {
    width: 60px;
    height: 60px;
    border: 2px solid var(--border-color);
    background: var(--card-bg);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-weight: bold;
    transition: all 0.2s;
    border-radius: 4px;
}

.pattern-cell:hover {
    border-color: var(--color-primary);
}

.pattern-cell.selected {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.pattern-cell.header {
    background: var(--color-primary);
    color: white;
    cursor: default;
    font-size: 1.5rem;
}

.pattern-list {
    display: grid;
    gap: 1rem;
}

.pattern-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    background: var(--card-bg);
}

.pattern-info {
    flex: 1;
}

.pattern-name {
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.pattern-desc {
    color: var(--text-muted);
    font-size: 0.875rem;
}

.pattern-actions {
    display: flex;
    gap: 0.5rem;
}

.badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-left: 0.5rem;
}

.badge-success {
    background: var(--color-success);
    color: white;
}

.badge-default {
    background: var(--text-muted);
    color: white;
}

.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
}

.modal.active {
    display: flex;
}

.modal-content {
    background: var(--card-bg);
    padding: 2rem;
    border-radius: 12px;
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

.modal-title {
    font-size: 1.5rem;
    font-weight: 600;
}

.close-btn {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--text-muted);
}

.close-btn:hover {
    color: var(--color-error);
}
</style>
</head>
<body>
<?php include_once("header.php"); ?>

<div class="main-container">
  <?php include_once("menu.php"); ?>
  <main class="content-area">
    <div class="content-header">
      <h2 class="content-title">🎯 Manage Winning Patterns</h2>
      <p class="content-subtitle">Add, edit, and delete custom winning patterns</p>
    </div>
    
    <div class="card mb-3">
      <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 class="card-title">Winning Patterns</h3>
        <button class="btn btn-primary" onclick="openAddModal()">➕ Add New Pattern</button>
      </div>
      <div class="card-body">
        <div class="pattern-list" id="patternList">
          <?php foreach ($patterns as $pattern): ?>
          <div class="pattern-item" data-id="<?= htmlspecialchars($pattern['id']); ?>">
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
                       <?= $pattern['enabled'] ? 'checked' : ''; ?>
                       onchange="togglePattern('<?= htmlspecialchars($pattern['id']); ?>', this.checked)">
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

<script>
let editingGrid = [];
const bingoLetters = ['B', 'I', 'N', 'G', 'O'];

// Initialize grid
function initGrid() {
  const grid = document.getElementById('editGrid');
  
  // Clear existing cells (except headers)
  const cells = grid.querySelectorAll('.pattern-cell:not(.header)');
  cells.forEach(cell => cell.remove());
  
  // Create 25 cells
  for (let row = 0; row < 5; row++) {
    for (let col = 0; col < 5; col++) {
      const cell = document.createElement('div');
      cell.className = 'pattern-cell';
      cell.dataset.col = col;
      cell.dataset.row = row;
      cell.textContent = (row === 2 && col === 2) ? '★' : '';
      cell.onclick = () => toggleCell(col, row);
      grid.appendChild(cell);
    }
  }
}

function toggleCell(col, row) {
  const cell = document.querySelector(`.pattern-cell[data-col="${col}"][data-row="${row}"]`);
  const index = editingGrid.findIndex(s => s.col === col && s.row === row);
  
  if (index >= 0) {
    editingGrid.splice(index, 1);
    cell.classList.remove('selected');
  } else {
    editingGrid.push({col, row});
    cell.classList.add('selected');
  }
}

function setGridFromData(grid) {
  editingGrid = grid ? [...grid] : [];
  
  // Clear all selections
  document.querySelectorAll('.pattern-cell:not(.header)').forEach(cell => {
    cell.classList.remove('selected');
  });
  
  // Apply selections
  editingGrid.forEach(square => {
    const cell = document.querySelector(`.pattern-cell[data-col="${square.col}"][data-row="${square.row}"]`);
    if (cell) cell.classList.add('selected');
  });
}

function openAddModal() {
  document.getElementById('modalTitle').textContent = 'Add New Pattern';
  document.getElementById('patternForm').reset();
  document.getElementById('patternId').value = '';
  setGridFromData([]);
  document.getElementById('patternModal').classList.add('active');
}

async function openEditModal(patternId) {
  try {
    const response = await fetch(`patterns.php?action=api&id=${patternId}`);
    const data = await response.json();
    
    if (data.success) {
      const pattern = data.pattern;
      document.getElementById('modalTitle').textContent = 'Edit Pattern';
      document.getElementById('patternId').value = pattern.id;
      document.getElementById('patternName').value = pattern.name;
      document.getElementById('patternDesc').value = pattern.description || '';
      document.getElementById('patternEnabled').checked = pattern.enabled;
      setGridFromData(pattern.grid);
      document.getElementById('patternModal').classList.add('active');
    } else {
      alert('Error loading pattern: ' + data.error);
    }
  } catch (error) {
    alert('Error: ' + error.message);
  }
}

function closeModal() {
  document.getElementById('patternModal').classList.remove('active');
}

async function savePattern(event) {
  event.preventDefault();
  
  if (editingGrid.length === 0) {
    alert('Please select at least one square in the pattern grid');
    return;
  }
  
  const formData = new FormData(event.target);
  formData.set('grid', JSON.stringify(editingGrid));
  formData.set('enabled', document.getElementById('patternEnabled').checked ? 'true' : 'false');
  formData.set('action', 'api');
  
  try {
    const response = await fetch('patterns.php?action=api', {
      method: 'POST',
      body: formData
    });
    
    const data = await response.json();
    
    if (data.success) {
      alert('Pattern saved successfully!');
      location.reload();
    } else {
      alert('Error: ' + data.error);
    }
  } catch (error) {
    alert('Error: ' + error.message);
  }
}

async function togglePattern(patternId, enabled) {
  const formData = new FormData();
  formData.set('id', patternId);
  formData.set('enabled', enabled ? 'true' : 'false');
  formData.set('action', 'api');
  
  try {
    const response = await fetch('patterns.php?action=api', {
      method: 'POST',
      body: formData
    });
    
    const data = await response.json();
    
    if (!data.success) {
      alert('Error: ' + data.error);
      location.reload();
    }
  } catch (error) {
    alert('Error: ' + error.message);
    location.reload();
  }
}

async function deletePattern(patternId, patternName) {
  if (!confirm(`Are you sure you want to delete the pattern "${patternName}"? This action cannot be undone.`)) {
    return;
  }
  
  const formData = new FormData();
  formData.set('delete_id', patternId);
  formData.set('action', 'api');
  
  try {
    const response = await fetch('patterns.php?action=api', {
      method: 'POST',
      body: formData
    });
    
    const data = await response.json();
    
    if (data.success) {
      alert('Pattern deleted successfully!');
      location.reload();
    } else {
      alert('Error: ' + data.error);
    }
  } catch (error) {
    alert('Error: ' + error.message);
  }
}

// Initialize grid when page loads
initGrid();
</script>

</body>
</html>
