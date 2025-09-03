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
    function getMonthHTML($m, $y, $showYear = 1,$tour_id="",$ajax_id="",$edit_mode='',$show_all_dates='')
    {
		$id=$tour_id;
		$tour_res=db_result("select * from mv_tours where tour_id='".$id."'");
		if(!strstr($_SERVER['PHP_SELF'],AGENT_ADMIN_DIR) && $show_all_dates==""){
			$latest_record_month=db_scalar("select month(tour_start_date) from mv_tour_dates where fk_tour_id='$id' and  month(tour_start_date) >= '".date("m")."' order by tour_start_date asc");
			if($latest_record_month > 0){
				$m=$latest_record_month;
			}
		}
		$date_array=array();
		if($tour_res['tour_schedule']=="R"){
			if($tour_res['tour_availability_days']=="Daily"){
				$date_array=get_all_dates_between($tour_res['tour_availability_start_date'],$tour_res['tour_availability_end_date']);
			}else{
				if(strstr($tour_res['tour_availability_days'],"Sun")){
					$sunday_date_array=getDateForSpecificDayBetweenDates($tour_res['tour_availability_start_date'],$tour_res['tour_availability_end_date'],"0");
					$date_array = array_merge($date_array, $sunday_date_array);
				}
				if(strstr($tour_res['tour_availability_days'],"Mon")){
					$monday_date_array=getDateForSpecificDayBetweenDates($tour_res['tour_availability_start_date'],$tour_res['tour_availability_end_date'],"1");
					$date_array = array_merge($date_array, $monday_date_array);
				}
				if(strstr($tour_res['tour_availability_days'],"Tues")){
					$tuesday_date_array=getDateForSpecificDayBetweenDates($tour_res['tour_availability_start_date'],$tour_res['tour_availability_end_date'],"2");
					$date_array = array_merge($date_array, $tuesday_date_array);
				}
				if(strstr($tour_res['tour_availability_days'],"Wed")){
					$wednesday_date_array=getDateForSpecificDayBetweenDates($tour_res['tour_availability_start_date'],$tour_res['tour_availability_end_date'],"3");
					$date_array = array_merge($date_array, $wednesday_date_array);
				}
				if(strstr($tour_res['tour_availability_days'],"Thurs")){
					$thurs_date_array=getDateForSpecificDayBetweenDates($tour_res['tour_availability_start_date'],$tour_res['tour_availability_end_date'],"4");
					$date_array = array_merge($date_array, $thurs_date_array);
				}
				if(strstr($tour_res['tour_availability_days'],"Fri")){
					$friday_date_array=getDateForSpecificDayBetweenDates($tour_res['tour_availability_start_date'],$tour_res['tour_availability_end_date'],"5");
					$date_array = array_merge($date_array, $friday_date_array);
				}
				if(strstr($tour_res['tour_availability_days'],"Sat")){
					$sat_date_array=getDateForSpecificDayBetweenDates($tour_res['tour_availability_start_date'],$tour_res['tour_availability_end_date'],"6");
					$date_array = array_merge($date_array, $sat_date_array);
				}
			}
		}
		if($ajax_id==""){
			$ajax_id="calview";
		}
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
		  $s.="<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\" style=\"border:1px solid #EDEDED;\" id=\"cal\" bgcolor='#85C006'>
			  <tr>
				<td align=\"center\"><table width=\"100%\" border=\"0\"   cellpadding=\"3\" cellspacing=\"1\"  >
				  <tr bgcolor='#739315' style='color:#fff;'>
					<td colspan=\"7\" align=\"left\"><table width=\"98%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\" >
					  <tr>
						<td width=\"3%\" align=\"left\" ><strong>".(($prevMonth == "") ? "&nbsp;" :"<a onclick=\"change_cal('".$prev[0]."','".$prev[1]."','".SITE_WS_PATH."','".$tour_id."','".$ajax_id."','".$edit_mode."');\" href=\"javascript: void(0);\"   title=\"Previous Month\" style='color:#fff;font-weight:bold;' ><<</a>") ."</strong></td>
						<td width=\"90%\" align=\"center\" style=\"font-size: 14px;\"><strong>$monthName - $year</strong>";
						if($edit_mode=='' && strstr($_SERVER['PHP_SELF'],AGENT_ADMIN_DIR)){
							$s.=" <a href='".SITE_WS_PATH."/".AGENT_ADMIN_DIR."/dmc/tour_dates.php?tour_id=$tour_id&id=".$tour_res['fk_supplier_id']."&town_month=$m&town_year=$y' class='newfancybox6'><b>(Edit)</b></a>";
						}
						$s.="</td>
						<td width=\"7%\" align=\"right\" style=\"color:#022b4f\"><strong>".(($nextMonth == "") ? "&nbsp;" :"<a onclick=\"change_cal('".$next[0]."','".$next[1]."','".SITE_WS_PATH."','".$tour_id."','".$ajax_id."','".$edit_mode."');\" href=\"javascript: void(0);\" title=\"Next Month\" style='color:#fff;font-weight:bold;' >>></a>")."</strong></td>
					  </tr>
					</table></td>
				  </tr>
				  <tr>
					<td align=\"center\" width=\"14.5%\" bgcolor=\"#b0e45b\"><strong class=\"text_12_white\">".$this->dayNames[($this->startDay)%7]."</strong></td>
					<td align=\"center\"  width=\"14.5%\" bgcolor=\"#b0e45b\"><strong class=\"text_12_white\">".$this->dayNames[($this->startDay+1)%7]."</strong></td>
					<td align=\"center\" width=\"14.5%\"  bgcolor=\"#b0e45b\"><strong class=\"text_12_white\">".$this->dayNames[($this->startDay+2)%7]."</strong></td>
					<td align=\"center\"  width=\"14.5%\" bgcolor=\"#b0e45b\"><strong class=\"text_12_white\">".$this->dayNames[($this->startDay+3)%7]."</strong></td>
					<td align=\"center\" width=\"14.5%\" bgcolor=\"#b0e45b\"><strong class=\"text_12_white\">".$this->dayNames[($this->startDay+4)%7]."</strong></td>
					<td align=\"center\" width=\"14.5%\" bgcolor=\"#b0e45b\"><strong class=\"text_12_white\">".$this->dayNames[($this->startDay+5)%7]."</strong></td>
					<td align=\"center\" width=\"14.5%\" bgcolor=\"#b0e45b\"><strong class=\"text_12_white\">".$this->dayNames[($this->startDay+6)%7]."</strong></td>
				  </tr>";
	$d = $this->startDay + 1 - $first;
			while ($d > 1)
			{
				$d -= 7;
			}
			while ($d <= $daysInMonth){
				$s .= "<tr>\n";
				for ($i = 0; $i < 7; $i++)
				{
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
						$is_exist=db_scalar("select count(*) from mv_tour_dates where fk_tour_id='$id' and  tour_start_date='$date_app'");
						//if((strtotime($date_app) < strtotime($tour_res['tour_availability_start_date'])) || (strtotime($date_app) > strtotime($tour_res['tour_availability_end_date']))){
							$flag=false;
							if((strtotime($date_app) < strtotime($tour_res['tour_availability_start_date']))){
								$flag=true;
							$s .= "<td   align=\"center\" valign=\"middle\" bgcolor=\"#739315\"><font color='#FFF'><strong>$d</strong></font>";
						}elseif($is_exist>0 || in_array($date_app,$date_array)){
					   		$s .= "<td   align=\"center\" valign=\"middle\" bgcolor=\"#FFFFFF\"><strong>";
							if($edit_mode!=""){
								$s .= "<a href=\"?tour_id=$id&edit_mode=$edit_mode&date_to_delete=$date_app&town_month=$m&town_year=$y\"><font color='#FFF'>".$d."</font></a>";
							}else{
								$s.="<font color='#000'>".$d."</font>";
							}
							$s .= "</strong>";
					   }
					   else{
						   if($flag){
							    $s .= "<td   align=\"center\" valign=\"middle\" bgcolor=\"#FFFFFF\" ><strong>";
								if($edit_mode!=""){
									$s .= "<a href=\"?tour_id=$id&edit_mode=$edit_mode&date_to_add=$date_app&town_month=$m&town_year=$y\">".$d."</a>";
								}else{
									$s.="<font color='#000'>".$d."</font>";
								}
								$s .= "</strong>";
						   }else{
								$s .= "<td   align=\"center\" valign=\"middle\" bgcolor=\"#739315\" ><strong>";
								if($edit_mode!=""){
									$s .= "<a href=\"?tour_id=$id&edit_mode=$edit_mode&date_to_add=$date_app&town_month=$m&town_year=$y\">".$d."</a>";
								}else{
									$s.="<font color='#fff'>".$d."</font>";
								}
								$s .= "</strong>";
							   }
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
						$s .= "<td  align=\"left\"  bgcolor=\"#739315\" class=\"$class_name\" >&nbsp;";
					}
					$s .= "</td>\n";
					$d++;
				}
				$s .= "</tr>\n";
			}
$s.="</table></td>
  </tr>
</table>";
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
   var $dayNames =array("Sun", "Mon", "Tues", "Wed", "Thurs", "Fri", "Sat");
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