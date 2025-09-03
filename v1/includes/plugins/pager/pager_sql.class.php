<?php
class midas_pager_sql extends midas_pager
{
    public function __construct($sql, $records_per_page, $start = '0', $theme = '')
    {
        $result = db_query($sql);
        $total_records = mysqli_num_rows($result);
        parent::__construct($total_records, $records_per_page, $start, $theme);
    }
    public function trim_from_end($sql, $token)
    {
        $pos = strpos($sql, $token);
        if ($pos !== false) {
            $sql = substr($sql, 0, $pos);
        }
        return $sql;
    }
}