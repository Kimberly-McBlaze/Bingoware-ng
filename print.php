<?php include_once("include/bootstrap.php"); 
include_once("include/virtual_cards.php");

// Check if we should exclude virtual player cards
$exclude_virtual = filter_input(INPUT_GET, 'exclude_virtual', FILTER_VALIDATE_BOOLEAN);
$virtual_card_numbers = [];

if ($exclude_virtual) {
$virtual_card_numbers = get_all_virtual_card_numbers();
}
?>
<html>
<head>
<title>View all cards</title>
</head>
<body>
<p>
<?php   
   $numcards = card_number();
   $printed_count = 0; // Track actual number of cards printed
   
   for ($i=0; $i<$numcards; $i++) {
   // Skip this card if it's assigned to a virtual player
   if ($exclude_virtual && in_array($i, $virtual_card_numbers)) {
   continue;
   }
   
   echo $printheader."<br>";
//transforms the $fourperpage string into a boolean
   display_card($i,($fourperpage=='on'),$namefile,$printrules);
   echo "<br>".$printfooter;
   
   if ($fourperpage=='on') {
   $i+=3; //step through
   }
   
   $printed_count++;
   
//if not last card then print page break instructions.
if ($i < ($numcards-1)) {
// Check if there are more non-virtual cards remaining
$has_more = false;
for ($check = $i + 1; $check < $numcards; $check++) {
if (!$exclude_virtual || !in_array($check, $virtual_card_numbers)) {
$has_more = true;
break;
}
}
if ($has_more) {
echo '</p><p style="page-break-before: always">';
}
} else {
echo "</p>";
}
} 

// If no cards were printed, show a message
if ($printed_count === 0 && $exclude_virtual) {
echo '<p style="text-align: center; margin: 2em;">No cards to display. All cards are assigned to virtual players.</p>';
}
?>
   
 
 </body>
 </html>
