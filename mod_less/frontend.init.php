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

include_once($phpwcms['modules']['less']['path'].'inc/backend.functions.php');
	
CallToCompileLess();
	
?>