<?php 
include_once("include/bootstrap.php");
include_once("include/virtual_cards.php");

// Get and validate token
$token = $_GET['token'] ?? '';
$card_data = get_card_from_token($token);

if (!$card_data) {
    include("header.php");
    echo '<div class="content-header">
      <h2 class="content-title">❌ Invalid Card</h2>
      <p class="content-subtitle">This card link is not valid</p>
    </div>
    <div class="alert alert-error">
      The card you are trying to access does not exist or the link is incorrect.
    </div>';
    include("footer.php");
    exit;
}

// Load the card set for the stored setid
$setid = $card_data['setid'];
$card_number = $card_data['card_number'];

if (!set_exists()) {
    include("header.php");
    echo '<div class="content-header">
      <h2 class="content-title">❌ Card Set Not Found</h2>
    </div>
    <div class="alert alert-error">
      The card set for this card no longer exists.
    </div>';
    include("footer.php");
    exit;
}

$set = load_set();
if (!isset($set[$card_number])) {
    include("header.php");
    echo '<div class="content-header">
      <h2 class="content-title">❌ Card Not Found</h2>
    </div>
    <div class="alert alert-error">
      This card does not exist in the current set.
    </div>';
    include("footer.php");
    exit;
}

$card = $set[$card_number];
$card_id = sprintf("%s%'04d", $setid, $card_number + 1);
?>
<!DOCTYPE html>
<html>
<head>
<title>Virtual Bingo Card <?= htmlspecialchars($card_id) ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="include/app.css">
<style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 20px;
    background: #f5f5f5;
}

