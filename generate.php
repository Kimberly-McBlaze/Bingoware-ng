	   
	   <?php 
	   $numcard = filter_input(INPUT_POST, 'numcard', FILTER_VALIDATE_INT);
	   if (isset($_POST["submit"]) && $numcard !== false && $numcard !== null && ($numcard>1) && ($numcard<$MAX_LIMIT)) {
	   		restart(); //clears winners and draws
			@unlink("sets/set.".$setid.".dat");
			$freesquare = filter_input(INPUT_POST, 'freesquare', FILTER_VALIDATE_INT);
			if ($freesquare === null || $freesquare === false) $freesquare = 2;
	   		generate_cards($numcard, $freesquare);	   	
		   	echo '<p><img src="images/gc.gif"><br><br><font size="4"><b>'.$numcard. ' cards generated!</b></font></p>';
	   	} else {
	   ?>
	   <p><img src="images/gc.gif"><br><br>(Set ID: <?= $setid; ?>)&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:explain('Set ID')">help?</a></p>
	   <form action="index.php?action=generate" method="post">
	   Enter the number of Bingo cards desired (between 1 and <?= $MAX_LIMIT; ?>):
	   &nbsp;&nbsp;&nbsp;<input type="text" name="numcard" maxlength="5" size="4" onkeypress="return entsub(this.form)"><br>
	   <br>
	   Free Squares Mode: &nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:explain('Free Squares')">help?</a><br>
	   <table border="1"><tr><td>
	   <input type="radio" name="freesquare" value="0">&nbsp;&nbsp;&nbsp;No "Free" Squares
	   <br>
	   <input type="radio" name="freesquare" value="1" checked>&nbsp;&nbsp;&nbsp;"Free" Squares in the center of every card
	   <br>
	   <input type="radio" name="freesquare" value="2">&nbsp;&nbsp;&nbsp;"Free" Squares randomly placed on every card
	   </td></tr></table>
	   
	   &nbsp;&nbsp;&nbsp;<br><input type="submit" value="Generate!" name="submit">
	   
	   </form>
	   
	   <?php
	}
	?>
