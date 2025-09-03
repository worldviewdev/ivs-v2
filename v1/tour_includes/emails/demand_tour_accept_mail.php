<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td valign="top">
	  <p>Congratulations!  You recently requested a tour that our tour operator/guide is eager to do with you.  Please open the link below to complete payment and generate a voucher.</p>
	  <p>TOUR DETAILS<br /><br />
		Tour Name: <?=$tour_title?><br />
		Tour Number: <?=$tour_code?><br />
		Source: <?=$tour_enq_website?><br />
		Reference number: <?=$tour_enq_order_number?><br />
		Service date: <?=show_tour_dates($tour_enq_pref_dates)?> <?=$tour_enq_pref_time?>
	  </p>
	  <p>Before we can issue a voucher for this tour please open this link where you will be able to open a secure payment page.  Once payment has been received you will receive a voucher which you should print and present to the tour operator/guide.</p>
	  <p><a href="<?=SITE_WS_PATH?>/tour_booking.php?tour_id=<?=$tour_id?>&unique_code=<?=$unique_code?>" target="_blank"><strong><?=SITE_WS_PATH?>/tour_booking.php?tour_id=<?=$tour_id?>&unique_code=<?=$unique_code?></strong></a></p>
	  <p>If you would like assistance or advice with any other aspect of your trip including flights, car rentals, transfers, tours, insurance or accommodation at any of your destinations please do not hesitate to contact us through our websites<br /><br />Thank you!</p>
    </td>
  </tr>
</table>