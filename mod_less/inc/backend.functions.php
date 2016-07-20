<?php	
// ----------------------------------------------------------------
// obligate check for phpwcms constants
if (!defined('PHPWCMS_ROOT')) {
   die("You Cannot Access This Script Directly, Have a Nice Day.");
}
// ----------------------------------------------------------------

/* ---- nav for be ----- */
function less_be_nav($action){
	
	global $BLM;
	$output = "<section id='less_nav'>";
	
	$isactive = "";
	if(!$action || $action == "board") $isactive = " active";
	$output .= "<div class='be_men $isactive'><a href=\"phpwcms.php?do=modules&module=less&mode=board\">".$BLM['be_nav_general']."</a></div>";

	$isactive = "";
	if($action == "revisions") $isactive = " active";
	//$output .= "<div class='be_men $isactive'><a href=\"phpwcms.php?do=modules&module=less&mode=revisions\">".$BLM['be_nav_rev']."</a></div>";

	$isactive = "";
	if($action == "log") $isactive = " active";
	$output .= "<div class='be_men $isactive'><a href=\"phpwcms.php?do=modules&module=less&mode=log\">".$BLM['be_nav_log']."</a></div>";


	$isactive = "";
	if($action == "config") $isactive = " active";
	$output .= "<div class='be_men $isactive'><a href=\"phpwcms.php?do=modules&module=less&mode=config\">".$BLM['be_nav_conf']."</a></div>";

	
	$output .= "</section>";
	
	return $output;
}	

/* --- save a less group --- */
function less_save_group($id = 0, $name = "", $cssname = "", $lessfiles = "", $options){
	
	if (!less_checkinstall()) return false;
	
	$optionsstr = "";
	foreach($options as $ref => $val){
		$optionsstr .= $ref.':'.($val ? 1 : 0).';';
	}
	
	// create
   	if(!$id || $id <= 0){		
		$sql = "INSERT INTO `".DB_PREPEND."phpwcms_mod_less_groups` SET
		`groupname`='".mysql_real_escape_string($name)."',
		`cssname`='".mysql_real_escape_string($cssname)."',
		`lessfiles`='".mysql_real_escape_string($lessfiles)."',
		`options`='".mysql_real_escape_string($optionsstr)."',
		`last_compile`='".date('Y-m-d H:i:s')."',
		`major_revision`=0,
		`minor_revision`=1
		";	
	} else {
		$sql = "UPDATE `".DB_PREPEND."phpwcms_mod_less_groups` SET
		`groupname`='".mysql_real_escape_string($name)."',
		`cssname`='".mysql_real_escape_string($cssname)."',
		`lessfiles`='".mysql_real_escape_string($lessfiles)."',
		`options`='".mysql_real_escape_string($optionsstr)."'
		WHERE `id`=".intval($id);
	}
	@_dbQuery($sql);	
	
	return true;  	
}
/* --- save a less group --- */
function less_delete_group($id = 0){
	
	if (!less_checkinstall()) return false;
	
	$sql = "DELETE FROM `".DB_PREPEND."phpwcms_mod_less_groups` WHERE `id` = $id";
	@_dbQuery($sql);	
	return true;  	
}

/* --- loading less groups --- */
function less_load_groups(){
	
	$output = array(0 => false);
	
	$sql = "SELECT * FROM  `".DB_PREPEND."phpwcms_mod_less_groups`";
	$rows = @_dbQuery($sql);
	
	foreach($rows as $result){
		$output[] = array(
			'name' => $result['groupname'],
			'id' => $result['id'],
			'cssname' => $result['cssname'],
			'autocomp' => ((strpos($result['options'],'autocomp:1') !== false) ? true : false),
			'autochange' => ((strpos($result['options'],'autochange:1') !== false) ? true : false),
			'domin' => ((strpos($result['options'],'domin:1') !== false) ? true : false),
			'lessfiles' => $result['lessfiles'],
			'lastcomp' => $result['last_compile']
		);
	}
	return $output;
}

/* --- loading less groups --- */
function less_load_group($id = 0){
	
	$output = array(0 => false);
	
	$sql = "SELECT * FROM  `".DB_PREPEND."phpwcms_mod_less_groups` WHERE id=$id";
	$rows = @_dbQuery($sql);
	
	foreach($rows as $result){
		$output[] = array(
			'name' => $result['groupname'],
			'id' => $result['id'],
			'cssname' => $result['cssname'],
			'autocomp' => ((strpos($result['options'],'autocomp:1') !== false) ? true : false),
			'autochange' => ((strpos($result['options'],'autochange:1') !== false) ? true : false),
			'domin' => ((strpos($result['options'],'domin:1') !== false) ? true : false),
			'lessfiles' => $result['lessfiles']
		);
	}
	return $output;
}


