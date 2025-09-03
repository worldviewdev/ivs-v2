<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td valign="top">
	  <p>Thank you for booking a tour on <?=str_replace("www.","",$_SERVER['HTTP_HOST'])?>. We appreciate your order.</p>
	  <p>Here is a link to your voucher which contains the details of your tour.  Please print the voucher and present to the tour operator.<br><br><a href="<?=SITE_WS_PATH?>/print_voucher.php?voucherID=<?=$voucherID?>" target="_blank"><strong>Print Voucher</strong></a><br /></p>
	  <p>Tour Name: <?=$tour_title?><br />
		 Tour Number: <?=$tour_code?><br />
		Source: <?=$tour_book_website?><br />
		Booking number: <?=$booking_sess_id?><br />
		Service date: <?=show_tour_dates($tour_book_pref_dates)?> <?=$tour_book_pref_time?>
	  </p>
	  <p>All tours are booked according to our standard Terms and Conditions which are linked to the voucher.  Our cancellation and refund policy is set out in these Terms and Conditions.<br /><br />If you would like assistance or advice with any other aspect of your trip including flights, car rentals, transfers, tours, insurance or accommodation at any of your destinations please do not hesitate to contact us through our websites.<br /><br />Thank you!</p>
    </td>
  </tr>
</table>