   
   <?php 
   // Handle switching to a new set and auto-generating cards
   if (isset($_GET["switch_to_set"])) {
       $new_setid = $_GET["switch_to_set"];
       
       // Validate setid format
       if (preg_match('/^[a-zA-Z0-9_-]+$/', $new_setid)) {
           // Update setid in config file first
           if (file_exists("config/settings.php")) {
               $filearray = file("config/settings.php");
               $fp = fopen("config/settings.php", "w");
               if ($fp) {
                   foreach ($filearray as $line) {
                       $line = preg_replace("/^(\\\$setid=').*?';/", "$1" . preg_quote($new_setid, '/') . "';", $line);
                       fwrite($fp, $line);
                   }
                   fclose($fp);
                   
                   // Reload to get new setid
                   include("config/settings.php");
                   
                   // If auto_cards is set, pre-fill the form
                   if (isset($_GET["auto_cards"])) {
                       $auto_card_count = intval($_GET["auto_cards"]);
                       if ($auto_card_count > 0 && $auto_card_count <= $MAX_LIMIT) {
                           // Auto-generate immediately
                           restart();
                           generate_cards($auto_card_count, 1); // Use center free square as default
                           echo '<div class="alert alert-success"><strong>✅ Set Switched!</strong><br>Switched to Set ' . htmlspecialchars($setid) . ' and generated ' . $auto_card_count . ' cards!</div>';
                           echo '<div style="margin-top: 1rem;"><a href="index.php?action=play" class="btn btn-primary">▶️ Start Playing</a></div>';
                           // Don't show the form
                           return;
                       }
                   }
               }
           }
       }
   }
   

	   
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
