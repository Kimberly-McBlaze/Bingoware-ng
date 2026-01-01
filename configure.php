	   <body onLoad="document.configForm.setidform.focus()">
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
					echo '<p><font size="4"><b>Configuration Accepted!</b></font></p>';
				} else {
					echo '<p><font size="4"><b>Configuration not Accepted!</b></font></p>';
				}
	   	
		 //not submitted for change yet  	
	   	} else {
	   ?>
	   <p><img src="images/cf.gif"></p>
	   <form name="configForm" action="index.php?action=config<?= ((isset($_GET['numberinplay']))?('&numberinplay='.$_GET['numberinplay']):''); ?>" method="post" onSubmit="return ConfigConfirmation()">
	   Enter the Set ID: 
	   &nbsp;&nbsp;&nbsp;<input type="text" name="setidform" value="<?= $setid; ?>" maxlength="10" size="4" align="right">&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:explain('Set ID')">help?</a><br>
	   <br><table border="1"><tr><td>
		   <table border="0"><tr><td colspan="2">
		   Choose the Winning Patterns: &nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:explain('Winning Pattern')">help?</a><br><br></td></tr>
		   <tr><td width="400" colspan="2">
		   	<input type="checkbox" name="winningpatternform0" <?= ($winningpattern0=="on")?"checked":""; ?>>
		   	&nbsp;&nbsp;&nbsp;<?= $patternkeywords[0];?> (any row, column or diagonal)
		   	
		   </td></tr>
		   <tr><td>
		   	<input type="checkbox" name="winningpatternform1" <?= ($winningpattern1=="on")?"checked":""; ?>>
		   	&nbsp;&nbsp;&nbsp;<?= $patternkeywords[1];?>
		   </td><td>
			   <a href="interactive.php?cardnumber=1" target=_blank>customize!</a>
	   	</td></tr>
	   	<tr><td>
	   		<input type="checkbox" name="winningpatternform2" <?= ($winningpattern2=="on")?"checked":""; ?>>
	   		&nbsp;&nbsp;&nbsp;<?= $patternkeywords[2];?>
	   	</td><td>
	   		<a href="interactive.php?cardnumber=2" target=_blank>customize!</a>
	   	</td></tr>
	   	<tr><td>
	   		<input type="checkbox" name="winningpatternform3" <?= ($winningpattern3=="on")?"checked":""; ?>>
	   		&nbsp;&nbsp;&nbsp;<?= $patternkeywords[3];?>
		   </td><td>
			   <a href="interactive.php?cardnumber=3" target=_blank>customize!</a>
	   	</td></tr>
	   	<tr><td>
			   <input type="checkbox" name="winningpatternform4" <?= ($winningpattern4=="on")?"checked":""; ?>>
			   &nbsp;&nbsp;&nbsp;<?= $patternkeywords[4];?>
	      </td><td>
			   <a href="interactive.php?cardnumber=4" target=_blank>customize!</a>
	   	</td></tr>
	   	<tr><td>
			   <input type="checkbox" name="winningpatternform5" <?= ($winningpattern5=="on")?"checked":""; ?>>
			   &nbsp;&nbsp;&nbsp;<?= $patternkeywords[5];?>
		   </td><td>
				<a href="interactive.php?cardnumber=5" target=_blank>customize!</a>
	   	</td></tr>
	   	<tr><td>
	   		<input type="checkbox" name="winningpatternform6" <?= ($winningpattern6=="on")?"checked":""; ?>>
	   		&nbsp;&nbsp;&nbsp;<?= $patternkeywords[6];?>
		   </td><td>
			   <a href="interactive.php?cardnumber=6" target=_blank>customize!</a>
	   	</td></tr>
	   	<tr><td>
			   <input type="checkbox" name="winningpatternform7" <?= ($winningpattern7=="on")?"checked":""; ?>>
			   &nbsp;&nbsp;&nbsp;<?= $patternkeywords[7];?>
		   </td><td>
			   <a href="interactive.php?cardnumber=7" target=_blank>customize!</a>
	   	</td></tr>
	   	<tr><td>
	   		<input type="checkbox" name="winningpatternform8" <?= ($winningpattern8=="on")?"checked":""; ?>>
	   		&nbsp;&nbsp;&nbsp;<?= $patternkeywords[8];?>
		   </td><td>
			   <a href="interactive.php?cardnumber=8" target=_blank>customize!</a>
	   	</td></tr>
		   <tr><td >
		   	<input type="checkbox" name="winningpatternform9" <?= ($winningpattern9=="on")?"checked":""; ?>>
		   	&nbsp;&nbsp;&nbsp;<?= $patternkeywords[9];?>
		   </td><td>
			   <a href="interactive.php?cardnumber=9" target=_blank>customize!</a>
	   	</td></tr>
		   <tr><td >
		   	<input type="checkbox" name="winningpatternform10" <?= ($winningpattern10=="on")?"checked":""; ?>>
		   	&nbsp;&nbsp;&nbsp;<?= $patternkeywords[10];?>
		   </td><td>
			   <a href="interactive.php?cardnumber=10" target=_blank>customize!</a>
	   	</td></tr>
	   	
	   
	   </table>
	   </td></tr></table>
		<br>		
		<br>
		<table border="1" width="30%"><tr><td><table border="0"> 
			<tr>
				<td>Draw Mode: &nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:explain('Draw Mode')">help?</a>
				</td>
			</tr><tr>
				<td>Automatic
				</td><td>
					<input type="radio" name="drawmodeform" value="automatic" <?= ($drawmode=="automatic")?"checked":""; ?>>
				</td>
			</tr><tr>
				<td>Manual
				</td><td>
					<input type="radio" name="drawmodeform" value="manual" <?= ($drawmode=="manual")?"checked":""; ?>>
				</td>
			</tr></table>
			</tr></table>
		<br>
		<table border="1" width="30%"><tr><td><table border="0"> 
			<tr>
				<td>Miscellaneous Options: 
				</td>
			</tr><tr><td >
		   	Name File</td><td><input type="checkbox" name="namefileform" <?= ($namefile=="on")?"checked":""; ?>>
		   	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:explain('Name File')">help?</a>
		   </td></tr>
	   		<tr><td >
		   	Print Rules</td><td><input type="checkbox" name="printrulesform" <?= ($printrules=="on")?"checked":""; ?>>
		   	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:explain('Print Rules')">help?</a>
		   </td></tr>
		   
		   <tr><td >
		   	Print 4 cards per page</td><td><input type="checkbox" name="fourperpageform" <?= ($fourperpage=="on")?"checked":""; ?>>
		   	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:explain('Four per page')">help?</a>
		   </td></tr></table>
			</tr></table>
		<br>
	   	<table border="1" width="30%">
	   		<tr>
	   			<td>
	   				<table border="0"> 
						<tr>
							<th width=40% align="center"><b>Colours</b>: &nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:explain('Colours')">help?</a> 
							</th>
							<th width=30% align="center">Background
							</th>
							<th width=30%>Font
							</th>
						</tr>
						<tr>
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
					</table>
				</td>
			</tr>
		</table>												
		<br>
	   <br>Page Title: <input type="text" name="pagetitleform" value="<?= $pagetitleconfig; ?>" size="55"><br>
	   
	   <br>When viewing one card (HTML codes allowed):&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:explain('Hint')">hint?</a><br><br>
	   Header: <input type="text" name="viewheaderform" value='<?= $viewheader; ?>' size="55"><br>
	   Footer: <input type="text" name="viewfooterform" value='<?= $viewfooter; ?>' size="55"><br><br>
	   When printing four card per page (HTML codes allowed):<br><br>
	   Header: <input type="text" name="printheaderform" value='<?= $printheader; ?>' size="55"><br>
	   Footer: <input type="text" name="printfooterform" value='<?= $printfooter; ?>' size="55"><br>
		<br>

		
	   &nbsp;&nbsp;&nbsp;<br><input type="submit" value="Change!" name="submit">
	   
	   </form>
	   
	   <?php
	}
	?>
	</body>
