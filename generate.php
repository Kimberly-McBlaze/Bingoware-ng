	   
	   <?php 
	   $numcard = filter_input(INPUT_POST, 'numcard', FILTER_VALIDATE_INT);
	   if (isset($_POST["submit"]) && $numcard !== false && $numcard !== null && ($numcard>=1) && ($numcard<=$MAX_LIMIT)) {
	   		restart(); //clears winners and draws
			// Remove old set file if it exists
			$set_file = "sets/set.".$setid.".dat";
			if (file_exists($set_file)) {
				if (!unlink($set_file)) {
					error_log("Failed to delete $set_file");
				}
			}
			$freesquare = filter_input(INPUT_POST, 'freesquare', FILTER_VALIDATE_INT);
			if ($freesquare === null || $freesquare === false) $freesquare = 2;
	   		generate_cards($numcard, $freesquare);
		   	echo '<div class="alert alert-success"><strong>✅ Success!</strong><br>'.$numcard. ' cards have been generated successfully!</div>';
	   	} else {
	   ?>
	   <div class="content-header">
	     <h2 class="content-title">🎲 Generate Cards</h2>
	     <p class="content-subtitle">Set ID: <?= $setid; ?> <a href="javascript:explain('Set ID')" class="help-icon">help?</a></p>
	   </div>
	   <form action="index.php?action=generate" method="post" class="modern-form">
	     <div class="form-group">
	       <label class="form-label">
	         Number of Bingo cards (between 1 and <?= $MAX_LIMIT; ?>):
	       </label>
	       <input type="number" name="numcard" class="form-input" min="1" max="<?= $MAX_LIMIT; ?>" placeholder="Enter number of cards" required>
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
	     
	     <button type="submit" name="submit" class="btn btn-primary btn-lg">
	       <span>🎲 Generate Cards</span>
	     </button>
	   </form>
	   
	   <?php
	}
	?>
