<?php
require_once("../includes/midas.inc.php");
include_once("calendar.inc.php");
$town_month=$_REQUEST['month'];
$town_year=$_REQUEST['year'];
$town_cal=new Calendar_town();
?>
<link href="<?=SITE_TOUR_WS_PATH?>/css/css.css" rel="stylesheet" type="text/css" />
<link href="<?=SITE_TOUR_WS_PATH?>/pop/All17062009.css" type="text/css" rel="stylesheet" />
<link href="<?=SITE_TOUR_WS_PATH?>/pop/shadowbox-light.css" rel="stylesheet" type="text/css" />
<table width="80%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td class="blue_bor_white_bgcolor" align="center">
	<?
	if($town_month=="") {
		echo $town_cal->getCurrentMonthView();
	} else {
			echo $town_cal->getMonthView($town_month, $town_year);
	}
	?>
	</td>
  </tr>
  <tr><td>&nbsp;</td></tr>
   <tr><td>&nbsp;</td></tr>
</table>