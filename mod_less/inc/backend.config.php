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
	
?>


	<section class="be_container">
		
		<h2><?php echo $BLM['be_nav_conf'] ?></h2>
		
		<div class="ninebox">
			<form action="phpwcms.php?do=modules&module=less&mode=config" method="post">
			
			<input type="submit" name="smmit" class="safebutton" value="Sichern">
			</form>		
		</div>
	
	</section>
		
	
	
	
	
	
	
	
	
	
	
	
	