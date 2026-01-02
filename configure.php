	   <body>
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

				if (isset($_POST["winningpatternform0"])) $winningpatternform0 = $_POST["winningpatternform0"]; else  $winningpatternform0 ="";
				if (isset($_POST["winningpatternform1"])) $winningpatternform1 = $_POST["winningpatternform1"]; else  $winningpatternform1 ="";
				if (isset($_POST["winningpatternform2"])) $winningpatternform2 = $_POST["winningpatternform2"]; else  $winningpatternform2 ="";
				if (isset($_POST["winningpatternform3"])) $winningpatternform3 = $_POST["winningpatternform3"]; else  $winningpatternform3 ="";
				if (isset($_POST["winningpatternform4"])) $winningpatternform4 = $_POST["winningpatternform4"]; else  $winningpatternform4 ="";
				if (isset($_POST["winningpatternform5"])) $winningpatternform5 = $_POST["winningpatternform5"]; else  $winningpatternform5 ="";
				if (isset($_POST["winningpatternform6"])) $winningpatternform6 = $_POST["winningpatternform6"]; else  $winningpatternform6 ="";
				if (isset($_POST["winningpatternform7"])) $winningpatternform7 = $_POST["winningpatternform7"]; else  $winningpatternform7 ="";
				if (isset($_POST["winningpatternform8"])) $winningpatternform8 = $_POST["winningpatternform8"]; else  $winningpatternform8 ="";
				if (isset($_POST["winningpatternform9"])) $winningpatternform9 = $_POST["winningpatternform9"]; else  $winningpatternform9 ="";
				if (isset($_POST["winningpatternform10"])) $winningpatternform10 = $_POST["winningpatternform10"]; else  $winningpatternform10 ="";
	   		          
				// Magic quotes were removed in PHP 5.4, no longer needed
	   		          
	   		if (@file_exists("config/settings.php")){
					$filearray=file("config/settings.php");
					@$fp=fopen("config/settings.php","w");
	
					foreach ($filearray as $line_num => $line) {
						//sequence all replacements.
						//There will be only one replacement completed, but
						//preg_replace will return the original line in any other cases.
						
						//if user forgets to choose 1 winning pattern, then the default, pattern 0, is chosen anyways
						if (($winningpattern0.$winningpattern1.$winningpattern2.$winningpattern3.$winningpattern4.$winningpattern5.
							$winningpattern6.$winningpattern7.$winningpattern8.$winningpattern9.$winningpattern10)=="" ) $winningpattern0 = 'on';
						
						
						$line = preg_replace("/(setid=').*'/","$1".$setidform."'",$line);
						$line = preg_replace("/(pagetitleconfig=').*'/","$1".$pagetitleform."'",$line);
						
						//winning patterns
						
						$line = preg_replace("/(winningpattern0=').*;/","$1".$winningpatternform0."';",$line);
						$line = preg_replace("/(winningpattern1=').*;/","$1".$winningpatternform1."';",$line);
						$line = preg_replace("/(winningpattern2=').*;/","$1".$winningpatternform2."';",$line);
						$line = preg_replace("/(winningpattern3=').*;/","$1".$winningpatternform3."';",$line);
						$line = preg_replace("/(winningpattern4=').*;/","$1".$winningpatternform4."';",$line);
						$line = preg_replace("/(winningpattern5=').*;/","$1".$winningpatternform5."';",$line);
						$line = preg_replace("/(winningpattern6=').*;/","$1".$winningpatternform6."';",$line);
						$line = preg_replace("/(winningpattern7=').*;/","$1".$winningpatternform7."';",$line);
						$line = preg_replace("/(winningpattern8=').*;/","$1".$winningpatternform8."';",$line);
						$line = preg_replace("/(winningpattern9=').*;/","$1".$winningpatternform9."';",$line);
						$line = preg_replace("/(winningpattern10=').*;/","$1".$winningpatternform10."';",$line);
						
						//misc settings
						
						$line = preg_replace("/(namefile=').*;/","$1".$namefileform."';",$line);
						$line = preg_replace("/(printrules=').*;/","$1".$printrulesform."';",$line);
						$line = preg_replace("/(fourperpage=').*;/","$1".$fourperpageform."';",$line);
						
						//headers and footers
						
						$line = preg_replace("/(viewheader=').*;/","$1".$viewheaderform."';",$line);
						$line = preg_replace("/(viewfooter=').*;/","$1".$viewfooterform."';",$line);
						$line = preg_replace("/(printheader=').*;/","$1".$printheaderform."';",$line);
						$line = preg_replace("/(printfooter=').*;/","$1".$printfooterform."';",$line);
						$line = preg_replace("/(drawmode=').*'/","$1".$drawmodeform."'",$line);
						
						//colours
						$line = preg_replace("/(headerfontcolor=').*;/","$1".$headerfontcolorform."';",$line);
						$line = preg_replace("/(headerbgcolor=').*;/","$1".$headerbgcolorform."';",$line);
						$line = preg_replace("/(mainfontcolor=').*;/","$1".$mainfontcolorform."';",$line);
						$line = preg_replace("/(mainbgcolor=').*;/","$1".$mainbgcolorform."';",$line);
						$line = preg_replace("/(selectedfontcolor=').*'/","$1".$selectedfontcolorform."'",$line);
						$line = preg_replace("/(selectedbgcolor=').*'/","$1".$selectedbgcolorform."'",$line);
						$line = preg_replace("/(bordercolor=').*'/","$1".$bordercolorform."'",$line);
																	
						@fwrite($fp, trim($line)."\n"); //@ to avoid warnings in Demo on sourceforge
					}
					@fclose($fp); //@ to avoid warnings in Demo on sourceforge
					if (isset($_POST["pagetitleform"])) $pagetitle=$_POST["pagetitleform"];
					restart();
					echo '<div class="alert alert-success"><strong>✅ Configuration Accepted!</strong><br>Your settings have been saved successfully.</div>';
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
	   
	   <div class="card mb-3">
	     <div class="card-header">
	       <h3 class="card-title">
	         Winning Patterns
	         <a href="javascript:explain('Winning Pattern')" class="help-icon">help?</a>
	       </h3>
	     </div>
	     <div class="card-body">
	       <div class="checkbox-group">
	         <label class="checkbox-option">
	   	   <input type="checkbox" name="winningpatternform0" <?= ($winningpattern0=="on")?"checked":""; ?>>
	   	   <span><?= $patternkeywords[0];?> (any row, column or diagonal)</span>
	         </label>
	         
	         <label class="checkbox-option">
	   	   <input type="checkbox" name="winningpatternform1" <?= ($winningpattern1=="on")?"checked":""; ?>>
	   	   <span><?= $patternkeywords[1];?></span>
	   	   <a href="interactive.php?cardnumber=1" target="_blank" class="btn btn-sm btn-secondary" style="margin-left: auto;">customize!</a>
	         </label>
	         
	         <label class="checkbox-option">
	   	   <input type="checkbox" name="winningpatternform2" <?= ($winningpattern2=="on")?"checked":""; ?>>
	   	   <span><?= $patternkeywords[2];?></span>
	   	   <a href="interactive.php?cardnumber=2" target="_blank" class="btn btn-sm btn-secondary" style="margin-left: auto;">customize!</a>
	         </label>
	         
	         <label class="checkbox-option">
	   	   <input type="checkbox" name="winningpatternform3" <?= ($winningpattern3=="on")?"checked":""; ?>>
	   	   <span><?= $patternkeywords[3];?></span>
	   	   <a href="interactive.php?cardnumber=3" target="_blank" class="btn btn-sm btn-secondary" style="margin-left: auto;">customize!</a>
	         </label>
	         
	         <label class="checkbox-option">
	   	   <input type="checkbox" name="winningpatternform4" <?= ($winningpattern4=="on")?"checked":""; ?>>
	   	   <span><?= $patternkeywords[4];?></span>
	   	   <a href="interactive.php?cardnumber=4" target="_blank" class="btn btn-sm btn-secondary" style="margin-left: auto;">customize!</a>
	         </label>
	         
	         <label class="checkbox-option">
	   	   <input type="checkbox" name="winningpatternform5" <?= ($winningpattern5=="on")?"checked":""; ?>>
	   	   <span><?= $patternkeywords[5];?></span>
	   	   <a href="interactive.php?cardnumber=5" target="_blank" class="btn btn-sm btn-secondary" style="margin-left: auto;">customize!</a>
	         </label>
	         
	         <label class="checkbox-option">
	   	   <input type="checkbox" name="winningpatternform6" <?= ($winningpattern6=="on")?"checked":""; ?>>
	   	   <span><?= $patternkeywords[6];?></span>
	   	   <a href="interactive.php?cardnumber=6" target="_blank" class="btn btn-sm btn-secondary" style="margin-left: auto;">customize!</a>
	         </label>
	         
	         <label class="checkbox-option">
	   	   <input type="checkbox" name="winningpatternform7" <?= ($winningpattern7=="on")?"checked":""; ?>>
	   	   <span><?= $patternkeywords[7];?></span>
	   	   <a href="interactive.php?cardnumber=7" target="_blank" class="btn btn-sm btn-secondary" style="margin-left: auto;">customize!</a>
	         </label>
	         
	         <label class="checkbox-option">
	   	   <input type="checkbox" name="winningpatternform8" <?= ($winningpattern8=="on")?"checked":""; ?>>
	   	   <span><?= $patternkeywords[8];?></span>
	   	   <a href="interactive.php?cardnumber=8" target="_blank" class="btn btn-sm btn-secondary" style="margin-left: auto;">customize!</a>
	         </label>
	         
	         <label class="checkbox-option">
	   	   <input type="checkbox" name="winningpatternform9" <?= ($winningpattern9=="on")?"checked":""; ?>>
	   	   <span><?= $patternkeywords[9];?></span>
	   	   <a href="interactive.php?cardnumber=9" target="_blank" class="btn btn-sm btn-secondary" style="margin-left: auto;">customize!</a>
	         </label>
	         
	         <label class="checkbox-option">
	   	   <input type="checkbox" name="winningpatternform10" <?= ($winningpattern10=="on")?"checked":""; ?>>
	   	   <span><?= $patternkeywords[10];?></span>
	   	   <a href="interactive.php?cardnumber=10" target="_blank" class="btn btn-sm btn-secondary" style="margin-left: auto;">customize!</a>
	         </label>
	       </div>
	     </div>
	   </div>
		
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
