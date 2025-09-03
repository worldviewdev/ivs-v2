<?php
function connect_db()
{
    global $ARR_CFGS;
    // Jika koneksi sudah ada dan masih valid, gunakan yang ada
    if (isset($GLOBALS['dbcon']) && is_object($GLOBALS['dbcon']) && $GLOBALS['dbcon']->ping()) {
        return;
    }
    // Tutup koneksi lama jika ada
    if (isset($GLOBALS['dbcon']) && is_object($GLOBALS['dbcon'])) {
        $GLOBALS['dbcon']->close();
        unset($GLOBALS['dbcon']);
    }
    // Coba buat koneksi baru dengan retry
    $max_retries = 3;
    $retry_delay = 1; // delay dalam detik
    for ($i = 0; $i < $max_retries; $i++) {
        try {
            $dbcon = new mysqli(
                $ARR_CFGS["db_host"],
                $ARR_CFGS["db_user"],
                $ARR_CFGS["db_pass"],
                $ARR_CFGS["db_name"]
            );
            if (!$dbcon->connect_error) {
                $GLOBALS['dbcon'] = $dbcon;
                // Set session wait_timeout lebih rendah untuk mencegah koneksi menganggur terlalu lama
                $dbcon->query("SET session wait_timeout=300");
                return;
            }
        } catch (Exception $e) {
            if ($i < $max_retries - 1) {
                sleep($retry_delay);
                continue;
            }
            die("Connection failed after $max_retries retries: " . $e->getMessage());
        }
    }
    die("Connection failed: Unable to establish database connection after $max_retries attempts");
}
// Fungsi untuk menutup koneksi database
function close_db()
{
    if (isset($GLOBALS['dbcon']) && is_object($GLOBALS['dbcon'])) {
        $GLOBALS['dbcon']->close();
        unset($GLOBALS['dbcon']);
    }
    if (isset($GLOBALS['dbcon2']) && is_object($GLOBALS['dbcon2'])) {
        $GLOBALS['dbcon2']->close();
        unset($GLOBALS['dbcon2']);
    }
}
// Register shutdown function untuk memastikan koneksi ditutup
register_shutdown_function('close_db');
function connect_db2()
{
    global $ARR_CFGS;
    if (!isset($GLOBALS['dbcon2'])) {
        $dbcon2 = new mysqli(
            $ARR_CFGS["db_host2"],
            $ARR_CFGS["db_user2"],
            $ARR_CFGS["db_pass2"],
            $ARR_CFGS["db_name2"]
        );
        if ($dbcon2->connect_error) {
            die("Connection failed: " . $dbcon2->connect_error);
        }
        $GLOBALS['dbcon2'] = $dbcon2;
    }
}
function db_query($sql, $dbcon1 = null, $dbcon2 = null)
{
    if (!is_object($dbcon1)) {
        if (!isset($GLOBALS['dbcon'])) {
            connect_db();
        }
        $dbcon1 = $GLOBALS['dbcon'];
    }
    try {
        if (!is_object($dbcon1)) {
            throw new Exception('Invalid database connection');
        }
        $result = $dbcon1->query($sql);
        if (!$result) {
            if (!is_object($dbcon2)) {
                if (!isset($GLOBALS['dbcon2'])) {
                    connect_db2();
                }
                $dbcon2 = $GLOBALS['dbcon2'];
            }
            if (!is_object($dbcon2)) {
                throw new Exception('Invalid secondary database connection');
            }
            $result = $dbcon2->query($sql);
            if (!$result) {
                db_error($sql);
            }
        }
        return $result;
    } catch (Exception $e) {
        db_error($sql . "\n" . $e->getMessage());
        return false;
    }
}
function print_all_sqls()
{
    if (LOCAL_MODE) {
        if (is_array($GLOBALS['ARR_ALL_SQLS'])) {
            echo '<div class="sql_logs" style="border:2px solid #333; padding: 5px; background:#ffc;height:100px; width:900px; overflow:auto;" ondblclick="this.style.height=\'\'">';
            $i = 0; // Inisialisasi $i
            foreach ($GLOBALS['ARR_ALL_SQLS'] as $arr) {
                echo ('<div class="sql_log" style="border-bottom:1px solid #666; padding: 2px;">' . (++$i) . ". " . $arr['sql'] . ', ' . $arr['time'] . ', ' . $arr['backtrace'] . '</div>');
            }
            echo '</div>';
        }
    }
}
function db_scalar($sql, $dbcon1 = null, $dbcon2 = null)
{
    $result = db_query($sql, $dbcon1, $dbcon2);
    if ($result && $row = $result->fetch_array()) {
        return $row[0];
    }
    return null;
}
function db_row($sql, $dbcon1 = null, $dbcon2 = null)
{
    $result = db_query($sql, $dbcon1, $dbcon2);
    if ($result && $row = $result->fetch_array(MYSQLI_ASSOC)) {
        return $row;
    }
    return null;
}
function db_error($sql)
{
    $error_message = "";
    // Get error message from primary connection if available
    if (isset($GLOBALS['dbcon'])) {
        $error_message = mysqli_error($GLOBALS['dbcon']);
    }
    // If no error message and secondary connection exists, try that
    if (!$error_message && isset($GLOBALS['dbcon2'])) {
        $error_message = mysqli_error($GLOBALS['dbcon2']);
    }
    // If still no error message, use generic one
    if (!$error_message) {
        $error_message = "Database error occurred";
    }
    if (LOCAL_MODE) {
        echo "<div style='font-family: tahoma; font-size: 11px; color: #333333'><br>" . $error_message . "<br>";
        print_error();
        echo "<br>sql: $sql";
        echo "</div>";
    } else {
        // Log error but show limited info in production
        $file = SITE_FS_PATH . '/sql_opt/errors.txt';
        if (!is_dir(SITE_FS_PATH . '/sql_opt')) {
            mkdir(SITE_FS_PATH . '/sql_opt', 0755, true);
        }
        // Check if file exists
        if (!file_exists($file)) {
            touch($file);
            chmod($file, 0644);
        }
        $handle = fopen($file, 'a');
        fwrite($handle, "\r\n" . $_SERVER['PHP_SELF'] . "\t" . preg_replace("/\s/m", " ", $sql) . "\t" . date("Y-m-d G:i:s", time()) . "\t" . session_id() . "\t" . $error_message);
        fclose($handle);
        // Show generic error to user
        // echo "<div style='font-family: tahoma; font-size: 11px; color: #333333'><br>A database error occurred. The error has been logged.<br></div>";
    }
}
function print_error()
{
    $debug_backtrace = debug_backtrace();
    for ($i = count($debug_backtrace) - 1; $i > 0; $i--) {
        $error = $debug_backtrace[$i];
        echo '<div style="background-color:#ffc;padding:2px;">';
        echo '<span ><b>File:</b> ' . str_replace(SITE_FS_PATH, '', str_replace('\\', '/', $error['file'])) . '</span>';
        echo ", <b>Line:</b> " . $error['line'] . "";
        echo ", <b>Function:</b> " . $error['function'] . "";
        echo "</div>";
    }
}
function mysql_time($hour, $minute, $ampm)
{
    if ($ampm == 'PM' && $hour != '12') {
        $hour += 12;
    }
    if ($ampm == 'AM' && $hour == '12') {
        $hour = '00';
    }
    $mysql_time = $hour . ':' . $minute . ':00';
    return $mysql_time;
}
function price_format($price)
{
    if ($price != '' && $price != '0') {
        $price = number_format($price, 2);
        return '$' . $price;
    }
}
function midas_date_format($date)
{
    if (!$date) {
        return '';
    }
    if (strlen((string)$date) >= 10) {
        if ($date == '0000-00-00 00:00:00' || $date == '0000-00-00') {
            return '';
        }
        $mktime = mktime(0, 0, 0, (int)substr($date, 5, 2), (int)substr($date, 8, 2), (int)substr($date, 0, 4));
        //return date("M j, Y", $mktime);
        return date("d F Y", $mktime);
    } else {
        return '';
    }
}
function pdf_date_format($date)
{
    if (!$date) {
        return '';
    }
    if (strlen((string)$date) >= 10) {
        if ($date == '0000-00-00 00:00:00' || $date == '0000-00-00') {
            return '';
        }
        $mktime = mktime(0, 0, 0, (int)substr($date, 5, 2), (int)substr($date, 8, 2), (int)substr($date, 0, 4));
        //return date("M j, Y", $mktime);
        return date("d-M-y", $mktime);
    } else {
        return '';
    }
}
function show_time($time)
{
    if (!$time) {
        return '';
    }
    $arr = explode(":", $time);
    if ($arr[0] != "" && $time != '00:00:00') {
        return $arr[0] . ":" . $arr[1];
    } else {
        return '';
    }
}
function midas_time_format($date)
{
    global $arr_month_short;
    if (!$date) {
        return '';
    }
    if (strlen((string)$date) >= 10) {
        if ($date == '0000-00-00 00:00:00' || $date == '0000-00-00') {
            return '';
        }
        $mktime = mktime((int)substr($date, 11, 2), (int)substr($date, 14, 2), (int)substr($date, 17, 2), (int)substr($date, 5, 2), (int)substr($date, 8, 2), (int)substr($date, 0, 4));
        return date("H:i ", $mktime);
    } else {
        return '';
    }
}
function season_date_format($date)
{
    if (!$date) {
        return '';
    }
    if (strlen((string)$date) >= 10) {
        if ($date == '0000-00-00 00:00:00' || $date == '0000-00-00') {
            return '';
        }
        $mktime = mktime(0, 0, 0, (int)substr($date, 5, 2), (int)substr($date, 8, 2), (int)substr($date, 0, 4));
        return date("j-M", $mktime);
    } else {
        return '';
    }
}
function datetime_format($date)
{
    global $arr_month_short;
    if (!$date) {
        return '';
    }
    if (strlen((string)$date) >= 10) {
        if ($date == '0000-00-00 00:00:00' || $date == '0000-00-00') {
            return '';
        }
        $mktime = mktime((int)substr($date, 11, 2), (int)substr($date, 14, 2), (int)substr($date, 17, 2), (int)substr($date, 5, 2), (int)substr($date, 8, 2), (int)substr($date, 0, 4));
        //return date("M j, Y h:i A ", $mktime);
        return date("d F Y h:i A ", $mktime);
    } else {
        return '';
    }
}
function time_format($time)
{
    if (!$time) {
        return '';
    }
    if (strlen((string)$time) >= 5) {
        $hour = substr($time, 0, 2);
        $hour = str_pad($hour, 2, "0", STR_PAD_LEFT);
        $ampm = isset($ampm) ? $ampm : '';
        return $hour . ':' . substr($time, 3, 2) . ' ' . $ampm;
    } else {
        return '';
    }
}
function ms_print_r($var)
{
    echo "<textarea rows='10' cols='148' style='font-size: 11px; font-family: tahoma'>";
    print_r($var);
    echo "</textarea>";
}
function ms_form_value($var)
{
    return is_array($var) ? array_map('ms_form_value', $var) : stripslashes(trim((string)$var));
}
function ms_form_display_value($var)
{
    if (is_array($var)) {
        return array_map('ms_form_display_value', $var);
    }
    if ($var === null) {
        return '';
    }
    return midas_html_chars(trim((string)$var));
}
function ms_escape_string($var)
{
    if (is_array($var)) {
        return array_map('ms_escape_string', $var);
    }
    if ($var === null) {
        return '';
    }
    return addslashes(trim((string)$var));
}
function ms_display_value($var)
{
    if (is_array($var)) {
        return array_map('ms_display_value', $var);
    }
    if ($var === null) {
        return '';
    }
    return stripslashes(trim($var));
}
function ms_stripslashes($var)
{
    if (is_array($var)) {
        return array_map('ms_stripslashes', $var);
    }
    if ($var === null) {
        return '';
    }
    return stripslashes(trim((string)$var));
}
function ms_addslashes($var)
{
    if (is_array($var)) {
        return array_map('ms_addslashes', $var);
    }
    if ($var === null) {
        return '';
    }
    return addslashes(trim((string)$var));
}
function ms_trim($var)
{
    if (is_array($var)) {
        return array_map('ms_trim', $var);
    }
    if ($var === null) {
        return '';
    }
    return trim((string)$var);
}
function is_image_valid($file_name)
{
    global $ARR_VALID_IMG_EXTS;
    $ext = file_ext($file_name);
    if (in_array($ext, $ARR_VALID_IMG_EXTS)) {
        return true;
    } else {
        return false;
    }
}
// function getmicrotime()
// {
//     list($usec, $sec) = explode(" ", microtime());
//     return ((float)$usec + (float)$sec);
// }
function file_ext($file_name)
{
    $path_parts = pathinfo($file_name);
    $ext = isset($path_parts["extension"]) ? strtolower($path_parts["extension"]) : '';
    return $ext;
}
function blank_filter($var)
{
    $var = trim($var);
    return ($var != '' && $var != '&nbsp;');
}
function apply_filter($sql, $field, $field_filter, $column)
{
    if ($field != '') {
        if ($field_filter == "=" || $field_filter == "") {
            $sql = $sql . "	and	$column	= '$field' ";
        } elseif ($field_filter == "like") {
            $sql = $sql . "	and	$column	like '%$field%'	";
        } elseif ($field_filter == "starts_with") {
            $sql = $sql . "	and	$column	like '$field%' ";
        } elseif ($field_filter == "ends_with") {
            $sql = $sql . "	and	$column	like '%$field' ";
        } elseif ($field_filter == "not_contains") {
            $sql = $sql . "	and	$column	not	like '%$field%'	";
        } elseif ($field_filter == ">") {
            $sql = $sql . " and $column > '$field' ";
        } elseif ($field_filter == "<") {
            $sql = $sql . " and $column < '$field' ";
        } elseif ($field_filter == "!=") {
            $sql = $sql . "	and	$column	!= '$field'	";
        }
    }
    return $sql;
}
function filter_dropdown($sel_value, $name = 'filter')
{
    $arr = array("like" => 'Contains', '=' => 'Is', "starts_with" => 'Starts with', "ends_with" => 'Ends with', "!=" => 'Is not', "not_contains" => 'Not contains');
    return array_dropdown($arr, $sel_value, $name);
}
function make_url($url)
{
    $parsed_url = parse_url($url);
    if ($parsed_url['scheme'] == '') {
        return 'http://' . $url;
    } else {
        return $url;
    }
}
function ms_mail($to, $subject, $message, $arr_headers = array())
{
    $str_headers = '';
    foreach ($arr_headers as $name => $value) {
        $str_headers .= "$name: $value\n";
    }
    @mail($to, $subject, $message, $str_headers);
    return true;
}
function date_to_mysql(string $date): string
{
    [$month, $day, $year] = explode('/', $date);
    return "$year-$month-$day";
}
function export_delimited_file($sql, $arr_columns, $file_name = '', $arr_substitutes = '', $arr_tpls = '')
{
    if ($file_name == '') {
        $file_name = time() . '.txt';
    }
    header("Content-type: application/txt");
    header("Content-Disposition: attachment; filename=$file_name");
    $arr_db_cols = array_keys($arr_columns);
    $arr_headers = array_values($arr_columns);
    $str_columns = implode(',', $arr_db_cols);
    $sql = "select " . $str_columns . " $sql";
    $result = db_query($sql);
    $num_cols = count($arr_columns);
    foreach ($arr_headers as $header) {
        echo $header . "\t";
    }
    while ($line = $result->fetch_array(MYSQLI_ASSOC)) {
        echo "\r\n";
        foreach ($line as $key => $value) {
            $value = str_replace("\n", "", $value);
            $value = str_replace("\r", "", $value);
            $value = str_replace("\t", "", $value);
            if (is_array($arr_substitutes[$key])) {
                $value = $arr_substitutes[$key][$value];
            }
            if (isset($arr_tpls[$key])) {
                $code = str_replace('{1}', $value, $arr_tpls[$key]);
                eval("\$value = $code;");
            }
            echo $value . "\t";
        }
    }
}
function checkpoint($from_start = false)
{
    global $PREV_CHECKPOINT;
    if ($PREV_CHECKPOINT == '') {
        $PREV_CHECKPOINT = SCRIPT_START_TIME;
    }
    $cur_microtime = getmicrotime();
    if ($from_start) {
        return $cur_microtime - SCRIPT_START_TIME;
    } else {
        $time_taken = $cur_microtime - $PREV_CHECKPOINT;
        $PREV_CHECKPOINT = $cur_microtime;
        return $time_taken;
    }
}
function readable_col_name($str)
{
    return ucwords(str_replace('_', ' ', strtolower($str)));
}
function ms_echo($str)
{
    if (LOCAL_MODE) {
        echo ($str);
    }
}
function sql_dropdown($sql, $dd_name, $sel_value = '', $extra = '', $choose_one = '', $skip = '')
{
    $result = db_query($sql);
    //if (mysql_num_rows($result) > 0) {
    $str_dropdown = "<select name='$dd_name' id='$dd_name' $extra>";
    if (is_array($choose_one)) {
        foreach ($choose_one as $key => $value) {
            $str_dropdown .= "<option value='$key'>$value</option>";
        }
    } elseif ($choose_one != '') {
        $str_dropdown .= "<option value=''>$choose_one</option>";
    }
    while ($line = mysqli_fetch_array($result)) {
        if ($skip != ms_form_display_value($line[0])) {
            $str_dropdown .= "<option value='" . ms_form_display_value($line[0]) . "'";
            if (is_array($sel_value)) {
                if (in_array($line[0], $sel_value)) {
                    $str_dropdown .= "	selected='selected' ";
                }
            } else {
                if ($sel_value == $line[0]) {
                    $str_dropdown .= "	selected='selected' ";
                }
            }
            $str_dropdown .= ">" . $line[1] . "</option>";
        }
    }
    $str_dropdown .= "</select>";
    //}
    return $str_dropdown;
}
function sql_location_dropdown($sql, $dd_name, $sel_value = '', $extra = '', $choose_one = '', $skip = '')
{
    $str_dropdown = ''; // Fix: Initialize variable to avoid undefined warning
    $result = db_query($sql);
    if (mysqli_num_rows($result) > 0) {
        $str_dropdown = "<select name='$dd_name' id='$dd_name' $extra>";
        if (is_array($choose_one)) {
            foreach ($choose_one as $key => $value) {
                $str_dropdown .= "<option value='$key'>$value</option>";
            }
        } elseif ($choose_one != '') {
            $str_dropdown .= "<option value=''>$choose_one</option>";
        }
        while ($line = mysqli_fetch_array($result)) {
            if ($skip != ms_form_display_value($line[0])) {
                $str_dropdown .= "<option value='" . ms_form_display_value($line[0]) . "'";
                if (is_array($sel_value)) {
                    if (in_array($line[0], $sel_value)) {
                        $str_dropdown .= "	selected='selected' ";
                    }
                } else {
                    if ($sel_value == $line[0]) {
                        $str_dropdown .= "	selected='selected' ";
                    }
                }
                $str_dropdown .= ">" . $line[1] . "</option>";
            }
        }
        $str_dropdown .= "</select>";
    }
    return $str_dropdown;
}
// Fix: Ensure the function is defined before use
function make_dropdown($sql, $dd_name, $extra = '', $choose_one = '', $sel_value = '')
{
    return sql_dropdown($sql, $dd_name, $sel_value, $extra, $choose_one);
}
function array_dropdown($arr, $sel_value = '', $name = '', $extra = '', $choose_one = '', $arr_skip = array())
{
    // Make sure $arr is an array
    if (!is_array($arr)) {
        $arr = array();
    }
    $combo = "<select name='$name' id='$name' $extra >";
    if ($choose_one != '') {
        $combo .= "<option value=\"\">$choose_one</option>";
    }
    foreach ($arr as $key => $value) {
        if (is_array($arr_skip) && in_array($key, $arr_skip)) {
            continue;
        }
        $combo .= '<option value="' . midas_html_chars($key) . '"';
        if (is_array($sel_value)) {
            if (in_array($key, $sel_value) || in_array(midas_html_chars($key), $sel_value)) {
                $combo .= " selected='selected' ";
            }
        } else {
            if (($sel_value == $key || $sel_value == midas_html_chars($key)) && $sel_value != "") {
                $combo .= " selected='selected' ";
            }
        }
        $combo .= " >$value</option>";
    }
    $combo .= " </select>";
    return $combo;
}
function make_checkboxes($arr_tmp, $cols, $missit, $checkname, $checksel = '', $style = '', $tableattr = '', $javascript = '')
{
    $checksel = explode(",", $checksel);
    if ($style != "") {
        $style = "class='" . $style . "'";
    }
    $cols = (int)$cols;
    if ($cols <= 0) {
        $cols = 1;
    }
    $colwidth = 100 / $cols;
    $colwidth = round($colwidth, 2);
    $j = 0;
    $checkstr = '';
    if (is_array($arr_tmp) && count($arr_tmp)) {
        foreach ($arr_tmp as $key => $value) {
            $tochecked = "";
            if (in_array($key, $checksel)) {
                $tochecked = "checked";
            }
            $show_this_option = false;
            if (is_array($missit)) {
                if (!in_array($key, $missit)) {
                    $show_this_option = true;
                }
            } else {
                if ($key != $missit) {
                    $show_this_option = true;
                }
            }
            if ($value != "" && $show_this_option) {
                if ($j == 0) {
                    $checkstr .= "<table $tableattr ><tr>\n";
                } elseif (($j % $cols) == 0) {
                    $checkstr .= "</tr><tr>\n";
                }
                $checkstr .= "<td valign='top' align='left' width='20'><INPUT TYPE='checkbox' $javascript	 NAME='$checkname" . '[]' . "' value='$key'	$tochecked ></td><td $style nowrap> $value	</td>\n";
                $j++;
            }
        }
        $j--;
        for ($x = $j % $cols; $x < 4; $x++) {
            if ($x != 3) {
                $checkstr .= "<td>&nbsp;</td>\n";
            } else {
                $checkstr .= "<td>&nbsp;</td></tr>\n";
            }
        }
        $checkstr .= "</table>";
    }
    return $checkstr;
}
function make_checkboxes2($arr_tmp, $cols, $missit, $checkname, $checksel = '', $style = '', $tableattr = '', $javascript = '')
{
    if ($style != "") {
        $style = "class='" . $style . "'";
    }
    // Prevent division by zero
    $cols = max(1, (int)$cols); // Ensure minimum value of 1
    $colwidth = 100 / $cols;
    $colwidth = round($colwidth, 2);
    $j = 0;
    if (is_array($arr_tmp) && count($arr_tmp)) {
        foreach ($arr_tmp as $key => $value) {
            $tochecked = "";
            if (is_array($checksel) && in_array($key, $checksel)) {
                $tochecked = "checked";
            }
            $show_this_option = false;
            if (is_array($missit)) {
                if (!in_array($key, $missit)) {
                    $show_this_option = true;
                }
            } else {
                if ($key != $missit) {
                    $show_this_option = true;
                }
            }
            if ($value != "" && $show_this_option) {
                if ($j == 0) {
                    $checkstr = "<table $tableattr ><tr>\n";
                } elseif (($j % $cols) == 0) {
                    $checkstr .= "</tr><tr>\n";
                }
                $checkstr .= "<td valign='top' align='left' width='20'><INPUT TYPE='checkbox' $javascript	 NAME='$checkname" . '[]' . "' value='$key'	$tochecked ></td><td $style nowrap> $value	</td>\n";
                $j++;
            }
        }
        $j--;
        for ($x = $j % $cols; $x < 4; $x++) {
            if ($x != 3) {
                $checkstr .= "<td>&nbsp;</td>\n";
            } else {
                $checkstr .= "<td>&nbsp;</td></tr>\n";
            }
        }
        $checkstr .= "</table>";
    }
    return $checkstr;
}
function make_radios($arr_tmp, $cols, $missit, $checkname, $checksel = '', $style = '', $tableattr = '')
{
    if ($style != "") {
        $style = "class='" . $style . "'";
    }
    $colwidth = 100 / $cols;
    $colwidth = round($colwidth, 2);
    $j = 1;
    foreach ($arr_tmp as $key => $value) {
        $tochecked = "";
        if ($checksel == $key) {
            $tochecked = "checked";
        }
        if ($key != $missit) {
            if ($value != "") {
                if ($j == 1) {
                    $checkstr .= "<table $tableattr><tr>\n";
                } elseif (($j % $cols) == 1) {
                    $checkstr .= "</tr><tr>\n";
                }
                $checkstr .= "<td width='" . $colwidth . "%' $style	valign=top><INPUT TYPE='radio' $javascript	 NAME='$checkname' value='$key'	$tochecked	   > $value	</td>\n";
                $j++;
            }
        }
    }
    $j--;
    for ($x = $j % $cols; $x < 4; $x++) {
        if ($x != 3) {
            $checkstr .= "<td>&nbsp;</td>\n";
        } else {
            $checkstr .= "<td>&nbsp;</td></tr>\n";
        }
    }
    $checkstr .= "</table>";
    return $checkstr;
}
function date_dropdown($pre, $selected_date = '', $start_year = '', $end_year = '', $sort = 'asc')
{
    $cur_date = date("Y-m-d");
    $cur_date_day = substr($cur_date, 8, 2);
    $cur_date_month = substr($cur_date, 5, 2);
    $cur_date_year = substr($cur_date, 0, 4);
    if ($selected_date != '') {
        $selected_date_day = substr($selected_date, 8, 2);
        $selected_date_month = substr($selected_date, 5, 2);
        $selected_date_year = substr($selected_date, 0, 4);
    }
    $date_dropdown .= month_dropdown($pre . "month", $selected_date_month);
    $date_dropdown .= day_dropdown($pre . "day", $selected_date_day);
    $date_dropdown .= year_dropdown($pre . "year", $selected_date_year, $start_year, $end_year, $sort);
    return $date_dropdown;
}
function month_dropdown($name, $selected_date_month = '', $extra = '')
{
    global $ARR_MONTHS;
    $date_dropdown = "	<select	name='$name' $extra> <option value=''>Month</option>";
    $i = 0;
    foreach ($ARR_MONTHS as $key => $value) {
        $date_dropdown .= " <option ";
        if ($key == $selected_date_month) {
            $date_dropdown .= " selected ";
        }
        $date_dropdown .= " value='" . str_pad($key, 2, "0", STR_PAD_LEFT) . "'>$value</option>";
    }
    $date_dropdown .= "</select>";
    return $date_dropdown;
}
function day_dropdown($name, $selected_date_day = '', $extra = '')
{
    // intialzie variable
    $date_dropdown = '';
    $date_dropdown .= "<select	name='$name' $extra>";
    $date_dropdown .= "<option	value=''>Date</option>";
    for ($i = 1; $i <= 31; $i++) {
        $date_dropdown .= " <option ";
        if ($i == $selected_date_day) {
            $date_dropdown .= " selected ";
        }
        $date_dropdown .= " value='" . str_pad($i, 2, "0", STR_PAD_LEFT) . "'>" . $i . $s . "</option>";
    }
    $date_dropdown .= "</select>";
    return $date_dropdown;
}
function year_dropdown($name, $selected_date_year = '', $start_year = '', $end_year = '', $extra = '')
{
    if ($start_year == '') {
        $start_year = DEFAULT_START_YEAR;
    }
    if ($end_year == '') {
        $end_year = DEFAULT_END_YEAR;
    }
    $date_dropdown = "";
    $date_dropdown .= "<select	name='$name' $extra>";
    $date_dropdown .= "<option	value=''>Year</option>";
    for ($i = $start_year; $i <= $end_year; $i++) {
        $date_dropdown .= " <option ";
        if ($i == $selected_date_year) {
            $date_dropdown .= " selected ";
        }
        $date_dropdown .= " value='" . str_pad($i, 2, "0", STR_PAD_LEFT) . "'>" . str_pad($i, 2, "0", STR_PAD_LEFT) . "</option>";
    }
    $date_dropdown .= "</select>";
    return $date_dropdown;
}
function time_dropdown($pre, $selected_time = '', $extra = '')
{
    $str = ''; // Fix: Initialize $str
    $selected_hour = '';
    $selected_minute = '';
    if ($selected_time != '' && $selected_time != ':' && !is_array($selected_time)) {
        $selected_hour = substr((string)$selected_time, 0, 2);
        $selected_minute = substr((string)$selected_time, 3, 2);
    }
    $str .= hour_dropdown($pre, $selected_hour, $extra);
    $str .= '<b> &nbsp;</b>';
    $str .= minute_dropdown($pre, $selected_minute, $extra);
    return $str;
}
function time_dropdown_frontend($pre, $selected_time = '', $extra = '')
{
    $str = ''; // Fix: Initialize $str
    $selected_hour = '';
    $selected_minute = '';
    if ($selected_time != '' && $selected_time != ':') {
        $selected_hour = substr($selected_time, 0, 2);
        $selected_minute = substr($selected_time, 3, 2);
    }
    $str .= '<div class="row">
                      <div class="col-sm-6">
                        <div class="form-group">';
    $str .= hour_dropdown($pre, $selected_hour, $extra);
    $str .= '</div>
                      </div>
                      <div class="col-sm-6">
                        <div class="form-group">';
    $str .= minute_dropdown($pre, $selected_minute, $extra);
    $str .= '</div>
                      </div>
                    </div>';
    return $str;
}
function hour_dropdown($pre, $selected_hour, $extra = '')
{
    $str = ''; // Fix: Initialize $str
    $str .= "<select	name='" . $pre . "hour' $extra>";
    $str .= "<option	value=''>Hour</option>";
    for ($i = 0; $i <= 23; $i++) {
        $str .= " <option ";
        if ($i == $selected_hour && $selected_hour != '') {
            $str .= " selected ";
        }
        $str .= " value='" . str_pad($i, 2, "0", STR_PAD_LEFT) . "'>" . str_pad($i, 2, "0", STR_PAD_LEFT) . "</option>";
    }
    $str .= "</select>";
    return $str;
}
function minute_dropdown($pre, $selected_minute, $extra = '')
{
    $str = ''; // Fix: Initialize $str
    $str .= "<select	name='" . $pre . "minute' $extra>";
    $str .= "<option	value=''>Minute</option>";
    for ($i = 0; $i <= 59; $i++) {
        $str .= " <option ";
        if (str_pad($i, 2, "0", STR_PAD_LEFT) === strval($selected_minute)) {
            $str .= " selected ";
        }
        $str .= " value='" . str_pad($i, 2, "0", STR_PAD_LEFT) . "'>" . str_pad($i, 2, "0", STR_PAD_LEFT) . "</option>";
    }
    $str .= "</select>";
    return $str;
}
function time_dropdown_array($pre, $selected_time = '', $extra = '')
{
    // Initialize variables
    $str = '';
    $selected_hour = '';
    $selected_minute = '';
    // Parse selected time if provided
    if ($selected_time != '' && $selected_time != ':') {
        $selected_hour = substr($selected_time, 0, 2);
        $selected_minute = substr($selected_time, 3, 2);
    }
    // Build dropdown HTML
    $str .= hour_dropdown_array($pre, $selected_hour, $extra);
    $str .= '<b> &nbsp;</b>';
    $str .= minute_dropdown_array($pre, $selected_minute, $extra);
    return $str;
}
function hour_dropdown_array($pre, $selected_hour, $extra = '')
{
    $str = ''; // Fix: Initialize $str
    $str .= "<select	name='" . $pre . "hour[]' $extra>";
    $str .= "<option	value=''>Hour</option>";
    for ($i = 0; $i <= 23; $i++) {
        $str .= " <option ";
        if ($i == $selected_hour && $selected_hour != '') {
            $str .= " selected ";
        }
        $str .= " value='" . str_pad($i, 2, "0", STR_PAD_LEFT) . "'>" . str_pad($i, 2, "0", STR_PAD_LEFT) . "</option>";
    }
    $str .= "</select>";
    return $str;
}
function minute_dropdown_array($pre, $selected_minute, $extra = '')
{
    $str = ''; // Fix: Initialize $str
    $str .= "<select	name='" . $pre . "minute[]' $extra>";
    $str .= "<option	value=''>Minute</option>";
    for ($i = 0; $i <= 59; $i++) {
        $str .= " <option ";
        if (str_pad($i, 2, "0", STR_PAD_LEFT) === strval($selected_minute)) {
            $str .= " selected ";
        }
        $str .= " value='" . str_pad($i, 2, "0", STR_PAD_LEFT) . "'>" . str_pad($i, 2, "0", STR_PAD_LEFT) . "</option>";
    }
    $str .= "</select>";
    return $str;
}
function ampm_dropdown($pre, $selected_ampm)
{
    $str = ''; // Fix: Initialize $str
    $str .= "<select name='" . $pre . "ampm' class='textarea_edit'>";
    $str .= " <option ";
    if ($selected_ampm == 'AM') {
        $str .= " selected ";
    }
    $str .= " value='AM'>AM</option>";
    $str .= " <option ";
    if ($selected_ampm == 'PM') {
        $str .= " selected ";
    }
    $str .= " value='PM'>PM</option>";
    $str .= "</select>";
    return $str;
}
function get_qry_str($over_write_key = array(), $over_write_value = array())
{
    global $_GET;
    $m = $_GET;
    if (is_array($over_write_key)) {
        $i = 0;
        foreach ($over_write_key as $key) {
            $m[$key] = $over_write_value[$i];
            $i++;
        }
    } else {
        $m[$over_write_key] = $over_write_value;
    }
    $qry_str = qry_str($m);
    return $qry_str;
}
function qry_str($arr, $skip = '')
{
    $s = "?";
    $i = 0;
    foreach ($arr as $key => $value) {
        if (is_array($skip)) {
            if (!in_array($key, $skip)) {
                if (is_array($value)) {
                    foreach ($value as $value2) {
                        $i == 0 ? $i = 1 : '';
                        if ($value2 != '') {
                            $s .= '&';
                            $s .= $key . '[]=' . $value2;
                        }
                    }
                } else {
                    $i == 0 ? $i = 1 : '';
                    if ($value != '') {
                        $s .= '&';
                        $s .= "$key=$value";
                    }
                }
            }
        } else {
            if ($key != $skip) {
                if (is_array($value)) {
                    foreach ($value as $value2) {
                        $i == 0 ? $i = 1 : '';
                        if ($value2 != '') {
                            $s .= '&';
                            $s .= $key . '[]=' . $value2;
                        }
                    }
                } else {
                    $i == 0 ? $i = 1 : '';
                    if ($value != '') {
                        $s .= '&';
                        $s .= "$key=$value";
                    }
                }
            }
        }
    }
    return $s;
}
function check_radio($s, $s2)
{
    if (is_array($s2)) {
        if (in_array($s, $s2)) {
            return " checked ";
        }
    } elseif ($s == $s2) {
        return " checked ";
    }
}
function sort_arrows($column)
{
    return '<A HREF="' . $_SERVER['PHP_SELF'] . get_qry_str(array('order_by', 'order_by2'), array($column, 'asc')) . '"><img src="images/icons/up_arrow.png" width="10" height="10" border="0"></a>	<a href="' . $_SERVER['PHP_SELF'] . get_qry_str(array('order_by', 'order_by2'), array($column, 'desc')) . '"><img src="images/icons/down_arrow.png" width="10" height="10" border="0"></a>';
}
function select_option($s, $s1)
{
    if ($s == $s1) {
        echo " selected	";
    }
}
function is_post_back()
{
    if (count($_POST) > 0) {
        return true;
    } else {
        return false;
    }
}
function request_to_hidden($arr_skip = '')
{
    $s = '';
    foreach ($_REQUEST as $name => $value) {
        $s .= '<input type="hidden" name="' . $name . '" value="' . midas_html_chars(stripslashes($value)) . '">' . "\n";
    }
    return $s;
}
function sql_to_array_file($arr_name, $sql, $file, $full_table = false)
{
    $str = "<?\n";
    $str .= '$' . $arr_name . " = array();\n";
    $result = db_query($sql);
    while ($line = mysqli_fetch_array($result)) {
        $line = ms_addslashes($line);
        if ($full_table) {
            $key = $line[0];
            foreach ($line as $name => $value) {
                if (!is_numeric($name)) {
                    $str .= '$' . $arr_name . "['" . $key . "']['" . $name . "'] = '" . str_replace("'", "\'", stripslashes($value)) . "';\n";
                }
            }
            $str .= "\n";
        } else {
            $str .= '$' . $arr_name . "['" . $line[0] . "'] = '" . str_replace("'", "\'", stripslashes($line[1])) . "';\n";
        }
    }
    $str .= "?>";
    $fh = fopen($file, 'w');
    fwrite($fh, $str);
    fclose($fh);
    return true;
}
function array_radios($arr, $sel_value = '', $name = '', $cols = 3, $extra = '')
{
    $style = '';
    if ($style != "") {
        $style = "class='" . $style . "'";
    }
    $colwidth = 100 / $cols;
    $colwidth = round($colwidth, 2);
    $j = 1;
    foreach ($arr as $key => $value) {
        $tochecked = "";
        if (is_array($sel_value) && in_array($key, $sel_value)) {
            $tochecked = "checked";
        }
        if ($key != $missit) {
            if ($value != "") {
                if ($j == 1) {
                    $checkstr .= "<table $tableattr><tr>\n";
                } elseif (($j % $cols) == 1) {
                    $checkstr .= "</tr><tr>\n";
                }
                $checkstr .= "<td width='" . $colwidth . "%' $style valign=top><INPUT TYPE='radio' $javascript  NAME='$name' value='$key' $tochecked     > $value </td>\n";
                $j++;
            }
        }
    }
    $j--;
    for ($x = $j % $cols; $x < 4; $x++) {
        if ($x != 3) {
            $checkstr .= "<td>&nbsp;</td>\n";
        } else {
            $checkstr .= "<td>&nbsp;</td></tr>\n";
        }
    }
    $checkstr .= "</table>";
    return $checkstr;
}
function show_thumb($file_org, $width, $height, $ratio_type = 'width_height', $mode = 'php')
{
    if (LOCAL_MODE) {
        $method = 'gd';
    } else {
        $method = 'im';
    }
    return midas_thumb::show_thumb($file_org, $width, $height, $ratio_type, $method, $mode);
}
function ms_parse_keywords($keywords)
{
    $arr_keywords = array();
    $cur_keyword = '';
    $dq_start = false;
    $dq_end = false;
    $sp_start = true;
    $sp_end = false;
    for ($i = 0; $i < strlen($keywords); $i++) {
        $cur_token = substr($keywords, $i, 1);
        if ($cur_token == '"') {
            if ($dq_start) {
                $dq_end = true;
                $dq_start = false;
                $arr_keywords[] = $cur_keyword;
                $cur_keyword = '';
            } elseif ($dq_end) {
                $dq_end = false;
                $dq_start = true;
                $sp_start = false;
            } else {
                $dq_end = false;
                $dq_start = true;
            }
        } elseif ($cur_token == ' ') {
            if ($sp_start || $dq_end) {
                $sp_end = true;
                $sp_start = false;
                $arr_keywords[] = $cur_keyword;
                $cur_keyword = '';
            } elseif ($sp_end && !$dq_start) {
                $sp_end = false;
                $sp_start = true;
            } elseif ($dq_start) {
                $cur_keyword .= $cur_token;
            }
        } else {
            $cur_keyword .= $cur_token;
        }
    }
    $arr_keywords[] = $cur_keyword;
    return $arr_keywords;
}
/**
 * Enhanced keyword parsing function specifically for tour search
 * Fixes the issues with multi-word searches like "Paris to Paradise"
 *
 * @param string $keywords The search keywords
 * @return array Array of parsed keywords
 */
