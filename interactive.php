<?php 
include_once("include/functions.php");
$cardnumber = filter_input(INPUT_GET, 'cardnumber', FILTER_VALIDATE_INT);
if ($cardnumber === false || $cardnumber === null || $cardnumber < 1) {
	$cardnumber = 1;
}

?>
<html>
<head>
<title>Interactive Bingo Card</title>

<script language="JavaScript" src="include/scripts.js">
</script>
</head>
<body bgcolor="#FFFFFF">
<?php if (isset($_POST["submit"])) {
		
		//the hiddenstring variable is passed automatically to this script
		//when the user submits the form.
		//the call to this function will update the first card of the 
		//previewpatterns set and resave the set.
		//the script closes the window also.
		
		update_winning_patterns($_POST["hiddenfield"], $cardnumber-1);
		?>
		<script language="JavaScript">
		window.close();
		</script>
	<?php } else {
		
		
		
		?>
		<center><b><font size=+2><p>Customized the winning pattern by clicking on the appropriate squares:</p></font></b></center>
		<br><center>
		
		<?php 
		//the display_interactive_card() function also returns a string composed of all the
		// checked cell in the first card (0) of the previewpatterns set.
		//this string is entered in the hidden field
		$hiddenfield = display_interactive_card($cardnumber-1); ?>
		<br>
		<form name="mainform" action="interactive.php?cardnumber=<?= $cardnumber; ?>" method="post">
		<p align="center">
		<input type="hidden" name="hiddenfield" value="<?= $hiddenfield; ?>">
		<input type="submit" name="submit" value="Accept">
		<input type="button" onClick="javascript:window.close()" value="Cancel">
		</form>
	<?php } ?>
</html>