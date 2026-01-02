<?php

include ("config/settings.php");

/** set_exists()
* This function tests the existence of a card set,
* corresponding to the $setid in the config file
*/
function set_exists() {
	global $setid;
	return (file_exists("sets/set.".$setid.".dat"));
}

/** card_number()
* This function returns the number of cards found in the card set
* user must verify that set exists before calling this function.
* This function will reload the set from the disk.  Use
* count($set) if you already opened and loaded the set to fasten
* the process
*/
function card_number() {
	$set=load_set();
	if (is_array($set))
		return count($set);
}

/** generate_cards()
* This function generate the appropriate number of cards
* All cards are randomly generated, and have a Free square
* in the center.  The function saves the newly created set.
* This function will overwrite an existing set with the same
* setid.
*/
function generate_cards($numbercards,$freesquare) {
	global $maxColumnNumber;

	for ($cardnumber = 0; $cardnumber <$numbercards; $cardnumber++) {

		// The next two variables are only used when freesquare == 2 (random location)
		$randcolumn = rand(0,4);
		$randrow = rand(0,4);

		for ($column = 0; $column<5; $column++) {
			for ($row = 0; $row<5; $row++) {
				if (($column==2) && ($row == 2) && ($freesquare==1)) {  //free
					$set[$cardnumber][$column][$row]["number"] = "Free";  //free
					$set[$cardnumber][$column][$row]["checked"] = true;  //free
				}
				else if (($column==$randcolumn) && ($row==$randrow) && ($freesquare==2)){
					$set[$cardnumber][$column][$row]["number"] = "Free";  //free
					$set[$cardnumber][$column][$row]["checked"] = true;  //free
				}
				else {
					//to ensure we do not have the same number twice in one column
					$numberexists = true; //loop entry
					while ($numberexists) {
						$random = rand(1+$column*$maxColumnNumber,($column+1)*$maxColumnNumber);

						$numberexists =false; //assume it is not repeated until it is found
						for ($checker=0; $checker<$row; $checker++)
						if ($set[$cardnumber][$column][$checker]["number"] == $random) $numberexists=true;
					}

					$set[$cardnumber][$column][$row]["number"] = $random;
					$set[$cardnumber][$column][$row]["checked"] = false;
				}
			} //row
		} //column
	}	//cardnumbber

	save_set($set);
}

/** display_card()
* This function draws an HTML table for one or four cards.
* By setting the $fourperpage parameter to 1, the tables
* are drawn smaller.
*/
function display_card ($cardnumber,$fourperpage=false,$name_on=false,$rules="") {
	global $bingoletters, $setid;
	global $headerfontcolor, $headerbgcolor, $mainfontcolor, $mainbgcolor, $selectedfontcolor, $selectedbgcolor, $bordercolor;

	$set = load_set();  //normal cards
	if ($name_on) $names = load_name_file();
	if (!isset($names)) $name_on = false; //turn off 'card name' feature if names could not be loaded
	
	if (is_array($set)) {
		$maxloop = ($fourperpage)?4:1; //if we attempt to print four per page, need to loop 4 times
		for ($c=0; $c< $maxloop; $c++) {
			$cardshown = $cardnumber+$c;
			
			//if four per page, generate big four cell table
			if ($fourperpage) {
				switch ($c) {
					case 0: echo '<center><table width=100% border=0 cellpadding=5><tr valign=top><td width=50%>';
							break;
					case 1:
					case 3: echo '<td width=50%>';
							break;
					case 2: echo '<tr><td>';
							break;
				}
			}
			
			if (isset($set[$cardshown])) {  //only if the card actually exists!
				//start printing card
				echo '<center><table width='.(($fourperpage)?'265':'625').' border=1 cellpadding=0 BORDERCOLOR="'.$bordercolor.'"><tr height='.(($fourperpage)?'50':'110').'>';
				//header
				for ($column = 0; $column<5; $column++) {
					echo '<td  width=20% align="center" bgcolor="'.$headerbgcolor.'"><b><font size="'.(($fourperpage)?'+3':'+7').'" color="'.$headerfontcolor.'">'.$bingoletters[$column].'</font></b></td>';
				}
				echo "</tr>";
		
				//table itself
				for ($row = 0; $row<5; $row++) {
					echo "<tr height=".(($fourperpage)?"50":"110").">";
		
					for ($column = 0; $column<5; $column++) { //column has to be inner loop due to HTML table
						echo "\n<td align=center bgcolor=\"".($set[$cardshown][$column][$row]["checked"]?$selectedbgcolor:$mainbgcolor)."\">";
						if ($fourperpage) echo '<font size=+3>';
						else echo '<font size=+5 color="'.($set[$cardshown][$column][$row]["checked"]?$selectedfontcolor:$mainfontcolor).'">';
						if ($set[$cardshown][$column][$row]["number"]=="Free") {
							echo '<img src="images/star.gif"'.(($fourperpage)?'height=45':'height=105') .' align="middle" >';
						} else {
							echo $set[$cardshown][$column][$row]["number"].'</font></td>';
						}
					}
					echo "</tr>";
				} //table itself
				echo "</table><table width=".(($fourperpage)?"65%>":"45%>");
				printf("<tr><td align=left colspan=2><b><font size=-1>%s<b></td><td align=right colspan=3><font size=-2> Card Number: %s%'04s </font></td></tr>",(($name_on && $cardshown < count($names))?$names[$cardshown]:''),$setid,$cardshown+1);
				echo "</table></center>";
			}
			//if four per page, terminate big four cell table
			if ($fourperpage) {
				switch ($c) {
					case 0:
					case 2: echo '</td>';
							break;
					case 1: echo '<br><br></td></tr>';
							break;
					case 3: echo '</td></tr></table>';
							break;
				} //switch
			} //if four per page
		} // for loop
		
		if ($rules=="on") {
			echo '<p style="page-break-before: always">';
			readfile ("config/rules.html");
			
		}
	}
	else echo "set could not be opened";
}

