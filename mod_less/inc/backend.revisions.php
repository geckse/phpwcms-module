<?php
// ----------------------------------------------------------------
// obligate check for phpwcms constants
if (!defined('PHPWCMS_ROOT')) {
   die("You Cannot Access This Script Directly, Have a Nice Day.");
}
// ----------------------------------------------------------------
	
	
	$log = "";
	if(isset($_POST['snapshot'])){
			higher_actual_subrev();
			$log .= "<br><strong>New Snapshot saved!</strong>";				
	}
	if(isset($_POST['release'])){
			higher_actual_rev();
			$log .= "<br><strong>New Release saved!</strong>";				
	}
	
	if(isset($_POST['revto_smmit'])){
		$log .= use_rev($_POST['revto']);
	}
	

?>	

	<section class="be_container">
		
		<h2><?php echo $BLM['be_nav_rev']; ?></h2>
			
				
			<?php if(strlen($log)) echo '<div class="posbox">'.$log.'</div>'; ?>
	
			<?php if(isset($_GET['revto'])){ ?>
				<div class="warnbox">
				<p>Sicher dass du die Revision laden möchtest?<br><strong>Es werden alle betroffene Dateien überschrieben! (.less und .css)</strong></p>
				<form action="phpwcms.php?do=modules&module=less&mode=revisions" method="POST">
					<input type="hidden" name="revto" value="<?php echo $_GET['revto']; ?>">
					<input type="submit" name="revto_smmit" value="Revision laden">
				</form>	
				</div>
			<?php } ?>
			
			<table style="width: 256px; margin-top: 16px;">
				<tr>
					<td>Current Release:</td><td>v.<?php echo get_actual_rev().'.'.get_actual_subrev(); ?> </td>
				</tr>
				<tr>
				
					<form action="phpwcms.php?do=modules&module=less&mode=revisions" method="post">	
						<td><input type="submit" name="release" value="Create Release"></td><td><input type="submit" name="snapshot" value="Create Revision snapshot"></td>
					</form>
				</tr>
						
			</table>
			<div id="ac_list" style="margin-top: 16px;">
			<?php echo revision_list(); ?>		
			</div>
	</section>		