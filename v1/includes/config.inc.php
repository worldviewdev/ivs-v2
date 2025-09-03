<?php
include_once('midas.inc.php');
if (!defined('LOCAL_MODE')) {
    die('<span style="font-family: tahoma, arial; font-size: 11px">config file cannot be included directly');
}
if (LOCAL_MODE) {
    $ARR_CFGS["db_host"] = 'localhost';
    $ARR_CFGS["db_name"] = 'brazilgr_ivs';
    $ARR_CFGS["db_user"] = 'root';
    $ARR_CFGS["db_pass"] = '';
    if (!defined('SITE_SUB_PATH')) define('SITE_SUB_PATH', '/ivsportal');
    if (!defined('ADMIN_EMAIL')) define('ADMIN_EMAIL', 'test2@localhost.com');
    if (!defined('PAYMENT_EMAIL')) define('PAYMENT_EMAIL', 'test2@localhost.com');
} else {
    $ARR_CFGS["db_host"] = 'localhost';
    $ARR_CFGS["db_name"] = 'ivs_28ag2025';
    $ARR_CFGS["db_user"] = 'ivs28ag2025';
    $ARR_CFGS["db_pass"] = 'e47b7a6948f4e9fef3685f6dd12eb';
    if (!defined('SITE_SUB_PATH')) define('SITE_SUB_PATH', '/ivsportal');
    if (!defined('ADMIN_EMAIL')) define('ADMIN_EMAIL', 'team@italyvacationspecialists.com');
    if (!defined('PAYMENT_EMAIL')) define('PAYMENT_EMAIL', 'accounting@italyvacationspecialists.com');
}
// Pastikan $_SERVER['HTTPS'] selalu ada
$is_https = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on');
if ($is_https) {
    if (!defined('IN_SSL')) define('IN_SSL', true);
    if (!defined('SITE_WS_PATH')) define('SITE_WS_PATH', 'https://' . $_SERVER['HTTP_HOST'] . SITE_SUB_PATH);
    if (!defined('SITE_TMP_WS_PATH')) define('SITE_TMP_WS_PATH', 'https://' . $_SERVER['HTTP_HOST']);
} else {
    if (!defined('IN_SSL')) define('IN_SSL', false);
    if (!defined('SITE_WS_PATH')) define('SITE_WS_PATH', 'http://' . $_SERVER['HTTP_HOST'] . SITE_SUB_PATH);
    if (!defined('SITE_TMP_WS_PATH')) define('SITE_TMP_WS_PATH', 'http://' . $_SERVER['HTTP_HOST']);
}
if (!LOCAL_MODE && defined('SITE_SUB_PATH') && SITE_SUB_PATH != '') {
    if (!defined('SITE_PR_PATH')) define('SITE_PR_PATH', 'https://' . $_SERVER['HTTP_HOST']);
} else {
    if (!defined('SITE_PR_PATH')) define('SITE_PR_PATH', SITE_WS_PATH);
}
if (!defined('THUMB_CACHE_DIR')) define('THUMB_CACHE_DIR', 'thumb_cache');
if (!defined('PLUGINS_DIR')) define('PLUGINS_DIR', 'includes/plugins');
if (!defined('UP_FILES_FS_PATH')) define('UP_FILES_FS_PATH', SITE_FS_PATH . '/uploaded_files');
if (!defined('UP_FILES_WS_PATH')) define('UP_FILES_WS_PATH', SITE_WS_PATH . '/uploaded_files');
//define('UP_FILES_FS_PATH', SITE_TMP_FS_PATH . '/uploaded_files');
//define('UP_FILES_WS_PATH', SITE_TMP_WS_PATH . '/uploaded_files');
if (!defined('DEFAULT_START_YEAR')) define('DEFAULT_START_YEAR', 2010);
if (!defined('DEFAULT_END_YEAR')) define('DEFAULT_END_YEAR', date('Y') + 10);
if (!defined('SITE_NAME')) define('SITE_NAME', 'Italy Vacation Specialists');
if (!defined('TEST_MODE')) define('TEST_MODE', false);
if (!defined('DEF_PAGE_SIZE')) define('DEF_PAGE_SIZE', 25);
if (!defined('AGENT_ADMIN_DIR')) define('AGENT_ADMIN_DIR', 'v1');
if (!defined('SITE_URL')) define('SITE_URL', SITE_WS_PATH . '/' . AGENT_ADMIN_DIR);
if (!defined('CKEDITOR_FS_PATH')) define('CKEDITOR_FS_PATH', SITE_FS_PATH . '/' . PLUGINS_DIR . '/fckeditor');
if (!defined('CKEDITOR_WS_PATH')) define('CKEDITOR_WS_PATH', SITE_WS_PATH . '/' . PLUGINS_DIR . '/fckeditor');
if (!defined('CKEDITOR_PATH')) define("CKEDITOR_PATH", SITE_FS_PATH . "/includes/plugins/fckeditor/");
if (!defined('GOOGLE_PUBLIC_KEY')) define("GOOGLE_PUBLIC_KEY", "6LdrSr4pAAAAALLOdc4-OkWUqtzDMnDMOhntny9J");
if (!defined('GOOGLE_PRIVATE_KEY')) define("GOOGLE_PRIVATE_KEY", "6LdrSr4pAAAAACU2f6pchxLF0qOEGGm_07i3lzfG");