/** random_number()
* This function generates a new random number.
* The number is unique: the function checks the draws.xx.dat file
* and loops until a new number randomly generated.
* The file draws.xx.dat is resorted and rewritten before the end of the function
* so it includes the new number.
* The function calls mark_cards() to have the new number set in all
* cards, and call check_bingo() to update the winners' list.
*/
function random_number($numberinplay) {
	global $bingoletters, $maxColumnNumber;

	$draws = load_draws();

	if ($draws!=null){
		$total=count($draws);
	} else $total=0;

	$samenumbertwice= true;
	while ($samenumbertwice) {

		$col = rand(0,4);
		$row = rand(1,$maxColumnNumber);
		$num = $maxColumnNumber*$col+$row;

		$samenumbertwice=false;

		if ($total >0 ) //no need to check if we have no numbers yet
		for ($i=0;$i < $total; $i++)
		if ($num==$draws[$i]) $samenumbertwice=true;
	}
	$draws[$total]=$num;
	save_draws($draws);

	mark_cards($col,$num);
	check_bingo($numberinplay);
	return $bingoletters[$col].$num;
}

/** submit_number()
* This function is used in manual mode to enter a number into the draw list.
* The file draws.xx.dat is resorted and rewritten before the end of the function
* so it includes the new number.
* The function calls mark_cards() to have the new number set in all
* cards, and call check_bingo() to update the winners' list.
*/
function submit_number($number,$numberinplay) {
	global $bingoletters;
	$draws = load_draws();

	if ($draws!=null){
		$total=count($draws);
	} else $total=0;

	$samenumbertwice=false;

	//extract the number out of the input string
	$convert = intval(substr($number,1));

	if ($total >0 ) //no need to check if we have no numbers yet
	for ($i=0;$i < $total; $i++)
	if ($convert==$draws[$i]) $samenumbertwice=true;

	if (!$samenumbertwice) {

		$draws[$total]=$convert;
		save_draws($draws);

		mark_cards(array_search(strtoupper(substr($number,0,1)),$bingoletters),$convert);
		check_bingo($numberinplay);
	} else echo '<font color="#ff3300"><b>This number has already been entered, please verify and enter a new number</b><br><hr></font>';
}


