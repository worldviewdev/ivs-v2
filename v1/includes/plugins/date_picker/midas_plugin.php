<?php
define('DATE_PICKER_WS_PATH', SITE_SUB_PATH . '/' . PLUGINS_DIR . '/date_picker');
function date_picker_includes()
{
    if (!defined('JSCAL_INCLUDED')) {
        define('JSCAL_INCLUDED', true);
        ob_start();
        include(dirname(__FILE__) . '/date_pick_files.inc.php');
        $date_picker = ob_get_contents();
        ob_end_clean();
    } else {
        $date_picker = ''; // Fix: Ensure $date_picker is always defined
    }
    return $date_picker ;
}
function get_date_picker($jscal_input_name, $jscal_def_date = '', $validation = 'date|yyyy/mm/dd|-', $validation_msg = 'Date should be in yyyy-mm-dd format')
{
    $date_picker = date_picker_includes();
    ob_start();
    include(dirname(__FILE__) . '/date_pick.inc.php');
    $date_picker .= ob_get_contents();
    ob_end_clean();
    return $date_picker;
}
function get_datetime_picker($jscal_input_name, $jscal_def_date = '', $validation = 'date|yyyy/mm/dd|-', $validation_msg = 'Date should be in yyyy-mm-dd format')
{
    $date_picker = date_picker_includes();
    ob_start();
    include(dirname(__FILE__) . '/datetime_pick.inc.php');
    $date_picker .= ob_get_contents();
    ob_end_clean();
    return $date_picker;
}