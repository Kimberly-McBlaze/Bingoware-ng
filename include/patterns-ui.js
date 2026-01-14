/**
 * Patterns Page UI JavaScript
 * Handles pattern grid editing, CRUD operations, and modal interactions
 */

let editingGrid = [];
const bingoLetters = ['B', 'I', 'N', 'G', 'O'];
let pendingChanges = {}; // Track pending enable/disable changes

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
    const response = await fetch(`api/patterns.php?id=${patternId}`);
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
  
  try {
    const response = await fetch('api/patterns.php', {
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

// Mark a pattern as changed (for enable/disable)
function markPatternChanged(patternId, enabled) {
  const patternItem = document.querySelector(`.pattern-item[data-id="${patternId}"]`);
  const originalEnabled = patternItem.dataset.enabled === '1';
  
  if (enabled !== originalEnabled) {
    pendingChanges[patternId] = enabled;
  } else {
    delete pendingChanges[patternId];
  }
  
  // Show/hide save button based on pending changes
  const saveContainer = document.getElementById('saveButtonContainer');
  if (Object.keys(pendingChanges).length > 0) {
    saveContainer.style.display = 'block';
  } else {
    saveContainer.style.display = 'none';
  }
}

// Save all pending pattern changes
async function savePatternChanges(event) {
  if (Object.keys(pendingChanges).length === 0) {
    return;
  }
  
  const saveButton = event.target;
  saveButton.disabled = true;
  saveButton.textContent = '💾 Saving...';
  
  try {
    let hasError = false;
    
    for (const [patternId, enabled] of Object.entries(pendingChanges)) {
      const formData = new FormData();
      formData.set('id', patternId);
      formData.set('enabled', enabled ? 'true' : 'false');
      
      const response = await fetch('api/patterns.php', {
        method: 'POST',
        body: formData
      });
      
      const data = await response.json();
      
      if (!data.success) {
        alert('Error updating pattern: ' + data.error);
        hasError = true;
        break;
      }
    }
    
    if (!hasError) {
      alert('Changes saved successfully!');
      location.reload();
    } else {
      saveButton.disabled = false;
      saveButton.textContent = '💾 Save Changes';
    }
  } catch (error) {
    alert('Error: ' + error.message);
    saveButton.disabled = false;
    saveButton.textContent = '💾 Save Changes';
  }
}

// Cancel pending changes
function cancelPatternChanges() {
  // Reset all checkboxes to original state
  document.querySelectorAll('.pattern-enable-checkbox').forEach(checkbox => {
    const patternId = checkbox.dataset.patternId;
    const patternItem = checkbox.closest('.pattern-item');
    const originalEnabled = patternItem.dataset.enabled === '1';
    checkbox.checked = originalEnabled;
  });
  
  // Clear pending changes
  pendingChanges = {};
  document.getElementById('saveButtonContainer').style.display = 'none';
}

async function deletePattern(patternId, patternName) {
  if (!confirm(`Are you sure you want to delete the pattern "${patternName}"? This action cannot be undone.`)) {
    return;
  }
  
  const formData = new FormData();
  formData.set('delete_id', patternId);
  
  try {
    const response = await fetch('api/patterns.php', {
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

async function resetToDefault() {
  if (!confirm('Are you sure you want to reset all patterns to defaults? This will:\n\n• Remove all custom patterns\n• Restore default patterns to their original state\n• Reset all enabled/disabled states\n\nThis action cannot be undone.')) {
    return;
  }
  
  const formData = new FormData();
  formData.set('reset_to_default', 'true');
  
  try {
    const response = await fetch('api/patterns.php', {
      method: 'POST',
      body: formData
    });
    
    const data = await response.json();
    
    if (data.success) {
      alert('Patterns reset to defaults successfully!');
      location.reload();
    } else {
      alert('Error: ' + data.error);
    }
  } catch (error) {
    alert('Error: ' + error.message);
  }
}

// Initialize grid when page loads
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initGrid);
} else {
  initGrid();
}
