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


	// put translation back to have easier access to it - use it as relation
	$BLM = & $BL['modules'][$module];
		
	// really need my functions.
	include_once($phpwcms['modules'][$module]['path'].'inc/backend.functions.php');
	$BE['HEADER']['jquery'] = '<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>';

	// load special backend CSS
	$BE['HEADER']['be_default_style.css'] = '<link href="'.$phpwcms['modules'][$module]['dir'].'inc/css/be_default_style.css" rel="stylesheet" type="text/css" />';

	require_once $phpwcms['modules'][$module]['path'].'inc/Less/Autoloader.php';
	Less_Autoloader::register();

	// config
	$mod_config = less_getconfig();
	
	
	echo '<section class="be_container">';
	echo "<h2>less-Compiler</h2>";
	echo '<div id="version">Modul "less-compiler"-version: '.$_module_less_version.'</div>';
	echo '</section>';
	$action = '';
	
	if(isset($_GET['mode'])) 
		{
		  if(gettype($_GET['mode']) == "string"){
			if ($_GET['mode']=='board')
			{
				$action	= 'board';
			} elseif($_GET['mode']=='revisions') {
				$action	= 'revisions';
			} elseif($_GET['mode']=='log') {
				$action	= 'log';
			} elseif($_GET['mode']=='config') {
				$action	= 'config';
			}
		  }	 
		} 


	echo less_be_nav($action);	

	if(!$action || $action == "board"){
		include_once($phpwcms['modules'][$module]['path'].'inc/backend.general.php');		
	}
	if($action == "revisions"){
		include_once($phpwcms['modules'][$module]['path'].'inc/backend.revisions.php');		
	}	
	if($action == "log"){
		include_once($phpwcms['modules'][$module]['path'].'inc/backend.log.php');		
	}
	if($action == "config"){
		include_once($phpwcms['modules'][$module]['path'].'inc/backend.config.php');		
	}
?>