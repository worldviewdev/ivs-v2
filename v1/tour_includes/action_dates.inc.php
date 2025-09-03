<?php
class Calendar_town
{
    protected $dayNames = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
    protected $monthNames = array(
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December'
    );
    protected $startDay = 0;
    protected $startMonth = 1;
    protected $daysInMonth = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    public function __construct()
    {
        // Constructor now empty since we initialize properties above
    }
    public function getDayNames()
    {
        return $this->dayNames;
    }
    public function setDayNames($names)
    {
        $this->dayNames = $names;
    }
    public function getMonthNames()
    {
        return $this->monthNames;
    }
    public function setMonthNames($names)
    {
        $this->monthNames = $names;
    }
    public function getStartDay()
    {
        return $this->startDay;
    }
    public function setStartDay($day)
    {
        $this->startDay = $day;
    }
    public function getStartMonth()
    {
        return $this->startMonth;
    }
    public function setStartMonth($month)
    {
        $this->startMonth = $month;
    }
    public function getCalendarLink($month, $year)
    {
        $_SERVER['QUERY_STRING'] = str_replace("&", "&", $_SERVER['QUERY_STRING']);
        if ($_SERVER['QUERY_STRING'] && strpos($_SERVER['QUERY_STRING'], 'month') == false && strpos($_SERVER['QUERY_STRING'], 'year') == false) {
            $Query_string = '?' . $_SERVER['QUERY_STRING'] . "&month=" . $month . "&year=" . $year;
        } elseif ($_SERVER['QUERY_STRING'] && strpos($_SERVER['QUERY_STRING'], 'month') == true && strpos($_SERVER['QUERY_STRING'], 'year') == true) {
            if ($_GET['month'] == 10 || $_GET['month'] == 11 || $_GET['month'] == 12) {
                $Query_string = '?' . substr($_SERVER['QUERY_STRING'], 0, -19) . "&month=" . $month . "&year=" . $year;
            } else {
                $Query_string = '?' . substr($_SERVER['QUERY_STRING'], 0, -18) . "&month=" . $month . "&year=" . $year;
            }
        } else {
            $Query_string = "?month=" . $month . "&year=" . $year;
        }
        return $_SERVER['PHP_SELF'] . $Query_string;
    }
    public function getDateLink($day, $month, $year)
    {
        return false;
    }
    public function getCurrentMonthView($event_type = '')
    {
        $d = getdate(time());
        return $this->getMonthView($d["mon"], $d["year"], $event_type);
    }
    public function getCurrentYearView()
    {
        $d = getdate(time());
        return $this->getYearView($d["year"]);
    }
    public function getMonthView($month, $year)
    {
        return $this->getMonthHTML($month, $year);
    }
    public function getYearView($year)
    {
        return $this->getYearHTML($year);
    }
    /********************************************************************************
        The rest are private methods. No user-servicable parts inside.
        You shouldn't need to call any of these functions directly.
     *********************************************************************************/
    /*
        Calculate the number of days in a month, taking into account leap years.
    */
    public function getDaysInMonth($month, $year)
    {
        if ($month < 1 || $month > 12) {
            return 0;
        }
        $d = $this->daysInMonth[$month - 1];
        if ($month == 2) {
            if ($year % 4 == 0) {
                if ($year % 100 == 0) {
                    if ($year % 400 == 0) {
                        $d = 29;
                    }
                } else {
                    $d = 29;
                }
            }
        }
        return $d;
    }
    /*
        Generate the HTML for a given month
    */
    public function getMonthHTML($m, $y, $showYear = 1, $id = "", $ajax_id = "", $edit_mode = '', $mode = 'mini')
    {
        // Penyesuaian tampilan berdasarkan mode
        $isMini = ($mode === 'mini');
        $cellHeight = $isMini ? 30 : 100;
        $fontSize = $isMini ? '12px' : '25px';
        $headerFontSize = $isMini ? '16px' : '30px';
        $tableClass = $isMini ? 'cal-mini' : 'caltb';
        $headerClass = $isMini ? 'cal-mini-header' : '';
        global $action_type_arr;
        if ($ajax_id == "") {
            $ajax_id = "calview";
        }
        $s = "";
        $header = ""; // Initialize header
        $a = $this->adjustDate($m, $y);
        $month = $a[0];
        $year = $a[1];
        $daysInMonth = $this->getDaysInMonth($month, $year);
        $date = getdate(mktime(12, 0, 0, $month, 1, $year));
        $first = $date["wday"];
        $monthName = $this->monthNames[$month - 1];
        $prev = $this->adjustDate($month - 1, $year);
        $next = $this->adjustDate($month + 1, $year);
        if ($showYear == 1) {
            $prevMonth = $this->getCalendarLink($prev[0], $prev[1]);
            $nextMonth = $this->getCalendarLink($next[0], $next[1]);
        } else {
            $prevMonth = "";
            $nextMonth = "";
        }
        /*#fbe5e5*/
        //$id=$id;
        $file_res = db_result("select * from igs_file_action_dates where fk_file_id='" . $id . "'");
        $header .= "<strong class='$headerClass' style='font-size: $headerFontSize;'> " . $monthName . "&nbsp;" . (($showYear > 0) ? " " . $year : "") . "</strong>";
        $s .= "<table class=\"$tableClass\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\" style=\"border:1px solid #EDEDED;\" id=\"cal\">
			  <tr>
				<td align=\"center\"><table width=\"100%\" border=\"0\"   cellpadding=\"3\" cellspacing=\"1\"  >
				  <tr>
					<td colspan=\"7\" align=\"left\"><table width=\"98%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\" >
					  <tr>
						<td width=\"3%\" align=\"left\" ><strong>" . (($prevMonth == "") ? "&nbsp;" : "<a onclick=\"show_action_cal('" . $prev[0] . "','" . $prev[1] . "','" . SITE_WS_PATH . "','" . $id . "','" . $ajax_id . "','" . $edit_mode . "');\" href=\"javascript: void(0);\"   title=\"Previous Month\" ><img src=\"" . SITE_WS_PATH . "/images/left_arrow.png\" border=\"0\" /></a>") . "</strong></td>
						<td width=\"90%\" align=\"center\" style=\"font-size: $headerFontSize;\"><strong>$monthName - $year</strong>";
        if ($edit_mode == '') {
            $s .= $isMini ? '' : " <b>(Edit)</b>";
        }
        $s .= "</td>
						<td width=\"7%\" align=\"right\" style=\"color:#022b4f\"><strong>" . (($nextMonth == "") ? "&nbsp;" : "<a onclick=\"show_action_cal('" . $next[0] . "','" . $next[1] . "','" . SITE_WS_PATH . "','" . $id . "','" . $ajax_id . "','" . $edit_mode . "');\" href=\"javascript: void(0);\" title=\"Next Month\" ><img src=\"" . SITE_WS_PATH . "/images/right_arrow.png\" border=\"0\" /></a>") . "</strong></td>
					  </tr>
					</table></td>
				  </tr>
				  <tr class=\"caltbtr\">";
        for ($j = 0; $j < 7; $j++) {
            $s .= "<td style=\"font-size: $fontSize;\" align=\"center\" width=\"14.5%\" bgcolor=\"#0390B1\"><strong class=\"text_12_white\">" . $this->dayNames[($this->startDay + $j) % 7] . "</strong></td>";
        }
        $s .= "</tr>";
        $d = $this->startDay + 1 - $first;
        while ($d > 1) {
            $d -= 7;
        }
        while ($d <= $daysInMonth) {
            $s .= "<tr>\n";
            for ($i = 0; $i < 7; $i++) {
                $class_name = "";
                if ($d > 0 && $d <= $daysInMonth) {
                    $date = $m . "/" . str_pad($d, 2, 0, STR_PAD_LEFT) . "/" . $y;
                    $arr_date = explode("/", $date);
                    $date_app = $y . "-";
                    if ($m < 10) {
                        $date_app .= "0" . $m;
                    } else {
                        $date_app .= $m;
                    }
                    if ($d < 10) {
                        $newdd = "0" . $d;
                    } else {
                        $newdd = $d;
                    }
                    $date_app .= "-" . $newdd;
                    if ($i == 0) {
                        $class_name = "date_sunday_bg";
                    } else {
                        $class_name = "date_bg";
                    }
                    $is_exist = db_query("select * from igs_file_action_dates where fk_file_id='$id' and act_date='$date_app'");
                    $actArr = array();
                    while ($actRes = mysqli_fetch_array($is_exist)) {
                        $act_type = $actRes['act_type'];
                        $actArr[$actRes['act_id']] = $action_type_arr[$act_type] ?? 'Unknown Action';
                    }
                    // Ambil log dari mv_file_activity_log
                    $log_exist = db_query("select * from mv_file_activity_log where fk_file_id='$id' and DATE(activity_added_on)='$date_app'");
                    $logArr = array();
                    while ($logRes = mysqli_fetch_array($log_exist)) {
                        $logArr[] = $logRes['activity_description'];
                    }
                    // Penentuan warna background <td>
                    $hasLog = (count($logArr) > 0);
                    $hasEvent = (mysqli_num_rows($is_exist) > 0);
                    $tdBgColor = $hasLog ? '#ffb6c1' : ($hasEvent ? '#008800' : '#E4E4E4');
                    if ($hasLog || $hasEvent) {
                        $s .= "<td style=\"text-align:right;padding-right:5px;height:{$cellHeight}px;font-size:$fontSize;\" align=\"right\" valign=\"top\" bgcolor=\"$tdBgColor\"><strong>";
                        if ($edit_mode != "") {
                            $s .= "<a style=\"font-size: $fontSize;text-align:right;\" href=\"" . SITE_WS_PATH . "/" . AGENT_ADMIN_DIR . "/file/file_action_task.php?id=$id&edit_mode=$edit_mode&mode=$mode&date_to_add=$date_app&town_month=$m&town_year=$y\"><font color='#FFF'>" . $d . "</font></a>";
                        } else {
                            $s .= "<font color='#FFF'>" . $d . "</font>";
                        }
                        // Tampilkan event (actArr) dengan warna putih
                        foreach ($actArr as $key => $val) {
                            $safeVal = htmlspecialchars($val ?? '');
                            $s .= "<div style='color:#FFF;font-size:" . ($isMini ? "10px" : "13px") . ";text-align:left;padding-left:2px;margin-bottom:1px;'>• $safeVal</div>";
                        }
                        // Tampilkan log activity (limit 2, sisanya ... dan tampilkan semua di tooltip)
                        if (count($logArr) > 0) {
                            $maxLog = 2;
                            $logPreview = array_slice($logArr, 0, $maxLog);
                            $logTooltip = htmlspecialchars(implode("\n", $logArr));
                            $logTextColor = $hasLog ? '#FFF' : '#333';
                            foreach ($logPreview as $log) {
                                $s .= "<div style='color:$logTextColor;font-size:" . ($isMini ? "10px" : "13px") . ";text-align:left;padding-left:2px;margin-bottom:1px;border-radius:3px;' title='$logTooltip'>• " . htmlspecialchars($log) . "</div>";
                            }
                            if (count($logArr) > $maxLog) {
                                $s .= "<div style='color:$logTextColor;font-size:" . ($isMini ? "10px" : "13px") . ";text-align:left;padding-left:2px;cursor:pointer;margin-bottom:1px;border-radius:3px;' title='$logTooltip'>• ...</div>";
                            }
                        }
                        $s .= "</strong>";
                    } else {
                        $s .= "<td style=\"text-align:right;padding-right:5px;height:{$cellHeight}px;font-size:$fontSize;\" align=\"center\" valign=\"top\" bgcolor=\"#E4E4E4\" ><strong>";
                        if ($edit_mode != "") {
                            $s .= "<a style=\"font-size: $fontSize;text-align:right;\" href=\"" . SITE_WS_PATH . "/" . AGENT_ADMIN_DIR . "/file/file_action_task.php?id=$id&edit_mode=$edit_mode&mode=$mode&&date_to_add=$date_app&town_month=$m&town_year=$y\">" . $d . "</a>";
                        } else {
                            $s .= $d;
                        }
                        $s .= "</strong>";
                    }
                } else {
                    if ($i == 0) {
                        $class_name = "sunday_date_blank";
                    } else {
                        $class_name = "date_blank";
                    }
                    $s .= "<td height=\"{$cellHeight}\" align=\"left\"  bgcolor=\"#FFFFFF\" class=\"$class_name\" >&nbsp;";
                }
                $s .= "</td>\n";
                $d++;
            }
            $s .= "</tr>\n";
        }
        $s .= "</table></td>\n  </tr>\n  \n</table>";
        return $s;
    }
    /*
        Generate the HTML for a given year
    */
    public function getYearHTML($year)
    {
        $s = "";
        $prev = $this->getCalendarLink(0, $year - 1);
        $next = $this->getCalendarLink(0, $year + 1);
        $s .= "<table class=\"calendar1\" cellspacing=\"0\">\n";
        $s .= "<tr>";
        $s .= "<td class=\"calendartop1\">" . (($prev == "") ? "&nbsp;" : "<a href=\"$prev\" class='r2' >&lt;&lt;</a>") . "</td>\n";
        $s .= "<td class=\"calendartop1\">" . (($this->startMonth > 1) ? $year . " - " . ($year + 1) : $year) . "</td>\n";
        $s .= "<td class=\"calendartop1\">" . (($next == "") ? "&nbsp;" : "<a href=\"$next\">&gt;&gt;</a>") . "</td>\n";
        $s .= "</tr>\n";
        $s .= "<tr>";
        $s .= "<td>" . $this->getMonthHTML(0 + $this->startMonth, $year, 0) . "</td>\n";
        $s .= "<td>" . $this->getMonthHTML(1 + $this->startMonth, $year, 0) . "</td>\n";
        $s .= "<td>" . $this->getMonthHTML(2 + $this->startMonth, $year, 0) . "</td>\n";
        $s .= "</tr>\n";
        $s .= "<tr>\n";
        $s .= "<td>" . $this->getMonthHTML(3 + $this->startMonth, $year, 0) . "</td>\n";
        $s .= "<td>" . $this->getMonthHTML(4 + $this->startMonth, $year, 0) . "</td>\n";
        $s .= "<td>" . $this->getMonthHTML(5 + $this->startMonth, $year, 0) . "</td>\n";
        $s .= "</tr>\n";
        $s .= "<tr>\n";
        $s .= "<td>" . $this->getMonthHTML(6 + $this->startMonth, $year, 0) . "</td>\n";
        $s .= "<td>" . $this->getMonthHTML(7 + $this->startMonth, $year, 0) . "</td>\n";
        $s .= "<td>" . $this->getMonthHTML(8 + $this->startMonth, $year, 0) . "</td>\n";
        $s .= "</tr>\n";
        $s .= "<tr>\n";
        $s .= "<td>" . $this->getMonthHTML(9 + $this->startMonth, $year, 0) . "</td>\n";
        $s .= "<td>" . $this->getMonthHTML(10 + $this->startMonth, $year, 0) . "</td>\n";
        $s .= "<td>" . $this->getMonthHTML(11 + $this->startMonth, $year, 0) . "</td>\n";
        $s .= "</tr>\n";
        $s .= "</table>\n";
        return $s;
    }
    /*
        Adjust dates to allow months > 12 and < 0. Just adjust the years appropriately.
        e.g. Month 14 of the year 2001 is actually month 2 of year 2002.
    */
    public function adjustDate($month, $year)
    {
        $a = array();
        $a[0] = $month;
        $a[1] = $year;
        while ($a[0] > 12) {
            $a[0] -= 12;
            $a[1]++;
        }
        while ($a[0] <= 0) {
            $a[0] += 12;
            $a[1]--;
        }
        return $a;
    }
}
// Tambahan: Kalender khusus log file
class Calendar_town_log extends Calendar_town
{
    public function __construct()
    {
        parent::__construct();
    }
    // Override getMonthHTML untuk menampilkan event dari mv_file_activity_log
    public function getMonthHTML($m, $y, $showYear = 1, $id = "", $ajax_id = "", $edit_mode = '', $mode = 'mini')
    {
        // Penyesuaian tampilan
        $isMini = ($mode === 'mini');
        $cellHeight = $isMini ? 30 : 100;
        $fontSize = $isMini ? '12px' : '25px';
        $headerFontSize = $isMini ? '16px' : '30px';
        $tableClass = $isMini ? 'cal-mini' : 'caltb';
        $headerClass = $isMini ? 'cal-mini-header' : '';
        if ($ajax_id == "") {
            $ajax_id = "calview";
        }
        $s = "";
        $header = "";
        $a = $this->adjustDate($m, $y);
        $month = $a[0];
        $year = $a[1];
        $daysInMonth = $this->getDaysInMonth($month, $year);
        $date = getdate(mktime(12, 0, 0, $month, 1, $year));
        $first = $date["wday"];
        $monthName = $this->monthNames[$month - 1];
        $prev = $this->adjustDate($month - 1, $year);
        $next = $this->adjustDate($month + 1, $year);
        if ($showYear == 1) {
            $prevMonth = $this->getCalendarLink($prev[0], $prev[1]);
            $nextMonth = $this->getCalendarLink($next[0], $next[1]);
        } else {
            $prevMonth = "";
            $nextMonth = "";
        }
        $header .= "<strong class='$headerClass' style='font-size: $headerFontSize;'> " . $monthName . "&nbsp;" . (($showYear > 0) ? " " . $year : "") . "</strong>";
        $s .= "<table class=\"$tableClass\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\" style=\"border:1px solid #EDEDED;\" id=\"cal\">
              <tr>
                <td align=\"center\"><table width=\"100%\" border=\"0\"   cellpadding=\"3\" cellspacing=\"1\"  >
                  <tr>
                    <td colspan=\"7\" align=\"left\"><table width=\"98%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\" >
                      <tr>
                        <td width=\"3%\" align=\"left\" ><strong>" . (($prevMonth == "") ? "&nbsp;" : "<a onclick=\"show_action_cal('" . $prev[0] . "','" . $prev[1] . "','" . SITE_WS_PATH . "','" . $id . "','" . $ajax_id . "','" . $edit_mode . "');\" href=\"javascript: void(0);\"   title=\"Previous Month\" ><img src=\"" . SITE_WS_PATH . "/images/left_arrow.png\" border=\"0\" /></a>") . "</strong></td>
                        <td width=\"90%\" align=\"center\" style=\"font-size: $headerFontSize;\"><strong>$monthName - $year</strong>";
        if ($edit_mode == '') {
            $s .= $isMini ? '' : " <b>(Edit)</b>";
        }
        $s .= "</td>
                        <td width=\"7%\" align=\"right\" style=\"color:#022b4f\"><strong>" . (($nextMonth == "") ? "&nbsp;" : "<a onclick=\"show_action_cal('" . $next[0] . "','" . $next[1] . "','" . SITE_WS_PATH . "','" . $id . "','" . $ajax_id . "','" . $edit_mode . "');\" href=\"javascript: void(0);\" title=\"Next Month\" ><img src=\"" . SITE_WS_PATH . "/images/right_arrow.png\" border=\"0\" /></a>") . "</strong></td>
                      </tr>
                    </table></td>
                  </tr>
                  <tr class=\"caltbtr\">";
        for ($j = 0; $j < 7; $j++) {
            $s .= "<td style=\"font-size: $fontSize;\" align=\"center\" width=\"14.5%\" bgcolor=\"#0390B1\"><strong class=\"text_12_white\">" . $this->dayNames[($this->startDay + $j) % 7] . "</strong></td>";
        }
        $s .= "</tr>";
        $d = $this->startDay + 1 - $first;
        while ($d > 1) {
            $d -= 7;
        }
        while ($d <= $daysInMonth) {
            $s .= "<tr>\n";
            for ($i = 0; $i < 7; $i++) {
                $class_name = "";
                if ($d > 0 && $d <= $daysInMonth) {
                    $date = $m . "/" . str_pad($d, 2, 0, STR_PAD_LEFT) . "/" . $y;
                    $arr_date = explode("/", $date);
                    $date_app = $y . "-";
                    if ($m < 10) {
                        $date_app .= "0" . $m;
                    } else {
                        $date_app .= $m;
                    }
                    if ($d < 10) {
                        $newdd = "0" . $d;
                    } else {
                        $newdd = $d;
                    }
                    $date_app .= "-" . $newdd;
                    if ($i == 0) {
                        $class_name = "date_sunday_bg";
                    } else {
                        $class_name = "date_bg";
                    }
                    // Ambil event dari igs_file_action_dates
                    $is_exist = db_query("select * from igs_file_action_dates where fk_file_id='$id' and act_date='$date_app'");
                    $actArr = array();
                    global $action_type_arr;
                    while ($actRes = mysqli_fetch_array($is_exist)) {
                        // Cek apakah key ada di $action_type_arr sebelum mengakses
                        $act_type_key = $actRes['act_type'];
                        $actArr[$actRes['act_id']] = isset($action_type_arr[$act_type_key]) ? $action_type_arr[$act_type_key] : '';
                    }
                    // Ambil log dari mv_file_activity_log
                    $log_exist = db_query("select * from mv_file_activity_log where fk_file_id='$id' and DATE(activity_added_on)='$date_app'");
                    $logArr = array();
                    while ($logRes = mysqli_fetch_array($log_exist)) {
                        $logArr[] = $logRes['activity_description'];
                    }
                    // Penentuan warna background <td>
                    $hasLog = (count($logArr) > 0);
                    $hasEvent = (mysqli_num_rows($is_exist) > 0);
                    $tdBgColor = $hasLog ? '#ffb6c1' : ($hasEvent ? '#008800' : '#E4E4E4');
                    if ($hasLog || $hasEvent) {
                        $s .= "<td style=\"text-align:right;padding-right:5px;height:{$cellHeight}px;font-size:$fontSize;\" align=\"right\" valign=\"top\" bgcolor=\"$tdBgColor\"><strong>";
                        if ($edit_mode != "") {
                            $s .= "<a style=\"font-size: $fontSize;text-align:right;\" href=\"" . SITE_WS_PATH . "/" . AGENT_ADMIN_DIR . "/file/file_action_task.php?id=$id&edit_mode=$edit_mode&mode=$mode&date_to_add=$date_app&town_month=$m&town_year=$y\"><font color='#FFF'>" . $d . "</font></a>";
                        } else {
                            $s .= "<font color='#FFF'>" . $d . "</font>";
                        }
                        // Tampilkan event (actArr) dengan warna putih
                        foreach ($actArr as $key => $val) {
                            // Pastikan $val adalah string, jika null ganti dengan string kosong
                            $safeVal = ($val === null) ? '' : $val;
                            $s .= "<div style='color:#FFF;font-size:" . ($isMini ? "10px" : "13px") . ";text-align:left;padding-left:2px;margin-bottom:1px;'>• " . htmlspecialchars($safeVal) . "</div>";
                        }
                        // Tampilkan log activity (limit 2, sisanya ... dan tampilkan semua di tooltip)
                        if (count($logArr) > 0) {
                            $maxLog = 2;
                            // Pastikan semua elemen $logArr adalah string, jika null ganti dengan string kosong
                            $safeLogArr = array_map(function ($v) {
                                return ($v === null) ? '' : $v;
                            }, $logArr);
                            $logPreview = array_slice($safeLogArr, 0, $maxLog);
                            $logTooltip = htmlspecialchars(implode("\n", $safeLogArr));
                            $logTextColor = $hasLog ? '#FFF' : '#333';
                            foreach ($logPreview as $log) {
                                $s .= "<div style='color:$logTextColor;font-size:" . ($isMini ? "10px" : "13px") . ";text-align:left;padding-left:2px;margin-bottom:1px;border-radius:3px;' title='$logTooltip'>• " . htmlspecialchars($log) . "</div>";
                            }
                            if (count($safeLogArr) > $maxLog) {
                                $s .= "<div style='color:$logTextColor;font-size:" . ($isMini ? "10px" : "13px") . ";text-align:left;padding-left:2px;cursor:pointer;margin-bottom:1px;border-radius:3px;' title='$logTooltip'>• ...</div>";
                            }
                        }
                        $s .= "</strong>";
                    } else {
                        $s .= "<td style=\"text-align:right;padding-right:5px;height:{$cellHeight}px;font-size:$fontSize;\" align=\"center\" valign=\"top\" bgcolor=\"#E4E4E4\" ><strong>";
                        if ($edit_mode != "") {
                            $s .= "<a style=\"font-size: $fontSize;text-align:right;\" href=\"" . SITE_WS_PATH . "/" . AGENT_ADMIN_DIR . "/file/file_action_task.php?id=$id&edit_mode=$edit_mode&mode=$mode&&date_to_add=$date_app&town_month=$m&town_year=$y\">" . $d . "</a>";
                        } else {
                            $s .= $d;
                        }
                        $s .= "</strong>";
                    }
                } else {
                    if ($i == 0) {
                        $class_name = "sunday_date_blank";
                    } else {
                        $class_name = "date_blank";
                    }
                    $s .= "<td height=\"{$cellHeight}\" align=\"left\"  bgcolor=\"#FFFFFF\" class=\"$class_name\" >&nbsp;";
                }
                $s .= "</td>\n";
                $d++;
            }
            $s .= "</tr>\n";
        }
        $s .= "</table></td>\n  </tr>\n  \n</table>";
        return $s;
    }
}