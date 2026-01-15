	   <body>
   <?php
   // Include virtual cards functions at the start
   if (file_exists("include/virtual_cards.php")) {
       include_once("include/virtual_cards.php");
   }
   
   // Handle quick set switching
   if (isset($_GET["switch_set"])) {
       $new_setid = $_GET["switch_set"];
       
       // Validate setid format
       if (preg_match('/^[a-zA-Z0-9_-]+$/', $new_setid)) {
           // Check if the new set exists
           $new_set_exists = file_exists(__DIR__ . "/sets/set." . $new_setid . ".dat");
           
           if (!$new_set_exists && !isset($_GET["confirm_switch"])) {
               // Prompt to auto-generate
               $current_card_count = set_exists() ? card_number() : 0;
               
               echo '<div class="alert alert-warning">';
               echo '<strong>⚠️ Set Does Not Exist</strong><br>';
               echo 'Set "' . htmlspecialchars($new_setid) . '" does not have any cards generated yet.<br><br>';
               
               if ($current_card_count > 0) {
                   echo 'Would you like to automatically generate ' . $current_card_count . ' cards for this new set?<br><br>';
                   echo '<a href="index.php?action=generate&switch_to_set=' . urlencode($new_setid) . '&auto_cards=' . $current_card_count . '" class="btn btn-primary">✨ Generate ' . $current_card_count . ' Cards</a> ';
               } else {
                   echo 'Would you like to generate cards for this new set?<br><br>';
                   echo '<a href="index.php?action=generate&switch_to_set=' . urlencode($new_setid) . '" class="btn btn-primary">✨ Generate Cards</a> ';
               }
               
               echo '<a href="index.php?action=play" class="btn btn-secondary">Cancel</a>';
               echo '</div>';
           } else {
               // Update setid in config file using safe write approach
               if (file_exists("config/settings.php")) {
                   $filearray = file("config/settings.php");
                   if ($filearray !== false) {
                       $new_content = "";
                       foreach ($filearray as $line) {
                           if (preg_match("/^(\\\$setid=').*?';/", $line)) {
                               $line = "\$setid='" . addslashes($new_setid) . "';\n";
                           }
                           $new_content .= $line;
                       }
                       
                       // Validate and write atomically
                       if (!empty($new_content) && preg_match('/^<\?php/', $new_content) && strlen($new_content) > 100) {
                           $temp_file = "config/settings.php.tmp";
                           $fp = fopen($temp_file, "w");
                           if ($fp && flock($fp, LOCK_EX)) {
                               fwrite($fp, $new_content);
                               flock($fp, LOCK_UN);
                               fclose($fp);
                               
                               if (rename($temp_file, "config/settings.php")) {
                                   // Redirect to play page with new set
                                   header("Location: index.php?action=play");
                                   exit;
                               } else {
                                   @unlink($temp_file);
                               }
                           } else if ($fp) {
                               fclose($fp);
                               @unlink($temp_file);
                           }
                       }
                   }
                   echo '<div class="alert alert-error">Failed to update set ID in configuration.</div>';
               }
           }
       } else {
           echo '<div class="alert alert-error">Failed to switch set. Invalid set ID format.</div>';
       }
   }
   ?>


	   <?php if (isset($_POST["submit"])) {
	   			
	   			//pull in data from the form post
	   			
	   			if (isset($_POST["setidform"])) $setidform = $_POST["setidform"]; else $setidform="";
	   			if (isset($_POST["pagetitleform"])) $pagetitleform = $_POST["pagetitleform"]; else $pagetitleform ="";
	   			if (isset($_POST["viewheaderform"])) $viewheaderform =  $_POST["viewheaderform"]; else $viewheaderform ="";
	   			if (isset($_POST["viewfooterform"])) $viewfooterform = $_POST["viewfooterform"]; else $viewfooterform="";
	   			if (isset($_POST["printheaderform"])) $printheaderform = $_POST["printheaderform"]; else $printheaderform ="";
	   			if (isset($_POST["printfooterform"])) $printfooterform = $_POST["printfooterform"]; else  $printfooterform ="";
	   			if (isset($_POST["drawmodeform"])) $drawmodeform = $_POST["drawmodeform"]; else  $drawmodeform ="automatic";
	   			if (isset($_POST["namefileform"])) $namefileform = $_POST["namefileform"]; else  $namefileform ="";
	   			if (isset($_POST["printrulesform"])) $printrulesform = $_POST["printrulesform"]; else  $printrulesform ="";
	   			if (isset($_POST["fourperpageform"])) $fourperpageform = $_POST["fourperpageform"]; else $fourperpageform ="";
	
				//echo "debug: ".$_POST["mainbgcolorform"]."<br>";
		   		if (isset($_POST["headerfontcolorform"])) $headerfontcolorform = $_POST["headerfontcolorform"]; else $headerfontcolorform ="";
		   		if (isset($_POST["headerbgcolorform"])) $headerbgcolorform = $_POST["headerbgcolorform"]; else $headerbgcolorform ="";
		   		if (isset($_POST["mainfontcolorform"])) $mainfontcolorform = $_POST["mainfontcolorform"]; else $mainfontcolorform ="";
		   		if (isset($_POST["mainbgcolorform"])) $mainbgcolorform = $_POST["mainbgcolorform"]; else $mainbgcolorform ="";
		   		if (isset($_POST["selectedfontcolorform"])) $selectedfontcolorform = $_POST["selectedfontcolorform"]; else $selectedfontcolorform ="";
		   		if (isset($_POST["selectedbgcolorform"])) $selectedbgcolorform = $_POST["selectedbgcolorform"]; else $selectedbgcolorform ="";
		   		if (isset($_POST["bordercolorform"])) $bordercolorform = $_POST["bordercolorform"]; else $bordercolorform ="";
		   		
		   		//echo "debug: ".$headerfontcolorform."<br>";
		   		
		   		// Virtual Bingo settings
		   		if (isset($_POST["virtualbingoform"])) $virtualbingoform = $_POST["virtualbingoform"]; else $virtualbingoform ="";
		   		if (isset($_POST["virtualbingo_max_requestform"])) $virtualbingo_max_requestform = $_POST["virtualbingo_max_requestform"]; else $virtualbingo_max_requestform ="12";

		   
   // Check if Virtual Bingo is being disabled and handle confirmation
   $virtualbingo_changing = ($virtualbingo != $virtualbingoform);
   $virtualbingo_being_disabled = ($virtualbingo == 'on' && $virtualbingoform == '');
   
   if ($virtualbingo_being_disabled && function_exists('has_virtual_stacks') && has_virtual_stacks()) {
   // Check if confirmation was provided
   if (!isset($_POST["confirm_disable_vb"])) {
   // Show confirmation prompt
   echo '<div class="alert alert-warning">';
   echo '<strong>⚠️ Warning: Virtual Bingo URLs Exist</strong><br>';
   echo 'There are existing Virtual Bingo card URLs that have been generated. ';
   echo 'Disabling Virtual Bingo will delete all these URLs.<br><br>';
   echo '<form method="post" action="index.php?action=config' . ((isset($_GET['numberinplay']))?('&numberinplay='.$_GET['numberinplay']):'') . '">';
   
   // Re-include all form values as hidden fields
   echo '<input type="hidden" name="setidform" value="'.htmlspecialchars($setidform).'">';
   echo '<input type="hidden" name="pagetitleform" value="'.htmlspecialchars($pagetitleform).'">';
   echo '<input type="hidden" name="viewheaderform" value="'.htmlspecialchars($viewheaderform).'">';
   echo '<input type="hidden" name="viewfooterform" value="'.htmlspecialchars($viewfooterform).'">';
   echo '<input type="hidden" name="printheaderform" value="'.htmlspecialchars($printheaderform).'">';
   echo '<input type="hidden" name="printfooterform" value="'.htmlspecialchars($printfooterform).'">';
   echo '<input type="hidden" name="drawmodeform" value="'.htmlspecialchars($drawmodeform).'">';
   echo '<input type="hidden" name="namefileform" value="'.htmlspecialchars($namefileform).'">';
   echo '<input type="hidden" name="printrulesform" value="'.htmlspecialchars($printrulesform).'">';
   echo '<input type="hidden" name="fourperpageform" value="'.htmlspecialchars($fourperpageform).'">';
   echo '<input type="hidden" name="headerfontcolorform" value="'.htmlspecialchars($headerfontcolorform).'">';
   echo '<input type="hidden" name="headerbgcolorform" value="'.htmlspecialchars($headerbgcolorform).'">';
   echo '<input type="hidden" name="mainfontcolorform" value="'.htmlspecialchars($mainfontcolorform).'">';
   echo '<input type="hidden" name="mainbgcolorform" value="'.htmlspecialchars($mainbgcolorform).'">';
   echo '<input type="hidden" name="selectedfontcolorform" value="'.htmlspecialchars($selectedfontcolorform).'">';
   echo '<input type="hidden" name="selectedbgcolorform" value="'.htmlspecialchars($selectedbgcolorform).'">';
   echo '<input type="hidden" name="bordercolorform" value="'.htmlspecialchars($bordercolorform).'">';
   echo '<input type="hidden" name="virtualbingoform" value="">'; // Disabling
   echo '<input type="hidden" name="virtualbingo_max_requestform" value="'.htmlspecialchars($virtualbingo_max_requestform).'">';
   echo '<input type="hidden" name="confirm_disable_vb" value="1">';
   
   echo '<button type="submit" name="submit" class="btn btn-warning">⚠️ Yes, Disable Virtual Bingo and Delete URLs</button> ';
   echo '<a href="index.php?action=config' . ((isset($_GET['numberinplay']))?('&numberinplay='.$_GET['numberinplay']):'') . '" class="btn btn-secondary">Cancel</a>';
   echo '</form>';
   echo '</div>';
   
   // Don't proceed with saving
   exit;
   } else {
   // Confirmed - delete the stacks
   if (function_exists('delete_all_virtual_stacks')) {
       delete_all_virtual_stacks();
   }
   }
   } else if ($virtualbingo_being_disabled && (!function_exists('has_virtual_stacks') || !has_virtual_stacks())) {
   // No stacks, just proceed
   // No action needed
   }


		// Winning patterns are now managed via the Winning Patterns page (patterns.php)
	   		          
				// Magic quotes were removed in PHP 5.4, no longer needed
	   		          
	   		if (file_exists("config/settings.php")){
					$filearray=file("config/settings.php");
					if ($filearray === false) {
						error_log("Failed to read config/settings.php");
						echo '<div class="alert alert-error">Failed to read configuration file.</div>';
					} else {
					
					// Build new content in memory first
					$new_content = "";
	
					foreach ($filearray as $line_num => $line) {
						//sequence all replacements.
						//There will be only one replacement completed, but
						//the check will ensure the original line is kept in any other cases.
						
						// Use simple string replacements for plain text substitutions
						// Pattern matching is done with regex, but replacement values need proper escaping
						if (preg_match("/^(\\$setid=').*?';/", $line)) {
							$line = "\$setid='" . addslashes($setidform) . "';\n";
						}
						if (preg_match("/^(\\$pagetitleconfig=').*?';/", $line)) {
							$line = "\$pagetitleconfig='" . addslashes($pagetitleform) . "';\n";
						}
					
						//misc settings
						if (preg_match("/^(\\$namefile=').*?';/", $line)) {
							$line = "\$namefile='" . addslashes($namefileform) . "';\n";
						}
						if (preg_match("/^(\\$printrules=').*?';/", $line)) {
							$line = "\$printrules='" . addslashes($printrulesform) . "';\n";
						}
						if (preg_match("/^(\\$fourperpage=').*?';/", $line)) {
							$line = "\$fourperpage='" . addslashes($fourperpageform) . "';\n";
						}
					
						//headers and footers - these can contain HTML, need proper escaping
						if (preg_match("/^(\\$viewheader=').*?';/", $line)) {
							$line = "\$viewheader='" . addslashes($viewheaderform) . "';\n";
						}
						if (preg_match("/^(\\$viewfooter=').*?';/", $line)) {
							$line = "\$viewfooter='" . addslashes($viewfooterform) . "';\n";
						}
						if (preg_match("/^(\\$printheader=').*?';/", $line)) {
							$line = "\$printheader='" . addslashes($printheaderform) . "';\n";
						}
						if (preg_match("/^(\\$printfooter=').*?';/", $line)) {
							$line = "\$printfooter='" . addslashes($printfooterform) . "';\n";
						}
						if (preg_match("/^(\\$drawmode=').*?';/", $line)) {
							$line = "\$drawmode='" . addslashes($drawmodeform) . "';\n";
						}
					
						//colours
						if (preg_match("/^(\\$headerfontcolor=').*?';/", $line)) {
							$line = "\$headerfontcolor='" . addslashes($headerfontcolorform) . "';\n";
						}
						if (preg_match("/^(\\$headerbgcolor=').*?';/", $line)) {
							$line = "\$headerbgcolor='" . addslashes($headerbgcolorform) . "';\n";
						}
						if (preg_match("/^(\\$mainfontcolor=').*?';/", $line)) {
							$line = "\$mainfontcolor='" . addslashes($mainfontcolorform) . "';\n";
						}
						if (preg_match("/^(\\$mainbgcolor=').*?';/", $line)) {
							$line = "\$mainbgcolor='" . addslashes($mainbgcolorform) . "';\n";
						}
						if (preg_match("/^(\\$selectedfontcolor=').*?';/", $line)) {
							$line = "\$selectedfontcolor='" . addslashes($selectedfontcolorform) . "';\n";
						}
						if (preg_match("/^(\\$selectedbgcolor=').*?';/", $line)) {
							$line = "\$selectedbgcolor='" . addslashes($selectedbgcolorform) . "';\n";
						}
						if (preg_match("/^(\\$bordercolor=').*?';/", $line)) {
							$line = "\$bordercolor='" . addslashes($bordercolorform) . "';\n";
						}
					
						//virtual bingo settings
						if (preg_match("/^(\\$virtualbingo=').*?';/", $line)) {
							$line = "\$virtualbingo='" . addslashes($virtualbingoform) . "';\n";
						}
						if (preg_match("/^(\\$virtualbingo_max_request=').*?';/", $line)) {
							$line = "\$virtualbingo_max_request='" . addslashes($virtualbingo_max_requestform) . "';\n";
						}
																
						$new_content .= $line;
					}
					
					// Validate generated content before writing
					if (empty($new_content)) {
						error_log("Generated settings content is empty");
						echo '<div class="alert alert-error">Failed to generate configuration. Content is empty.</div>';
					} else if (!preg_match('/^<\?php/', $new_content)) {
						error_log("Generated settings content does not start with <?php");
						echo '<div class="alert alert-error">Failed to generate valid configuration.</div>';
					} else if (strlen($new_content) < 100) {
						error_log("Generated settings content is suspiciously short: " . strlen($new_content) . " bytes");
						echo '<div class="alert alert-error">Failed to generate configuration. Content is too short.</div>';
					} else {
						// Write to temporary file first, then atomically rename
						$temp_file = "config/settings.php.tmp";
						$fp = fopen($temp_file, "w");
						if (!$fp) {
							error_log("Failed to open temporary file for writing: " . $temp_file);
							echo '<div class="alert alert-error">Failed to save configuration. Check file permissions.</div>';
						} else {
							// Use file locking for safe write
							if (flock($fp, LOCK_EX)) {
								$write_result = fwrite($fp, $new_content);
								flock($fp, LOCK_UN);
								fclose($fp);
								
								if ($write_result === false) {
									error_log("Failed to write to temporary settings file");
									echo '<div class="alert alert-error">Failed to write configuration.</div>';
									@unlink($temp_file);
								} else {
									// Atomically replace the original file
									if (!rename($temp_file, "config/settings.php")) {
										error_log("Failed to rename temporary settings file");
										echo '<div class="alert alert-error">Failed to save configuration.</div>';
										@unlink($temp_file);
									} else {
										// Success!
										if (isset($_POST["pagetitleform"])) $pagetitle=$_POST["pagetitleform"];
										restart();
										echo '<div class="alert alert-success"><strong>✅ Configuration Accepted!</strong><br>Your settings have been saved successfully.</div>';
									}
								}
							} else {
								error_log("Failed to lock temporary settings file");
								fclose($fp);
								@unlink($temp_file);
								echo '<div class="alert alert-error">Failed to lock configuration file.</div>';
							}
						}
					}
					}
				} else {
					echo '<div class="alert alert-error"><strong>❌ Configuration not Accepted!</strong><br>Unable to save settings. Please check file permissions.</div>';
				}
	   	
		 //not submitted for change yet  	
	   	} else {
	   ?>
	   <div class="content-header">
	     <h2 class="content-title">⚙️ Configure</h2>
	     <p class="content-subtitle">Customize your Bingoware-ng settings</p>
	   </div>
	   
	   <form name="configForm" action="index.php?action=config<?= ((isset($_GET['numberinplay']))?('&numberinplay='.$_GET['numberinplay']):''); ?>" method="post" class="modern-form" onSubmit="return ConfigConfirmation()">
	   
	   <div class="form-group">
	     <label class="form-label">
	       Set ID:
	       <a href="javascript:explain('Set ID')" class="help-icon">help?</a>
	     </label>
	     <input type="text" name="setidform" value="<?= $setid; ?>" maxlength="10" class="form-input" style="max-width: 200px;">
	   </div>
	   
	   <!-- Winning Patterns are now managed via the dedicated Winning Patterns page (patterns.php) -->
		
		<div class="card mb-3">
		  <div class="card-header">
		    <h3 class="card-title">
		      Draw Mode
		      <a href="javascript:explain('Draw Mode')" class="help-icon">help?</a>
		    </h3>
		  </div>
		  <div class="card-body">
		    <div class="radio-group">
		      <label class="radio-option">
		        <input type="radio" name="drawmodeform" value="automatic" <?= ($drawmode=="automatic")?"checked":""; ?>>
		        <span>Automatic</span>
		      </label>
		      <label class="radio-option">
		        <input type="radio" name="drawmodeform" value="manual" <?= ($drawmode=="manual")?"checked":""; ?>>
		        <span>Manual</span>
		      </label>
		    </div>
		  </div>
		</div>
		
		<div class="card mb-3">
		  <div class="card-header">
		    <h3 class="card-title">Miscellaneous Options</h3>
		  </div>
		  <div class="card-body">
		    <div class="checkbox-group">
		      <label class="checkbox-option">
		        <input type="checkbox" name="namefileform" <?= ($namefile=="on")?"checked":""; ?>>
		        <span>Name File</span>
		        <a href="javascript:explain('Name File')" class="help-icon" style="margin-left: auto;">help?</a>
		      </label>
		      <label class="checkbox-option">
		        <input type="checkbox" name="printrulesform" <?= ($printrules=="on")?"checked":""; ?>>
		        <span>Print Rules</span>
		        <a href="javascript:explain('Print Rules')" class="help-icon" style="margin-left: auto;">help?</a>
		      </label>
		      <label class="checkbox-option">
		        <input type="checkbox" name="fourperpageform" <?= ($fourperpage=="on")?"checked":""; ?>>
		        <span>Print 4 cards per page</span>
		        <a href="javascript:explain('Four per page')" class="help-icon" style="margin-left: auto;">help?</a>
		      </label>
		    </div>
		  </div>
		</div>
		
	   	<div class="card mb-3">
	   	  <div class="card-header">
	   	    <h3 class="card-title">
	   	      Card Colours
	   	      <a href="javascript:explain('Colours')" class="help-icon">help?</a>
	   	    </h3>
	   	  </div>
	   	  <div class="card-body">
	   	    <div class="modern-table">
	   	      <table style="width: 100%;">
	   	        <thead>
	   	          <tr>
	   	            <th>Element</th>
	   	            <th>Background</th>
	   	            <th>Font</th>
	   	          </tr>
	   	        </thead>
	   	        <tbody>
							<td>Header
							</td>
							<td align="center">
								<a href="javascript:pickColor('pick1067301017');" id="pick1067301017"
									style="border: 1px solid #000000; font-family:Verdana; font-size:10px;
									text-decoration: none;">&nbsp;&nbsp;&nbsp;</a>
								<input id="pick1067301017field" size="7" type="hidden" name="headerbgcolorform" value="<?= $headerbgcolor; ?>">
								<script language="javascript">relateColor('pick1067301017', getObj('pick1067301017field').value);</script>
							</td>
							<td align="center">
								<a href="javascript:pickColor('pick1067300926');" id="pick1067300926"
									style="border: 1px solid #000000; font-family:Verdana; font-size:10px;
									text-decoration: none;">&nbsp;&nbsp;&nbsp;</a>
								<input id="pick1067300926field" size="7" type="hidden" name="headerfontcolorform" value="<?= $headerfontcolor; ?>">
								<script language="javascript">relateColor('pick1067300926', getObj('pick1067300926field').value);</script>
							</td>
						</tr>
						<tr>
							<td >Non-selected squares
							</td>
							<td align="center">
								<a href="javascript:pickColor('pick1067301091');" id="pick1067301091"
									style="border: 1px solid #000000; font-family:Verdana; font-size:10px;
									text-decoration: none;">&nbsp;&nbsp;&nbsp;</a>
								<input id="pick1067301091field" size="7" type="hidden" name="mainbgcolorform" value="<?= $mainbgcolor; ?>">
								<script language="javascript">relateColor('pick1067301091', getObj('pick1067301091field').value);</script>								

							</td>
							<td align="center">
								<a href="javascript:pickColor('pick1067300494');" id="pick1067300494"
									style="border: 1px solid #000000; font-family:Verdana; font-size:10px;
									text-decoration: none;">&nbsp;&nbsp;&nbsp;</a>
								<input id="pick1067300494field" size="7" type="hidden" name="mainfontcolorform" value="<?= $mainfontcolor; ?>">
								<script language="javascript">relateColor('pick1067300494', getObj('pick1067300494field').value);</script>
							</td>
						</tr>
						<tr>
							<td>Selected squares
							</td>
							<td align="center">
								<a href="javascript:pickColor('pick1067301185');" id="pick1067301185"
									style="border: 1px solid #000000; font-family:Verdana; font-size:10px;
									text-decoration: none;">&nbsp;&nbsp;&nbsp;</a>
								<input id="pick1067301185field" size="7" type="hidden" name="selectedbgcolorform" value="<?= $selectedbgcolor; ?>">
								<script language="javascript">relateColor('pick1067301185', getObj('pick1067301185field').value);</script>


							</td>
							<td align="center">
								<a href="javascript:pickColor('pick1067301286');" id="pick1067301286"
									style="border: 1px solid #000000; font-family:Verdana; font-size:10px;
									text-decoration: none;">&nbsp;&nbsp;&nbsp;</a>
								<input id="pick1067301286field" size="7" type="hidden" name="selectedfontcolorform" value="<?= $selectedfontcolor; ?>">
								<script language="javascript">relateColor('pick1067301286', getObj('pick1067301286field').value);</script>
							</td>
						</tr>
						<tr>
							<td>Border Color
							</td>
							<td align="center">
								<center><a href="javascript:pickColor('pick1067301200',3);" id="pick1067301200"
									style="border: 1px solid #000000; font-family:Verdana; font-size:10px;
									text-decoration: none;">&nbsp;&nbsp;&nbsp;</a>
								<input id="pick1067301200field" size="7" type="hidden" name="bordercolorform" value="<?= $bordercolor; ?>">
								<script language="javascript">relateColor('pick1067301200', getObj('pick1067301200field').value);</script>
							</td>
							<td align="center"><a href="javascript:explain('Border colour')">Note</a></center>

							</td>
						</tr>
	   	        </tbody>
	   	      </table>
	   	    </div>
	   	    <p style="margin-top: 1rem; font-size: 0.875rem; color: var(--text-muted);">
	   	      <a href="javascript:explain('Border colour')" class="help-icon">Note about border colors</a>
	   	    </p>
	   	  </div>
	   	</div>
	   	
	   	<div class="card mb-3">
	   	  <div class="card-header">
	   	    <h3 class="card-title">
	   	      🌐 Virtual Bingo Mode
	   	    </h3>
	   	    <p style="font-size: 0.875rem; color: var(--text-muted); margin-top: 0.5rem;">Enable remote play with shareable card links</p>
	   	  </div>
	   	  <div class="card-body">
	   	    <div class="checkbox-group">
	   	      <label class="checkbox-option">
	   	        <input type="checkbox" name="virtualbingoform" <?= ($virtualbingo=="on")?"checked":""; ?>>
	   	        <span>Enable Virtual Bingo Mode</span>
	   	      </label>
	   	    </div>
	   	    
	   	    <div class="form-group" style="margin-top: 1.5rem;">
	   	      <label class="form-label">Maximum Cards Per Request:</label>
	   	      <input type="number" name="virtualbingo_max_requestform" value="<?= isset($virtualbingo_max_request) ? $virtualbingo_max_request : '12'; ?>" min="1" max="100" class="form-input" style="max-width: 150px;">
	   	      <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Limits abuse by restricting cards per request (1-100, default: 12)</p>
	   	    </div>
	   	  </div>
	   	</div>
		
	   <div class="form-group">
	     <label class="form-label">Page Title:</label>
	     <input type="text" name="pagetitleform" value="<?= $pagetitleconfig; ?>" class="form-input">
	   </div>
	   
	   <div class="card mb-3">
	     <div class="card-header">
	       <h3 class="card-title">
	         Custom Headers & Footers
	         <a href="javascript:explain('Hint')" class="help-icon">hint?</a>
	       </h3>
	       <p style="font-size: 0.875rem; color: var(--text-muted); margin-top: 0.5rem;">(HTML codes allowed)</p>
	     </div>
	     <div class="card-body">
	       <div class="form-group">
	         <label class="form-label">View Header (single card):</label>
	         <input type="text" name="viewheaderform" value='<?= $viewheader; ?>' class="form-input">
	       </div>
	       <div class="form-group">
	         <label class="form-label">View Footer (single card):</label>
	         <input type="text" name="viewfooterform" value='<?= $viewfooter; ?>' class="form-input">
	       </div>
	       <div class="form-group">
	         <label class="form-label">Print Header (four cards per page):</label>
	         <input type="text" name="printheaderform" value='<?= $printheader; ?>' class="form-input">
	       </div>
	       <div class="form-group">
	         <label class="form-label">Print Footer (four cards per page):</label>
	         <input type="text" name="printfooterform" value='<?= $printfooter; ?>' class="form-input">
	       </div>
	     </div>
	   </div>

		
	   <button type="submit" value="Change!" name="submit" class="btn btn-primary btn-lg">
	     💾 Save Configuration
	   </button>
	   
	   </form>
	   
	   <?php
	}
	?>
	</body>
