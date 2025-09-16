<?php
// Make sure nothing is output before headers are sent
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$ARR_PERMISSION = array("SuperAdmin" => "SuperAdmin", "Agent" => "Agent", "Admin" => "Admin", "Accountant" => "Accountant", "Expedia" => "Expedia");
$ARR_TOUR_YEAR = array("2011" => "2011", "2012" => "2012");
$arr_trip_language = array("English" => "English", "French" => "French", "German" => "German", "Spanish" => "Spanish", "Italian" => "Italian");
$arr_rome_areas = array('Colosseum-Forum', 'Trevi Fountain', 'Spanish Steps', 'Piazza Navona', 'Via Veneto', 'Termini Station', 'Villa Borghese', 'Pantheon', 'Trastevere', 'Porta Portese', 'Vatican', 'Aventino-Testaccio', 'Prati', 'Parioli', 'Flaminio', 'Reppublica', 'Marsala', 'San Lorenzo', 'Esquilino', 'University', 'Ciampino Airport', 'Fiumicino Airport');
$arr_venice_areas = array("Burano", "Cannareggio East", "Cannareggio West", "Castello East", "Castello West", "Dorsoduro East", "Dorsoduro West", "Giudecca", "Lido North", "Lido South", "Malamocco - Alberoni", "Murano", "Murano", "San Marco", "San Polo", "Sant Elena", "Santa Croce East", "Santa Croce West", "Venice Airport");
$arr_credit_cards = array("Visa" => "Visa", "Mastercard" => "Mastercard", "American Express" => "American Express", "Discovery" => "Discovery");
$ARR_ACTIVE = array("Active" => "Active", "Inactive" => "Inactive", "Delete" => "Delete");
function display_agent_message()
{
    $var = "";
    if (!empty($_SESSION['agent_msg'])) {
        $cls = (!empty($_SESSION['agent_msg_class']) ? $_SESSION['agent_msg_class'] : 'error_msg');
        $var = '<table cellspacing="0" cellpadding="0" width="100%" border="0" class="' . $cls . '"><tr><td valign="top" align="center" style="padding:5px 0px 5px 5px;" width="5%">';
        if ($cls == "error_msg") {
            $img = SITE_WS_PATH . '/' . AGENT_ADMIN_DIR . '/images/error.png';
        } else {
            $img = SITE_WS_PATH . '/' . AGENT_ADMIN_DIR . '/images/sucess.png';
        }
        $var .= '<img src="' . $img . '" border="0" alt="" /></td><td align="left"><ul>' . $_SESSION['agent_msg'] . '</ul></td></tr></table><br />';
        // Reset session messages
        $_SESSION['agent_msg'] = '';
        $_SESSION['agent_msg_class'] = '';
    }
    return $var;
}
function protect_agent_page()
{
    $cur_page = basename($_SERVER['PHP_SELF']);
    if ($cur_page != 'my-secure-login.php' && $cur_page != 'forget.php') {
        if ($_SESSION['sess_agent_id'] == '' || (time() - $_SESSION['sess_last_activity'] > SESSION_TIMEOUT)) {
            // destroy all session
            session_destroy();
            $_SESSION['sess_agent_redirect_url'] = $_SERVER['REQUEST_URI'];
            header('Location: ' . SITE_WS_PATH . '/' . AGENT_ADMIN_DIR . '/my-secure-login.php');
            exit;
        } else {
            $chk_permission = 0;
            if ($_SESSION['sess_agent_type'] == 'Agent') {
                $chk_permission = db_scalar("select count(*) from mv_page_permission where mv_file_name='" . $cur_page . "' and mv_agent_permission='N'");
            } elseif ($_SESSION['sess_agent_type'] == 'Accountant') {
                $chk_permission = db_scalar("select count(*) from mv_page_permission where mv_file_name='" . $cur_page . "' and mv_accountant_permission='N'");
            }
            if ($chk_permission > 0) {
                header('Location: ' . SITE_WS_PATH . '/' . AGENT_ADMIN_DIR . '/index.php');
                exit;
            }
        }
    }
}
function protect_agent_page_new()
{
    $cur_page = basename($_SERVER['PHP_SELF']);
    if ($cur_page != 'my-secure-login.php' && $cur_page != 'forget.php') {
        if ($_SESSION['sess_agent_id'] == '') {
            $_SESSION['sess_agent_redirect_url'] = $_SERVER['REQUEST_URI'];
            header('Location: /my-secure-login.php');
            exit;
        } else {
            $chk_permission = 0;
            if ($_SESSION['sess_agent_type'] == 'Agent') {
                $chk_permission = db_scalar("select count(*) from mv_page_permission where mv_file_name='" . $cur_page . "' and mv_agent_permission='N'");
            } elseif ($_SESSION['sess_agent_type'] == 'Accountant') {
                $chk_permission = db_scalar("select count(*) from mv_page_permission where mv_file_name='" . $cur_page . "' and mv_accountant_permission='N'");
            }
            if ($chk_permission > 0) {
                header('Location: ' . SITE_WS_PATH . '/' . AGENT_ADMIN_DIR . '/index.php');
                exit;
            }
        }
    }
}
function get_state_id($state_slug)
{
    return db_scalar("select state_id from tbl_italy_region where state_slug='" . $state_slug . "'");
}
function currency_ratio($currency1, $currency2)
{
    $ratio = 1;
    if ($currency1 != $currency2) {
        $res = db_result("select currency_value from mv_currency where convert_currency_from='$currency1' and convert_currency_to='$currency2'");
        $ratio = isset($res['currency_value']) ? $res['currency_value'] : 1;
    }
    return $ratio;
}
function currency_api_ratio($currency1, $currency2)
{
    $ratio = 1;
    if ($currency1 != $currency2) {
        $res = db_result("select api_currency_value,currency_value from mv_currency where convert_currency_from='$currency1' and convert_currency_to='$currency2'");
        $ratio = $res['api_currency_value'];
        if ($ratio <= 0) {
            $ratio = $res['currency_value'];
        }
    }
    return $ratio;
}
function convert_price_with_commission($price, $currency1, $currency2, $commission)
{
    $price = convert_price($price, $currency1, $currency2, $commission);
    return $price;
}
function display_price($price)
{
    $price = number_format($price, 2, '.', '');
    return $price;
}
function convert_price($price, $currency1, $currency2, $commission = '')
{
    $res = db_result("select currency_value from mv_currency where convert_currency_from='$currency1' and convert_currency_to='$currency2'");
    $ratio = 1;
    if ($currency1 != $currency2) {
        $ratio = $res['currency_value'];
    }
    if ($commission != '') {
        $comm = ($price * $commission) / 100;
        $price = $price + $comm;
    }
    $price = $price * $ratio;
    $price = round($price, 2);
    $price = number_format($price, 2, '.', '');
    return $price;
}
function convert_price_gross($price, $currency1, $currency2, $commission = '')
{
    $ratio = 1;
    if ($currency1 != $currency2) {
        $res = db_result("select currency_value from mv_currency where convert_currency_from='$currency1' and convert_currency_to='$currency2'");
        if (isset($res['currency_value']) && $res['currency_value'] > 0) {
            $ratio = $res['currency_value'];
        }
    }
    if ($commission != '') {
        $comm = ($price * $commission) / 100;
        $price = $price + $comm;
    }
    $price = $price * $ratio;
    $price = ceil($price);
    $price = number_format($price, 2, '.', '');
    return $price;
}
function get_tax_value($price)
{
    $tax = db_scalar("select tax from tbl_price_config where config_id='1'");
    return round((($price * $tax) / 100), 2);
}
function get_tax_percentage()
{
    $tax = db_scalar("select tax from tbl_price_config where config_id='1'");
    return $tax;
}
function create_tour_code($tour_id)
{
    return "TOU-" . str_pad($tour_id, 5, 0, STR_PAD_LEFT);
}
function create_trip_code($trip_id)
{
    return "TRP-" . str_pad($trip_id, 5, 0, STR_PAD_LEFT);
}
function secondsToWords($seconds)
{
    $ret = "";
    $hours = intval(intval($seconds) / 3600);
    if ($hours > 0) {
        $ret .= "$hours";
    }
    /*** get the minutes ***/
    $minutes = bcmod((intval($seconds) / 60), 60);
    if ($minutes > 0) {
        if ($minutes == 15) {
            $ret .= ".25";
        } elseif ($minutes == 30) {
            $ret .= ".5";
        } elseif ($minutes == 45) {
            $ret .= ".75";
        }
    }
    $ret .= " Hours";
    return $ret;
}
function show_am_pm($time)
{
    $arr = explode(":", $time);
    if ($arr[0] < 12) {
        return "AM";
    } else {
        return "PM";
    }
}
function generate_order_id($session)
{
    $l = strlen($session);
    $z = str_repeat("0", (6 - $l));
    return ("TRF" . $z . $session);
}
function rand_uniqid()
{
    $out = strtoupper(substr(md5(uniqid(rand(), true)), 0, 12));
    return $out;
}
function supplier_username_exists($username, $current_id = '')
{
    $sql = "select count(*) from mv_supplier where supplier_username='{$username}' and supplier_status!='Delete' ";
    if ($current_id != "") {
        $sql .= " and `supplier_id` != '$current_id' ";
    }
    $count = db_scalar($sql);
    if ($count > 0) {
        return true;
    } else {
        return false;
    }
}
function emp_username_exists($username, $current_id = '')
{
    $sql = "select count(*) from mv_employee where emp_username='{$username}' and emp_status!='Delete' ";
    if ($current_id != "") {
        $sql .= " and `emp_id` != '$current_id' ";
    }
    $count = db_scalar($sql);
    if ($count > 0) {
        return true;
    } else {
        return false;
    }
}
function client_email_exists($client_email, $current_id = '')
{
    $sql = "select count(*) from mv_client where client_email='{$client_email}' and client_status!='Delete' ";
    if ($current_id != "") {
        $sql .= " and `client_id` != '$current_id' ";
    }
    $count = db_scalar($sql);
    if ($count > 0) {
        return true;
    } else {
        return false;
    }
}
function agent_username_exists($username, $current_id = '')
{
    $sql = "select count(*) from tbl_agent where agent_username='{$username}'";
    if ($current_id != "") {
        $sql .= " and `agent_id` != '$current_id' ";
    }
    $count = db_scalar($sql);
    if ($count > 0) {
        return true;
    } else {
        return false;
    }
}
function agent_email_exists($email, $current_id = '')
{
    $sql = "select count(*) from mv_agent where agent_email='{$email}' and agent_status!='Delete'";
    if ($current_id != "") {
        $sql .= " and `agent_id` != '$current_id' ";
    }
    $count = db_scalar($sql);
    if ($count > 0) {
        return true;
    } else {
        return false;
    }
}
function ref_category_list($field_name, $field_value, $default = "Select Category")
{
    echo  sql_dropdown("SELECT ref_cat_id,ref_cat_name FROM tbl_ref_category where 1  order by ref_cat_name", $field_name, $field_value, 'class="textarea_edit"', $default);
}
function array_dropdown_with_keys_values_same($arr, $sel_value = '', $name = '', $extra = '', $choose_one = '', $arr_skip = array())
{
    $combo = "<select name='$name' id='$name' $extra >";
    if ($choose_one != '') {
        $combo .= "<option value=\"\" selected=\"selected\">$choose_one</option>";
    }
    foreach ($arr as $key => $value) {
        if (is_array($arr_skip) && in_array($key, $arr_skip)) {
            continue;
        }
        $combo .= '<option value="' . midas_html_chars($value) . '"';
        if (is_array($sel_value)) {
            if (in_array($value, $sel_value) || in_array(midas_html_chars($value), $sel_value)) {
                $combo .= " selected='selected' ";
            }
        } else {
            if ($sel_value == $value || $sel_value == midas_html_chars($value)) {
                $combo .= " selected='selected' ";
            }
        }
        $combo .= " >$value</option>";
    }
    $combo .= " </select>";
    return $combo;
}
function faq_question_list($name, $selectedVal)
{
    echo  sql_dropdown("SELECT faq_question_id,faq_question_name FROM tbl_faq_question where 1  order by faq_question_name", $name, $selectedVal, 'class="textarea_edit"', '--Select--');
}
function getAdminName($admin_id)
{
    $sql_admin = db_query("SELECT * FROM tbl_iv_agents WHERE admin_id = '$admin_id'");
    $res_admin = mysqli_fetch_assoc($sql_admin);
    return $name = $res_admin['admin_first_name'] . " " . $res_admin['admin_last_name'];
}
function calculate_age($dob)
{
    $current_date = date("Y-m-d");
    $query = "SELECT datediff('$current_date', '$dob') as difference";
    $result = db_query($query);
    $data = mysqli_fetch_array($result);
    $years = floor($data['difference'] / 365);
    $months = floor(($data['difference'] - ($years * 365)) / 30);
    $age = "";
    if ($years > 0) {
        $age .= $years . " Years";
    }
    if ($years == 0 && $months > 0) {
        $age .= $months . " Months";
    }
    return $age;
}
function return_age($dob)
{
    $current_date = date("Y-m-d");
    $query = "SELECT datediff('$current_date', '$dob') as difference";
    $result = db_query($query);
    $data = mysqli_fetch_array($result);
    $years = floor($data['difference'] / 365);
    return $years;
}
function GetDays($sStartDate, $sEndDate)
{
    $sStartDate = gmdate("Y-m-d", strtotime($sStartDate));
    $sEndDate = gmdate("Y-m-d", strtotime($sEndDate));
    $aDays[] = $sStartDate;
    $sCurrentDate = $sStartDate;
    while ($sCurrentDate < $sEndDate) {
        $sCurrentDate = gmdate("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
        $aDays[] = $sCurrentDate;
    }
    return count($aDays) - 1;
}
function strTime($s)
{
    $str = '';
    $d = intval($s / 86400);
    $s -= $d * 86400;
    $h = intval($s / 3600);
    $s -= $h * 3600;
    $m = intval($s / 60);
    $s -= $m * 60;
    if ($h) {
        $str .= $h . 'hrs : ';
    } else {
        $str .= '0 hrs : ';
    }
    if ($m) {
        $str .= $m . 'mins ';
    } else {
        $str .= '0 mins ';
    }
    if ($s) {
        $str .= ": " . $s . 'sec ';
    }
    return $str;
}
function format_web_url($url)
{
    if (strstr($url, "http://")) {
        return $url;
    } else {
        return "http://" . $url;
    }
}
function number_dropdown($field_name, $field_value, $max_value, $option = '')
{
    $var = '<select name="' . $field_name . '" id="' . $field_name . '" class="textarea_edit">';
    if ($option != '') {
        $var .= '<option value="0" selected="selected">' . $option . '</option>';
    }
    for ($i = 1; $i <= $max_value; $i++) {
        if ($field_value == $i) {
            $sel = "selected=\"selected\"";
        } else {
            $sel = "";
        }
        $var .= '<option value="' . $i . '" ' . $sel . '>' . $i . '</option>';
    }
    $var .= '</select>';
    return $var;
}
function hour12_dropdown($pre, $selected_hour, $extra = '')
{
    $str = "";
    $str .= "<select	name='" . $pre . "hour' $extra>";
    $str .= "<option	value=''>Hour</option>";
    for ($i = 0; $i <= 12; $i++) {
        $str .= " <option ";
        if ($i == $selected_hour && $selected_hour != '') {
            $str .= " selected ";
        }
        $str .= " value='" . str_pad($i, 2, "0", STR_PAD_LEFT) . "'>" . str_pad($i, 2, "0", STR_PAD_LEFT) . "</option>";
    }
    $str .= "</select>";
    return $str;
}
function region_comune_list($region_id, $comune_id)
{
    return sql_dropdown("select comune_id, comune_name from tbl_comune c left join tbl_province p on c.comune_province_id=p.province_id where comune_status='Active' and pregion_id = '" . $region_id . "' order by comune_name", 'hotel_comune_id', $comune_id, 'style="width:200px;"', 'Select City/Town');
}
function get_slug_from_title($page_title)
{
    return preg_replace("/[^A-Za-z0-9]/", "_", $page_title);
}
function phone_number_text_box($field_name, $country_code, $area_code, $phone)
{
    return '<table cellspacing="0" cellpadding="0"><tr>
		<td><input name="' . $field_name . '_country_code" type="text"  class="textfield_edit"  value="' . $country_code . '" style="width: 40px;" >&nbsp; - &nbsp;</td>
		<td><input name="' . $field_name . '_area_code" type="text"  class="textfield_edit"  value="' . $area_code . '" style="width: 40px;" >&nbsp; - &nbsp;</td>
		<td><input name="' . $field_name . '" type="text"  class="textfield_edit"  value="' . $phone . '" style="width: 100px;" ></td></tr></table>';
}
function phone_number_text_box_array($field_name, $country_code, $area_code, $phone)
{
    return '<table cellspacing="0" cellpadding="0"><tr>
		<td><input name="' . $field_name . '_country_code[]" type="text"  class="textfield_edit"  value="' . $country_code . '" style="width: 40px;" >&nbsp; - &nbsp;</td>
		<td><input name="' . $field_name . '_area_code[]" type="text"  class="textfield_edit"  value="' . $area_code . '" style="width: 40px;" >&nbsp; - &nbsp;</td>
		<td><input name="' . $field_name . '[]" type="text"  class="textfield_edit"  value="' . $phone . '" style="width: 100px;" ></td></tr></table>';
}
function user_phone_number_display($phone)
{
    $var = '';
    if ($phone != "") {
        $var .= substr($phone, 0, 3) . "-" . substr($phone, 3, strlen($phone));
    }
    return $var;
}
function phone_number_display($country_code, $area_code, $phone)
{
    $var = '';
    if ($country_code != "") {
        $var .= "+" . $country_code . " ";
    }
    if ($area_code != "") {
        $var .= "(" . $area_code . ") ";
    }
    if ($phone != "") {
        $var .= $phone;
    }
    return $var;
}