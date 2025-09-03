<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>A new <?=$etype?> has been added to your order log:</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><strong>Reference No.:</strong> <?=$tour_enq_order_number?></td>
  </tr>
  <tr>
    <td><strong>Tour name:</strong> <?=$rs['tour_title']?></td>
  </tr>
  <tr>
    <td><strong>Tour Code:</strong> <?=$rs['tour_code']?></td>
  </tr>
  <tr>
    <td><strong>Preferred Date:</strong> <?=show_tour_dates($tour_enq_pref_dates)?> <?=$tour_enq_pref_time?></td>
  </tr>
  <tr>
    <td><strong>PAX name:</strong> <?=$tour_enq_salutation?> <?=$tour_enq_lname?> <?=$tour_enq_fname?></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>The Administrator will contact you to discuss this enquiry.</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><a href="http://www.toursitalia.com/supplier/enquiry_log.php" target="_blank">Click here to see full details.</a></td>
  </tr>
</table>