.virtual-card-container {
    max-width: 650px;
    margin: 0 auto;
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.card-header-section {
    text-align: center;
    margin-bottom: 20px;
}

.card-title {
    font-size: 24px;
    margin: 0 0 10px 0;
    color: #333;
}

.card-id {
    font-size: 14px;
    color: #666;
    margin: 5px 0;
}

.bingo-table {
    width: 100%;
    max-width: 600px;
    margin: 0 auto 20px auto;
    border-collapse: collapse;
    border: 3px solid <?= $bordercolor ?>;
}

.bingo-table th {
    background: <?= $headerbgcolor ?>;
    color: <?= $headerfontcolor ?>;
    font-size: 48px;
    font-weight: bold;
    padding: 20px;
    border: 2px solid <?= $bordercolor ?>;
}

.bingo-table td {
    width: 20%;
    height: 100px;
    text-align: center;
    vertical-align: middle;
    font-size: 32px;
    border: 2px solid <?= $bordercolor ?>;
    background: <?= $mainbgcolor ?>;
    color: <?= $mainfontcolor ?>;
    cursor: pointer;
    user-select: none;
    transition: all 0.2s;
}

.bingo-table td:hover {
    opacity: 0.8;
}

.bingo-table td.marked {
    background: <?= $selectedbgcolor ?> !important;
    color: <?= $selectedfontcolor ?> !important;
}

.bingo-table td.free-square {
    background: <?= $selectedbgcolor ?>;
    cursor: default;
}

.bingo-table td.free-square img {
    height: 80px;
    width: auto;
}

.controls {
    text-align: center;
    margin: 20px 0;
    display: flex;
    gap: 10px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    transition: all 0.2s;
}

.btn-primary {
    background: #4CAF50;
    color: white;
}

.btn-primary:hover {
    background: #45a049;
}

.btn-secondary {
    background: #2196F3;
    color: white;
}

.btn-secondary:hover {
    background: #0b7dda;
}

.btn-danger {
    background: #f44336;
    color: white;
}

.btn-danger:hover {
    background: #da190b;
}

.info-box {
    background: #e3f2fd;
    border-left: 4px solid #2196F3;
    padding: 15px;
    margin: 20px 0;
    border-radius: 4px;
}

.info-box p {
    margin: 0;
    line-height: 1.6;
}

/* Print styles */
@media print {
    body {
        background: white;
        padding: 0;
    }
    
    .virtual-card-container {
        box-shadow: none;
        padding: 10px;
    }
    
    .controls, .info-box {
        display: none !important;
    }
    
    .bingo-table td {
        cursor: default;
    }
}

/* Mobile responsive */
@media (max-width: 600px) {
    .bingo-table th {
        font-size: 32px;
        padding: 15px 10px;
    }
    
    .bingo-table td {
        height: 70px;
        font-size: 24px;
    }
    
    .bingo-table td.free-square img {
        height: 60px;
    }
    
    .btn {
        padding: 10px 20px;
        font-size: 14px;
    }
}
</style>
</head>
<body>

<div class="virtual-card-container">
    <div class="card-header-section">
        <h1 class="card-title">🎱 Virtual Bingo Card</h1>
        <div class="card-id">Card Number: <strong><?= htmlspecialchars($card_id) ?></strong></div>
    </div>
    
    <table class="bingo-table" id="bingoCard">
        <thead>
            <tr>
                <?php foreach ($bingoletters as $letter): ?>
                    <th><?= $letter ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php for ($row = 0; $row < 5; $row++): ?>
            <tr>
                <?php for ($col = 0; $col < 5; $col++): ?>
                    <?php 
                    $number = $card[$col][$row]["number"];
                    $is_free = ($number === "Free");
                    $cell_id = "cell-{$col}-{$row}";
                    ?>
                    <td id="<?= $cell_id ?>" 
                        data-col="<?= $col ?>" 
                        data-row="<?= $row ?>"
                        <?= $is_free ? 'class="free-square marked"' : '' ?>
                        onclick="<?= $is_free ? '' : 'toggleCell(this)' ?>">
                        <?php if ($is_free): ?>
                            <img src="images/star.gif" alt="Free">
                        <?php else: ?>
                            <?= $number ?>
                        <?php endif; ?>
                    </td>
                <?php endfor; ?>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>
    
    <div class="controls">
        <button onclick="resetMarks()" class="btn btn-danger">🔄 Reset Marks</button>
        <button onclick="window.print()" class="btn btn-secondary">🖨️ Print Card</button>
    </div>
    
    <div class="info-box">
        <p><strong>💡 How to use:</strong> Click or tap any square to mark it. Your marks are saved automatically and will be restored when you reopen this page. Use the Reset button to clear all marks.</p>
    </div>
</div>

<script>
// Use token as unique identifier for localStorage
const CARD_TOKEN = <?= json_encode($token, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
const STORAGE_KEY = 'bingo_marks_' + CARD_TOKEN;

// Load marks from localStorage on page load
function loadMarks() {
    const saved = localStorage.getItem(STORAGE_KEY);
    if (saved) {
        try {
            const marks = JSON.parse(saved);
            marks.forEach(cellId => {
                const cell = document.getElementById(cellId);
                if (cell && !cell.classList.contains('free-square')) {
                    cell.classList.add('marked');
                }
            });
        } catch (e) {
            console.error('Failed to load marks:', e);
        }
    }
}

// Save marks to localStorage
function saveMarks() {
    const marked = [];
    document.querySelectorAll('#bingoCard td.marked:not(.free-square)').forEach(cell => {
        marked.push(cell.id);
    });
    localStorage.setItem(STORAGE_KEY, JSON.stringify(marked));
}

// Toggle cell marked state
function toggleCell(cell) {
    cell.classList.toggle('marked');
    saveMarks();
}

// Reset all marks
function resetMarks() {
    if (confirm('Are you sure you want to clear all marks?')) {
        document.querySelectorAll('#bingoCard td:not(.free-square)').forEach(cell => {
            cell.classList.remove('marked');
        });
        localStorage.removeItem(STORAGE_KEY);
    }
}

// Load marks when page loads
document.addEventListener('DOMContentLoaded', loadMarks);
</script>

</body>
</html>
