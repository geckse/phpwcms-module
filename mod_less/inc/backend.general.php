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
	$lessfiles = $lessfiles + array_map('basename', glob(PHPWCMS_TEMPLATE.'inc_less/*.less')); 
		
	if(isset($_POST['installcompiler'])){
		
		$sql = "CREATE TABLE IF NOT EXISTS `".DB_PREPEND."phpwcms_mod_less_groups` (
		  `id` int(11) NOT NULL auto_increment,
		  `groupname` varchar(64) COLLATE utf8_bin NOT NULL,
		  `cssname` varchar(64) COLLATE utf8_bin NOT NULL,
		  `lessfiles` varchar(2048) COLLATE utf8_bin NOT NULL,
		  `options` varchar(1024) COLLATE utf8_bin NOT NULL,
		  `last_compile` datetime NOT NULL,
		  `major_revision` int(11) NOT NULL,
		  `minor_revision` int(11) NOT NULL,
		  PRIMARY KEY  (`id`)
		 ) ENGINE = MyISAM";
		
	
		if (mysql_fetch_row(mysql_query('SHOW TABLES FROM ' . $GLOBALS['phpwcms']['db_table'] . ' LIKE "%phpwcms_mod_less_groups"'))) {
	   	} else if(_dbQuery($sql, 'CREATE')) { //create table  
	    	$errormes .= '<strong>Table:</strong> phpwcms_mod_less_groups successful created.<br>';
	    	$errormes = '<div class="posbox">'.$errormes.'</div>';
		} else {
		 $errormes .= '<strong>Table</strong> - phpwcms_catalogue_attributes:<br> ERROR during creation:<br>';
		 $errormes .= '<p style="margin: 3px 10px; font-size: 10px">'.@htmlentities(@mysql_error(), ENT_QUOTES, PHPWCMS_CHARSET).'</p>';
		 $errormes = '<div class="errorbox">'.$errormes.'</div>';
		}	
		
	}
	
	// anzahl an gruppen
	$maxGroups = (isset($_POST['groupcount']) ? intval($_POST['groupcount']) : 0);	
	
	// save action
	if(isset($_POST['save-all']) || isset($_POST['add-group']) ){	
		for($i = 1; $i <= $maxGroups; $i++){
			$_name = strip_tags(substr($_POST['group-name-'.$i],0,64));
			$_id = intval($_POST['group-id-'.$i]);
			$_autocomp = intval((isset($_POST['option-'.$i.'-1']) ? true : false));
			$_autochange = intval((isset($_POST['option-'.$i.'-2']) ? true : false));
			$_domin = intval((isset($_POST['option-'.$i.'-3']) ? true : false));
			$_cssname = preg_replace('/[^a-z0-9_-]{1,64}/m','',str_replace('.css','',substr($_POST['cssname-'.$i],0,64)));
			$_lessinput = implode(',', (isset($_POST['select-lessfiles-'.$i]) ? $_POST['select-lessfiles-'.$i] : array()) );
			
			$_options = array('autocomp' => $_autocomp, 'autochange' => $_autochange, 'domin' => $_domin);
			
			less_save_group($_id,$_name,$_cssname,$_lessinput,$_options);
			
		}	
		$errormes .= '<div class="posbox">All groups saved.</div>';
	}
	
	// compile now action
	for($i = 1; $i <= $maxGroups; $i++){	
		if(isset($_POST['compile-'.$i])){	
			$_name = substr($_POST['group-name-'.$i],0,64);
			$_id = intval($_POST['group-id-'.$i]);
			$_cssname = preg_replace('/[^a-z0-9_-]{1,64}/m','',str_replace('.css','',substr($_POST['cssname-'.$i],0,64)));
			
			$lesscomp = less_compile_group($_id);
			if($lesscomp === true){
				$errormes .= '<div class="posbox">'.$_name.' compiled: '.$_cssname.'.css saved!</div><br>';
			} else {
				$errormes .= '<div class="errorbox">Compilation failed: '.$lesscomp.'</div><br>';	
			}
				// remove message
				file_put_contents($modpath.'log/log.txt', "");		
		}
	}
	
	// delete action
	for($i = 1; $i <= $maxGroups; $i++){	
		if(isset($_POST['del-'.$i])){	
			$_name = substr($_POST['group-name-'.$i],0,64);
			$_id = intval($_POST['group-id-'.$i]);
			$_cssname = preg_replace('/[^a-z0-9_-]{1,64}/m','',str_replace('.css','',substr($_POST['cssname-'.$i],0,64)));
			
			if(less_delete_group($_id) === true){
				$errormes .= '<div class="warnbox">'.$_name.' deleted!</div><br>';
			}
		}
	}

	
	if(less_checkinstall()){
?>		
		<div class="ninebox">
		<h2><?php echo $BLM['be_nav_general']; ?></h2>
		<?php
		if(count($lessfiles) <= 0) echo '<div class="warnbox">Could not find a .less-file in template/inc_css/.</div>';
		if(strlen($errormes)) echo $errormes;
		?>
		</div>
		<form action="phpwcms.php?do=modules&module=less&mode=board" method="post">
		<div class="ninebox">
			
			<script type="text/javascript">
				var lessfiles = <?php echo json_encode($lessfiles); ?>;
			</script>	
				
			<div id="builder">
				<?php 
				$groups = less_load_groups();	
				
				if(isset($_POST['add-group'])){
					
					$newID = sizeof($groups);
					$groups[] = array(
						'name' => 'group '.$newID,
						'id' => 0,
						'autocomp' => false,
						'autochange' => false,
						'domin' => false,
						'lessfiles' => array(),
						'cssname' => 'compiled'.$newID.'',
					);
				}	
				?>
				<input type="hidden" value="<?php echo sizeof($groups)-1; ?>" name="groupcount">
				<?php	
				foreach($groups as $ind => $row){
					
					if($ind == 0) continue;
					
					// keep postdata
					if(empty($row['name']) && isset($_POST['group-name-'.$ind])) $row['name'] = $_POST['group-name-'.$ind];
					if(empty($row['lessfiles']) && isset($_POST['select-lessfiles-'.$ind])) $row['lessfiles'] = implode(',',$_POST['select-lessfiles-'.$ind]);
					if(empty($row['cssname']) && isset($_POST['cssname-'.$ind])) $row['cssname'] = str_replace('.css','',$_POST['cssname-'.$ind]);
					if(empty($row['autocomp']) && isset($_POST['option-'.$ind.'-1'])) $row['autocomp'] = $_POST['option-'.$ind.'-1'];
					if(empty($row['autochange']) && isset($_POST['option-'.$ind.'-2'])) $row['autochange'] = $_POST['option-'.$ind.'-2'];
					if(empty($row['domin']) && isset($_POST['option-'.$ind.'-3'])) $row['domin'] = $_POST['option-'.$ind.'-3'];
					
				?>
				<div class="row row-<?php echo $ind; ?>">
					<h3>Less Group:</h3>&nbsp;<input type="text" style="width: 200px;" name="group-name-<?php echo $ind; ?>" value="<?php echo $row['name']; ?>" placeholder="name your group"><br>
					<input type="hidden" name="group-id-<?php echo $ind; ?>" value="<?php echo $row['id']; ?>">
					<input type="submit" class="delbutton" name="del-<?php echo $ind; ?>" onclick="return confirm('Do you really want to delete this group?');return false;" value="&times;">
				
					<div class="fiftybox lighter">
						<h4>1. Input</h4>
						<select class="lessfiles" multiple="multiple" name="select-lessfiles-<?php echo $ind; ?>[]">
							<?php 
							$_selectedLess = explode(',',$row['lessfiles']);
							
							foreach($lessfiles as $lessfile){
								echo '<option '.(in_array($lessfile,$_selectedLess) ? 'selected="selected"' : '').' value="'.$lessfile.'">'.$lessfile.'</option>';
							}
							?>
						</select>
						<p>Choose the less files.</p>
					</div>
					<div class="fiftybox lighter">
						<h4>2. Output</h4>
						<input type="checkbox" <?php echo ($row['autocomp'] ? 'checked="checked"' : '') ?> id="option-<?php echo $ind; ?>-1" name="option-<?php echo $ind; ?>-1"> <label for="option-<?php echo $ind; ?>-1">Bei Seitenaufruf durchführen</label><br>
						<input type="checkbox" <?php echo ($row['autochange'] ? 'checked="checked"' : '') ?> id="option-<?php echo $ind; ?>-2" name="option-<?php echo $ind; ?>-2"> <label for="option-<?php echo $ind; ?>-2">Bei Änderung durchführen</label><br>
						<input type="checkbox" <?php echo ($row['domin'] ? 'checked="checked"' : '') ?> id="option-<?php echo $ind; ?>-3" name="option-<?php echo $ind; ?>-3"> <label for="option-<?php echo $ind; ?>-3">Minify CSS</label><br>
						<hr>
						<input type="text" class="css-name" name="cssname-<?php echo $ind; ?>" value="<?php echo ($row['cssname'] ? $row['cssname'].'.css' : 'compiled.css') ?>" placeholder="yourcompiled.css">
						<input type="submit" class="butn compile-less-group" name="compile-<?php echo $ind; ?>" value="Compile now">
						<p>Define Output options.</p>
					</div>
				</div>
				<?php 
				}
				
				?>
				
			</div>
			<div class="fiftybox">
				<input type="submit" class="butn compile-less-group" name="add-group" value="Add less-Group">
			</div>
			<div class="fiftybox">
				<input type="submit" class="butn compile-less-group" name="save-all" value="Save">	
			</div>		
			
		</div>
		</form>
	</section>	
<?php
 	} else {	
?>	<section id="setup">
	<h2>Setup</h2>
	<?php
	if(strlen($errormes)) echo $errormes;
	?>	
	<form action="phpwcms.php?do=modules&module=less&mode=board" method="post">
		<p>This Module requires a table in your database. This will be done automatically.<br>Do you want to continue?</p>
		<input type="submit" id="installbutton" name="installcompiler" value="Install less-compiler">
	</form>
	
	</section>
<?php
	}
?>			