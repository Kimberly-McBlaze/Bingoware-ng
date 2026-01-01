	   
	   <div class="content-header">
	     <h2 class="content-title">👁️ View Cards</h2>
	     <p class="content-subtitle">Select a card to view or print all cards</p>
	   </div>
	   
	   <?php 
		if (!set_exists()) exit;
		else $numbercards = card_number();
	   ?>
	   
	   <div class="mb-3">
	     <a href="print.php" target="_blank" class="btn btn-primary">
	       <span>🖨️ Show All Cards (for printing)</span>
	     </a>
	   </div>
	   
	   <div class="card">
	     <div class="card-header">
	       <h3 class="card-title">Select a Card (Total: <?= $numbercards ?>)</h3>
	     </div>
	     <div class="card-body" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(60px, 1fr)); gap: 0.75rem;">
	   <?php
	   for ($i = 0; $i < $numbercards; $i++) {
	   	echo '<a href="view.php?cardnumber='.($i+1).'" target="_blank" class="btn btn-secondary" style="padding: 0.75rem;">'.($i+1).'</a>'."\n";
	   }
	   ?>
	     </div>
	   </div>
