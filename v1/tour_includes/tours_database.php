<?php
if (!LOCAL_MODE) {
   $ARR_CFGS["db_host"] = 'www.midaswebsolution.co.uk';
    $ARR_CFGS["db_name"] = 'midassol_igsitaly';
    $ARR_CFGS["db_user"] = 'midassol_igsital';
    $ARR_CFGS["db_pass"] = 'b5OpsX*CVzPp';
    define('SITE_SUB_PATH', '/clientpro/IVS');
}else{
	$ARR_CFGS["db_host2"] = 'localhost';
	$ARR_CFGS["db_name"] = 'medvisits';
	$ARR_CFGS["db_user2"] = 'user';
	$ARR_CFGS["db_pass2"] = 'user';
}
?>