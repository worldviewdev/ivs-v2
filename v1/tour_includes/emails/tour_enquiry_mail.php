<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>Dear
      <?=$tour_enq_salutation?>
      <?=$tour_enq_lname?>
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Thank you for your enquiry.  Your message has been sent to our HelpDesk Administrator and you will be contacted soon by email.</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><strong>Reference No.</strong>:
      <?=$tour_enq_order_number?>
    </td>
  </tr>
  <tr>
    <td><strong>Tour name</strong>:
      <?=$rs['tour_title']?>
    </td>
  </tr>
  <tr>
    <td><strong>Tour Code</strong>:
      <?=$rs['tour_code']?>
    </td>
  </tr>
  <tr>
    <td><strong>Starting location</strong>:
      <?=$start_loc?>
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><strong>Your Name</strong>:
      <?=$tour_enq_salutation?>
      <?=$tour_enq_fname?>
      <?=$tour_enq_lname?>
    </td>
  </tr>
  <tr>
    <td><strong>Your Email-address</strong>:
      <?=$tour_enq_email?>
    </td>
  </tr>
  <tr>
    <td><strong>Country</strong>:
      <?=$tour_enq_country?>
    </td>
  </tr>
  <tr>
    <td><strong>Preferred Date</strong>:
      <?=show_tour_dates($tour_enq_pref_dates)?>
      <?=$tour_enq_pref_time?>
    </td>
  </tr>
  <tr>
    <td><strong>Number of guests</strong>: &nbsp;Adults -
      <?=$tour_enq_guest_no?>
      Children under 2 yrs -
      <?=$tour_enq_children_under2?>
      Children above 2 yrs -
      <?=$tour_enq_children_above2?>
    </td>
  </tr>
  <?php
  if($tour_enq_double_room > 0){
  ?>
  <tr>
    <td><strong>Double Room(s)</strong>:
      <?php echo $tour_enq_double_room;
	  	 if($tour_enq_double_room_with_child=='Yes'){
		 	echo " (With child/senior)";
		 }
	  ?>
    </td>
  </tr>
  <?php } ?>
  <?php
  if($tour_enq_twin_room > 0){
  ?>
  <tr>
    <td><strong>Twin Room(s)</strong>:
      <?php echo $tour_enq_twin_room;
	  	 if($tour_enq_twin_room_with_child=='Yes'){
		 	echo " (With child/senior)";
		 }
	  ?>
    </td>
  </tr>
  <?php } ?>
  <?php
  if($tour_enq_single_room > 0){
  ?>
  <tr>
    <td><strong>Single Room(s)</strong>:
      <?=$tour_enq_single_room?>
    </td>
  </tr>
  <?php } ?>
  <?php
  if($tour_enq_pickup == "Yes"){
  ?>
  <tr>
    <td><strong>Pick up location</strong>:
      <?=$tour_enq_pick_loc?>
    </td>
  </tr>
  <?php
  }
  if($tour_enq_lunch_reqd == "Yes"){
  ?>
  <tr>
    <td><strong>Lunch Reqd.</strong>:
    </td>
  </tr>
  <?php } ?>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><strong>Comments/Questions</strong>:</td>
  </tr>
  <tr>
    <td>
      <?=$tour_enq_comment?>
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>If any of this information is incorrect, please contact the HelpDesk with the correct information.</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><a href="mailto:helpdesk@<?=str_replace("www.","",$_SERVER['HTTP_HOST']);?>">helpdesk@
      <?=str_replace("www.","",$_SERVER['HTTP_HOST']);?>
      </a></td>
  </tr>
  <tr>
    <td>We look forward to assisting you with this request.</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>
      <p> <br />
        Sincerely<br />
        The Travel Experts at<br />
        MedVisits.com and Italy for Agents.com<br />
        <a href="http://www.medvisits.com" target="_blank">www.medvisits.com</a><br />
        <a href="http://www.italyforagents.com" target="_blank">www.italyforagents.com</a></p>
    </td>
  </tr>
</table>