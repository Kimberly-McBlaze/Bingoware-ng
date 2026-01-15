   
   <div class="content-header">
     <h2 class="content-title">👁️ View Cards</h2>
     <p class="content-subtitle">Select a card to view or print all cards</p>
   </div>
   
   <?php 
if (!set_exists()) exit;
else $numbercards = card_number();

// Check if virtual bingo is enabled
include_once("include/virtual_cards.php");
$virtual_enabled = $virtualbingo === 'on';
$has_virtual_cards = $virtual_enabled && has_virtual_stacks();
   ?>
   
   <div class="mb-3">
     <a href="print.php" target="_blank" class="btn btn-primary" id="showAllCardsBtn">
       <span>🖨️ Show All Cards (for printing)</span>
     </a>
     
     <?php if ($has_virtual_cards): ?>
     <div style="margin-top: 1rem;">
       <label style="display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer;">
         <input type="checkbox" id="excludeVirtualCheckbox" style="cursor: pointer;">
         <span>Exclude virtual-player cards from print output</span>
       </label>
       <p style="margin: 0.5rem 0 0 1.75rem; font-size: 0.875rem; opacity: 0.7;">
         Virtual-player cards are assigned to URLs in the Virtual Bingo section
       </p>
     </div>
     <?php endif; ?>
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
   
   <script>
   // Handle exclude virtual cards checkbox
   (function() {
       const checkbox = document.getElementById('excludeVirtualCheckbox');
       const showAllBtn = document.getElementById('showAllCardsBtn');
       
       if (!checkbox || !showAllBtn) return;
       
       // Update the link when checkbox state changes
       checkbox.addEventListener('change', function() {
           if (this.checked) {
               showAllBtn.href = 'print.php?exclude_virtual=1';
           } else {
               showAllBtn.href = 'print.php';
           }
       });
   })();
   </script>