/** mark_cards()
* This function will "color" a given number on all cards
* that possess this number.  The card set will be resaved
* before the end of the function.
* This function should be called before check_bingo() which
* determine winning cards.
*/
function mark_cards($col, $num) { //col provided here to speed up search in the right column only

	$set = load_set();
	$numcards=count($set);

	for ($n=0; $n<$numcards;$n++) {
		for ($r=0; $r<=4; $r++) { //go down the given column
			if ($set[$n][$col][$r]["number"]==$num)
			$set[$n][$col][$r]["checked"]=true;
		}
	}
	save_set($set);
}

/** check_bingo()
* This function goes through the complete set and attempts to find
* winning cards.
* Winning cards are cards that match the current winning pattern.
* All cards are saved to the file winners.xx.dat and the file
* is re-written each time.
* The array tracks the success of all winning patterns for each card.
* Each card can be a winner in many patterns at the same time
*/
function check_bingo ($numberinplay) {
	global $winningpatternarray;
	global $setid;

	$set=load_set();
	$numcards = count($set);
	
	// Load patterns from new JSON store
	$jsonPatterns = load_patterns_json();
	
	// For backward compatibility, also load old patterns
	$winningset = load_winning_patterns();

	$new_winners = load_new_winners(); //load the latest winner array

	if ($new_winners<>null) save_old_winners($new_winners); //saves the current list of winners as the old one

	@unlink("data/new_winners.".$setid.".dat"); //erases the new winners file

	//if a numberinplay is given that is smaller than the the number
	// of cards in the set, the rest of the cards are not verified.
	// because of multiple winning patterns, each card must be verified whether or not it was a previous winner

	for ($n=0; $n<min($numberinplay,$numcards);$n++) {  //checking each card

		// Check pattern 0 (Normal) if enabled
		$p = 0;
		if ($winningpatternarray[$p] == "on") {
			if (!isset($new_winners[$n][$p]) || !$new_winners[$n][$p]) {
				//normal bingo - check rows, columns, diagonals
				$winner = false;
				
				for ($c=0; $c<5; $c++) {
					$rowbingo=true; //assume there is bingo in rows and prove wrong
					$colbingo=true; //assume there is bingo in columns and prove wrong
					for ($r=0; $r<5;$r++) {
						if (!$set[$n][$c][$r]["checked"]) $colbingo=false;
						if (!$set[$n][$r][$c]["checked"]) $rowbingo=false;
					}
					if ($rowbingo||$colbingo){
						$winner = true;
						break;
					}
				}
				
				//if it is still not a winner, check the diagonals
				if (!$winner) { 
					$bingod1=true; //assume there is bingo in diagonals, prove wrong
					$bingod2=true;
					for ($d=0; $d<5 ; $d++) {
						if (!$set[$n][$d][$d]["checked"]) $bingod1=false;
						if (!$set[$n][$d][4-$d]["checked"]) $bingod2=false;
					}
					if ($bingod1||$bingod2) {
						$winner = true;
					}
				}
				
				$new_winners[$n][0] = $winner;
			}
		} else {
			$new_winners[$n][0] = false;
		}
		
		// Check patterns from JSON store (patterns 1-N)
		foreach ($jsonPatterns as $pattern) {
			if (!$pattern['enabled']) {
				$p = $pattern['id']; // Use pattern ID as index
				$new_winners[$n][$p] = false;
				continue;
			}
			
			$p = $pattern['id'];
			
			// Skip if already won
			if (isset($new_winners[$n][$p]) && $new_winners[$n][$p]) {
				continue;
			}
			
			// Check if card matches this pattern
			$winner = true;
			foreach ($pattern['mask'] as $cell) {
				list($c, $r) = $cell;
				if (!$set[$n][$c][$r]["checked"]) {
					$winner = false;
					break;
				}
			}
			
			$new_winners[$n][$p] = $winner;
		}

	} //for each card


	//refresh the new winner list
	save_new_winners($new_winners);

}

