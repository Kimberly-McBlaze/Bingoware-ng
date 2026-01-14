<?php include_once("include/bootstrap.php");
   include_once("header.php");

?>
<div class="main-container">
  <?php include_once ("menu.php"); ?>
  <main class="content-area">
    <?php
 	if (isset($_GET["action"])) {
	    	if (($_GET["action"]=="generate") || ($_GET["action"]=="regenerate")) {
	    		if (set_exists() && $_GET["action"]=="generate" && !isset($_POST["submit"])) {
	    			echo '<div class="alert alert-info"><strong>ℹ️ Set exists</strong><br>You already have a set of '.card_number().' cards (Set ID: '.$setid.'). Would you like to <a href="index.php?action=regenerate" style="color: var(--color-info); font-weight: bold; text-decoration: underline;">create a new set</a>?</div>';
	    		} else include_once ("generate.php");
	    	} else if ($_GET["action"]=="view") {
	    		if (set_exists()) 
	    			include_once ("choose.php");
	    		 else echo '<div class="alert alert-warning"><strong>⚠️ No card set found</strong><br>You do not have a set of cards (Set ID: '.$setid.')</div>';
	    	} else if ($_GET["action"]=="play") {
	    		if (set_exists()) include_once ("play.php");
	    		else echo '<div class="alert alert-warning"><strong>⚠️ No card set found</strong><br>You do not have a set of cards (Set ID: '.$setid.')</div>';
	    	} else if ($_GET["action"]=="config") {
	    		include_once ("configure.php");
		}
   	
   	} else if (set_exists()) {
   		echo '<div class="card"><div class="card-header"><h2 class="card-title">✅ Card Set Ready</h2></div><div class="card-body"><p style="font-size: 1.125rem;">You have <strong>'.card_number().'</strong> cards in your set</p><p style="color: var(--text-muted); margin-top: 0.5rem;">(Set ID: '.$setid.')</p></div></div>';
   	} else {
   		include_once("generate.php");
   	}
    		
    ?>
  </main>
</div>
<?php include_once ("footer.php"); ?>
