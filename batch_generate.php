   
   <?php 
   // Handle batch generation form submission
   $num_sets = filter_input(INPUT_POST, 'num_sets', FILTER_VALIDATE_INT);
   $cards_per_set = filter_input(INPUT_POST, 'cards_per_set', FILTER_VALIDATE_INT);
   $base_setid = filter_input(INPUT_POST, 'base_setid', FILTER_SANITIZE_SPECIAL_CHARS);
   
   if (isset($_POST["submit"]) && $num_sets !== false && $num_sets !== null && 
       $cards_per_set !== false && $cards_per_set !== null) {
       
       // Validate inputs
       if ($num_sets < 1 || $num_sets > 100) {
           echo '<div class="alert alert-error"><strong>❌ Error!</strong><br>Number of sets must be between 1 and 100.</div>';
       } else if ($cards_per_set < 1 || $cards_per_set > $MAX_LIMIT) {
           echo '<div class="alert alert-error"><strong>❌ Error!</strong><br>Cards per set must be between 1 and '.$MAX_LIMIT.'.</div>';
       } else {
           // Get freesquare mode
           $freesquare = filter_input(INPUT_POST, 'freesquare', FILTER_VALIDATE_INT);
           if ($freesquare === null || $freesquare === false) $freesquare = 1;
           
           // Use provided base setid or current setid
           if (empty($base_setid)) {
               $base_setid = $setid;
           }
           
           // Validate base setid format
           if (!preg_match('/^[a-zA-Z0-9_-]+$/', $base_setid)) {
               echo '<div class="alert alert-error"><strong>❌ Error!</strong><br>Invalid base SET ID. Use only letters, numbers, hyphens, and underscores.</div>';
           } else {
               // Call batch generation function
               $results = batch_generate_sets($num_sets, $cards_per_set, $freesquare, $base_setid);
               
               if ($results['success']) {
                   echo '<div class="alert alert-success"><strong>✅ Batch Generation Complete!</strong><br>';
                   echo 'Successfully created ' . count($results['sets']) . ' set(s) with ' . $cards_per_set . ' cards each.</div>';
                   
                   // Display results table
                   echo '<div class="card">';
                   echo '<div class="card-header"><h3 class="card-title">Generated Sets</h3></div>';
                   echo '<div class="card-body">';
                   echo '<table class="modern-table" style="width: 100%;">';
                   echo '<thead><tr><th>Set ID</th><th>Cards Generated</th><th>Status</th></tr></thead>';
                   echo '<tbody>';
                   
                   foreach ($results['sets'] as $set_result) {
                       $status = $set_result['success'] ? '✅ Success' : '❌ Failed';
                       $status_class = $set_result['success'] ? 'success' : 'error';
                       echo '<tr>';
                       echo '<td><strong>' . htmlspecialchars($set_result['setid']) . '</strong></td>';
                       echo '<td>' . $set_result['cards'] . '</td>';
                       echo '<td><span class="badge badge-' . $status_class . '">' . $status . '</span></td>';
                       echo '</tr>';
                   }
                   
                   echo '</tbody></table>';
                   echo '</div></div>';
                   
                   echo '<div style="margin-top: 1rem;">';
                   echo '<a href="index.php" class="btn btn-primary">← Back to Home</a> ';
                   echo '<a href="index.php?action=batch_generate" class="btn btn-secondary">Generate More Sets</a>';
                   echo '</div>';
               } else {
                   echo '<div class="alert alert-error"><strong>❌ Error!</strong><br>' . htmlspecialchars($results['error']) . '</div>';
               }
           }
       }
   } else {
   ?>
   <div class="content-header">
     <h2 class="content-title">🎲 Batch Generate Card Sets</h2>
     <p class="content-subtitle">Create multiple Bingo card sets in one operation</p>
   </div>
   
   <div class="card" style="margin-bottom: 1.5rem;">
     <div class="card-body">
       <p><strong>ℹ️ About Batch Generation:</strong></p>
       <p>This feature allows you to create multiple card sets at once. Each set will have its own unique SET ID and the specified number of cards.</p>
       <ul style="margin-left: 1.5rem; margin-top: 0.5rem;">
         <li>Enter the number of sets you want to create (1-100)</li>
         <li>Enter how many cards each set should have (1-<?= $MAX_LIMIT ?>)</li>
         <li>Provide a base SET ID (optional - defaults to current: <?= htmlspecialchars($setid) ?>)</li>
         <li>If creating multiple sets, they will be numbered as: <code>Base-1</code>, <code>Base-2</code>, etc.</li>
       </ul>
     </div>
   </div>
   
   <form action="index.php?action=batch_generate" method="post" class="modern-form">
     <div class="form-group">
       <label class="form-label">
         Base SET ID (optional):
         <a href="javascript:explain('Set ID')" class="help-icon">help?</a>
       </label>
       <input type="text" name="base_setid" class="form-input" 
              placeholder="Leave empty to use current: <?= htmlspecialchars($setid) ?>" 
              pattern="[a-zA-Z0-9_-]+" 
              title="Only letters, numbers, hyphens, and underscores allowed">
       <small style="display: block; margin-top: 0.25rem; opacity: 0.7;">
         Leave empty to use current SET ID (<?= htmlspecialchars($setid) ?>). 
         If creating multiple sets, numbers will be appended (e.g., Base-1, Base-2).
       </small>
     </div>
     
     <div class="form-group">
       <label class="form-label">
         Number of sets to create:
       </label>
       <input type="number" name="num_sets" class="form-input" 
              min="1" max="100" value="10" 
              placeholder="Enter number of sets (1-100)" required>
       <small style="display: block; margin-top: 0.25rem; opacity: 0.7;">
         How many different card sets do you want to create?
       </small>
     </div>
     
     <div class="form-group">
       <label class="form-label">
         Cards per set:
       </label>
       <input type="number" name="cards_per_set" class="form-input" 
              min="1" max="<?= $MAX_LIMIT ?>" value="100"
              placeholder="Enter cards per set (1-<?= $MAX_LIMIT ?>)" required>
       <small style="display: block; margin-top: 0.25rem; opacity: 0.7;">
         Each set will contain this many cards.
       </small>
     </div>
     
     <div class="form-group">
       <label class="form-label">
         Free Squares Mode:
         <a href="javascript:explain('Free Squares')" class="help-icon">help?</a>
       </label>
       <div class="radio-group">
         <label class="radio-option">
           <input type="radio" name="freesquare" value="0">
           <span>No "Free" Squares</span>
         </label>
         <label class="radio-option">
           <input type="radio" name="freesquare" value="1" checked>
           <span>"Free" Squares in the center of every card</span>
         </label>
         <label class="radio-option">
           <input type="radio" name="freesquare" value="2">
           <span>"Free" Squares randomly placed on every card</span>
         </label>
       </div>
     </div>
     
     <div class="alert alert-info" style="margin-bottom: 1rem;">
       <strong>⚠️ Note:</strong> Batch generation may take a moment depending on the number of sets and cards. 
       Each set will be created with its own SET ID and card data files.
     </div>
     
     <button type="submit" name="submit" class="btn btn-primary btn-lg">
       <span>🎲 Generate Sets</span>
     </button>
     
     <a href="index.php" class="btn btn-secondary" style="margin-left: 0.5rem;">
       <span>← Cancel</span>
     </a>
   </form>
   
   <?php
   }
   ?>