/** restart()
* This function allows one to restart the game mode.
* Current list of winners will be erased, as well as all drawn numbers.
* The card set will remain untouched, but all "colours" will be reset, except
* for the free square. Very useful if you are using the same set of bingo cards for
* several games.
*/
function restart() { //erases winners, draws, and clears all cards but keeps numbers
	global $setid;

	@unlink ("data/old_winners.".$setid.".dat");
	@unlink ("data/new_winners.".$setid.".dat");
	@unlink("data/draws.".$setid.".dat");

	if (set_exists()) {
		$set = load_set();
		$numcards=count($set);

		for ($n=0; $n<$numcards;$n++) {
			for ($c=0; $c<5; $c++) {
				for ($r=0; $r<5; $r++) { //go down the column
					$set[$n][$c][$r]["checked"]=($set[$n][$c][$r]["number"]=="Free"); //don't forget the free

				}
			}
		}
		save_set($set);
	}

}

/** draws_table()
* This function generates an HTML table of all numbers drawn.
* It is used in game mode on the right side. The file containing the
* numbers drawn (draws.xx.dat) is already sorted.
*/
function draws_table() { //table of all numbers drawn
	$draws = load_draws();

	echo '<table width="100%" border=1 cols=5><tr>';
	if ($draws!=null) {
		$number = count($draws);

		for ($i =0; $i<$number; $i++) {
                  echo '<td align=center width="20%">';
                  	echo '<table border="0" cellpadding="0" cellspacing="0" width="40" height="40"><tr>';
					echo '<td align=center width="20%" background="images/ball.gif"><b>'.find_letter($draws[$i]).$draws[$i].'</b></td>';
                  	echo "</tr></table>";
                  echo '</td>';
			if (($i+1)%5==0) echo "</tr><tr>";
		}

	} else echo "<td>No numbers drawn yet</td>";
	echo '</tr></table>';
}

/** winners_table()
* This function generates an HTML table of all winning cards.
* It is used in game mode at the bottom.
* Each winning card number is displayed with its view link.
*/
function winners_table() {
	global $patternkeywords;
	global $winningpatternarray;

	$winners = load_new_winners();
	$old_winners = load_old_winners(); //to be indicated in a different color
	
	// Load patterns from JSON store
	$jsonPatterns = load_patterns_json();
	
	// Build a combined list of patterns to display
	$displayPatterns = [];
	
	// Add pattern 0 (Normal) if enabled
	if ($winningpatternarray[0] == "on") {
		$displayPatterns[] = [
			'id' => 0,
			'name' => 'Normal',
			'enabled' => true
		];
	}
	
	// Add patterns from JSON store
	foreach ($jsonPatterns as $pattern) {
		if ($pattern['enabled']) {
			$displayPatterns[] = $pattern;
		}
	}
	
	// Sort by ID descending for display (highest first)
	usort($displayPatterns, function($a, $b) {
		return $b['id'] - $a['id'];
	});

	echo '<table width="100%" border=1><tr>';

	foreach ($displayPatterns as $pattern) {
		$patternId = $pattern['id'];
		$patternName = $pattern['name'];
		
		//write the title of the winning pattern
		echo '<td nowrap valign="top" align=center><font size="1"><b>'.htmlspecialchars($patternName).'</b></font><br>';

		//Place graphic to match winning pattern
		switch ($patternName) {
			case 'Normal':
				echo '<img src="images/nc.gif">';
				break;
			case 'Four Corners':
				echo '<img src="images/fc.gif">';
				break;
			case 'Cross-Shaped':
				echo '<img src="images/cs.gif">';
				break;
			case 'T-Shaped':
				echo '<img src="images/ts.gif">';
				break;
			case 'X-Shaped':
				echo '<img src="images/xs.gif">';
				break;
			case '+ Shaped':
				echo '<img src="images/ps.gif">';
				break;
			case 'Z-Shaped':
				echo '<img src="images/zs.gif">';
				break;
			case 'N-Shaped':
				echo '<img src="images/ns.gif">';
				break;
			case 'Box Shaped':
				echo '<img src="images/bs.gif">';
				break;
			case 'Square Shaped':
				echo '<img src="images/ss.gif">';
				break;
			case 'Blackout (Full Card)':
			case 'Full Card':
				echo '<img src="images/ffc.gif">';
				break;
			default:
				// For custom patterns, show a generic icon or no icon
				echo '<span style="font-size: 40px;">🎯</span>';
				break;
		}

		echo '</td><td><table cols=12><tr>';

		$wincounter = 0;  // counts the number of winning cards per pattern for line return
		// and enables the No winners yet! msg for each category
		if ($winners!=null) {

			for ($i =0; $i<count($winners); $i++) {  //cycle through all cards
				if (isset($winners[$i][$patternId]) && $winners[$i][$patternId]) {
					$wincounter++;

					//if this card pattern combinations was already true, black, else red.					
					$color = (isset($old_winners[$i][$patternId]) && $old_winners[$i][$patternId])? "#000000":"#ff5555";

					//Resize card numbers to fit background image
					if ($i<100) $font_size="3";
					else if ($i<1000) $font_size="2";
					else $font_size="1";

					echo '<td align=center width="23" height="25" background="images/ubb2.gif"><a href="view.php?cardnumber='.($i+1).'" target=_blank><font size="'.$font_size.'" color="'.$color.'">'.($i+1).'</font></a></td>';
					if (($wincounter)%12==0) echo "</tr><tr>";
				}
			}
		}
		if ($wincounter==0) echo "<td>No winners yet</td>";
		echo '</tr></table></td></tr>';
	}
	echo '</tr></table>';
}



