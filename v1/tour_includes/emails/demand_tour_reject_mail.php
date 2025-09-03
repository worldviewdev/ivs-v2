<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td valign="top">
	  <p>Sorry!  You recently requested a tour that our tour operator/guide is not able to perform because of a conflict of dates.</p>
	  <p>TOUR DETAILS<br /><br />
		Tour Name: <?=$tour_title?><br />
		Tour Number: <?=$tour_code?><br />
		Source: <?=$tour_enq_website?><br />
		Reference number: <?=$tour_enq_order_number?><br />
		Service date: <?=show_tour_dates($tour_enq_pref_dates)?> <?=$tour_enq_pref_time?>
	  </p>
	  <p>We have many tours so please visit our website again and select another tour option.</p>
	  <p><a href="http://<?=$tour_enq_website?>/" target="_blank"><strong>http://<?=$tour_enq_website?>/</strong></a></p>
	  <p>If you would like assistance or advice with any other aspect of your trip including flights, car rentals, transfers, tours, insurance or accommodation at any of your destinations please do not hesitate to contact us through our websites<br /><br />Thank you!</p>
    </td>
  </tr>
</table>