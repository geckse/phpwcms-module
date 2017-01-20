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
	$modpath = $phpwcms['modules']['less']['path'];
				
	// really need my functions.
	include_once($phpwcms['modules'][$module]['path'].'inc/backend.functions.php');

	// load special backend CSS
	$BE['HEADER']['be_default_style.css'] = '<link href="'.$phpwcms['modules'][$module]['dir'].'inc/css/be_default_style.css" rel="stylesheet" type="text/css" />';

	require_once $phpwcms['modules'][$module]['path'].'inc/Less/Autoloader.php';
	Less_Autoloader::register();
		
	echo '<section class="be_container">';
	echo '<a href="http://www.geckse.de/lesscompiler" target="_blank"><img width="400px" height="97px" alt="" src="http://rec.geckse.de/modules/'.str_replace('.','-',$_module_less_version).'/'.md5($_SERVER['SERVER_ADDR']).'/'.(_getConfig('less_counter') !== false ? intval(_getConfig('less_counter')) : 0).'/less-compiler.png"/></a>';
	echo '<div id="version">v. '.$_module_less_version.'<br></div>';
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

	if(less_checkinstall()){
		echo less_be_nav($action);	
	} else {
		$action = "board";
	}
	
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

	<div class="subinfo">module by <a href="http://www.geckse.de" target="_blank">geckse</a> &middot; powered by <a href="http://leafo.net/lessphp/" target="_blank">lessphp</a> &middot; <a href="http://lesscss.org/" target="_blank">Read about less</a> &middot; <a href="phpwcms.php?do=modules&module=less&mode=terms">Terms of Use</a></div>