/** find_letter()
* This function returns the letter associated with the
* bingo number passed as a parameter (1 to 75): B, I, N, G or O
*/
function find_letter($num) {
	global $bingoletters, $maxColumnNumber;
	return $bingoletters[intval(($num-1) /$maxColumnNumber)];
}


/** load_set()
* This function attemps to load the card set
* from the file set.xx.dat where xx is the setid.
*/
function load_set() {
	global $setid;
	if (set_exists()) {
		$filearray = file("sets/set.".$setid.".dat");
		for ($i=0; $i< $filearray[0]; $i++) { //first row is number of rows
			$set[$i] = unserialize(trim($filearray[$i+1]), ['allowed_classes' => false]);
		}
		return $set;
	} else {
		echo "set could not be loaded";
		return null;
	}
}

/** save_set()
* This function attemps to save the card set
* to the file set.xx.dat where xx is the setid.
* Error msgs were removed because the demo
* on sourceforge.net cannot save files.
* The file is written by serializing each card onto a different line
* Serializing the whole set table also works, but the file
* is a lot more difficult to observe with a text editor.
*/
function save_set(&$set) {
	global $setid;
	$numcards = count($set);

	// Ensure the sets directory exists
	if (!file_exists("sets")) {
		@mkdir("sets", 0755, true);
	}

	if (@$fp = fopen("sets/set.".$setid.".dat","w")) {

		fwrite($fp,$numcards."\n");
		for ($i =0; $i<$numcards; $i++) {
			fwrite($fp, serialize($set[$i])."\n");  //one card per row
		}
		fclose($fp);
		return true;
	}
}

/** load_draws()
* This function attemps to load the series of numbers drawn
* from the file draws.xx.dat where xx is the setid.
*/
function load_draws() {
	global $setid;
	if (@file_exists("data/draws.".$setid.".dat")){
		$filearray=file("data/draws.".$setid.".dat");
		$draws = unserialize(trim($filearray[0]), ['allowed_classes' => false]);
		return $draws;
	} else return null;
}

/** save_draws()
* This function attemps to save the series of numbers drawn
* to the file draws.xx.dat where xx is the setid.
* Error msgs were removed because the demo
* on sourceforge.net cannot save files.
* The file is written by serializing the whole
* draws table, once sorted.
*/
function save_draws(&$draws) {
	global $setid;

	sort($draws);
	if (@$fp=fopen("data/draws.".$setid.".dat","w")) {
		fwrite($fp, serialize($draws));
		fclose($fp);
	}
}

/** load_old_winners()
* This function attemps to load the series of winning card numbers
* from the file old_winners.xx.dat where xx is the setid.
*/
function load_old_winners() {
	global $setid;
	if (@file_exists("data/old_winners.".$setid.".dat")){
		$filearray=file("data/old_winners.".$setid.".dat");
		$winners = unserialize(trim($filearray[0]), ['allowed_classes' => false]);
		return $winners;
	} else return null;
}