function ms_parse_keywords_tour($keywords)
{
    $keywords = trim($keywords);
    if (empty($keywords)) {
        return array();
    }
    $arr_keywords = array();
    $current_word = '';
    $in_quotes = false;
    $i = 0;
    while ($i < strlen($keywords)) {
        $char = $keywords[$i];
        if ($char == '"') {
            if ($in_quotes) {
                // End of quoted phrase
                if (!empty($current_word)) {
                    $arr_keywords[] = $current_word;
                    $current_word = '';
                }
                $in_quotes = false;
            } else {
                // Start of quoted phrase
                if (!empty($current_word)) {
                    $arr_keywords[] = $current_word;
                    $current_word = '';
                }
                $in_quotes = true;
            }
        } elseif ($char == ' ') {
            if ($in_quotes) {
                // Space inside quotes - add to current word
                $current_word .= $char;
            } else {
                // Space outside quotes - end current word
                if (!empty($current_word)) {
                    $arr_keywords[] = $current_word;
                    $current_word = '';
                }
            }
        } else {
            // Regular character
            $current_word .= $char;
        }
        $i++;
    }
    // Add final word if any
    if (!empty($current_word)) {
        $arr_keywords[] = $current_word;
    }
    return $arr_keywords;
}
function pagesize_dropdown($name, $value)
{
    $arr = array('10' => '10', '25' => '25', '50' => '50', '100' => '100');
    $m = $_GET;
    unset($m['pagesize']);
    return array_dropdown($arr, $value, $name, '  onchange="location.href=\'' . $_SERVER['PHP_SELF'] . qry_str($m) . '&pagesize=\'+this.value" ');
}
function pagesize_dropdown_new($name, $value, $base_url = null)
{
    $arr = array('10' => '10', '25' => '25', '50' => '50', '100' => '100');
    $m = $_GET;
    unset($m['pagesize']);
    if (!$base_url) {
        $base_url = $_SERVER['PHP_SELF'];
    }
    return array_dropdown(
        $arr,
        $value,
        $name,
        '  onchange="location.href=\'' . $base_url . qry_str($m) . '&pagesize=\'+this.value" '
    );
}
function sql_to_assoc_array($sql)
{
    $arr = array();
    $result = db_query($sql);
    while ($line = $result->fetch_array(MYSQLI_NUM)) {
        $arr[$line[0]] = $line[1];
    }
    return $arr;
}
function sql_to_index_array($sql)
{
    $arr = array();
    $result = db_query($sql);
    while ($line = mysqli_fetch_array($result)) {
        $line = ms_form_display_value($line);
        $arr[] = $line[0];
    }
    return $arr;
}
function sql_to_array($sql)
{
    $arr = array();
    $result = db_query($sql);
    while ($line = mysqli_fetch_array($result)) {
        $line = ms_form_display_value($line);
        array_push($arr, $line);
    }
    return $arr;
}
function get_unique_file_name($file_name)
{
    return str_shuffle(md5(uniqid(rand(), true))) . '.' . file_ext($file_name);
}
function qry_str_to_hidden($str)
{
    $fields = '';
    if (substr($str, 0, 1) == '?') {
        $str = substr($str, 1);
    }
    $arr = explode('&', $str);
    foreach ($arr as $pair) {
        list($name, $value) = explode('=', $pair);
        if ($name != '') {
            $fields .= '<input type="hidden" name="' . $name . '" id="' . $name . '" value="' . $value . '" />';
        }
    }
    return $fields;
}
function enum_to_array($table, $column)
{
    $result = db_query("show fields from $table");
    while ($line = mysqli_fetch_assoc($result)) {
        if ($line['Field'] == $column) {
            $Type = $line['Type'];
            $Type = substr($Type, 6, -2);
            $arr_tmp = explode("','", $Type);
            foreach ($arr_tmp as $val) {
                $arr[$val] = $val;
            }
            return $arr;
        }
    }
}
function absolute_to_fs($path)
{
    $path = str_replace(SITE_SUB_PATH, '', str_replace('\\', '/', $path));
    return SITE_FS_PATH . '/' . $path;
}
function fs_to_absolute($path)
{
    return str_replace(SITE_FS_PATH, SITE_SUB_PATH, str_replace('\\', '/', $path));
}
function get_absolute_dir($file)
{
    return fs_to_absolute(dirname($file));
}
function enum_dropdown($table, $column, $name, $sel_value = '', $extra = '', $choose_one = '', $arr_skip = array())
{
    $arr = enum_to_array($table, $column);
    return array_dropdown($arr, $sel_value, $name, $extra, $choose_one, $arr_skip);
}
function make_field($field_info)
{
    $type = $field_info['type'];
    $name = $field_info['name'];
    $sel_value = $field_info['sel_value'];
    $values = $field_info['values'];
    $extra = $field_info['extra'];
    if ($type == 'select' || $type == 'radio' || $type == 'checkbox') {
        $arr_tmp = explode("\n", $values);
        $arr_values = array();
        foreach ($arr_tmp as $row) {
            list($key, $value) = explode("|", $row);
            $arr_values[$key] = $value;
        }
    }
    $str = '';
    switch ($type) {
        case 'textfield':
            $str = '<input name="' . $name . '" type="text" id="' . $name . '" value="' . $sel_value . '" ' . $extra . ' class="textfield">';
            break;
        case 'password':
            $str = '<input name="' . $name . '" type="password" id="' . $name . '" value="' . $sel_value . '" ' . $extra . ' class="textfield">';
            break;
        case 'textarea':
            $str = '<textarea name="' . $name . '" id="' . $name . '" rows="5" cols="50" ' . $extra . ' class="textfield">' . $sel_value . '</textarea>';
            break;
        case 'select':
            if (is_array($arr_values)) {
                $str = '<select name="' . $name . '" ' . $extra . '>';
                foreach ($arr_values as $key => $value) {
                    $str .= '<option value="' . $key . '" ';
                    if ($sel_value == $key) {
                        $str .= 'selected';
                    }
                    $str .= ' >' . $value . "</option>";
                    $str .= "\r\n";
                }
                $str .= '</select>';
            }
            break;
        case 'list':
            if (is_array($arr_values)) {
                $str = '<select name="' . $name . '" size ="4" multiple  ' . $extra . '>';
                foreach ($arr_values as $key => $value) {
                    $str .= '<option value="' . $key . '" ';
                    if (in_array($key, $sel_value)) {
                        $str .= 'selected';
                    }
                    $str .= $value . "</option>";
                    $str .= "\r\n";
                }
                $str .= '</select>';
            }
            break;
        case 'radio':
            if (is_array($arr_values)) {
                foreach ($arr_values as $key => $value) {
                    $str .= '<input type="radio" name="' . $name . '" value="' . $key . '" ';
                    if ($sel_value == $key) {
                        $str .= 'checked';
                    }
                    $str .= $extra . '> ' . $value;
                }
            }
            break;
        case 'checkbox':
            if (is_array($arr_values)) {
                foreach ($arr_values as $key => $value) {
                    $str .= '<input type="checkbox" name="arr_' . $name . '[]" value="' . $key . '"';
                    if (in_array($key, $sel_value)) {
                        $str .= 'checked';
                    }
                    $str .= $extra . '> ' . $value;
                }
            }
            break;
        case 'hidden':
            $str = '<input name="' . $name . '" type="hidden" id="' . $name . '" value="' . $sel_value . '" ' . $extra . '>';
            break;
        case 'function':
            $function = str_replace('{1}', $name, $values);
            $function = str_replace('{v}', $extra, $function);
            // echo("$function");
            $str = eval('echo ' . $function);
            break;
    }
    return $str;
}
function file_size_format($file_size)
{
    $file_size = (float)($file_size ?? 0); // Ensure numeric type, default to 0 if null
    if ($file_size > 1024 * 1024 * 1024) {
        return round($file_size / (1024 * 1024 * 1024), 2) . ' GB';
    } elseif ($file_size > 1024 * 1024) {
        return round($file_size / (1024 * 1024), 2) . ' MB';
    } elseif ($file_size > 1024) {
        return round($file_size / 1024, 2) . ' KB';
    } else {
        return round($file_size, 2) . ' bytes';
    }
}
function recursive_dropdown($id_column, $name_column, $parent_id_column, $order_column, $table, $where, $name, $sel_value, $skip, $extra = '', $choose_one = '', $parent_id = 0, $level = 0)
{
    $level++;
    $sql = "select $id_column, $name_column from $table where $parent_id_column='$parent_id' and $id_column!='$skip'";
    if ($where != '') {
        $sql .= " and $where ";
    }
    $sql .= " order by $order_column ";
    $result = db_query($sql);
    if (mysqli_num_rows($result)) {
        if ($level == 1) {
            $str_dropdown .= "<select name='$name' id='$name' $extra >";
            if ($choose_one != '') {
                $str_dropdown .= "<option value=\"\">$choose_one</option>";
            }
        }
        while ($line_raw = mysqli_fetch_array($result)) {
            $line = ms_display_value($line_raw);
            @extract($line);
            $str_dropdown .= '<option value="' . $line[0] . '"';
            if ($sel_value == $line[0]) {
                $str_dropdown .= "	selected ";
            }
            $str_dropdown .= ">";
            for ($i = 2; $i <= $level; $i++) {
                if ($i == $level) {
                    $str_dropdown .= " &nbsp;&nbsp;&raquo; ";
                } else {
                    $str_dropdown .= " &nbsp;&nbsp;&nbsp; ";
                }
            }
            $str_dropdown .= "$line[1]</option>";
            $str_dropdown .= recursive_dropdown($id_column, $name_column, $parent_id_column, $order_column, $table, $where, $name, $sel_value, $skip, $extra, $choose_one, $$id_column, $level);
        }
        $str_dropdown .= $level == 1 ? '</select>' : '';
    }
    return $str_dropdown;
}
function midas_html_chars($string)
{
    $array_trans = array('<' => "&lt;", '>' => "&gt;", '"' => "&quot;", "'" => "&#039;");
    return strtr($string, $array_trans);
}
function file_absolute_path($file)
{
    return fs_to_absolute(dirname($file));
}
function load_plugin($name, $type = '')
{
    global $ARR_LOADED_PLUGINS, $ARR_PLUGINS_INFO;
    if ($type == 'up') {
        $dir = SITE_FS_PATH . '/under_process/' . $name;
    } else {
        $dir = SITE_FS_PATH . '/' . PLUGINS_DIR . '/' . $name;
    }
    if (file_exists($dir . '/midas_plugin.php')) {
        // Pastikan $plugin_version didefinisikan di dalam file plugin
        $plugin_version = null;
        require_once($dir . '/midas_plugin.php');
        // Jika $plugin_version tidak didefinisikan di plugin, beri default
        if (!isset($plugin_version)) {
            $plugin_version = '1.0.0'; // default versi
        }
        // Pastikan global array sudah diinisialisasi
        if (!isset($ARR_LOADED_PLUGINS) || !is_array($ARR_LOADED_PLUGINS)) {
            $ARR_LOADED_PLUGINS = [];
        }
        if (!isset($ARR_PLUGINS_INFO) || !is_array($ARR_PLUGINS_INFO)) {
            $ARR_PLUGINS_INFO = [];
        }
        $ARR_LOADED_PLUGINS[] = $name;
        $ARR_PLUGINS_INFO[$name] = array('version' => $plugin_version, 'type' => $type);
    }
}
function plugin_loaded($name)
{
    global $ARR_LOADED_PLUGINS;
    return isset($ARR_LOADED_PLUGINS) && in_array($name, $ARR_LOADED_PLUGINS);
}
function plugin_path($type, $name = '')
{
    global $CURRENT_PLUGIN, $ARR_PLUGINS_INFO;
    if ($name == '') {
        $name = $CURRENT_PLUGIN ?? ''; // pastikan $CURRENT_PLUGIN didefinisikan
    }
    $plugin_info_type = $ARR_PLUGINS_INFO[$name]['type'] ?? '';
    $dir = ($plugin_info_type == 'up') ? 'under_process' : PLUGINS_DIR;
    $type = strtoupper($type);
    switch ($type) {
        case 'WS':
            return SITE_WS_PATH . '/' . $dir . '/' . $name;
        case 'FS':
            return SITE_FS_PATH . '/' . $dir . '/' . $name;
        case 'SUB':
            return SITE_SUB_PATH . '/' . $dir . '/' . $name;
        case 'AWS':
            return SITE_WS_PATH . '/' . $dir . '/' . $name . '/' . ADMIN_DIR;
        case 'AFS':
            return SITE_FS_PATH . '/' . $dir . '/' . $name . '/' . ADMIN_DIR;
        case 'ASUB':
            return SITE_SUB_PATH . '/' . $dir . '/' . $name . '/' . ADMIN_DIR;
        default:
            return false;
    }
}
function plugin_fs_path($name = '')
{
    return plugin_path('FS', $name);
}
function plugin_ws_path($name = '')
{
    return plugin_path('WS', $name);
}
function plugin_sub_path($name = '')
{
    return plugin_path('SUB', $name);
}
function plugin_admin_sub_path($name = '')
{
    return plugin_path('ASUB', $name);
}
function plugin_admin_fs_path($name = '')
{
    return plugin_path('AFS', $name);
}
function pdo_query($sql, $arr = null)
{
    if (!$GLOBALS['pcon']) {
        pdo_connect();
    }
    $sth = $GLOBALS['pcon']->prepare($sql) or pdo_error($sth);
    if (is_array($arr)) {
        $arr = pdo_array($arr);
    }
    $time_before_sql = checkpoint();
    $sth = pdo_execute_sth($sth, $arr);
    $time_taken_for_sql = checkpoint();
    if ($time_taken_for_sql > .5) {
        $file = SITE_FS_PATH . '/sql_log/opt.txt';
        if (filesize($file) > 500 * 1024) {
            $handle = fopen($file, 'w');
        } else {
            $handle = fopen($file, 'a');
        }
        fwrite($handle, $time_taken_for_sql . "\t" . $sql);
        fclose($handle);
    }
    return $sth;
}
function pdo_execute_sth($sth, $arr)
{
    $sth->execute($arr) or die(pdo_error($sth, $arr));
    return $sth;
}
function pdo_prepare($sql)
{
    $sth = $GLOBALS['pcon']->prepare($sql) or die("mysql error");
    return $sth;
}
function pdo_fetch_array($sth, $result_type = null)
{
    return $sth->fetch($result_type);
}
function pdo_fetch_assoc($sth)
{
    return $sth->fetch(PDO::FETCH_ASSOC);
}
function pdo_scalar($sql, $arr = null)
{
    $sth = pdo_query($sql, $arr);
    if ($result = $sth->fetch(PDO::FETCH_NUM)) {
        return $result[0];
    }
}
function pdo_num_rows($sth)
{
    return $sth->rowCount();
}
function pdo_insert_id($name = null)
{
    return $GLOBALS['pcon']->lastInsertId($name);
}
function pdo_error($sth)
{
    $pdo_error = $sth->errorInfo();
    if (LOCAL_MODE) {
        echo "<div style='font-family: tahoma; font-size: 11px; color: #333333'>";
        echo "<br><b>" . $pdo_error[2] . "</b><br />";
        echo "<br><b>SQL:</b> " . $sth->queryString . "<br />";
        echo "<br><b>MySQL Code:</b> " . $pdo_error[1];
        echo "<br><b>Ansi Code:</b> " . $pdo_error[0] . "<br />";
        print_error();
        echo "</div>";
    } else {
        $file = SITE_FS_PATH . '/sql_opt/errors.txt';
        $handle = fopen($file, 'a');
        fwrite($handle, "\r\n" . $_SERVER['PHP_SELF'] . "\t" . $sth->queryString . "\t" . $pdo_error[2] . "\t" . date("Y-m-d G:i:s", time()) . "\t" . session_id());
        fclose($handle);
        die("Internal server error.");
    }
}
function pdo_build_sql($sql, $arr)
{
    foreach ($arr as $name => $value) {
        $sql = preg_replace('/\:' . $name . '\b/', "'" . $value . "'", $sql);
    }
    return $sql;
}
function pdo_in_clause(&$sql, &$sql_params, $column, $arr)
{
    $sql .= " and $column in (";
    for ($i = 0, $j = count($arr); $i < $j; $i++) {
        $sql .= ' :' . $column . $i . ', ';
        $sql_params[$column . $i] = $arr[$i];
    }
    $sql = substr($sql, 0, -2);
    $sql .= ')';
}
function pdo_array($arr)
{
    return array_map("null_to_blank", $arr);
}
function null_to_blank($var)
{
    if (is_null($var)) {
        return '';
    }
    return $var;
}
function resize_image($source_path, $target_path, $target_width, $target_height)
{
	// Dapatkan ukuran dan jenis file gambar
	list($original_width, $original_height, $image_type) = getimagesize($source_path);
	switch ($image_type) {
		case IMAGETYPE_JPEG:
			$source_image = imagecreatefromjpeg($source_path);
			break;
		case IMAGETYPE_PNG:
			$source_image = imagecreatefrompng($source_path);
			break;
		case IMAGETYPE_GIF:
			$source_image = imagecreatefromgif($source_path);
			break;
		default:
			return false; // Unsupported type
	}
	// Buat canvas baru dengan ukuran yang diinginkan
	$resized_image = imagecreatetruecolor($target_width, $target_height);
	// Untuk PNG: atur transparansi
	if ($image_type == IMAGETYPE_PNG) {
		imagealphablending($resized_image, false);
		imagesavealpha($resized_image, true);
	}
	// Resize image
	imagecopyresampled(
		$resized_image,
		$source_image,
		0,
		0,
		0,
		0,
		$target_width,
		$target_height,
		$original_width,
		$original_height
	);
	// Simpan ke file baru (atau timpa)
	switch ($image_type) {
		case IMAGETYPE_JPEG:
			imagejpeg($resized_image, $target_path, 90); // Quality 90
			break;
		case IMAGETYPE_PNG:
			imagepng($resized_image, $target_path);
			break;
		case IMAGETYPE_GIF:
			imagegif($resized_image, $target_path);
			break;
	}
	// Bersihkan memory
	imagedestroy($source_image);
	imagedestroy($resized_image);
	return true;
}