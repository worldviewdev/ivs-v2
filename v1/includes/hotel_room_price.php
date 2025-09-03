<?php
require_once('../../includes/midas.inc.php');
$validator = new midas_validator();
$closethiswindow = false;
$seasons_res = db_result("select * from mv_hotel_seasons where season_id='$seasonID'");
$result = db_result("select * from mv_room_price where fk_hotel_id='$id' and fk_room_id = '$rID' and fk_season_id='$seasonID' and fk_supplier_id='$hotel_supplier_id' ");
$supplier_result = db_result("select * from mv_supplier  where supplier_id = '$id'");
if (is_post_back()) {
  if (count($season_ids) == 0) {
    $season_ids[] = $seasonID;
  }
  for ($i = 0; $i < count($season_ids); $i++) {
    $price_id = db_scalar("select price_id from mv_room_price where fk_hotel_id = '$id' and fk_room_id = '$rID' and fk_season_id = '" . $season_ids[$i] . "' and fk_supplier_id = '$hotel_supplier_id' ");
    if ($price_id != "") {
      $log_previous_arr = serialize($result);
      $sql = "UPDATE mv_room_price SET price_sunday = '$price_sunday' ,price_monday = '$price_monday' ,price_tuesday = '$price_tuesday' ,price_wednesday = '$price_wednesday' ,price_thursday = '$price_thursday' ,price_friday = '$price_friday' ,price_saturday = '$price_saturday'  WHERE price_id = '$price_id'";
      db_query($sql);
    } else {
      $sql = "insert into mv_room_price SET fk_hotel_id = '$id',fk_room_id = '$rID' ,fk_season_id = '" . $season_ids[$i] . "' ,fk_supplier_id = '$hotel_supplier_id' ,price_sunday = '$price_sunday' ,price_monday = '$price_monday' ,price_tuesday = '$price_tuesday' ,price_wednesday = '$price_wednesday' ,price_thursday = '$price_thursday' ,price_friday = '$price_friday' ,price_saturday = '$price_saturday' ,price_added_by = '" . $_SESSION['sess_agent_id'] . "' ,price_added_on = now()";
      db_query($sql);
      $price_id = mysqli_insert_id($GLOBALS['dbcon']);
    }
    ###############################  For Logging ###########################
    $log_updated_arr = serialize(db_result("select * from mv_room_price where price_id = '$price_id'"));
    $log_title = "Room price has been updated for supplier  (" . $supplier_result['supplier_company_name'] . ").";
    db_query("insert into mv_daily_log set employee_id='" . $_SESSION['sess_agent_id'] . "',log_title='" . addslashes($log_title) . "',log_action='Update',log_added_date=now(),log_mysql_query='" . addslashes($sql) . "',log_url='http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "',log_user_agent='" . $_SERVER['HTTP_USER_AGENT'] . "',log_previous_arr='" . addslashes($log_previous_arr) . "',log_updated_arr='" . addslashes($log_updated_arr) . "'");
    #########################################################################
  }
  $_SESSION['agent_msg_class'] = 'sucess_msg';
  $_SESSION['agent_msg'] = '<li>Price updated successfully.</li>';
  /*if($price_id!=""){
			$log_previous_arr=serialize($result);
			$sql="UPDATE mv_room_price SET price_sunday = '$price_sunday' ,price_monday = '$price_monday' ,price_tuesday = '$price_tuesday' ,price_wednesday = '$price_wednesday' ,price_thursday = '$price_thursday' ,price_friday = '$price_friday' ,price_saturday = '$price_saturday'  WHERE price_id = '$price_id'";
			db_query($sql);
			$_SESSION['agent_msg_class']='sucess_msg';
			$_SESSION['agent_msg'] = '<li>Price updated successfully.</li>';
	}else{
			$sql="insert into mv_room_price SET fk_hotel_id = '$id',fk_room_id = '$rID' ,fk_season_id = '$seasonID' ,fk_supplier_id = '$hotel_supplier_id' ,price_sunday = '$price_sunday' ,price_monday = '$price_monday' ,price_tuesday = '$price_tuesday' ,price_wednesday = '$price_wednesday' ,price_thursday = '$price_thursday' ,price_friday = '$price_friday' ,price_saturday = '$price_saturday' ,price_added_by = '".$_SESSION['sess_agent_id']."' ,price_added_on = now()";
			db_query($sql);
			$price_id=mysql_insert_id();
			$_SESSION['agent_msg_class']='sucess_msg';
			$_SESSION['agent_msg'] = '<li>Price added successfully.</li>';
	}
	###############################  For Logging ###########################
	$log_updated_arr = serialize(db_result("select * from mv_room_price where price_id = '$price_id'"));
	$log_title="Room price has been updated for supplier  (".$supplier_result['supplier_company_name'].").";
	db_query("insert into mv_daily_log set employee_id='".$_SESSION['sess_agent_id']."',log_title='".addslashes($log_title)."',log_action='Update',log_added_date=now(),log_mysql_query='".addslashes($sql)."',log_url='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."',log_user_agent='".$_SERVER['HTTP_USER_AGENT']."',log_previous_arr='".addslashes($log_previous_arr)."',log_updated_arr='".addslashes($log_updated_arr)."'");
	#########################################################################
	*/
  if ($action == "Save & Continue") {
    $seasons_arr_list = db_scalar("select group_concat(season_id) from mv_hotel_seasons where fk_supplier_id='$id' and season_supplier_id='$hotel_supplier_id' and season_status='Active' order by season_start_date");
    $seasons_arr = explode(",", $seasons_arr_list);
    $current_index = array_search($seasonID, $seasons_arr);
    $next_index = $current_index + 1;
    if ($seasons_arr[$next_index] > 0) {
      $seasonID = $seasons_arr[$next_index];
    } else {
      $seasonID = $seasons_arr[0];
    }
    header("Location: hotel_room_price.php?id=$id&rID=$rID&seasonID=$seasonID&hotel_supplier_id=$hotel_supplier_id");
    exit;
  } else {
    $closethiswindow = true;
    $targetpage = "hotel/hotel_rooms.php?id=$id&rID=$rID&seasonID=$seasonID&hotel_supplier_id=$hotel_supplier_id";
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <base href="<?= SITE_WS_PATH ?>/<?= AGENT_ADMIN_DIR ?>/">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title><?php echo SITE_NAME; ?>:: Control Panel</title>
  <link href="css/style.css" rel="stylesheet" type="text/css" />
</head>
<?php
$line = ms_form_value($result);
@extract($line);
close_fancybox($closethiswindow, $targetpage);
?>
<body>
  <script>
    function duplicate_price_for_all_day(chk_val) {
      var sunday_data = document.getElementById('price_sunday').value;
      if (chk_val) {
        document.getElementById('price_monday').value = sunday_data;
        document.getElementById('price_tuesday').value = sunday_data;
        document.getElementById('price_wednesday').value = sunday_data;
        document.getElementById('price_thursday').value = sunday_data;
        document.getElementById('price_friday').value = sunday_data;
        document.getElementById('price_saturday').value = sunday_data;
      }
    }
  </script>
  <table width="90%" border="0" align="center" style="padding-top:15px;">
    <?php if (isset($_SESSION['agent_msg']) && $_SESSION['agent_msg'] != "") { ?>
      <tr>
        <td align="left" valign="middle"><?php echo display_agent_message(); ?></td>
      </tr>
    <?php } ?>
    <tr>
      <td class="form_green_rounded">
        <form action="" method="post" name="frm1" enctype="multipart/form-data">
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <th colspan="2" align="left">Add/Edit Pricing Info (
                <?= season_date_format($seasons_res['season_start_date']) ?>
                to
                <?= season_date_format($seasons_res['season_end_date']) ?>
                )</th>
            </tr>
            <tr>
              <td width="60%">
                <table width="100%" border="0" cellspacing="10" cellpadding="0">
                  <tr>
                    <td width="25%" align="left" valign="middle"> <strong>Sunday</strong> </td>
                    <td width="75%" align="left" valign="top">
                      <input type="text" name="price_sunday" class="inp" id="price_sunday" value="<?= $price_sunday ?>" />
                    </td>
                  </tr>
                  <tr>
                    <td></td>
                    <td><input type="checkbox" name="duplicate_price" id="duplicate_price" onclick="duplicate_price_for_all_day(this.checked)" /> Set this for entire week</td>
                  </tr>
                  <tr>
                    <td width="25%" align="left" valign="middle"> <strong>Monday</strong> </td>
                    <td width="75%" align="left" valign="middle">
                      <input type="text" name="price_monday" class="inp" id="price_monday" value="<?= $price_monday ?>" />
                    </td>
                  </tr>
                  <tr>
                    <td width="25%" align="left" valign="middle"> <strong>Tuesday</strong> </td>
                    <td width="75%" align="left" valign="middle">
                      <input type="text" name="price_tuesday" class="inp" id="price_tuesday" value="<?= $price_tuesday ?>" />
                    </td>
                  </tr>
                  <tr>
                    <td width="25%" align="left" valign="middle"> <strong>Wednesday</strong> </td>
                    <td width="75%" align="left" valign="middle">
                      <input type="text" name="price_wednesday" class="inp" id="price_wednesday" value="<?= $price_wednesday ?>" />
                    </td>
                  </tr>
                  <tr>
                    <td width="25%" align="left" valign="middle"> <strong>Thursday</strong> </td>
                    <td width="75%" align="left" valign="middle">
                      <input type="text" name="price_thursday" class="inp" id="price_thursday" value="<?= $price_thursday ?>" />
                    </td>
                  </tr>
                  <tr>
                    <td width="25%" align="left" valign="middle"> <strong>Friday</strong> </td>
                    <td width="75%" align="left" valign="middle">
                      <input type="text" name="price_friday" class="inp" id="price_friday" value="<?= $price_friday ?>" />
                    </td>
                  </tr>
                  <tr>
                    <td width="25%" align="left" valign="middle"> <strong>Saturday</strong> </td>
                    <td width="75%" align="left" valign="middle">
                      <input type="text" name="price_saturday" class="inp" id="price_saturday" value="<?= $price_saturday ?>" />
                    </td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>
                      <input type="submit" name="action" value="Save" class="button" /> &nbsp;
                      <input type="submit" name="action" value="Save & Continue" class="button" />
                      <input type="hidden" name="act" value="add_edit" />
                    </td>
                  </tr>
                </table>
              </td>
              <td width="40%" valign="top">
                <table width="98%" border="0" align="center">
                  <tr>
                    <td><span class="blueFont bold font18">Seasons</span></td>
                    <td style="text-align:right">&nbsp; </td>
                  </tr>
                  <?php
                  $seasons_sql = db_query("select * from mv_hotel_seasons where fk_supplier_id='$id' and season_supplier_id='$hotel_supplier_id' and season_status='Active' order by season_start_date");
                  if (mysqli_num_rows($seasons_sql) > 0) {
                  ?>
                    <tr>
                      <td colspan="2">
                        <table width="100%" border="0" class="tbl-border" cellspacing="0" cellpadding="0">
                          <tr class="row1bg">
                            <td width="40%">&nbsp;Start</td>
                            <td width="40%">End</td>
                            <td width="20%">&nbsp;</td>
                          </tr>
                          <?php
                          $i = 0;
                          while ($seasons_res = mysqli_fetch_array($seasons_sql)) {
                            if ($i % 2 == 0) {
                              $cls = "row2bg";
                            } else {
                              $cls = "row3bg";
                            }
                            $i++;
                          ?>
                            <tr class="<?= $cls ?>">
                              <td>&nbsp;<a href="hotel/hotel_rooms.php?id=<?= $id ?>&rID=<?= $rID ?>&seasonID=<?= $seasons_res['season_id'] ?>&hotel_supplier_id=<?= $hotel_supplier_id ?>">
                                  <?php if ($seasons_res['season_id'] == $seasonID) {
                                    echo "<span class='selected_link'>" . season_date_format($seasons_res['season_start_date']) . "</span>";
                                  } else {
                                    echo season_date_format($seasons_res['season_start_date']);
                                  }
                                  ?>
                                </a></td>
                              <td> <a href="hotel/hotel_rooms.php?id=<?= $id ?>&rID=<?= $rID ?>&seasonID=<?= $seasons_res['season_id'] ?>&hotel_supplier_id=<?= $hotel_supplier_id ?>">
                                  <?php if ($seasons_res['season_id'] == $seasonID) {
                                    echo "<span class='selected_link'>" . season_date_format($seasons_res['season_end_date']) . "</span>";
                                  } else {
                                    echo season_date_format($seasons_res['season_end_date']);
                                  }
                                  ?>
                                </a> </td>
                              <td style="text-align:left"><input type="checkbox" name="season_ids[]" value="<?= $seasons_res['season_id'] ?>" <?php if ($seasons_res['season_id'] == $seasonID) {
                                                                                                                                              echo "checked";
                                                                                                                                            } ?> /></td>
                            </tr>
                          <?php } ?>
                        </table>
                    </tr>
                  <?php } ?>
                </table>
              </td>
            </tr>
          </table>
        </form>
      </td>
    </tr>
  </table>
</body>
</html>