/** save_old_winners()
* This function attemps to save the series of winning cards
* to the file old_winners.xx.dat where xx is the setid.
* Error msgs were removed because the demo
* on sourceforge.net cannot save files.
* The file is written by serializing the whole
* winners table.
*/
function save_old_winners(&$winners) {
	global $setid;

	if (@$fp=fopen("data/old_winners.".$setid.".dat","w")) {
		fwrite($fp, serialize($winners));
		fclose($fp);
	}
}

/** load_new_winners()
* This function attemps to load the series of new winning card numbers
* from the file new_winners.xx.dat where xx is the setid.
*/
function load_new_winners() {
	global $setid;
	if (@file_exists("data/new_winners.".$setid.".dat")){
		$filearray=file("data/new_winners.".$setid.".dat");
		$new_winners = unserialize(trim($filearray[0]), ['allowed_classes' => false]);
		return $new_winners;
	} else return null;
}

/** save_new_winners()
* This function attemps to save the series of new winning cards
* to the file new_winners.xx.dat where xx is the setid.
* Error msgs were removed because the demo
* on sourceforge.net cannot save files.
* The file is written by serializing the table containing the new
* winners table.
*/
function save_new_winners(&$new_winners) {
	global $setid;

	if (@$fp=fopen("data/new_winners.".$setid.".dat","w")) {
		fwrite($fp, serialize($new_winners));
		fclose($fp);
	}
}

/** load_name_file()
* This function attempts to load the word list (wordlist.txt)
*/
function load_name_file() {

	global $setid;
	if (file_exists("config/names.txt")) {
		$filearray = file("config/names.txt");

		for ($i=0; $i<count($filearray); $i++)
		if ($i==(count($filearray)-1)) {
			$names[$i]=$filearray[$i];
		}
		else $names[$i] = substr($filearray[$i],0,strlen($filearray[$i])-2);
		return $names;
	} else {
		echo "Name file could not be loaded";
		return null;
	}
}

/** load_winning_patterns()
* This function attemps to load the winning patterns set (winningpatterns.dat) which contains
* all winning patterns, with the exception of the normal winning pattern (pattern 0)
* (any row, any column, any diagonal).  The set is loaded when the user wishes to preview
* customize a given winning pattern.
*/
function load_winning_patterns() {

	if (file_exists("data/winningpatterns.dat")) {
		$filearray = file("data/winningpatterns.dat");
		for ($i=0; $i< $filearray[0]; $i++) { //first row is number of rows
			$set[$i] = unserialize(trim($filearray[$i+1]), ['allowed_classes' => false]);
		}
		return $set;
	} else {
		echo "set could not be loaded";
		return null;
	}
}


/** update_winning_patterns()
* This function updates the given $cardnumber within the "winningp atterns" set based
* on the information graphically entered from the web page.  The squares selected on the
* interactive web page are converted to a hidden string.  The string is passed to this
* function as a parameter, along with the pattern number chosen.  The set is updated
* to "check" all squares that were selected by the user.
* The functions saves the previewpatterns set prior to exiting.
*/
function update_winning_patterns($hiddenstring, $cardnumber) {
	global $bingoletters;

	@$winningset=load_winning_patterns();

	if (is_array($winningset)) {

		for ($row = 0; $row<5; $row++) {
			for ($column = 0; $column<5; $column++) {

				//eg. if B0 is in the string, then we must ensure that square becomes checked in
				//the first card of the previewpattern set
				if (str_contains($hiddenstring, $bingoletters[$column].$row)) {
					$winningset[$cardnumber][$column][$row]["checked"]=true;
				} else $winningset[$cardnumber][$column][$row]["checked"]=false;
			}
		}
		save_winning_patterns($winningset);
	}
}

/** save_winning_patterns()
* This function attemps to save the pattern preview set
* to the winningpatterns.dat file, a carefully crafted file that stores all winning patterns.
* with the exception of the normal winning pattern (pattern 0) which is not easily represented
* (any row, any column, any diagonal).  This function is called once the user has selected
* the squares of the winning pattern from the GUI.  The string of winning squares is converted
* from the interactive form with the function call update_winning_patterns() and then saved here
*/
function save_winning_patterns(&$set) {
	if (file_exists("data/winningpatterns.dat")) {
		//we must first open the file in reading to determine the number of rows

		$filearray = file("data/winningpatterns.dat");
		$numcards= trim($filearray[0]);

		//then we write the appropriate number of cards in the previewpatterns set
		if (@$fp=fopen("data/winningpatterns.dat","w")) {
			fwrite($fp,$numcards."\n");
			for ($i =0; $i<$numcards; $i++) {
				fwrite($fp, serialize($set[$i])."\n");  //one card per row
			}
			fclose($fp);
		}
	}
}


