<?php 
include_once("include/bootstrap.php"); 

// Check if Virtual Bingo is enabled
if ($virtualbingo !== 'on') {
    header('Location: index.php');
    exit;
}

$error = '';
$success = false;
$stack_data = null;

// Load previously generated stacks
include_once("include/virtual_cards.php");
$all_existing_stacks = get_all_virtual_stacks_for_display();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate card count
    $card_count = filter_input(INPUT_POST, 'card_count', FILTER_VALIDATE_INT);
    $max_request = (int)($virtualbingo_max_request ?? 12);
    
    if ($card_count === false || $card_count < 1) {
        $error = 'Please enter a valid number of cards (minimum 1).';
    } elseif ($card_count > $max_request) {
        $error = "Maximum $max_request cards allowed per request.";
    } else {
        // Generate card stack
        include_once("include/virtual_cards.php");
        $result = generate_virtual_cards($card_count);
        
        if ($result['success']) {
            $success = true;
            $stack_data = $result;
        } else {
            $error = $result['error'] ?? 'Failed to generate cards. Please ensure a card set exists.';
        }
    }
}

include("header.php");
?>

<div class="content-header">
  <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
    <a href="index.php" class="btn btn-secondary" style="text-decoration: none;">
      ← Back to Menu
    </a>
  </div>
  <h2 class="content-title">🌐 Virtual Bingo - Administrator</h2>
  <p class="content-subtitle">Generate shareable card URLs for remote players</p>
</div>

<?php if ($error): ?>
<div class="alert alert-error">
  <strong>❌ Error:</strong> <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<?php if ($success): ?>
<div class="alert alert-success">
  <strong>✅ Success!</strong> Your card stack has been generated.
</div>

<div class="card mb-3">
  <div class="card-header">
    <h3 class="card-title">📦 Your Virtual Bingo Card Stack (<?= $stack_data['count'] ?> cards)</h3>
  </div>
  <div class="card-body">
    <p style="margin-bottom: 1rem; color: var(--text-muted);">
      Share this URL with players. It contains <?= $stack_data['count'] ?> card(s) that can be viewed interactively or printed.
    </p>
    
    <div style="border: 1px solid var(--border-color); border-radius: 8px; padding: 1rem; background: var(--card-bg);">
      <div style="margin-bottom: 1rem;">
        <strong style="display: block; margin-bottom: 0.5rem;">Cards in this stack:</strong>
        <div style="font-family: monospace; color: var(--text-muted);">
          <?= htmlspecialchars(implode(', ', $stack_data['card_ids'])) ?>
        </div>
      </div>
      
      <div style="margin-bottom: 0.5rem;">
        <strong style="display: block; margin-bottom: 0.5rem;">Shareable Stack URL:</strong>
        <input type="text" 
               value="<?= htmlspecialchars($stack_data['stack_url']) ?>" 
               readonly 
               id="stack-url"
               style="width: 100%; padding: 0.5rem; font-family: monospace; font-size: 0.875rem; border: 1px solid var(--border-color); border-radius: 4px; background: var(--bg-color);">
      </div>
      
      <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 1rem;">
        <button onclick="copyStackUrl()" class="btn btn-secondary btn-sm">
          📋 Copy URL
        </button>
        <a href="<?= htmlspecialchars($stack_data['stack_url']) ?>" target="_blank" class="btn btn-primary btn-sm">
          🔗 Open Stack
        </a>
      </div>
    </div>
    
    <div style="margin-top: 1.5rem; padding: 1rem; background: var(--bg-color); border-radius: 8px;">
      <p style="margin: 0; font-size: 0.875rem; color: var(--text-muted);">
        <strong>💡 Tip:</strong> Players can view all cards in the stack, click squares to mark them during play, 
        and print up to 4 cards per page. Marks are saved automatically in their browser.
      </p>
    </div>
    
    <div style="margin-top: 1rem;">
      <a href="virtual_request.php" class="btn btn-primary">
        ➕ Generate More Cards
      </a>
    </div>
  </div>
</div>

<script>
function copyStackUrl() {
    const input = document.getElementById('stack-url');
    
    // Try modern Clipboard API first
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(input.value).then(() => {
            const btn = event.target;
            const originalText = btn.textContent;
            btn.textContent = '✅ Copied!';
            setTimeout(() => {
                btn.textContent = originalText;
            }, 2000);
        }).catch(err => {
            // Fallback to older method
            copyUrlFallback(input);
        });
    } else {
        // Fallback for older browsers
        copyUrlFallback(input);
    }
}

