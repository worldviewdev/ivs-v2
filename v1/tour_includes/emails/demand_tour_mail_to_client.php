<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td valign="top">
	  <p>Thank you for booking an ON DEMAND tour on <?=str_replace("www.","",$_SERVER['HTTP_HOST'])?>. We appreciate your request.</p>
	  <p>Before we can confirm your tour your request has been sent to the tour operator/guide who will either Reject or Accept your request.  Most tour requests are accepted within 24 to 28 hours.</p>
	  <p>If you request is Accepted you will receive another email where you can complete the order.  If your request is Rejected you can request another tour option.</p>
	  <p>TOUR DETAILS<br /><br />
		Tour Name: <?=$tour_title?><br />
		Tour Number: <?=$tour_code?><br />
		Source: <?=$_SERVER['HTTP_HOST']?><br />
		Reference number: <?=$tour_enq_order_number?><br />
		Service date: <?=show_tour_dates($tour_enq_pref_dates)?> <?=$tour_enq_pref_time?>
	  </p>
	  <p>If you would like assistance or advice with any other aspect of your trip including flights, car rentals, transfers, tours, insurance or accommodation at any of your destinations please do not hesitate to contact us through our websites<br /><br />Thank you!</p>
    </td>
  </tr>
</table>