/** display_interactive_card()
* function similar to display_card() that is called when the user wishes
* to preview or customize one of the "winning patterns".  The technique
* used to customize the winning pattern involves Javascript and CSS components
* that are not supported by Netscape 4 (Opera 6 and IE 6 have been tested and
* are fully compatible.  The card is an HTML table in which the cells react to mouse
* clicks.  When the card is first loaded, the information is retreived from the
* winningpatterns.dat set and is represented visually on the screen.  A string
* is generated from the "checked cell" and stored (hidden) in the form.  When the user
* clicks a cell from the table, its color is reversed, and the hidden string is truncated
* or expanded with the name of the chosen cell.  When the form is submitted, the
* updated winningpatterns.dat file is saved.
* This function returns a string composed of the names of the selected cell of the given
* card in the winningpatterns.dat file: eg. B0;I0;N0;N1;N2;N3;N4;G0;O0 would be returned
* if the T-shaped winning pattern was selected.
*/
function display_interactive_card($cardnumber) {
	global $bingoletters;
	
	@$winningset=load_winning_patterns(); //display a pattern preview
	$hiddenstring="";  //sets the initial value to avoid error msg below.
	if (is_array($winningset)) {
		
		echo '<center><table width="75%" border="1" cellpadding="20" bgcolor="silver" bordercolor="red"><tr>';
		//header
		for ($column = 0; $column<5; $column++) { 
				echo '<td  width="20%" align="center" bgcolor="#dd00dd"><b><font size="+7">'.$bingoletters[$column].'</font></b></td>';
		}
		echo "</tr>";

		//table
		for ($row = 0; $row<5; $row++) {
			echo "<tr>\n";
			
			for ($column = 0; $column<5; $column++) { //column has to be inner loop due to HTML table
				echo "\n<td align=\"center\" style=\"background:".($winningset[$cardnumber][$column][$row]["checked"]?"#eeee00;":"silver;")."\" onClick = \"this.style.background=clickcell(this.style.background,'".$bingoletters[$column].$row."')\">";
				echo '<font size="+5">';
				echo $winningset[$cardnumber][$column][$row]["number"].'</font></td>';
				if ($winningset[$cardnumber][$column][$row]["checked"]) $hiddenstring.=($bingoletters[$column].$row.';');
			}
			echo "</tr>";
		}
		echo "</table></center>";
		
	}
	else echo "set could not be opened";
	return $hiddenstring;
}

/** load_patterns_json()
* Load patterns from the new JSON-based storage system.
* If the patterns.json file doesn't exist, it will be created from defaults.
* Returns an array of pattern objects with structure:
* [
*   {
*     "id": 1,
*     "name": "Pattern Name",
*     "mask": [[col, row], ...],
*     "isPreinstalled": true/false,
*     "enabled": true/false
*   },
*   ...
* ]
*/
function load_patterns_json() {
	// Ensure data directory exists
	if (!file_exists("data")) {
		@mkdir("data", 0755, true);
	}
	
	$patternsFile = "data/patterns.json";
	
	// If patterns.json doesn't exist, create it from defaults
	if (!file_exists($patternsFile)) {
		$defaultsFile = "data/winningpatterns.defaults.json";
		if (file_exists($defaultsFile)) {
			$patterns = json_decode(file_get_contents($defaultsFile), true);
			// Add enabled state based on current settings
			global $winningpatternarray;
			foreach ($patterns as &$pattern) {
				$patternIndex = $pattern['id']; // IDs 1-10 map to patterns 1-10
				$pattern['enabled'] = isset($winningpatternarray[$patternIndex]) && $winningpatternarray[$patternIndex] === 'on';
			}
			save_patterns_json($patterns);
			return $patterns;
		}
		return [];
	}
	
	$json = file_get_contents($patternsFile);
	$patterns = json_decode($json, true);
	return is_array($patterns) ? $patterns : [];
}