function copyUrlFallback(input) {
    input.select();
    input.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        document.execCommand('copy');
        const btn = event.target;
        const originalText = btn.textContent;
        btn.textContent = '✅ Copied!';
        setTimeout(() => {
            btn.textContent = originalText;
        }, 2000);
    } catch (err) {
        alert('Failed to copy URL. Please copy manually.');
    }
}
</script>

<?php else: ?>

<div class="card">
  <div class="card-body">
    <form method="POST" action="virtual_request.php" class="modern-form">
      
      <div class="form-group">
        <label class="form-label">Number of Cards:</label>
        <input type="number" 
               name="card_count" 
               min="1" 
               max="<?= (int)($virtualbingo_max_request ?? 12) ?>" 
               value="1" 
               required 
               class="form-input" 
               style="max-width: 150px;">
        <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">
          Maximum: <?= (int)($virtualbingo_max_request ?? 12) ?> cards per request
        </p>
      </div>
      
      <button type="submit" class="btn btn-primary btn-lg">
        🎟️ Generate Cards
      </button>
    </form>
  </div>
</div>

<div class="card" style="margin-top: 1.5rem;">
  <div class="card-header">
    <h3 class="card-title">ℹ️ How It Works</h3>
  </div>
  <div class="card-body">
    <ol style="margin: 0; padding-left: 1.5rem; line-height: 1.8;">
      <li>Enter the number of bingo cards you need</li>
      <li>Click "Generate Cards" to create unique card links</li>
      <li>Share the links with players via email, chat, or other methods</li>
      <li>Players can open their card link to view and interact with their card</li>
      <li>Players can click/tap squares to mark them during play</li>
      <li>Cards can be printed directly from the browser</li>
    </ol>
  </div>
</div>

<?php endif; ?>

<?php if (!empty($all_existing_stacks)): ?>
<div class="card" style="margin-top: 1.5rem;">
  <div class="card-header">
    <h3 class="card-title">📚 Previously Generated Card Stacks (<?= count($all_existing_stacks) ?>)</h3>
  </div>
  <div class="card-body">
    <p style="margin-bottom: 1rem; color: var(--text-muted);">
      All previously generated virtual bingo card stacks for Set ID: <strong><?= htmlspecialchars($setid) ?></strong>
    </p>
    
    <div style="display: flex; flex-direction: column; gap: 1rem;">
      <?php foreach ($all_existing_stacks as $idx => $stack): ?>
      <div style="border: 1px solid var(--border-color); border-radius: 8px; padding: 1rem; background: var(--card-bg);">
        <div style="margin-bottom: 0.5rem;">
          <strong style="display: block; margin-bottom: 0.5rem;">
            Stack <?= ($idx + 1) ?> - <?= $stack['count'] ?> card(s)
          </strong>
          <div style="font-family: monospace; font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.5rem;">
            Cards: <?= htmlspecialchars(implode(', ', array_slice($stack['card_ids'], 0, 5))) ?><?= count($stack['card_ids']) > 5 ? '...' : '' ?>
          </div>
        </div>
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
          <div style="flex: 1; min-width: 200px;">
            <input type="text" 
                   value="<?= htmlspecialchars($stack['url']) ?>" 
                   readonly 
                   id="existing-stack-<?= $idx ?>"
                   style="width: 100%; padding: 0.5rem; font-family: monospace; font-size: 0.875rem; border: 1px solid var(--border-color); border-radius: 4px; background: var(--bg-color);">
          </div>
          <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <button onclick="copyExistingStack(<?= $idx ?>)" class="btn btn-secondary btn-sm">
              📋 Copy
            </button>
            <a href="<?= htmlspecialchars($stack['url']) ?>" target="_blank" class="btn btn-primary btn-sm">
              🔗 Open
            </a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<script>
function copyExistingStack(idx) {
    const input = document.getElementById('existing-stack-' + idx);
    
    // Try modern Clipboard API first
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(input.value).then(() => {
            const btn = event.target;
            const originalText = btn.textContent;
            btn.textContent = '✅ Copied!';
            setTimeout(() => {
                btn.textContent = originalText;
            }, 2000);
        }).catch(err => {
            // Fallback to older method
            copyStackFallback(input);
        });
    } else {
        // Fallback for older browsers
        copyStackFallback(input);
    }
}

function copyStackFallback(input) {
    input.select();
    input.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        document.execCommand('copy');
        const btn = event.target;
        const originalText = btn.textContent;
        btn.textContent = '✅ Copied!';
        setTimeout(() => {
            btn.textContent = originalText;
        }, 2000);
    } catch (err) {
        alert('Failed to copy URL. Please copy manually.');
    }
}
</script>
<?php endif; ?>

<?php include("footer.php"); ?>
