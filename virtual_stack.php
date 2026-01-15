<?php 
include_once("include/bootstrap.php");
include_once("include/virtual_cards.php");

// Get and validate stack ID
$stack_id = $_GET['stack'] ?? '';
$stack_data = get_stack_from_id($stack_id);

if (!$stack_data) {
    include("header.php");
    echo '<div class="content-header">
      <h2 class="content-title">❌ Invalid Stack</h2>
      <p class="content-subtitle">This card stack link is not valid</p>
    </div>
    <div class="alert alert-error">
      The card stack you are trying to access does not exist or the link is incorrect.
    </div>';
    include("footer.php");
    exit;
}

// Load the card set for the stored setid
$setid = $stack_data['setid'];
$card_numbers = $stack_data['card_numbers'];

if (!set_exists()) {
    include("header.php");
    echo '<div class="content-header">
      <h2 class="content-title">❌ Card Set Not Found</h2>
    </div>
    <div class="alert alert-error">
      The card set for these cards no longer exists.
    </div>';
    include("footer.php");
    exit;
}

$set = load_set();
$cards = [];
foreach ($card_numbers as $card_number) {
    if (!isset($set[$card_number])) {
        include("header.php");
        echo '<div class="content-header">
          <h2 class="content-title">❌ Card Not Found</h2>
        </div>
        <div class="alert alert-error">
          One or more cards in this stack do not exist in the current set.
        </div>';
        include("footer.php");
        exit;
    }
    $card_id = sprintf("%s%'04d", $setid, $card_number + 1);
    $cards[] = [
        'data' => $set[$card_number],
        'id' => $card_id,
        'number' => $card_number,
    ];
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Virtual Bingo Card Stack (<?= count($cards) ?> cards)</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="include/app.css">
<style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 20px;
    background: #f5f5f5;
}

.stack-container {
    max-width: 1200px;
    margin: 0 auto;
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.stack-header {
    text-align: center;
    margin-bottom: 20px;
}

.stack-title {
    font-size: 24px;
    margin: 0 0 10px 0;
    color: #333;
}

.stack-subtitle {
    font-size: 14px;
    color: #666;
    margin: 5px 0;
}

.cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
    gap: 30px;
    margin-bottom: 20px;
}

.card-wrapper {
    page-break-inside: avoid;
    break-inside: avoid;
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
    font-size: 36px;
    font-weight: bold;
    padding: 15px;
    border: 2px solid <?= $bordercolor ?>;
}

.bingo-table td {
    width: 20%;
    height: 80px;
    text-align: center;
    vertical-align: middle;
    font-size: 28px;
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
    height: 60px;
    width: auto;
}

.card-id-label {
    text-align: center;
    font-weight: bold;
    font-size: 18px;
    margin-bottom: 10px;
    color: #333;
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
    
    .stack-container {
        box-shadow: none;
        padding: 0;
    }
    
    .controls, .info-box, .stack-header {
        display: none !important;
    }
    
    .cards-grid {
        display: block;
    }
    
    .card-wrapper {
        page-break-after: always;
        page-break-inside: avoid;
        margin-bottom: 20px;
    }
    
    /* Print up to 4 cards per page */
    @supports (display: grid) {
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        
        .card-wrapper {
            page-break-after: auto;
        }
        
        .card-wrapper:nth-child(4n) {
            page-break-after: always;
        }
    }
    
    .bingo-table {
        max-width: 100%;
    }
    
    .bingo-table th {
        font-size: 24px;
        padding: 8px;
    }
    
    .bingo-table td {
        height: 50px;
        font-size: 18px;
        cursor: default;
    }
    
    .bingo-table td.free-square img {
        height: 40px;
    }
    
    .card-id-label {
        font-size: 14px;
        margin-bottom: 5px;
    }
}

/* Mobile responsive */
@media (max-width: 600px) {
    .cards-grid {
        grid-template-columns: 1fr;
    }
    
    .bingo-table th {
        font-size: 28px;
        padding: 12px 8px;
    }
    
    .bingo-table td {
        height: 60px;
        font-size: 20px;
    }
    
    .bingo-table td.free-square img {
        height: 50px;
    }
    
    .btn {
        padding: 10px 20px;
        font-size: 14px;
    }
}
</style>
</head>
<body>

<div class="stack-container">
    <div class="stack-header">
        <h1 class="stack-title">🎱 Virtual Bingo Card Stack</h1>
        <div class="stack-subtitle">
            <?= count($cards) ?> card(s) in this stack
        </div>
    </div>
    
    <div class="controls">
        <button onclick="resetAllMarks()" class="btn btn-danger">🔄 Reset All Marks</button>
        <button onclick="window.print()" class="btn btn-secondary">🖨️ Print Cards</button>
    </div>
    
    <div class="info-box">
        <p><strong>💡 How to use:</strong> Click or tap any square to mark it. Your marks are saved automatically and will be restored when you reopen this page. Use the Print button to print up to 4 cards per page.</p>
    </div>
    
    <div class="cards-grid" id="cardsGrid">
        <?php foreach ($cards as $card_info): ?>
        <div class="card-wrapper">
            <div class="card-id-label">Card: <?= htmlspecialchars($card_info['id']) ?></div>
            <table class="bingo-table" data-card-number="<?= $card_info['number'] ?>">
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
                            $number = $card_info['data'][$col][$row]["number"];
                            $is_free = ($number === "Free");
                            $cell_id = "cell-{$card_info['number']}-{$col}-{$row}";
                            ?>
                            <td id="<?= $cell_id ?>" 
                                data-card="<?= $card_info['number'] ?>"
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
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
// Use stack ID as unique identifier for localStorage
const STACK_ID = <?= json_encode($stack_id, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
const STORAGE_KEY = 'bingo_stack_marks_' + STACK_ID;

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
    document.querySelectorAll('.bingo-table td.marked:not(.free-square)').forEach(cell => {
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
function resetAllMarks() {
    if (confirm('Are you sure you want to clear all marks on all cards?')) {
        document.querySelectorAll('.bingo-table td:not(.free-square)').forEach(cell => {
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
