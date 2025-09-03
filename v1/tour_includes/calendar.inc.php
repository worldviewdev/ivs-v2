<?
class Calendar_town{
    function Calendar_town(){
	}
    function getDayNames(){
        return $this->dayNames;
    }
    function setDayNames($names){
        $this->dayNames = $names;
    }
    function getMonthNames(){
        return $this->monthNames;
    }
    function setMonthNames($names){
        $this->monthNames = $names;
    }
	function getStartDay(){
        return $this->startDay;
    }
    function setStartDay($day){
        $this->startDay = $day;
    }
    function getStartMonth(){
        return $this->startMonth;
    }
    function setStartMonth($month){
        $this->startMonth = $month;
    }
    function getCalendarLink($month, $year){
	   $_SERVER['QUERY_STRING']=str_replace("&","&",$_SERVER['QUERY_STRING']);
	   if($_SERVER['QUERY_STRING'] && strpos($_SERVER['QUERY_STRING'],'month')==false && strpos($_SERVER['QUERY_STRING'],'year')==false){
		   $Query_string='?'.$_SERVER['QUERY_STRING']."&month=".$month."&year=".$year;
	    }else if($_SERVER['QUERY_STRING'] && strpos($_SERVER['QUERY_STRING'],'month')==true && strpos($_SERVER['QUERY_STRING'],'year')==true){
		     if($_GET['month']==10 || $_GET['month']==11 || $_GET['month']==12){
			    $Query_string='?'.substr($_SERVER['QUERY_STRING'], 0, -19)."&month=".$month."&year=".$year;
			  }else{
			    $Query_string='?'.substr($_SERVER['QUERY_STRING'], 0, -18)."&month=".$month."&year=".$year;
              }
		}
		else{
		 $Query_string="?month=".$month."&year=".$year;
		}
		return $_SERVER['PHP_SELF'].$Query_string;
    }
    function getDateLink($day, $month, $year)
    {
		return false;
	}
	function getCurrentMonthView()
	{
		$d = getdate(time());
		return $this->getMonthView($d["mon"], $d["year"]);
	}
    function getCurrentYearView()
    {
        $d = getdate(time());
        return $this->getYearView($d["year"]);
    }
    function getMonthView($month, $year)
    {
        return $this->getMonthHTML($month, $year);
    }
    function getYearView($year)
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
    function getDaysInMonth($month, $year)
    {
        if ($month < 1 || $month > 12)
        {
            return 0;
        }
        $d = $this->daysInMonth[$month - 1];
        if ($month == 2)
        {
            if ($year%4 == 0)
            {
                if ($year%100 == 0)
                {
                    if ($year%400 == 0)
                    {
                        $d = 29;
                    }
                }
                else
                {
                    $d = 29;
                }
            }
        }
        return $d;
    }
    /*
        Generate the HTML for a given month
    */
    function getMonthHTML($m, $y, $showYear = 1)
    {
		global $id;
        $s = "";
        $a = $this->adjustDate($m, $y);
        $month = $a[0];
        $year = $a[1];
    	$daysInMonth = $this->getDaysInMonth($month, $year);
    	$date = getdate(mktime(12, 0, 0, $month, 1, $year));
    	$first = $date["wday"];
    	$monthName = $this->monthNames[$month - 1];
    	$prev = $this->adjustDate($month - 1, $year);
    	$next = $this->adjustDate($month + 1, $year);
    	if ($showYear == 1)
    	{
    	    $prevMonth = $this->getCalendarLink($prev[0], $prev[1]);
    	    $nextMonth = $this->getCalendarLink($next[0], $next[1]);
    	}
    	else
    	{
    	    $prevMonth = "";
			$nextMonth = "";
		}
		/*#fbe5e5*/
		$header = "<strong> ".$monthName."&nbsp;".(($showYear > 0) ? " " .$year: "")."</strong>";
		  $s.="<form name=\"frm\" method=\"post\" action=\"\" onsubmit=\"return confirm('Are you sure you want to make these dates unavailable?');\">
		  <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\" style=\"border:2px solid #000;\">
			  <tr>
				<td align=\"center\"><table width=\"100%\" border=\"0\"   cellpadding=\"3\" cellspacing=\"1\" bgcolor=\"#fbe5e5\" >
				  <tr>
					<td colspan=\"7\" align=\"left\"  bgcolor=\"#d41715\"><table width=\"98%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\" >
					  <tr class=\"cal\">
						<td width=\"3%\" align=\"left\" style=\"color:#022b4f\"><strong>".(($prevMonth == "") ? "&nbsp;" :"<a href=\"$prevMonth\"  class='calendor_month_town' title=\"Previous Month\" >&laquo;</a>") ."</strong></td>
						<td width=\"90%\" align=\"center\" style=\"color:#fff;font-size: 16px;\"><strong>$monthName - $year</strong></td>
						<td width=\"7%\" align=\"right\" style=\"color:#022b4f\"><strong>".(($nextMonth == "") ? "&nbsp;" :"<a href=\"$nextMonth\" class='calendor_month_town'  title=\"Next Month\" >&raquo;</a>")."</strong></td>
					  </tr>
					</table></td>
				  </tr>
				  <tr class=\"cal\">
					<td align=\"center\" width=\"14.5%\" bgcolor=\"#000\" class=\"sunday\"><strong class=\"text_12_white\">".$this->dayNames[($this->startDay)%7]."</strong></td>
					<td align=\"center\"  width=\"14.5%\" bgcolor=\"#d41715\" class=\"text_12_white\"><strong class=\"text_12_white\">".$this->dayNames[($this->startDay+1)%7]."</strong></td>
					<td align=\"center\" width=\"14.5%\"  bgcolor=\"#d41715\" class=\"text_12_white\"><strong class=\"text_12_white\">".$this->dayNames[($this->startDay+2)%7]."</strong></td>
					<td align=\"center\"  width=\"14.5%\" bgcolor=\"#d41715\" class=\"text_12_white\"><strong class=\"text_12_white\">".$this->dayNames[($this->startDay+3)%7]."</strong></td>
					<td align=\"center\" width=\"14.5%\" bgcolor=\"#d41715\" class=\"text_12_white\"><strong class=\"text_12_white\">".$this->dayNames[($this->startDay+4)%7]."</strong></td>
					<td align=\"center\" width=\"14.5%\" bgcolor=\"#d41715\" class=\"text_12_white\"><strong class=\"text_12_white\">".$this->dayNames[($this->startDay+5)%7]."</strong></td>
					<td align=\"center\" width=\"14.5%\" bgcolor=\"#d41715\" class=\"text_12_white\"><strong class=\"text_12_white\">".$this->dayNames[($this->startDay+6)%7]."</strong></td>
				  </tr>";
	$d = $this->startDay + 1 - $first;
			while ($d > 1)
			{
				$d -= 7;
			}
			while ($d <= $daysInMonth){
				$s .= "<tr class=\"cal\">\n";
				for ($i = 0; $i < 7; $i++)
				{
					if ($d == date("d")) {
					//	$class = "today1";
						$bgColor="#ffffff";
					}
					elseif ($d < 0 || $d > $daysInMonth)
					{
							//$class = "nonday1";
							$bgColor="#d0e8f3";
					}
					else {
						//$class = "day1";
						$bgColor="#d0e8f3";
					}
					$class_name="";
					if ($d > 0 && $d <= $daysInMonth){
						$date=$m."/".str_pad($d,2,0,STR_PAD_LEFT)."/".$y;
						$arr_date=explode("/",$date);
						$date_app=$y."-";
						if($m<10)
						{
						$date_app.="0".$m;
						}
						else
						{
							$date_app.=$m;
						}
						if($d<10)
						{
							$newdd="0".$d;
						}
						else
						{
							$newdd=$d;
						}
						$date_app.="-".$newdd;
						if($i == 0){
							$class_name = "date_sunday_bg";
						}
						else{
							$class_name = "date_bg";
						}
						$id=$_REQUEST['id'];
						$tour_type=db_scalar("select days_in_multiday from tbl_tours where tour_id='".$id."'");
						if($tour_type=="0" || $tour_type==""  || $tour_type=="-1"){
							$sql="select * from tour_dates where pk_tour_id='$id' and  date(tour_start_date)='$date_app'";
						}else{
							$sql="select *,date(tour_start_date) as tour_first_day,date(tour_end_date) as tour_end_day from tour_dates where pk_tour_id='$id' and '$date_app' between date(tour_start_date) and date(tour_end_date)";
						}
						$sql.= " order by tour_start_date";
					   $result_app=db_result($sql);
					   if(mysqli_num_rows(db_query($sql))>0){
							$exclude=db_scalar("select count(*) from tbl_tour_date_exclude where edate='$date_app' and etour_id='$id'");
							if($exclude>0){
								$show_event_bg="#d41715";
							}else{
								$show_event_bg="green";
							}
					   		$s .= "<td   align=\"center\" valign=\"middle\" bgcolor=\"$show_event_bg\"><strong>";
							if($exclude>0 && ($tour_type=="0" || $tour_type=="-1" || $tour_type=="")){
								$link11=$this->getCalendarLink($m, $y)."&act=add&edate=".$date_app;
								#$s .= "<a onclick=\"return confirm('Are you sure you want to add this date in tour dates.');\" href=\"".$link11."\"  class=\"white_link\">".$d."</a>";
								$s .= "<input type='checkbox' name='tour_dates[]' value='".$date_app."' checked=\"checked\" /> <font style=\"color: #FFFFFF\">".$d."</font>";
							}elseif($tour_type=="0" || $tour_type=="" || $tour_type=="-1"){
								$link11=$this->getCalendarLink($m, $y)."&act=remove&edate=".$date_app;
								$s .= "<input type='checkbox' name='tour_dates[]' value='".$date_app."' /> <font style=\"color: #FFFFFF\">".$d."</font>";
							}else{
								$link11="";
								$s .= "<span class=\"white_text\">".$d."</span>";
							}
							/*if($exclude>0 && $tour_type!="1" && $tour_type!="4"){
								$link11=$this->getCalendarLink($m, $y)."&act=add&edate=".$date_app;
								$s .= "<a onclick=\"return confirm('Are you sure you want to add this date in tour dates.');\" href=\"".$link11."\"  class=\"white_link\">".$d."</a>";
							}elseif($exclude>0 && ($tour_type=="1" || $tour_type=="4")  && $result_app[tour_first_day]==$date_app){
								$link11=$this->getCalendarLink($m, $y)."&act=addmultiple&edate=".$date_app."&edate2=".$result_app[tour_end_day];
								$s .= "<a onclick=\"return confirm('Are you sure you want to add this date in tour dates.');\" href=\"".$link11."\"  class=\"white_link\">".$d."</a>";
							}elseif($tour_type!="1" && $tour_type!="4"){
								$link11=$this->getCalendarLink($m, $y)."&act=remove&edate=".$date_app;
								$s .= "<a onclick=\"return confirm('Are you sure you want to remove this date from tour dates.');\" href=\"".$link11."\"  class=\"white_link\">".$d."</a>";
							}elseif($tour_type=="1"  && $tour_type!="4" && $result_app[tour_first_day]==$date_app){
								$link11=$this->getCalendarLink($m, $y)."&act=removemultiple&edate=".$date_app."&edate2=".$result_app[tour_end_day];
								$s .= "<a onclick=\"return confirm('Are you sure you want to remove this dates from tour dates.');\" href=\"".$link11."\"  class=\"white_link\">".$d."</a>";
							}else{
								$link11="";
								$s .= "<span class=\"white_text\">".$d."</span>";
							}
							*/
							$s .= "</strong>";
					   }
					   else{
							$s .= "<td   align=\"center\" valign=\"middle\" bgcolor=\"#FFFFFF\" class=\"$class_name\" ><strong>$d</strong>";
					   }
					}
			else
					{
						if($i == 0){
							$class_name = "sunday_date_blank";
						}
						else{
							$class_name = "date_blank";
						}
						$s .= "<td  align=\"left\"  bgcolor=\"#FFFFFF\" class=\"$class_name\" >&nbsp;";
					}
					$s .= "</td>\n";
					$d++;
				}
				$s .= "</tr>\n";
			}
$s.="</table></td>
  </tr>";
$s.="</table><br />";
$s.='<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
		  <td class="tdData">
			<input type="hidden" name="act" value="update_single_dates">
			<input type="image" name="imageField" src="images/buttons/submit.gif" />
		  </td>
		</tr>
	 </table><br />
    </form>';
    	return $s;
    }
    /*
        Generate the HTML for a given year
    */
    function getYearHTML($year)
    {
        $s = "";
    	$prev = $this->getCalendarLink(0, $year - 1);
    	$next = $this->getCalendarLink(0, $year + 1);
        $s .= "<table class=\"calendar1\" cellspacing=\"0\">\n";
        $s .= "<tr>";
    	$s .= "<td class=\"calendartop1\">" . (($prev == "") ? "&nbsp;" : "<a href=\"$prev\" class='r2' >&lt;&lt;</a>")  . "</td>\n";
        $s .= "<td class=\"calendartop1\">" . (($this->startMonth > 1) ? $year . " - " . ($year + 1) : $year) ."</td>\n";
    	$s .= "<td class=\"calendartop1\">" . (($next == "") ? "&nbsp;" : "<a href=\"$next\">&gt;&gt;</a>")  . "</td>\n";
        $s .= "</tr>\n";
        $s .= "<tr>";
        $s .= "<td>" . $this->getMonthHTML(0 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "<td>" . $this->getMonthHTML(1 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "<td>" . $this->getMonthHTML(2 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "</tr>\n";
        $s .= "<tr>\n";
        $s .= "<td>" . $this->getMonthHTML(3 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "<td>" . $this->getMonthHTML(4 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "<td>" . $this->getMonthHTML(5 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "</tr>\n";
        $s .= "<tr>\n";
        $s .= "<td>" . $this->getMonthHTML(6 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "<td>" . $this->getMonthHTML(7 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "<td>" . $this->getMonthHTML(8 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "</tr>\n";
        $s .= "<tr>\n";
        $s .= "<td>" . $this->getMonthHTML(9 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "<td>" . $this->getMonthHTML(10 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "<td>" . $this->getMonthHTML(11 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "</tr>\n";
        $s .= "</table>\n";
        return $s;
    }
    /*
        Adjust dates to allow months > 12 and < 0. Just adjust the years appropriately.
        e.g. Month 14 of the year 2001 is actually month 2 of year 2002.
    */
    function adjustDate($month, $year)
    {
        $a = array();
        $a[0] = $month;
        $a[1] = $year;
        while ($a[0] > 12)
        {
            $a[0] -= 12;
            $a[1]++;
        }
        while ($a[0] <= 0)
        {
            $a[0] += 12;
            $a[1]--;
        }
        return $a;
    }
    /*
        The start day of the week. This is the day that appears in the first column
        of the calendar. Sunday = 0.
    */
    var $startDay = 0;
    /*
        The start month of the year. This is the month that appears in the first slot
        of the calendar in the year view. January = 1.
    */
    var $startMonth = 1;
    /*
        The labels to display for the days of the week. The first entry in this array
        represents Sunday.
    */
   var $dayNames =array("Sun", "Mon", "Tue", "Wed", "Thur", "Fri", "Sat");
    /*
        The labels to display for the months of the year. The first entry in this array
        represents January.
    */
	 var $monthNames = array("January", 		"February",		"March", 		"April",
	"May", 			"June", 		"July", 		"August",
	"September", 	"October", 		"November",		"December");
    /*var $monthNames = array("Leden", "nor", "Brezen", "Duben", "Kveten", "Cerven",
                            "Cervenec", "Srpen", "Zr", "Rjen", "Listopad", "Prosinec");*/
    /*
        The number of days in each month. You're unlikely to want to change this...
        The first entry in this array represents January.
    */
    var $daysInMonth = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
}
?>