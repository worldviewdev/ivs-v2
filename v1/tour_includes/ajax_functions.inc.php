<?php
$validator = new midas_validator();
// Initialize variables
$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
$tour_id = isset($_REQUEST['tour_id']) ? $_REQUEST['tour_id'] : 0;
$disp_id = isset($_REQUEST['disp_id']) ? $_REQUEST['disp_id'] : 0;
$edit_mode = isset($_REQUEST['edit_mode']) ? $_REQUEST['edit_mode'] : '';
$ftype = isset($_REQUEST['ftype']) ? $_REQUEST['ftype'] : '';
$country_code = isset($_REQUEST['country_code']) ? $_REQUEST['country_code'] : '';
$field_name = isset($_REQUEST['field_name']) ? $_REQUEST['field_name'] : 'supplier_state';
$month = isset($_REQUEST['month']) ? $_REQUEST['month'] : date('m');
$year = isset($_REQUEST['year']) ? $_REQUEST['year'] : date('Y');
$fk_country_id = isset($_REQUEST['fk_country_id']) ? $_REQUEST['fk_country_id'] : '';
$fk_region_id = isset($_REQUEST['fk_region_id']) ? $_REQUEST['fk_region_id'] : '';
$route_id = isset($_REQUEST['route_id']) ? $_REQUEST['route_id'] : '';
$vehicle_id = isset($_REQUEST['vehicle_id']) ? $_REQUEST['vehicle_id'] : '';
$fk_pickup_id = isset($_REQUEST['fk_pickup_id']) ? $_REQUEST['fk_pickup_id'] : '';
$fk_dropoff_id = isset($_REQUEST['fk_dropoff_id']) ? $_REQUEST['fk_dropoff_id'] : '';
$no_pax = isset($_REQUEST['no_pax']) ? $_REQUEST['no_pax'] : '';
$fk_arrival_airport_id = isset($_REQUEST['fk_arrival_airport_id']) ? $_REQUEST['fk_arrival_airport_id'] : '';
$fk_departure_airport_id = isset($_REQUEST['fk_departure_airport_id']) ? $_REQUEST['fk_departure_airport_id'] : '';
$fk_pickup_station_id = isset($_REQUEST['fk_pickup_station_id']) ? $_REQUEST['fk_pickup_station_id'] : '';
$fk_dropoff_station_id = isset($_REQUEST['fk_dropoff_station_id']) ? $_REQUEST['fk_dropoff_station_id'] : '';
$fk_arrival_region_id = isset($_REQUEST['fk_arrival_region_id']) ? $_REQUEST['fk_arrival_region_id'] : '';
$fk_departure_region_id = isset($_REQUEST['fk_departure_region_id']) ? $_REQUEST['fk_departure_region_id'] : '';
$fk_pickup_id = isset($_REQUEST['fk_pickup_id']) ? $_REQUEST['fk_pickup_id'] : '';
$fk_dropoff_id = isset($_REQUEST['fk_dropoff_id']) ? $_REQUEST['fk_dropoff_id'] : '';
$fk_arrival_airport_id = isset($_REQUEST['fk_arrival_airport_id']) ? $_REQUEST['fk_arrival_airport_id'] : '';
$fk_departure_airport_id = isset($_REQUEST['fk_departure_airport_id']) ? $_REQUEST['fk_departure_airport_id'] : '';
$fk_pickup_station_id = isset($_REQUEST['fk_pickup_station_id']) ? $_REQUEST['fk_pickup_station_id'] : '';
$fk_dropoff_station_id = isset($_REQUEST['fk_dropoff_station_id']) ? $_REQUEST['fk_dropoff_station_id'] : '';
$route = isset($_REQUEST['route']) ? $_REQUEST['route'] : '';
$fk_cat_id = isset($_REQUEST['fk_cat_id']) ? $_REQUEST['fk_cat_id'] : '';
$consortium_id = isset($_REQUEST['consortiumid']) ? $_REQUEST['consortiumid'] : '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$business_type = isset($_REQUEST['business_type']) ? $_REQUEST['business_type'] : '';
$location_tag = isset($_REQUEST['location_tag']) ? $_REQUEST['location_tag'] : '';
$accom_type = isset($_REQUEST['accom_type']) ? $_REQUEST['accom_type'] : '';
$transfer_type = isset($_REQUEST['transfer_type']) ? $_REQUEST['transfer_type'] : '';
$dep_transfer_country = isset($_REQUEST['dep_transfer_country']) ? $_REQUEST['dep_transfer_country'] : '';
$dep_region_id = isset($_REQUEST['dep_region_id']) ? $_REQUEST['dep_region_id'] : '';
$arr_transfer_country = isset($_REQUEST['arr_transfer_country']) ? $_REQUEST['arr_transfer_country'] : '';
$arr_region_id = isset($_REQUEST['arr_region_id']) ? $_REQUEST['arr_region_id'] : '';
$fk_pickup_station_id = isset($_REQUEST['fk_pickup_station_id']) ? $_REQUEST['fk_pickup_station_id'] : '';
$fk_dropoff_station_id = isset($_REQUEST['fk_dropoff_station_id']) ? $_REQUEST['fk_dropoff_station_id'] : '';
$fk_arrival_region_id = isset($_REQUEST['fk_arrival_region_id']) ? $_REQUEST['fk_arrival_region_id'] : '';
$fk_departure_region_id = isset($_REQUEST['fk_departure_region_id']) ? $_REQUEST['fk_departure_region_id'] : '';
$fk_pickup_id = isset($_REQUEST['fk_pickup_id']) ? $_REQUEST['fk_pickup_id'] : '';
$fk_dropoff_id = isset($_REQUEST['fk_dropoff_id']) ? $_REQUEST['fk_dropoff_id'] : '';
$fk_arrival_airport_id = isset($_REQUEST['fk_arrival_airport_id']) ? $_REQUEST['fk_arrival_airport_id'] : '';
$fk_departure_airport_id = isset($_REQUEST['fk_departure_airport_id']) ? $_REQUEST['fk_departure_airport_id'] : '';
$fk_pickup_id = isset($_REQUEST['fk_pickup_id']) ? $_REQUEST['fk_pickup_id'] : '';
$fk_dropoff_id = isset($_REQUEST['fk_dropoff_id']) ? $_REQUEST['fk_dropoff_id'] : '';
$fk_arrival_airport_id = isset($_REQUEST['fk_arrival_airport_id']) ? $_REQUEST['fk_arrival_airport_id'] : '';
$fk_departure_airport_id = isset($_REQUEST['fk_departure_airport_id']) ? $_REQUEST['fk_departure_airport_id'] : '';
$fk_pickup_station_id = isset($_REQUEST['fk_pickup_station_id']) ? $_REQUEST['fk_pickup_station_id'] : '';
$fk_dropoff_station_id = isset($_REQUEST['fk_dropoff_station_id']) ? $_REQUEST['fk_dropoff_station_id'] : '';
$fk_region_id = isset($_REQUEST['fk_region_id']) ? $_REQUEST['fk_region_id'] : '';
$fk_country_id = isset($_REQUEST['fk_country_id']) ? $_REQUEST['fk_country_id'] : '';
$fk_location_id = isset($_REQUEST['fk_location_id']) ? $_REQUEST['fk_location_id'] : '';
$keyword = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : '';
switch ($_REQUEST['operation']) {
	case 'get_states_dropdown':
		$code = get_country_code($country_code);
		$sql = "select count(*) from tbl_states where state_country_code = '$code' order by state_name";
		$count = db_scalar($sql);
		if ($count > 0) {
			echo state_dropdown($code, $field_name, '', '  style="width:155px;"', 'Select State');
		} else {
			echo '<input name="' . $field_name . '" type="text"  style="width:155px;" />';
		}
		break;
	case 'show_cal':
		require_once(MAIN_TOUR_DIR . '/calendar_front.inc.php');
		$town_cal = new Calendar_town();
		echo $town_cal->getMonthHTML($month, $year, '1', $tour_id, $disp_id, $edit_mode);
		break;
	case 'show_action_cal':
		require_once(MAIN_TOUR_DIR . '/action_dates.inc.php');
		$town_cal = new Calendar_town_log();
		echo $town_cal->getMonthHTML($month, $year, '1', $id, $disp_id, $edit_mode);
		break;
	case 'change_cal':
		require_once(MAIN_TOUR_DIR . '/tour_calendar.inc.php');
		$town_cal = new Calendar_town();
		echo $town_cal->getMonthHTML($month, $year, '1', $tour_id, $disp_id, $edit_mode, 'Yes');
		break;
	case 'show_states':
		echo state_dropdown($_REQUEST['countryid'], "supplier_state", "", '', 'Select State/Province');
		break;
	case 'show_states_with_field':
		echo state_dropdown($_REQUEST['countryid'], $_REQUEST['fieldname'], "", '', 'Select State/Province');
		break;
	case 'show_states_with_textfield':
		echo '<input name="' . $_REQUEST['fieldname'] . '" id="' . $_REQUEST['fieldname'] . '" value="" class="inp" />';
		break;
	case 'show_dmc_from_country':
		echo sql_dropdown("select supplier_id,supplier_company_name from mv_supplier where supplier_status!='Delete' and FIND_IN_SET('3',supplier_business_type) and supplier_country='" . $_REQUEST['countryid'] . "' order by supplier_company_name", $_REQUEST['fieldname'],  '', '', 'Select');
		break;
	case 'show_hotels_from_type':
		echo sql_dropdown("select supplier_id,supplier_company_name from mv_supplier where supplier_status!='Delete' and FIND_IN_SET('1',supplier_business_type) and supplier_hotel_type='" . $_REQUEST['type'] . "' order by supplier_company_name", $_REQUEST['fieldname'],  '', '', 'Select');
		break;
	case 'show_consortium_commission':
		echo db_scalar("select consortium_commission_rate from mv_consortium where consortium_id='" . $_REQUEST['consortiumid'] . "'");
		break;
	case 'show_flight_city_dropdown':
		echo sql_dropdown("select t.tag_id,t.tag_name from mv_location_airport a inner join mv_tags t on a.fk_location_id=t.tag_id where airport_status!='Delete' and fk_country_id='" . $_REQUEST['countryid'] . "' group by  fk_location_id order by tag_name", "fk_region_id",  '', 'onchange="show_flight_airport_dropdown(this.value,\'' . SITE_WS_PATH . '\')"', 'Select City');
		break;
	case 'show_flight_airport_dropdown':
		echo sql_dropdown("select airport_id,airport_title from mv_location_airport where airport_status!='Delete' and fk_location_id='" . $_REQUEST['cityid'] . "' order by airport_title", "fk_airport_id",  '', 'onchange="show_flight_supplier(\'' . SITE_WS_PATH . '\');"', 'Select Airport');
		break;
	case 'show_flight_arrival_city_dropdown':
		echo sql_dropdown("select t.tag_id,t.tag_name from mv_location_airport a inner join mv_tags t on a.fk_location_id=t.tag_id where airport_status!='Delete' and fk_country_id='" . $_REQUEST['countryid'] . "' group by  fk_location_id order by tag_name", "fk_arrival_region_id",  '', 'onchange="show_flight_arrival_airport_dropdown(this.value,\'' . SITE_WS_PATH . '\')"', 'Select City');
		break;
	case 'show_flight_arrival_airport_dropdown':
		echo sql_dropdown("select airport_id,airport_title from mv_location_airport where airport_status!='Delete' and fk_location_id='" . $_REQUEST['cityid'] . "' order by airport_title", "fk_arrival_airport_id",  '', 'onchange="show_flight_supplier(\'' . SITE_WS_PATH . '\');"', 'Select Airport');
		break;
	case 'show_regions':
		echo region_list($_REQUEST['countryid'], "");
		break;
	case 'show_provinces':
		echo province_list($_REQUEST['regionid'], "");
		break;
	case 'show_comunes':
		echo comune_list($_REQUEST['provinceid'], "");
		break;
	case 'show_region_comunes':
		echo region_comune_list($_REQUEST['regionid'], "");
		break;
	case 'show_country_tags_dropdown':
		$transportation_type = $_REQUEST['tagid'];
		if ($transportation_type == "217") {
			$country_parent = '35';
		} elseif ($transportation_type == "218") {
			$country_parent = '163';
		} elseif ($transportation_type == "219") {
			$country_parent = '162';
		} elseif ($transportation_type == "220") {
			$country_parent = '164';
		} elseif ($transportation_type == "221") {
			$country_parent = '222';
		}
		echo sql_dropdown("select tag_id,tag_name from mv_tags where tag_status!='Delete' and tag_parent_id='$country_parent' and tag_parent_id!='' order by tag_name", "fk_country_id",  $fk_country_id, 'onchange="show_region_tags_dropdown(this.value,\'fk_country_id\',\'' . SITE_WS_PATH . '\')"', 'Select');
		break;
	case 'show_region_tags_dropdown':
		echo sql_dropdown("select tag_id,tag_name from mv_tags where tag_status!='Delete' and tag_parent_id='" . $_REQUEST['tagid'] . "' and tag_parent_id!='' order by tag_name", "fk_region_id",  $fk_region_id, 'onchange="show_routes_tags_dropdown(this.value,\'fk_region_id\',\'' . SITE_WS_PATH . '\')"', 'Select');
		break;
	case 'show_departure_location_tags_dropdown':
		echo sql_dropdown("select tag_id,tag_name from mv_tags where tag_status!='Delete' and tag_parent_id='" . $_REQUEST['tagid'] . "' and tag_parent_id!='' order by tag_name", "fk_region_id",  $fk_region_id, 'onchange="show_tags_dropdown(this.value,\'fk_pickup_id\',\'disp_pickup\',\'' . SITE_WS_PATH . '\',\'pick up\');show_tags_dropdown(this.value,\'fk_dropoff_id\',\'disp_dropoff\',\'' . SITE_WS_PATH . '\',\'drop off\')"', 'Select');
		break;
	case 'show_tags_dropdown':
		if ($_REQUEST['show_parent'] != "") {
			if ($_REQUEST['show_parent'] != "") {
				$parent = db_scalar("select tag_id from mv_tags where tag_status!='Delete' and tag_parent_id='" . $_REQUEST['tagid'] . "' and LOWER(tag_name)='" . $_REQUEST['show_parent'] . "'");
			}
			echo sql_dropdown("select tag_id,tag_name from mv_tags where tag_status!='Delete' and tag_parent_id='" . $parent . "' and tag_parent_id!='' order by tag_name", $_REQUEST['fieldname'],  '', '', '');
		} else {
			echo sql_dropdown("select tag_id,tag_name from mv_tags where tag_status!='Delete' and tag_parent_id='" . $_REQUEST['tagid'] . "' and tag_parent_id!='' order by tag_name", $_REQUEST['fieldname'],  '', '', '');
		}
		break;
	case 'show_location_tags_dropdown':
		if ($_REQUEST['level'] != "") {
			$level = $_REQUEST['level'] + 1;
		}
		$onlick = '';
		if ($_REQUEST['funtion_to_call'] == "function1") {
			$keyword = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : '';
			$onlick = 'show_supplier_list(\'' . SITE_WS_PATH . '\',\'' . $keyword . '\');';
		}
		if ($_REQUEST['prefix'] != "") {
			$prefix = $_REQUEST['prefix'];
		} else {
			$prefix = "";
		}
		echo sql_location_dropdown("select tag_id,tag_name from mv_tags where tag_status!='Delete' and tag_parent_id='" . $_REQUEST['tagid'] . "' and tag_parent_id!='' order by tag_name", $_REQUEST['fieldname'],  '', 'onchange="show_location_tags_dropdown(this.value,\'level' . $prefix . $level . '\',\'disp' . $prefix . $level . '\',\'' . SITE_WS_PATH . '\',\'' . $level . '\',\'' . $prefix . '\',\'' . $_REQUEST['funtion_to_call'] . '\');' . $onlick . '" style="margin-top:5px;"', 'Select');
		break;
	case 'show_location_tags_for_accommodation':
		if ($_REQUEST['level'] != "") {
			$level = $_REQUEST['level'] + 1;
		}
		if ($_REQUEST['prefix'] != "") {
			$prefix = $_REQUEST['prefix'];
		} else {
			$prefix = "";
		}
		echo sql_location_dropdown("select tag_id,tag_name from mv_tags where tag_status!='Delete' and tag_parent_id='" . $_REQUEST['tagid'] . "' and tag_parent_id!='' order by tag_name", $_REQUEST['fieldname'],  '', 'onchange="show_location_tags_for_accommodation(this.value,\'level' . $prefix . $level . '\',\'disp' . $prefix . $level . '\',\'' . SITE_WS_PATH . '\',\'' . $level . '\',\'' . $prefix . '\');show_accommodation_list(\'' . SITE_WS_PATH . '\');" style="margin-top:5px;"', 'Select');
		break;
	case 'show_routes_tags_dropdown':
		echo make_sql_checkboxes("select tag_id,tag_name from mv_tags where tag_parent_id='" . $_REQUEST['tagid'] . "' and tag_status!='Delete'  and tag_parent_id!=''", 'route', $route, '2', '', '', 'width="100%"');
		break;
	case 'show_location_region_tags':
		echo sql_dropdown("select tag_id,tag_name from mv_tags where tag_status!='Delete' and tag_parent_id='" . $_REQUEST['tagid'] . "' and tag_parent_id!='' order by tag_name", "tour_region",  "", 'onchange="show_city_tags_dropdown(this.value,\'' . SITE_WS_PATH . '\')"', 'Select');
		break;
	case 'show_city_tags_dropdown':
		echo sql_dropdown("select tag_id,tag_name from mv_tags where tag_status!='Delete' and tag_parent_id='" . $_REQUEST['tagid'] . "' and tag_parent_id!='' order by tag_name", "tour_city",  "", '', 'Select');
		break;
	case 'show_faq_category':
		echo sql_dropdown("select faq_cat_id,faq_cat_name from mv_faq_category where faq_cat_status!='Delete' and faq_parent_id='" . $_REQUEST['catid'] . "' and faq_parent_id>0 order by faq_cat_name", "fk_cat_id",  $fk_cat_id, '', 'Select');
		break;
	case 'show_other_transer':
		echo sql_dropdown("select tag_id,tag_name from mv_tags where tag_status!='Delete' and tag_parent_id='" . $_REQUEST['transfer_type'] . "' and tag_parent_id!='' order by tag_name", "dep_transfer_country",  '', 'onchange="show_other_transfer_region_tags_dropdown(this.value,\'dep_region_id\',\'' . SITE_WS_PATH . '\');"', 'Select');
		break;
	case 'show_arrival_transer_country':
		echo sql_dropdown("select tag_id,tag_name from mv_tags where tag_status!='Delete' and tag_parent_id='" . $_REQUEST['transfer_type'] . "' and tag_parent_id!='' order by tag_name", "arr_transfer_country",  '', 'onchange="show_arrival_transfer_region_tags_dropdown(this.value,\'arr_region_id\',\'' . SITE_WS_PATH . '\');"', 'Select');
		break;
	case 'show_locality':
		echo locality_list($_REQUEST['comuneid'], "");
		break;
	case 'get_agency_list':
		$res = db_query("select agency_id,agency_name from mv_agency where agency_status!='Delete'");
		break;
	case 'show_exchange_price':
		$var = '<table cellspacing="0" cellpadding="0" width="100%" border="0">
			  <tr>
				<th colspan="2"><strong> Output NET Cost</strong></th>
			  </tr>
			  <tr><td>EUR</td><td>' . convert_price($_REQUEST['amount'], $_REQUEST['currency'], "EUR") . '</td></tr>
			  <tr  class="green_back"><td>USD</td><td>' . convert_price($_REQUEST['amount'], $_REQUEST['currency'], "USD") . '</td></tr>
			  <tr><td>GBP</td><td>' . convert_price($_REQUEST['amount'], $_REQUEST['currency'], "GBP") . '</td></tr>
			  <tr  class="green_back"><td>CDN</td><td>' . convert_price($_REQUEST['amount'], $_REQUEST['currency'], "CAD") . '</td></tr>
			  <tr><td>AUS</td><td>' . convert_price($_REQUEST['amount'], $_REQUEST['currency'], "AUD") . '</td></tr>';
		if ($_REQUEST['commission'] != "") {
			$var .= '<tr>
						<th colspan="2"><strong> Output Price With Commission</strong></th>
					  </tr>
					  <tr><td>EUR</td><td>' . convert_price_with_commission($_REQUEST['amount'], $_REQUEST['currency'], "EUR", $_REQUEST['commission']) . '</td></tr>
					  <tr  class="green_back"><td>USD</td><td>' . convert_price_with_commission($_REQUEST['amount'], $_REQUEST['currency'], "USD", $_REQUEST['commission']) . '</td></tr>
					  <tr><td>GBP</td><td>' . convert_price_with_commission($_REQUEST['amount'], $_REQUEST['currency'], "GBP", $_REQUEST['commission']) . '</td></tr>
					  <tr  class="green_back"><td>CDN</td><td>' . convert_price_with_commission($_REQUEST['amount'], $_REQUEST['currency'], "CAD", $_REQUEST['commission']) . '</td></tr>
					  <tr><td>AUS</td><td>' . convert_price_with_commission($_REQUEST['amount'], $_REQUEST['currency'], "AUD", $_REQUEST['commission']) . '</td></tr>';
		}
		$var .= '</table>';
		echo $var;
		break;
		case 'show_accmmodation_result':
			$sql_add = "";
			// Filter keyword
			if (!empty($_REQUEST['keyword'])) {
				$keyword = trim($_REQUEST['keyword']);
				$keyword_escaped = addslashes($keyword);
				// Gabungkan OR untuk frasa dan setiap kata
				$sql_add .= " AND (supplier_company_name LIKE '%$keyword_escaped%'";
				$keyword_array = preg_split('/\s+/', $keyword);
				foreach ($keyword_array as $word) {
					$word_escaped = addslashes($word);
					$sql_add .= " OR supplier_company_name LIKE '%$word_escaped%'";
				}
				$sql_add .= ")";
			}
			// Filter location tag
			$location_tag = ltrim($_REQUEST['location_tag'], ',');
			if (!empty($location_tag)) {
				$tags = db_query("SELECT TRIM(tag_name) AS tag_name FROM mv_tags WHERE tag_id IN ($location_tag)");
				$location_conditions = [];
				while ($row = mysqli_fetch_assoc($tags)) {
					$tag_name = addslashes($row['tag_name']);
					$location_conditions[] = "location_tags_name LIKE '%$tag_name%'";
				}
				if (!empty($location_conditions)) {
					$sql_add .= " AND (" . implode(" OR ", $location_conditions) . ")";
				}
			}
			// Filter accommodation type
			if (!empty($_REQUEST['accom_type'])) {
				$accom_type = addslashes($_REQUEST['accom_type']);
				$sql_add .= " AND supplier_accommodation_type = '$accom_type'";
			}
			if (!empty($_REQUEST['keyword'])) {
				$keyword = trim($_REQUEST['keyword']);
				$keyword_escaped = addslashes($keyword);
				$sql_add .= " AND supplier_company_name LIKE '%$keyword_escaped%'";
			}
			// Tanggal
			$start_date = $_REQUEST['start_date'];
			$end_date = $_REQUEST['end_date'];
			$sql = "SELECT s.*,
						   t.tag_name AS supp_type,
						   t2.tag_name AS hotel_type
					FROM mv_supplier s
					LEFT JOIN mv_tags t ON s.supplier_accommodation_type = t.tag_id
					LEFT JOIN mv_tags t2 ON s.supplier_hotel_type = t2.tag_id
					WHERE supplier_status != 'Delete'
					  AND (
						   FIND_IN_SET('1', supplier_business_type)
						   OR FIND_IN_SET('5', supplier_business_type)
						   OR FIND_IN_SET('17', supplier_business_type)
					  )
					  AND (
						   supplier_closed_from IS NULL OR '$start_date' NOT BETWEEN supplier_closed_from AND supplier_closed_to
					  )
					  AND (
						   supplier_closed_to IS NULL OR '$end_date' NOT BETWEEN supplier_closed_from AND supplier_closed_to
					  )
					  AND (
						   supplier_closed_from IS NULL OR supplier_closed_from NOT BETWEEN '$start_date' AND '$end_date'
					  )
					  AND (
						   supplier_closed_to IS NULL OR supplier_closed_to NOT BETWEEN '$start_date' AND '$end_date'
					  )
					  $sql_add
					ORDER BY is_prefered DESC, supplier_company_name ASC";
			$result = db_query($sql);
			$var = '';
			if (mysqli_num_rows($result) > 0) {
				$var .= '<table width="100%" border="0" cellspacing="1" cellpadding="0">
							<tr class="row1bg">
								<td width="5%">Available</td>
								<td width="10%">Type</td>
								<td width="30%">Supplier Name</td>
								<td width="35%">Location</td>
								<td width="15%">Contract Type</td>
								<td style="text-align: center;" width="5%">&nbsp;</td>
							</tr>';
				$ii = 0;
				while ($line_raw = mysqli_fetch_array($result)) {
					$line = ms_display_value($line_raw);
					@extract($line);
					$cls = ($ii % 2 == 0) ? ($is_prefered ? 'row9bg' : 'row2bg') : ($is_prefered ? 'row9bg' : 'row3bg');
					$ii++;
					$season_ids = db_scalar("SELECT GROUP_CONCAT(season_id)
											 FROM mv_hotel_seasons
											 WHERE season_status = 'Active'
											   AND fk_supplier_id = '$supplier_id'
											   AND ('$start_date' <= season_end_date)
											   AND ('$end_date' >= season_start_date)");
					$var .= '<tr class="'.$cls.'">';
					$var .= '<td valign="top">' . ($season_ids != '' ? '<img src="'.SITE_WS_PATH.'/'.AGENT_ADMIN_DIR.'/images/icons/tick.png">' : '<img src="'.SITE_WS_PATH.'/'.AGENT_ADMIN_DIR.'/images/icons/delete.gif">') . '</td>';
					$var .= '<td valign="top">'.$supp_type . ($hotel_type != '' && $hotel_type != 'Hotel' ? " ($hotel_type)" : '') . '</td>';
					$var .= '<td valign="top">'.$supplier_company_name.'</td>';
					$var .= '<td valign="top">'.str_replace(",", " -> ", $location_tags_name).'</td>';
					$var .= '<td valign="top">'.get_contract_type($supplier_id, $season_ids).'</td>';
					$var .= '<td align="center"><input name="selected_hotel" type="radio" value="'.$supplier_id.'" /></td>';
					$var .= '</tr>';
				}
				$var .= '</table>
						<br />
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td align="right" style="padding:2px">
									<input type="submit" class="button" name="Submit" value="Add Accommodation" />
									<input name="action" type="hidden" id="action" value="continue" />
								</td>
							</tr>
						</table>';
			}
			echo $var;
			break;
		case 'show_transfer_tags_dropdown':
		echo sql_dropdown("select tag_id,tag_name from mv_tags where tag_status!='Delete' and tag_parent_id='" . $_REQUEST['tagid'] . "' and tag_parent_id!='' order by tag_name", $_REQUEST['fieldname'],  '', 'onchange="show_other_transfer_supplier(\'' . SITE_WS_PATH . '\');"', 'Select');
		break;
	case 'show_transfer_region_tags_dropdown':
		echo sql_dropdown("select tag_id,tag_name from mv_tags where tag_status!='Delete' and tag_parent_id='" . $_REQUEST['tagid'] . "' and tag_parent_id!='' order by tag_name", $_REQUEST['fieldname'],  '', 'onchange="show_transfer_pickup_tags(this.value,\'fk_pickup_id\',\'' . SITE_WS_PATH . '\');show_transfer_dropoff_tags(this.value,\'fk_dropoff_id\',\'' . SITE_WS_PATH . '\');"', 'Select');
		break;
	case 'show_transfer_pickup_tags':
		$pickup_parent = db_scalar("select tag_id from mv_tags where tag_status!='Delete' and tag_parent_id='" . $_REQUEST['tagid'] . "' and LOWER(tag_name)='pick up'");
		echo sql_dropdown("select tag_id,tag_name from mv_tags where tag_status!='Delete' and tag_parent_id='" . $pickup_parent . "' and tag_parent_id!='' order by tag_name", $_REQUEST['fieldname'],  '', 'onchange="show_transfer_price(\'' . SITE_WS_PATH . '\');show_transfer_supplier(\'' . SITE_WS_PATH . '\')"', 'Select');
		break;
	case 'show_transfer_dropoff_tags':
		$dropoff_parent = db_scalar("select tag_id from mv_tags where tag_status!='Delete' and tag_parent_id='" . $_REQUEST['tagid'] . "' and LOWER(tag_name)='Drop Off'");
		echo sql_dropdown("select tag_id,tag_name from mv_tags where tag_status!='Delete' and tag_parent_id='" . $dropoff_parent . "' and tag_parent_id!='' order by tag_name", $_REQUEST['fieldname'],  '', 'onchange="show_transfer_price(\'' . SITE_WS_PATH . '\');show_transfer_supplier(\'' . SITE_WS_PATH . '\')"', 'Select');
		break;
	case 'show_online_transfer_pickup_tags':
		$pickup_parent = db_scalar("select tag_id from mv_tags where tag_status!='Delete' and tag_parent_id='" . $_REQUEST['tagid'] . "' and LOWER(tag_name)='pick up'");
		echo sql_dropdown("select distinct(fk_pickup_id),tag_name from mv_transfers_description_list tr left join mv_tags t on tr.fk_pickup_id=t.tag_id where description_status='Active' and tag_status='Active' and tag_parent_id='" . $pickup_parent . "'  and tag_parent_id!='' order by tag_name", $_REQUEST['fieldname'],  '', 'class="form-control" title="From" required onchange="show_online_transfer_dropoff_tags(this.value,\'fk_dropoff_id\',\'' . SITE_WS_PATH . '\');"', 'Select');
		break;
	case 'show_online_transfer_dropoff_tags':
		echo sql_dropdown("select distinct(fk_dropoff_id),tag_name from mv_transfers_description_list tr left join mv_tags t on tr.fk_dropoff_id=t.tag_id where description_status='Active' and tag_status='Active' and fk_pickup_id='" . $_REQUEST['tagid'] . "'  and tag_parent_id!='' order by tag_name", $_REQUEST['fieldname'],  '', 'class="form-control" title="From" required onchange="show_vehicle_price(\'' . SITE_WS_PATH . '\');"', 'Select');
		break;
	case 'show_transfer_price':
		$tp_price = db_scalar("SELECT tp_price FROM igs_transfer_price WHERE tp_pickup='$fk_pickup_id' AND tp_dropoff='$fk_dropoff_id' AND tp_pax='$no_pax' and tp_status='Active'");
		if ($tp_price != "")
			echo display_price($tp_price);
		break;
	case 'show_vehicle_price':
		echo show_online_transfer_price($fk_pickup_id, $fk_dropoff_id);
		break;
	case 'show_other_transfer_region_tags_dropdown':
		echo sql_dropdown("select tag_id,tag_name from mv_tags where tag_status!='Delete' and tag_parent_id='" . $_REQUEST['tagid'] . "' and tag_parent_id!='' order by tag_name", $_REQUEST['fieldname'],  '', 'onchange="show_transfer_tags_dropdown(this.value,\'fk_pickup_station_id\',\'disp_departure\',\'' . SITE_WS_PATH . '\')"', 'Select');
		break;
	case 'show_ferry_region_tags_dropdown':
		echo sql_dropdown("select tag_id,tag_name from mv_tags where tag_status!='Delete' and tag_parent_id='" . $_REQUEST['tagid'] . "' and tag_parent_id!='' order by tag_name", $_REQUEST['fieldname'],  '', 'onchange="show_other_transfer_supplier(\'' . SITE_WS_PATH . '\');"', 'Select');
		break;
	case 'show_arrival_transfer_region_tags_dropdown':
		echo sql_dropdown("select tag_id,tag_name from mv_tags where tag_status!='Delete' and tag_parent_id='" . $_REQUEST['tagid'] . "' and tag_parent_id!='' order by tag_name", $_REQUEST['fieldname'],  '', 'onchange="show_transfer_tags_dropdown(this.value,\'fk_dropoff_station_id\',\'disp_arrival\',\'' . SITE_WS_PATH . '\')"', 'Select');
		break;
	case 'show_transfer_result':
		echo transfer_list($_REQUEST['transfer_country'], $_REQUEST['fk_region_id'], $_REQUEST['fk_pickup_id'], $_REQUEST['fk_dropoff_id'], $_REQUEST['dmc_transfer_type'], '', '', $_REQUEST['pax']);
		break;
	case 'show_other_transfer_result':
		echo other_transfer_list($_REQUEST['dmc_transfer_type'], $_REQUEST['dep_transfer_country'], $_REQUEST['dep_region_id'], $_REQUEST['fk_pickup_station_id'], $_REQUEST['arr_transfer_country'], $_REQUEST['arr_region_id'], $_REQUEST['fk_dropoff_station_id']);
		break;
	case 'show_flight_supplier':
		echo flight_supplier_list($_REQUEST['dep_airport_id'], $_REQUEST['arrival_airport_id']);
		break;
	case 'show_supplier_result':
		supplier_list($_REQUEST['business_type'], $_REQUEST['keyword'], $_REQUEST['location_tag']);
		break;
	case 'show_route_pricing_option':
		$result = db_result("SELECT *,t.tag_name as pickup,t2.tag_name as dropoff FROM mv_transportation_route r left join mv_tags t on t.tag_id=r.fk_pickup_id left join mv_tags t2 on t2.tag_id=r.fk_dropoff_id where route_id = '$route_id'");
		if ($result['is_vehicle_available'] == "Yes") {
			echo '<table width="100%" border="0"><tr><td width="35%"><strong>Rate Method</strong></td><td width="65%"><input type="radio" onclick="disp_vehicles(this.value);" name="pricing_option" value="PP" checked> Per Pax <input type="radio" onclick="disp_vehicles(this.value);" name="pricing_option" value="PV"> Vehicle </td></tr><tr><td colspan="2" width="100%" ><div id="disp_vehicle_list" style="display:none;"><table width="100%" border="0"><tr><td width="30%" valign="top"><strong>Vehicles</strong></td><td width="70%">';
			echo make_sql_checkboxes("select route_vehicle_id,concat(vehicle_name,' (',vehicle_capacity,') ') from mv_transportation_vehicle where fk_route_id='$route_id' and vehicle_status!='Delete' order by vehicle_name", 'vehicle_id', $vehicle_id, '1', '', '', 'width="100%"');
			echo "</td></tr></table></td></tr></table>";
		} else {
			echo '<input type="hidden" name="pricing_option" value="PP">';
		}
		break;
	case 'booking_engine_country_operation':
		country_operation($_REQUEST['counter']);
		break;
	case 'show_multilocation_tags_dropdown':
		if ($_REQUEST['level'] != "") {
			$level = $_REQUEST['level'] + 1;
		}
		$onlick = '';
		if (isset($_SESSION['showLatLon'])) {
			if ($_SESSION['showLatLon'] != 'multilocation') {
				$onlick .= 'show_location_map_lat_lon(this.value,\'' . SITE_WS_PATH . '\');';
			} else {
				$onlick .= 'show_location_multi_map_lat_lon(this.value,\'' . SITE_WS_PATH . '\',\'' . $_REQUEST['mid'] . '\');';
			}
		}
		if ($_REQUEST['funtion_to_call'] == "function1") {
			$onlick .= 'show_supplier_list(\'' . SITE_WS_PATH . '\',\'' . $_REQUEST['keyword'] . '\');';
		}
		if ($_REQUEST['prefix'] != "") {
			$prefix = $_REQUEST['prefix'];
		} else {
			$prefix = "";
		}
		echo sql_location_dropdown("select tag_id,tag_name from mv_tags where tag_status!='Delete' and tag_parent_id='" . $_REQUEST['tagid'] . "' and tag_parent_id!='' order by tag_name", $_REQUEST['fieldname'],  '', 'onchange="show_multi_location_tags_dropdown(this.value,\'level' . $prefix . $level . '\',\'disp' . $prefix . $level . '\',\'' . SITE_WS_PATH . '\',\'' . $level . '\',\'' . $prefix . '\',\'' . $_REQUEST['funtion_to_call'] . '\',\'' . $_REQUEST['mid'] . '\');' . $onlick . '" style="margin-top:5px;"', 'Select');
		break;
	case 'show_location_map_lat_lon':
		$result = db_result("select map_latitude,map_longitude from mv_tags where tag_status!='Delete' and tag_id='" . $_REQUEST['tagid'] . "' and tag_parent_id!=''");
		//echo "select map_latitude,map_longitude from mv_tags where tag_status!='Delete' and tag_id='".$_REQUEST[tagid]."' and tag_parent_id!=''";
		echo $result['map_latitude'] . ':' . $result['map_longitude'];
		break;
	default:
		echo 'Invalid Operation';
		break;
}