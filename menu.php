<?php
// Safely validate and encode numberinplay parameter
include_once("include/input_helpers.php");
$numberinplay = validate_int($_GET["numberinplay"] ?? null, 1, 10000);
$query_params = $numberinplay ? '&' . build_query_string(['numberinplay' => $numberinplay]) : '';
?>
<aside class="sidebar">
  <div class="sidebar-header">
    <h2 class="sidebar-title">Menu</h2>
  </div>
  <nav class="nav-menu">
    <a href="index.php?action=generate<?= $query_params ?>" class="nav-button">
      <span class="nav-icon">🎲</span>
      <span>Generate Cards</span>
    </a>
    <a href="index.php?action=view<?= $query_params ?>" class="nav-button">
      <span class="nav-icon">👁️</span>
      <span>View Cards</span>
    </a>
    <a href="index.php?action=play<?= $query_params ?>" class="nav-button">
      <span class="nav-icon">🎮</span>
      <span>Play Bingo</span>
    </a>
    <a href="patterns.php" class="nav-button">
      <span class="nav-icon">🎯</span>
      <span>Winning Patterns</span>
    </a>
    <a href="index.php?action=config<?= $query_params ?>" class="nav-button">
      <span class="nav-icon">⚙️</span>
      <span>Configure</span>
    </a>
  </nav>
</aside>
