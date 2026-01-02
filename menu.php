<aside class="sidebar">
  <div class="sidebar-header">
    <h2 class="sidebar-title">Menu</h2>
  </div>
  <nav class="nav-menu">
    <a href="index.php?action=generate<?= (isset($_GET["numberinplay"]))?'&numberinplay='.$_GET["numberinplay"]:''; ?>" class="nav-button">
      <span class="nav-icon">🎲</span>
      <span>Generate Cards</span>
    </a>
    <a href="index.php?action=view<?= (isset($_GET["numberinplay"]))?'&numberinplay='.$_GET["numberinplay"]:''; ?>" class="nav-button">
      <span class="nav-icon">👁️</span>
      <span>View Cards</span>
    </a>
    <a href="index.php?action=play<?= (isset($_GET["numberinplay"]))?'&numberinplay='.$_GET["numberinplay"]:''; ?>" class="nav-button">
      <span class="nav-icon">🎮</span>
      <span>Play Bingo</span>
    </a>
    <a href="patterns.php" class="nav-button">
      <span class="nav-icon">🎯</span>
      <span>Winning Patterns</span>
    </a>
    <a href="index.php?action=config<?= (isset($_GET["numberinplay"]))?'&numberinplay='.$_GET["numberinplay"]:''; ?>" class="nav-button">
      <span class="nav-icon">⚙️</span>
      <span>Configure</span>
    </a>
  </nav>
</aside>
