<?php
// (c) 2015 q23.medien GmbH - planen . beraten . entwickeln
// by Marcel "geckse" Claus
//

// ----------------------------------------------------------------
// obligate check for phpwcms constants
if (!defined('PHPWCMS_ROOT')) {
   die("You Cannot Access This Script Directly, Have a Nice Day.");
}
// ----------------------------------------------------------------
	$config = less_getconfig();	
	
	$autocomp = $config['autoComp'];
	$revis = $config['revision'];
	$domin = $config['doMin'];
	$cssname = $config['cssName'];
		
		
	// speichern	
	if(isset($_POST['smmit'])){
		
		$savedata = "";	
		
		if($_POST['autocomp'] && $_POST['autocomp'] == "an"){
			$autocomp = true;
		} else {
			$autocomp = false;
		}
		less_setconfig('autoComp',$autocomp);
		
		if($_POST['dorev'] && $_POST['dorev'] == "an"){
			$revis = true;
		} else {
			$revis = false;
		}	
		less_setconfig('revision',$revis);
		
		if($_POST['domin'] && $_POST['domin'] == "an"){
			$domin = true;
		} else {
			$domin = false;
		}		
		less_setconfig('doMin',$domin);
		
		if($_POST['cssname'] && strlen($_POST['cssname']) ){
			$cssname= trim($_POST['cssname']);
			if(substr(trim($cssname),sizeof($cssname)-5,5) == ".css") $cssname = substr($cssname,0,sizeof($cssname)-5);
			less_setconfig('cssName',$cssname);
		} 
		
	}

?>


	<section class="be_container">
		
		<h2><?php echo $BLM['be_nav_conf'] ?></h2>
		
		<div class="ninebox">
			<form action="phpwcms.php?do=modules&module=less&mode=config" method="post">
			
			<input type="checkbox" id="autocomp" <?php if($autocomp) echo 'checked="checked"'; ?> name="autocomp" value="an"><label for="autocomp">Bei Seitenaufruf automatisch compilen</label><br>
			<p class="checkboxinfo"><i>Nur für Entwicklungszwecke empfohlen.</i></p>		
			<input type="checkbox" id="dorev" <?php if($revis) echo 'checked="checked"'; ?> name="dorev" value="an"><label for="revis">Revisionen automatisch anlegen</label><br>
			<p class="checkboxinfo"><i>Es werden in Kombination mit "Compilen bei Seitenaufruf" maximal alle 2 Stunden Revisionen erstellt.</i></p>
			<input type="checkbox" id="domin" <?php if($domin) echo 'checked="checked"'; ?> name="domin" value="an"><label for="domin">Minify</label><br>
			
			<input type="text" id="cssname" name="cssname" placeholder="mycompiled.css" value="<?php echo $cssname; ?>"><label for="cssname"> CSS-Name</label><br>
			<input type="submit" name="smmit" class="safebutton" value="Sichern">
			</form>		
		</div>
	
	</section>
	
	<div class="subinfo">by geckse<br />powered by <a href="http://leafo.net/lessphp/" target="_blanc">lessphp</a></div>'
	
	
	
	
	
	
	
	
	
	
	
	
	