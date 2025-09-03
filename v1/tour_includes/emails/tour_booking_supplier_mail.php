<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>A new CONFIRMED booking has been added to your order log:</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><strong>Tour name:</strong> <?=$tour_title?></td>
  </tr>
  <tr>
    <td><strong>Tour Code:</strong> <?=$tour_code?></td>
  </tr>
  <tr>
    <td><strong>Preferred Date:</strong> <?=show_tour_dates($tour_book_pref_dates)?> <?=$tour_book_pref_time?></td>
  </tr>
  <tr>
    <td><strong>PAX name:</strong> <?=$tour_book_salutation?> <?=$tour_book_fname?> <?=$tour_book_lname?></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><a href="http://www.toursitalia.com/supplier/order_log.php" target="_blank">Click here to see full details.</a></td>
  </tr>
</table>