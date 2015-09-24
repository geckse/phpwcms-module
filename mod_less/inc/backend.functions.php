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
	$output .= "<div class='be_men $isactive'><a href=\"phpwcms.php?do=modules&module=less&mode=revisions\">".$BLM['be_nav_rev']."</a></div>";

	$isactive = "";
	if($action == "log") $isactive = " active";
	$output .= "<div class='be_men $isactive'><a href=\"phpwcms.php?do=modules&module=less&mode=log\">".$BLM['be_nav_log']."</a></div>";


	$isactive = "";
	if($action == "config") $isactive = " active";
	$output .= "<div class='be_men $isactive'><a href=\"phpwcms.php?do=modules&module=less&mode=config\">".$BLM['be_nav_conf']."</a></div>";

	
	$output .= "</section>";
	
	return $output;
}	

/* ---- config functions ------ */


// read config (returns array)
function less_getconfig(){
	
	$configfile = __DIR__.'/less.config.php';
	
	$config = include $configfile;
	
	return $config;
}

function less_setconfig($index,$value){
	
	$configfile = __DIR__.'/less.config.php';
	
	$config = include $configfile;
	
	$config[$index] = $value;
	file_put_contents($configfile, '<?php return ' . var_export($config, true) . ';');
	return $value;
}


function less_getlastcomp(){
	
	$configfile = __DIR__.'/less.config.php';
	
	$data = include $configfile;
	
	return $data['lastComp'];
}

function less_setlastcomp(){
	return(less_setconfig('lastComp',time()));
}


/* ---- compiling -------- */

function CallToCompileLess($fe_init = false){
	global $phpwcms;
	require_once $phpwcms['modules']['less']['path'].'inc/Less/Autoloader.php';
	Less_Autoloader::register();
	
	$config = less_getconfig();
	
	if($config['autoComp'] != "true") return false;
	
	$lasttime = less_getlastcomp();
		
	if($config['revision'] == "true" && $fe_init && time()-$lasttime >= (60 * 60 * 2)){
		higher_actual_subrev();
	}
	
	$activefiles = less_getconfig()['compFiles'];

	
	$lessfiles = array_map('basename', glob(PHPWCMS_TEMPLATE.'inc_css/*.less')); 
	$activefiles = less_getconfig()['compFiles'];
	
	if(!sizeof($activefiles)){
		less_compile_list($lessfiles);	
	} else {
		less_compile_list($activefiles);	
	}
	return true;
}



function less_compile_list($list,$onlybackup = false){
	
	$mes = "";
	$buLESS = array();
	$collected_css = "";
	global $phpwcms;
	$modpath = $phpwcms['modules']['less']['path'];
	
	
	file_put_contents($modpath.'log/less.txt', "");   		
	
	foreach($list as $file){
	   if(file_exists(PHPWCMS_TEMPLATE.'inc_css/'.$file)){
		    $mes .= "<strong>less-file:</strong> $file found.<br>";
		    $state = less_compile($file, $onlybackup);
			$mes .= $state[0];
			$buLESS_ind = count($buLESS_ind);
			$buLESS[$buLESS_ind]['name'] = $file;
			$buLESS[$buLESS_ind]['less'] = file_get_contents(PHPWCMS_TEMPLATE.'inc_css/'.$file);
			$collected_css .= $state[1]; 
			if(strlen($state[1]) > 0) $mes .= " ^-- combining ...<br>";	 
		} else {
			$mes .= "less-file: $file not found. <strong>Skiped</strong> in compiling.<br>";
		}
	}
	$do_revision = true;
	$pathtocss = PHPWCMS_TEMPLATE.'inc_css/';
	$wannadir = $modpath."revisions/v_".get_actual_rev()."_".get_actual_subrev()."/";

		$config = less_getconfig();
		$cssname = $config['cssName'];
		if(!$cssname) $cssname = "outputstyles.css";

	
	if (!file_exists($wannadir) && !is_dir($wannadir)) {
		if (!mkdir ($wannadir,0775)){
			$mes .= "ERROR: Could not create dir in modules_less/revisions/. Please check the folder-permissions.\n";
			$do_revision = false;
		}
	}	

	if(strlen($collected_css)){	
		//	>_ 
		$newCSS = trim($pathtocss.$cssname.".css");
		$buCSS = trim($wannadir.$cssname.".css");
			
		if(!file_exists($newCSS) && $do_revision){
			 if($handle = fopen($newCSS, 'w')){ 
			 fclose($handle);
			 } else {
				 $mes .= 'ERROR: Cannot write css-file. Check the folder-permissions.\n Check: inc_css';
				 $do_revision = false;
			 }
		}		
		
		if(!file_put_contents($newCSS, $collected_css) && $do_revision) $mes .= " ^-- ERROR: could not write in new CSS-file.<br>";	
		
		if(!file_exists($buCSS) && $do_revision){
			 if($handle = fopen($buCSS, 'w')){
			 	fclose($handle);
			  } else {
			  	$mes .= "ERROR: Could not create dir in modules_less/revisions/. Please check the folder-permissions.\n";
			  	$do_revision = false;
			  }
		}		

		if(!file_exists($wannadir."revdata.txt") && $do_revision){
			 if($handle = fopen($wannadir."revdata.txt", 'w')){ 
			 	fclose($handle);
			 } else {
			 	$mes .= "ERROR: Could not create dir in modules_less/revisions/. Please check the folder-permissions.\n";
			 	$do_revision = false;
			 }
		}
		
		$revdata = "v_".get_actual_rev()."_".get_actual_subrev().";\n";
		$revdata .= "TIME:".time().";";
		
		if(!file_put_contents($wannadir."revdata.txt", $revdata) && $do_revision) $mes .= " ^-- ERROR: could not write in new rev-data-file.<br>";			
		
		
		if(!file_put_contents($buCSS, $collected_css) && $do_revision) $mes .= " ^-- ERROR: could not write in new Backup-CSS-file.<br>";			
		
		for($i = 0; $i < count($buLESS); $i++){
			$lessbu = $buLESS[$i];
			$lessbu_file = $wannadir.$lessbu['name'];
			
			if(!file_exists($lessbu_file) && $do_revision){
				 if($handle = fopen($lessbu_file, 'w')){
					fclose($handle);
				  } else {
				  	$mes .= "Could not create dir in modules_less/revisions/. Please check the folder-permissions.\n";
				 }
			}
			if(!file_put_contents($lessbu_file, $lessbu['less'] && $do_revision) && $do_revision) $mes .= " ^-- ERROR: could not write in new Backup-less-file.<br>";			
					
		}
		
		if(strlen($collected_css) > 0){ $mes .= "<br><strong>DONE:</strong> $cssname.css created / updated. ";
			if($do_revision){
				 $mes .= "Backup (v_".get_actual_rev()."_".get_actual_subrev().") $cssname.css created / updated.";	
			} else {
				$mes .= "Backup-creation failed.";
			}	 
		}
		
		$logfile = $modpath.'log/log.txt'; 
		file_put_contents($logfile, str_replace('<br>',"\n",$mes));
		
		
	}
	
	less_setlastcomp();
	
	return $mes;
	
}