/* ---- compile ----- */
function less_compile_group($id = 0){
	global $phpwcms;
	global $modpath;
	$group = less_load_group($id);
	$group = $group[1];
	
	if(isset($group['lessfiles']) && !empty($group['lessfiles'])){
		
		$files = explode(',',$group['lessfiles']);
		$css = "";
		$fileerror = "";
		
		foreach($files as $file){
			
			// file exists?
			$filepath = "";
			if(file_exists(PHPWCMS_TEMPLATE.'inc_css/'.$file)){
				$filepath = PHPWCMS_TEMPLATE.'inc_css/'.$file;
			} elseif(file_exists(PHPWCMS_TEMPLATE.'inc_less/'.$file)){
				$filepath = PHPWCMS_TEMPLATE.'inc_less/'.$file;
			} else {
				$fileerror .= "File: $file not found.<br>";
			} 
			
			// file found?
			if($filepath != ""){
				
				
				$options = array();	
				if($group['domin']) $options = array( 'compress'=>true );
				
				try{	
					$parser = new Less_Parser($options);
					$parser->parseFile( $filepath, $phpwcms["site"]);
					$css .= $parser->getCss();
					file_put_contents($modpath.'log/'.str_replace('.less','',$file).'.txt', "");
		    	
				} 
				catch(Exception $e) {
		    		file_put_contents($modpath.'log/'.str_replace('.less','',$file).'.txt', "<strong>LESS-ERROR</strong>: (in $file)".PHP_EOL.$e->getMessage()."\n");
		    		$fileerror .= "ERROR: less-syntax-error. See log for more information.<br>";
				}	
			}
				
		}
				
		// feedback or everything done?
		if($fileerror != ""){
			return $fileerror;
		} else {
			// write css
			if($css != ""){
				$css = "/* ---  ".str_replace('.css','',$group['cssname'])." - compiled v.".date('Y-m-d H:i:s').' --- */'.PHP_EOL.$css;
				
				if(file_put_contents(PHPWCMS_TEMPLATE.'inc_css/'.$group['cssname'].'.css',$css)){
					
					$sql = "UPDATE `".DB_PREPEND."phpwcms_mod_less_groups` SET
							`last_compile` = '".date('Y-m-d H:i:s')."' WHERE id=$id";
					@_dbQuery($sql);
					
					$count = (_getConfig('less_counter') !== false ? intval(_getConfig('less_counter')) : 0);
					_setConfig('less_counter', $count+1, 'module_less_compiler');
					
					return true;
				} else {
					return "Cannot write css file.";
				}
				
			} else {
				return "Less-input results no css.";
			}
		}
		
	} else {
		return "No less input selected.";
	}
}

/* ---- fe init actions --- */
function CallToCompileLess(){
	global $modpath;
	global $phpwcms;
	$module = "less";
	$modpath = dirname(__DIR__).'/'; 
	
	$groups = less_load_groups();	
	$result = "";
	
	foreach($groups as $group){
		if(isset($group['id']) && $group['id'] > 0){
			if($group['autocomp']){
				
				require_once $phpwcms['modules'][$module]['path'].'inc/Less/Autoloader.php';
				Less_Autoloader::register();
	
				$lesscomp = less_compile_group($group['id']);
				if($lesscomp !== true) $result .= $lesscomp;
				
			} else if($group['autochange']) {
				require_once $phpwcms['modules'][$module]['path'].'inc/Less/Autoloader.php';
				Less_Autoloader::register();
	
				$reftime = strtotime($group['lastcomp']);
				$files_changed = false;
				$files = explode(',',$group['lessfiles']);
				
				foreach($files as $file){
					if(file_exists(PHPWCMS_TEMPLATE.'inc_css/'.$file) && @filemtime(PHPWCMS_TEMPLATE.'inc_css/'.$file) > $reftime){
						$files_changed = true;
						break;
					} elseif(file_exists(PHPWCMS_TEMPLATE.'inc_less/'.$file) && @filemtime(PHPWCMS_TEMPLATE.'inc_less/'.$file) > $reftime){
						$files_changed = true;
						break;					
					} 
				}
				
				if($files_changed){
					$lesscomp = less_compile_group($group['id']);
					if($lesscomp !== true) $result .= $lesscomp;
				}
				
			}
		}
	}
	file_put_contents($modpath.'log/log.txt', $result);
	return true;
}

/* ---- module installed? ----- */
function less_checkinstall(){
	if(!mysql_fetch_row(mysql_query('SHOW TABLES FROM `' . $GLOBALS['phpwcms']['db_table'] . '` LIKE "%'.DB_PREPEND.'phpwcms_mod_less_groups"'))){
		return false;
	} else {
		return true;
	}
}




?>