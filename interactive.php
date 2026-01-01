<?php 
include_once("include/functions.php");

// Get pattern ID from query parameter
$patternId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$cardnumber = filter_input(INPUT_GET, 'cardnumber', FILTER_VALIDATE_INT);

// Support both old 'cardnumber' parameter (for backward compat) and new 'id' parameter
if ($patternId === false || $patternId === null) {
	if ($cardnumber === false || $cardnumber === null || $cardnumber < 1) {
		$patternId = 1; // Default to pattern 1
	} else {
		$patternId = $cardnumber; // Use cardnumber as pattern ID
	}
}

// Check if this is a new pattern being created
$isNewPattern = isset($_GET['new']) && $_GET['new'] === '1';

?>
<html>
<head>
<title>Interactive Bingo Card</title>

<script language="JavaScript" src="include/scripts.js">
</script>
</head>
<body bgcolor="#FFFFFF">
<?php if (isset($_POST["submit"])) {
		
		// Get the pattern name if provided
		$patternName = isset($_POST["patternname"]) ? trim($_POST["patternname"]) : "";
		
		// Convert hiddenfield to mask format
		$hiddenfield = isset($_POST["hiddenfield"]) ? $_POST["hiddenfield"] : "";
		$mask = [];
		$bingoletters = array("B", "I", "N", "G", "O");
		
		// Parse the hiddenfield string (e.g., "B0;I0;N0;")
		$cells = explode(';', $hiddenfield);
		foreach ($cells as $cell) {
			$cell = trim($cell);
			if (strlen($cell) >= 2) {
				$letter = $cell[0];
				$row = (int)substr($cell, 1);
				$col = array_search($letter, $bingoletters);
				if ($col !== false) {
					$mask[] = [$col, $row];
				}
			}
		}
		
		if ($isNewPattern) {
			// Create new pattern
			if (empty($patternName)) {
				$patternName = "Custom Pattern";
			}
			$newId = add_pattern_json($patternName, $mask);
			echo '<script language="JavaScript">alert("New pattern created successfully!"); window.close();</script>';
		} else {
			// Update existing pattern
			if (!empty($patternName)) {
				update_pattern_json($patternId, $patternName, $mask);
			} else {
				update_pattern_json($patternId, null, $mask);
			}
			
			// Also update the old winningpatterns.dat for backward compatibility if pattern ID is 1-10
			if ($patternId >= 1 && $patternId <= 10) {
				update_winning_patterns($hiddenfield, $patternId - 1);
			}
			
			echo '<script language="JavaScript">window.close();</script>';
		}
		
	} else {
		
		?>
		<center><b><font size=+2><p>Customize the winning pattern by clicking on the appropriate squares:</p></font></b></center>
		<br><center>
		
		<?php 
		// Load pattern from JSON store
		$pattern = null;
		$hiddenfield = "";
		
		if ($isNewPattern) {
			// Create empty pattern for new pattern
			$pattern = [
				'id' => 0,
				'name' => 'New Pattern',
				'mask' => []
			];
		} else {
			$pattern = get_pattern_by_id($patternId);
			if (!$pattern) {
				// Fallback: try loading from old system if pattern ID is 1-10
				if ($patternId >= 1 && $patternId <= 10) {
					$cardnumber = $patternId;
					$hiddenfield = display_interactive_card($cardnumber - 1);
					$pattern = null; // Signal we're using old display
				} else {
					echo "<p>Pattern not found.</p>";
					echo '<input type="button" onClick="javascript:window.close()" value="Close">';
					exit;
				}
			}
		}
		
		// Display the pattern if using new system
		if ($pattern !== null) {
			$bingoletters = array("B", "I", "N", "G", "O");
			
			echo '<center><table width="75%" border="1" cellpadding="20" bgcolor="silver" bordercolor="red"><tr>';
			//header
			for ($column = 0; $column<5; $column++) { 
				echo '<td  width="20%" align="center" bgcolor="#dd00dd"><b><font size="+7">'.$bingoletters[$column].'</font></b></td>';
			}
			echo "</tr>";

			// Initialize card with all unchecked
			$cardDisplay = [];
			for ($col = 0; $col < 5; $col++) {
				for ($row = 0; $row < 5; $row++) {
					$cardDisplay[$col][$row] = [
						'number' => ($col * 15 + $row + 1),
						'checked' => false
					];
				}
			}
			
			// Mark cells in mask as checked
			foreach ($pattern['mask'] as $cell) {
				list($col, $row) = $cell;
				$cardDisplay[$col][$row]['checked'] = true;
				$hiddenfield .= $bingoletters[$col] . $row . ';';
			}

			//table
			for ($row = 0; $row<5; $row++) {
				echo "<tr>\n";
				
				for ($column = 0; $column<5; $column++) {
					echo "\n<td align=\"center\" style=\"background:".($cardDisplay[$column][$row]["checked"]?"#eeee00;":"silver;")."\" onClick = \"this.style.background=clickcell(this.style.background,'".$bingoletters[$column].$row."')\">";
					echo '<font size="+5">';
					echo $cardDisplay[$column][$row]["number"].'</font></td>';
				}
				echo "</tr>";
			}
			echo "</table></center>";
		}
		?>
		<br>
		<form name="mainform" action="interactive.php?id=<?= $patternId; ?><?= $isNewPattern ? '&new=1' : ''; ?>" method="post">
		<p align="center">
		<label>Pattern Name: <input type="text" name="patternname" value="<?= htmlspecialchars($pattern ? $pattern['name'] : ''); ?>" size="30"></label>
		<br><br>
		<input type="hidden" name="hiddenfield" value="<?= $hiddenfield; ?>">
		<input type="submit" name="submit" value="Accept">
		<input type="button" onClick="javascript:window.close()" value="Cancel">
		</form>
	<?php } ?>
</html>