function less_compile($lessname, $onlybackup){

	global $phpwcms;
	$modpath = $phpwcms['modules']['less']['path'];
	$pathtocss = PHPWCMS_TEMPLATE.'inc_css/';
	$revdir = 'inc/data/';
	
	$feedback = "";
	
	$config = less_getconfig();
	$options = array();	
	if($config['doMin'] == "true") $options = array( 'compress'=>true );
	
	
	try{
		
		$parser = new Less_Parser($options);
		$parser->parseFile( $pathtocss.$lessname, $phpwcms["site"]);
		$css = $parser->getCss();
    		$feedback = " ^-- collecting and compiling styles from $lessname ...<br>";		
		} 
		catch(Exception $e) {
    		file_put_contents($modpath.'log/less.txt', "<strong>LESS-ERROR</strong>: (in $lessname)".$e->getMessage()."\n", FILE_APPEND);
    		$feedback = " ^-- ERROR: less-error detected.<br>";
		}	
		
		$ret = array($feedback,$css);
		return($ret);		
}

/* ----- Revision-Management --------- */

function get_actual_rev(){	
	$configfile = __DIR__.'/less.config.php';
	
	$data = include $configfile;
	
	return(trim(intval($data['actualRev'])));
}

function higher_actual_rev(){
	$configfile = __DIR__.'/less.config.php';
	$data = include $configfile;
	less_setconfig('actualSubRev',0);
	less_setconfig('actualRev',$data['actualRev']+1);
	return true;
	
}
function get_actual_subrev(){	
	$configfile = __DIR__.'/less.config.php';
	$data = include $configfile;
	return(trim(intval($data['actualSubRev'])));
}

function higher_actual_subrev(){
	$configfile = __DIR__.'/less.config.php';
	$data = include $configfile;
	less_setconfig('actualSubRev',$data['actualSubRev']+1);
	return true;	
}

function revision_list(){
	$output = "<div class='list_box'><div class='ac_row theader'><div class='ac_a_title'>Version</div><div class='ac_a_title'>Datum</div><div class='ac_a_title'>Aktion</div></div>";
	$dir = dirname(__DIR__).'/revisions/';
	
	if(!file_exists($dir)) return '<i>No Revision-Folder found.</i>';
	
	$dirs = scandir($dir, 1);
	
	foreach($dirs as $subdir){
		if(strstr($subdir,'v_')){
			
			$isAct = "";
			
			if($subdir == 'v_'.get_actual_rev().'_'.get_actual_subrev()) $isAct = " active";
			
			$files = scandir($dir.$subdir,1); 
			$data = explode(';',file_get_contents($dir.$subdir.'/'.$files[array_search('revdata.txt', $files)]));
			$link = 'phpwcms.php?do=modules&module=less&mode=revisions';
			$output .= "<div class='ac_row ".$isAct."'><div class='ac_a_title'>".str_replace('_','.',trim($data[0]))."</div><div class='ac_a_title'>".date('d.m.Y H:i:s',trim(str_replace('TIME:','',$data[1])))."</div><div class='ac_a_title'><a href='".$link."&revto=".$subdir."'>Wiederherstellen</a></div></div>";
			  
		}	 
	}
	
	return $output.'</div>';
}

function use_rev($version){
	global $phpwcms;
	$modpath = $phpwcms['modules']['less']['path'];
	$cv = explode('_',str_replace('v_', '', $version));
	$pathtocss = PHPWCMS_TEMPLATE.'inc_css/';
	$dir = $modpath."revisions/v_".$cv[0]."_".$cv[1]."/";
	
	$files = scandir($dir,1); 
	
	less_setconfig('actualRev',$cv[0]);
	less_setconfig('actualSubRev',$cv[1]);
	
	$feedback = "<strong>Turning back to Version: ".$version."</strong><br />";
			
	foreach($files as $file){
		if(pathinfo($dir.$file, PATHINFO_EXTENSION) == "css" || pathinfo($dir.$file, PATHINFO_EXTENSION) == "less"){
			if(copy ($dir.$file, $pathtocss.$file)){
				$feedback .= $file." moved to inc_css<br />";
			} else {
				$feedback .= $file." couldn't move to inc_css<br />";
			}
		}
	}	
	return $feedback;
}












?>