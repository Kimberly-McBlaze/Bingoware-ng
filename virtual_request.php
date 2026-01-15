<?php 
include_once("include/bootstrap.php"); 

// Start session for persistence
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if Virtual Bingo is enabled
if ($virtualbingo !== 'on') {
    header('Location: index.php');
    exit;
}

// Handle clear cards request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_cards'])) {
    unset($_SESSION['virtual_card_links']);
    header('Location: virtual_request.php');
    exit;
}

$error = '';
$success = false;
$card_links = [];

// Check if we have stored cards in session
if (isset($_SESSION['virtual_card_links']) && !empty($_SESSION['virtual_card_links'])) {
    $card_links = $_SESSION['virtual_card_links'];
    $success = true;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (true) {
        // Validate card count
        $card_count = filter_input(INPUT_POST, 'card_count', FILTER_VALIDATE_INT);
        $max_request = (int)($virtualbingo_max_request ?? 10);
        
        if ($card_count === false || $card_count < 1) {
            $error = 'Please enter a valid number of cards (minimum 1).';
        } elseif ($card_count > $max_request) {
            $error = "Maximum $max_request cards allowed per request.";
        } else {
            // Generate card tokens and store mappings
            include_once("include/virtual_cards.php");
            $result = generate_virtual_cards($card_count);
            
            if ($result['success']) {
                $success = true;
                $card_links = $result['cards'];
                // Store in session
                $_SESSION['virtual_card_links'] = $card_links;
            } else {
                $error = $result['error'] ?? 'Failed to generate cards. Please ensure a card set exists.';
            }
        }
    }
}

include("header.php");
?>

<div class="content-header">
  <h2 class="content-title">🌐 Request Virtual Bingo Cards</h2>
  <p class="content-subtitle">Get shareable links for remote play</p>
  <div style="margin-top: 1rem;">
    <a href="index.php" class="btn btn-secondary">
      ← Back to Main Menu
    </a>
  </div>
</div>

<?php if ($error): ?>
<div class="alert alert-error">
  <strong>❌ Error:</strong> <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<?php if ($success): ?>
<div class="alert alert-success">
  <strong>✅ Success!</strong> Your cards have been generated.
</div>

<div class="card mb-3">
  <div class="card-header">
    <h3 class="card-title">📋 Your Virtual Bingo Cards (<?= count($card_links) ?>)</h3>
  </div>
  <div class="card-body">
    <p style="margin-bottom: 1rem; color: var(--text-muted);">
      Share these links with players. Each link opens an interactive card that can be marked during play.
    </p>
    
    <div style="display: flex; flex-direction: column; gap: 1rem;">
      <?php foreach ($card_links as $idx => $card): ?>
      <div style="border: 1px solid var(--border-color); border-radius: 8px; padding: 1rem; background: var(--card-bg);">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
          <div style="flex: 1; min-width: 200px;">
            <strong style="display: block; margin-bottom: 0.5rem;">Card <?= ($idx + 1) ?> - <?= htmlspecialchars($card['card_id']) ?></strong>
            <input type="text" 
                   value="<?= htmlspecialchars($card['url']) ?>" 
                   readonly 
                   id="link-<?= $idx ?>"
                   style="width: 100%; padding: 0.5rem; font-family: monospace; font-size: 0.875rem; border: 1px solid var(--border-color); border-radius: 4px; background: var(--bg-color);">
          </div>
          <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <button onclick="copyLink(<?= $idx ?>)" class="btn btn-secondary btn-sm">
              📋 Copy
            </button>
            <a href="<?= htmlspecialchars($card['url']) ?>" target="_blank" class="btn btn-primary btn-sm">
              🔗 Open
            </a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    
    <div style="margin-top: 1.5rem; padding: 1rem; background: var(--bg-color); border-radius: 8px;">
      <p style="margin: 0; font-size: 0.875rem; color: var(--text-muted);">
        <strong>💡 Tip:</strong> Players can click squares on their cards to mark them during play. 
        Marks are saved automatically in their browser.
      </p>
    </div>
    
    <div style="margin-top: 1rem;">
      <a href="virtual_request.php" class="btn btn-primary">
        ➕ Request More Cards
      </a>
      <form method="POST" action="virtual_request.php" style="display: inline; margin-left: 0.5rem;">
        <input type="hidden" name="clear_cards" value="1">
        <button type="submit" class="btn btn-danger">
          🗑️ Clear All Cards
        </button>
      </form>
    </div>
  </div>
</div>

<script>
function copyLink(idx) {
    const input = document.getElementById('link-' + idx);
    
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
            copyLinkFallback(input);
        });
    } else {
        // Fallback for older browsers
        copyLinkFallback(input);
    }
}

function copyLinkFallback(input) {
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
        alert('Failed to copy link. Please copy manually.');
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
               max="<?= (int)($virtualbingo_max_request ?? 10) ?>" 
               value="1" 
               required 
               class="form-input" 
               style="max-width: 150px;">
        <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">
          Maximum: <?= (int)($virtualbingo_max_request ?? 10) ?> cards per request
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

<?php include("footer.php"); ?>
