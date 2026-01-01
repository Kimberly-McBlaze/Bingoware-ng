<?php include_once("include/functions.php"); 
$cardnumber = filter_input(INPUT_GET, 'cardnumber', FILTER_VALIDATE_INT);
if ($cardnumber === false || $cardnumber === null || $cardnumber < 1) {
	$cardnumber = 1;
}
?>
<html>
<head>
<title><?php printf("Bingo Card Number %s%'04s",$setid,$cardnumber); ?>
</title>
</head>
<body>
<?php	$names = load_name_file();   
	echo $viewheader."<br>";
	display_card($cardnumber-1,0,((($cardnumber-1<=count($names))&&$namefile)?$names[$cardnumber-1]:""));
	echo "<br>".$viewfooter;
 ?>
 </body>
 </html>
