<?php
class midas_pager
{
    public $total_records = 0;
    public $records_per_page = 25;
    public $theme = '';
    public $tpl_pager = 'pager.inc.php';
    public $tpl_displaying = 'displaying.inc.php';
    public $css_file = 'pager.css';
    public $qry_str = '';
    public $needs_paging = true;
    // Add these property declarations:
    public $css;
    public $start;
    public $page_name;
    public $total_pages;
    public $current_page;
    public $first_page;
    public $previous_page;
    public $next_page;
    public $last_page;
    public function static_pager($total_records, $records_per_page, $start = '0', $theme = '')
    {
        $midas_pager = new midas_pager($total_records, $records_per_page, $start, $theme);
        $midas_pager->show_pager();
        return $midas_pager;
    }
    public function static_displaying($total_records, $records_per_page, $start = '0', $theme = '')
    {
        $midas_pager = new midas_pager($total_records, $records_per_page, $start, $theme);
        $midas_pager->show_displaying();
        return $midas_pager;
    }
    public function __construct($total_records, $records_per_page, $start = '0', $theme = '')
    {
        if ($theme == '') {
            $theme = 'default';
        }
        $this->total_records = intval($total_records);
        $this->records_per_page = intval($records_per_page);
        $this->theme = $theme;
        if ($records_per_page >= $total_records) {
            $this->needs_paging = false;
            return '';
        } else {
            $this->needs_paging = true;
        }
        $this->css = fs_to_absolute(dirname(__FILE__)) . '/themes/' . $this->theme . '/' . $this->css_file;
        $this->start = $start;
        $this->page_name = $_SERVER['PHP_SELF'];
        //$qry_str = $_SERVER['argv'][0];
        $tmp = $_GET;
        unset($tmp['start']);
        $this->qry_str = qry_str($tmp);
        $this->total_pages = ceil($this->total_records / $this->records_per_page);
        $this->current_page = floor($this->start / $this->records_per_page) + 1;
        $this->first_page = $this->page_name . $this->qry_str . "&start=0";
        $this->previous_page = $this->page_name . $this->qry_str . "&start=" . ($this->start - $this->records_per_page);
        $this->next_page = $this->page_name . $this->qry_str . "&start=" . ($this->start + $this->records_per_page);
        $mod = $this->total_records % $this->records_per_page == 0 ? $this->records_per_page : $this->total_records % $this->records_per_page;
        $this->last_page = $this->page_name . $this->qry_str . "&start=" . ($this->total_records - $mod);
    }
    public function show_pager()
    {
        include(dirname(__FILE__) . '/themes/' . $this->theme . '/' . $this->tpl_pager);
    }
    public function show_displaying()
    {
        include(dirname(__FILE__) . '/themes/' . $this->theme . '/' . $this->tpl_displaying);
    }
    public function has_previous()
    {
        return $this->start != 0 ? true : false;
    }
    public function has_next()
    {
        return $this->start + $this->records_per_page < $this->total_records ? true : false;
    }
    /*
        function is_current_page($page_num) {
             return ($this->records_per_page*($page_num))==$this->start):true:false;
        }
        function first_page() {
            return $this->page_name.$this->qry_str."&start=0";
        }
        function previous_page() {
            return $this->page_name.$this->qry_str."&start=".($this->start - $this->records_per_page);
        }
        function next_page() {
            return $this->page_name.$this->qry_str."&start=".($this->start + $this->records_per_page);
        }
        function last_page() {
            $mod = $reccnt % $pagesize;
            if ($mod == 0) {
                $mod = $pagesize;
            }
            return $this->page_name.$this->qry_str."&start=".($this->total_records-$mod);
        }
    */
    public function get_page_link($page_num)
    {
        return $this->page_name . $this->qry_str . "&start=" . $this->records_per_page * ($page_num - 1);
    }
    public function get_numbered_links()
    {
        $j = $this->start / $this->records_per_page - 5;
        if ($j < 0) {
            $j = 0;
        }
        $k = $j + 10;
        $num_pages = $this->total_pages;
        if ($k > $num_pages) {
            $k = $num_pages;
        }
        $j = floor($j);
        $arr_numbered_links = array();
        for ($i = $j + 1;$i <= $k;$i++) {
            $arr_numbered_links[$i] = $this->get_page_link($i);
        }
        return $arr_numbered_links;
    }
}