/** save_patterns_json()
* Save patterns to the new JSON-based storage system.
*/
function save_patterns_json($patterns) {
	// Ensure data directory exists
	if (!file_exists("data")) {
		@mkdir("data", 0755, true);
	}
	
	$patternsFile = "data/patterns.json";
	$json = json_encode($patterns, JSON_PRETTY_PRINT);
	
	if ($json === false) {
		error_log("JSON encoding failed for patterns");
		return false;
	}
	
	if (@file_put_contents($patternsFile, $json) === false) {
		error_log("Failed to save patterns to $patternsFile");
		return false;
	}
	return true;
}

/** get_pattern_by_id()
* Get a single pattern by its ID from the JSON store.
*/
function get_pattern_by_id($id) {
	$patterns = load_patterns_json();
	foreach ($patterns as $pattern) {
		if ($pattern['id'] == $id) {
			return $pattern;
		}
	}
	return null;
}

/** add_pattern_json()
* Add a new pattern to the JSON store.
* Returns the new pattern ID.
*/
function add_pattern_json($name, $mask = []) {
	$patterns = load_patterns_json();
	
	// Find the next available ID
	$maxId = 0;
	foreach ($patterns as $pattern) {
		if ($pattern['id'] > $maxId) {
			$maxId = $pattern['id'];
		}
	}
	
	$newPattern = [
		'id' => $maxId + 1,
		'name' => $name,
		'mask' => $mask,
		'isPreinstalled' => false,
		'enabled' => false
	];
	
	$patterns[] = $newPattern;
	save_patterns_json($patterns);
	
	return $newPattern['id'];
}

/** update_pattern_json()
* Update an existing pattern in the JSON store.
*/
function update_pattern_json($id, $name = null, $mask = null, $enabled = null) {
	$patterns = load_patterns_json();
	$updated = false;
	
	foreach ($patterns as &$pattern) {
		if ($pattern['id'] == $id) {
			if ($name !== null) {
				$pattern['name'] = $name;
			}
			if ($mask !== null) {
				$pattern['mask'] = $mask;
			}
			if ($enabled !== null) {
				$pattern['enabled'] = $enabled;
			}
			$updated = true;
			break;
		}
	}
	
	if ($updated) {
		save_patterns_json($patterns);
	}
	
	return $updated;
}

/** delete_pattern_json()
* Delete a pattern from the JSON store.
*/
function delete_pattern_json($id) {
	$patterns = load_patterns_json();
	$newPatterns = [];
	$deleted = false;
	
	foreach ($patterns as $pattern) {
		if ($pattern['id'] != $id) {
			$newPatterns[] = $pattern;
		} else {
			$deleted = true;
		}
	}
	
	if ($deleted) {
		save_patterns_json($newPatterns);
	}
	
	return $deleted;
}

/** reset_patterns_to_defaults()
* Reset all patterns to factory defaults.
*/
function reset_patterns_to_defaults() {
	$defaultsFile = "data/winningpatterns.defaults.json";
	if (!file_exists($defaultsFile)) {
		return false;
	}
	
	$patterns = json_decode(file_get_contents($defaultsFile), true);
	// Set all patterns to enabled by default on reset
	foreach ($patterns as &$pattern) {
		$pattern['enabled'] = true;
	}
	
	save_patterns_json($patterns);
	return true;
}

/** pattern_mask_to_card_format()
* Convert a pattern mask (array of [col, row] pairs) to the old card format
* used by load_winning_patterns() for backward compatibility.
*/
function pattern_mask_to_card_format($mask) {
	$card = [];
	
	// Initialize all cells as unchecked
	for ($col = 0; $col < 5; $col++) {
		for ($row = 0; $row < 5; $row++) {
			$card[$col][$row] = [
				'number' => ($col * 15 + $row + 1), // Dummy number
				'checked' => false
			];
		}
	}
	
	// Mark the cells in the mask as checked
	foreach ($mask as $cell) {
		list($col, $row) = $cell;
		$card[$col][$row]['checked'] = true;
	}
	
	return $card;
}


?>