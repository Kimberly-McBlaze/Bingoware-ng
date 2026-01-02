	   <div class="content-header">
	     <h2 class="content-title">🎮 Play Bingo</h2>
	     <p class="content-subtitle">Draw numbers and track winning cards</p>
	   </div>
	   
	   <?php 
		if (!set_exists()) exit;
		else $numbercards = card_number();
		
		//The number in play is used if not all cards are distributed
		//If no number is registered, or if the number exceeds the maximum,
		//it is reverted to the maximum, meaning all cards generated for that set are
		//in play.
		$numberinplay=$numbercards; 
		
		$numberinplay_input = filter_input(INPUT_POST, 'numberinplay', FILTER_VALIDATE_INT);
		if ($numberinplay_input !== false && $numberinplay_input !== null && $numberinplay_input > 0 && $numberinplay_input <= $numbercards) {
			$numberinplay = $numberinplay_input;
		}
	   ?>
	   
	   <form name="random" method="post" action="index.php?action=play&numberinplay=<?= $numberinplay;?>" onSubmit="return validate_number(<?= $maxColumnNumber; ?>)">
	   
	   <div style="display: grid; grid-template-columns: 350px 1fr; gap: 2rem; margin-bottom: 2rem;">
	     <div>
	       <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-align: center; padding: 2rem; margin-bottom: 1.5rem;">
	         <?php 
	   		echo '<input type="hidden" name="letters" value="'.$bingoletters[0].$bingoletters[1].$bingoletters[2].$bingoletters[3].$bingoletters[4].'">';
	   		if (isset($_POST["gimme"]) && $drawmode=="automatic") {
	   			echo '<div style="font-size: 3rem; font-weight: bold; margin: 1rem 0;">'.random_number($numberinplay).'</div>';
	   		} else if ($drawmode=="automatic") {
	   			echo '<div style="font-size: 3rem; font-weight: bold; margin: 1rem 0;">???</div>';
	   		}
	   		
	   		if (isset($_POST["gimme"]) && $drawmode=="manual") submit_number($_POST["enterednumber"],$numberinplay);
	   		
	   		if (isset($_GET["restart"])) restart();
	   		$draws=load_draws();
	   		$drawsCount = ($draws !== null) ? count($draws) : 0;
	   		
	   		if ($drawmode=="manual" && ($drawsCount<$maxNumber)) {
	   			echo '<div style="margin: 1rem 0;"><label style="display: block; margin-bottom: 0.5rem;">Enter a number:</label>';
	   			echo '<input type="text" name="enterednumber" size="8" maxlength="3" class="form-input" style="text-align: center; font-size: 1.25rem;" placeholder="e.g. '.$bingoletters[0].'4" autofocus></div>';
	   		} else {
	   			echo '<input type="hidden" name="enterednumber" value="'.$bingoletters[0].'1">';
	   		}
	   		?>
	   		
	   		<?php 
	   		if ($drawsCount<$maxNumber) {
	   			if ($drawmode=="automatic") {
	   				echo '<button name="gimme" type="submit" class="btn btn-success btn-lg" style="width: 100%; margin-top: 1rem;">🎲 Give Me a Number!</button>';
	   			} else {
	   				echo '<button name="gimme" type="submit" class="btn btn-success btn-lg" style="width: 100%; margin-top: 1rem;">✅ Enter Number</button>';
	   			}
	   		} else {
	   			echo '<button name="empty" type="button" class="btn btn-secondary btn-lg" style="width: 100%; margin-top: 1rem;" disabled>All numbers drawn!</button>';
	   		}
	   		?>
	       </div>
	       
	       <button name="restart" type="button" class="btn btn-warning" style="width: 100%;" onClick="RestartConfirmation(<?= $numberinplay;?>);">
	         🔄 Restart Game
	       </button>
	       
	       <?php 
	       // Winner Indicator
	       $total_winners = count_total_winners();
	       $indicator_color = ($total_winners > 0) ? 'var(--color-success)' : 'var(--text-muted)';
	       $indicator_icon = ($total_winners > 0) ? '🏆' : 'ℹ️';
	       $indicator_text = ($total_winners > 0) ? "Winners: $total_winners" : "No winners yet";
	       ?>
	       <div class="winner-indicator" style="margin-top: 1rem; padding: 1rem; background: var(--bg-secondary); border: 2px solid <?= $indicator_color; ?>; border-radius: 8px; text-align: center; box-shadow: var(--shadow-sm);">
	         <div style="font-size: 1.5rem; margin-bottom: 0.25rem;"><?= $indicator_icon; ?></div>
	         <div style="font-weight: bold; font-size: 1.125rem; color: <?= $indicator_color; ?>;"><?= $indicator_text; ?></div>
	       </div>
	     </div>
	     
	     <div>
	       <div class="card mb-3">
	         <div class="card-header">
	           <h3 class="card-title" style="display: flex; align-items: center; gap: 0.5rem;">
	             <span>Number of cards in play:</span>
	             <input name="numberinplay" type="number" min="1" max="<?= $numbercards; ?>" value="<?= $numberinplay; ?>" class="form-input" style="width: 80px; display: inline-block;">
	             <a href="javascript:explain('Cards in play')" class="help-icon">help?</a>
	           </h3>
	           <p style="color: var(--text-muted); font-size: 0.875rem; margin-top: 0.5rem;">(This set has a maximum of <?= $numbercards; ?> cards)</p>
	         </div>
	         <div class="card-body">
	           <p style="color: var(--color-warning); font-weight: 600; margin-bottom: 1rem;">
	             Numbers drawn so far (<?= $drawsCount; ?> of <?= $maxNumber; ?>):
	           </p>
	           <?php
	           // Modern draws table
	           $draws = load_draws();
	           echo '<div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 0.5rem;">';
	           if ($draws != null) {
	             $number = count($draws);
	             for ($i = 0; $i < $number; $i++) {
	               echo '<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 0.75rem; border-radius: 8px; text-align: center; font-weight: bold; font-size: 1.125rem;">';
	               echo find_letter($draws[$i]).$draws[$i];
	               echo '</div>';
	             }
	           } else {
	             echo '<div style="grid-column: 1 / -1; text-align: center; color: var(--text-muted); padding: 2rem;">No numbers drawn yet</div>';
	           }
	           echo '</div>';
	           ?>
	         </div>
	       </div>
	     </div>
	   </div>
	   
	   <div class="card">
	     <div class="card-header">
	       <h3 class="card-title" style="color: var(--color-success);">🏆 Winning Card Numbers</h3>
	       <p style="color: var(--text-muted); font-size: 0.875rem;">(New winners shown in red)</p>
	     </div>
	     <div class="card-body">
	       <?php winners_table(); ?>
	     </div>
	   </div>
	   
	   </form>
