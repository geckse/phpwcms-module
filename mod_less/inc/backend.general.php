<?php
// ----------------------------------------------------------------
// obligate check for phpwcms constants
if (!defined('PHPWCMS_ROOT')) {
   die("You Cannot Access This Script Directly, Have a Nice Day.");
}
// ----------------------------------------------------------------
	
	$errormes = "";
	$log = "";
	
	$lessfiles = array_map('basename', glob(PHPWCMS_TEMPLATE.'inc_css/*.less')); 
	
	$activefiles = less_getconfig()['compFiles'];
	
	if(isset($_POST['compnow'])){
		
		if(count($_POST['less'])){
			less_setconfig('compFiles',$_POST['less']);
			$log = less_compile_list($_POST['less']);
			
		} else {
			$errormes .= '<div class="warnbox">No less file selected.</div>';
		}
		
	}
	
	if(isset($_POST['snapshot'])){
		
			higher_actual_subrev();
			$log .= "<br><strong>New Snapshot saved!</strong>";
					
	}
	
?>	


	<section class="be_container">
		
		<h2><?php echo $BLM['be_nav_general']; ?></h2>
		<?php
		if(count($lessfiles) <= 0) echo '<div class="warnbox">Could not find a .less-file in template/inc_css/.</div>';
		if(strlen($errormes)) echo $errormes;
		?>
		<form action="phpwcms.php?do=modules&module=less&mode=board" method="post">
		<div class="ninebox">
			<table style="width: 256px;">
				<tr>
					<td>Last Compile:</td><td><?php echo $lastcomp = (intval(less_getlastcomp()) > 0) ? date("d.m.y H:i:s", less_getlastcomp()) : "<i>never before</i>"; ?></td>
				</tr>					
				<tr>
					<td>Current Revision:</td><td>v.<?php echo get_actual_rev().'.'.get_actual_subrev(); ?> </td>
				</tr>	
			</table>
			
			<?php if(count($lessfiles) > 0){ ?> 
			<table style="width: 256px;">
				<tr>
					<td><p>Select .less-files to compile</p>
					<select name="less[]" multiple="multiple" size="<?php echo $lesscount = (count($lessfiles)+1 < 16) ? count($lessfiles)+1 : 16; ?>" style="width: 240px;"> 
						<?php
							foreach($lessfiles as $lessfile){
								$meactive = "";
								if(in_array($lessfile,$activefiles)) $meactive = 'selected="selected"';
								echo '<option value="'.$lessfile.'" '.$meactive.'>'.$lessfile.'</option>';	
							}
						?>	
					</select>
					</td>
				</tr>					
			</table>
			<?php } ?>						
			
			<table style="width: 256px; margin-top: 16px;">
				<tr>
					<td><input type="submit" name="compnow" value="Compile"></td><td><input type="submit" name="snapshot" value="Create Revision snapshot"></td>
				</tr>						
			</table>
			<?php echo $log; ?>
		</div>
		</form>
	</section>		