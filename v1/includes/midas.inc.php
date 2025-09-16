<?php

ob_start();

// Best practice penggunaan session di PHP adalah:
// 1. Memastikan session_start() hanya dipanggil jika session belum dimulai.
// 2. Mengatur session.gc_maxlifetime sebelum session_start() jika ingin mengubah lifetime.
// 3. Mengatur session.cookie_lifetime sebelum session_start() jika ingin mengubah lifetime cookie.
// 4. Untuk production, jangan set lifetime terlalu pendek (10 detik hanya untuk testing).

// Untuk testing, session di-set hanya 10 detik supaya mudah cek session berjalan atau tidak
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', 3600); // 10 detik
    ini_set('session.cookie_lifetime', 3600); // 10 detik
    session_start();
}
//ini_set("memory_limit", "128M");
ini_set('max_execution_time', 900); //900 seconds = 15 minutes
date_default_timezone_set('Canada/Pacific');
//error_reporting(E_ERROR);
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
if ($_SERVER['HTTP_HOST'] == "midas" || $_SERVER['HTTP_HOST'] == "localhost" || $_SERVER['HTTP_HOST'] == "adminv2.local") {
    define('LOCAL_MODE', true);
    define('SITE_WS_PATH', 'http://localhost');
} else {
    define('LOCAL_MODE', false);
    define('SITE_WS_PATH', 'https://' . $_SERVER['HTTP_HOST']);
}
$tmp = dirname(__FILE__);
$tmp = str_replace('\\', '/', $tmp);
$tmp = substr($tmp, 0, strrpos($tmp, '/'));
define('SITE_FS_PATH', $tmp);
define('PASSWORD_KEY', "2y$10$@12)927^%");
define('SESSION_TIMEOUT', 3600);
//For temporary //
$tmp2 = dirname(dirname(__FILE__));
$tmp2 = str_replace('\\', '/', $tmp2);
$tmp2 = substr($tmp2, 0, strrpos($tmp2, '/'));
define('SITE_TMP_FS_PATH', $tmp2 . '/public');
define('SITE_IVS_PUBLIC', 'https://www.italyvacationspecialists.com');
//echo SITE_TMP_FS_PATH; exit;
define('SITE_NEWFS_PATH', dirname(SITE_FS_PATH));
define('UP_FILES_NEWFS_PATH', SITE_NEWFS_PATH . '/public/uploaded_files');
if (LOCAL_MODE) {
    define('TOUR_DIR', 'tour_includes');
    define('MAIN_TOUR_UPLOAD_DIR', SITE_FS_PATH . '/uploaded_files');
    define('MAIN_TOUR_UPLOAD_PATH', SITE_WS_PATH . '/uploaded_files');
} else {
    define('TOUR_DIR', 'tour_includes');
    define('MAIN_TOUR_UPLOAD_DIR', SITE_FS_PATH . '/uploaded_files');
    define('MAIN_TOUR_UPLOAD_PATH', SITE_WS_PATH . '/uploaded_files');
    //define('MAIN_TOUR_UPLOAD_DIR', SITE_TMP_FS_PATH.'/uploaded_files');
    //define('MAIN_TOUR_UPLOAD_PATH', SITE_TMP_WS_PATH.'/uploaded_files');
    //ini_set('session.cookie_domain','.medvisits.com');
}
define('MAIN_TOUR_DIR', SITE_FS_PATH . '/' . TOUR_DIR);
require(MAIN_TOUR_DIR . "/functions.php");
require_once(SITE_FS_PATH . "/includes/config.inc.php");
require_once(SITE_FS_PATH . "/includes/funcs_lib.inc.php");
require_once(SITE_FS_PATH . "/includes/funcs_cur.inc.php");
require_once(SITE_FS_PATH . "/includes/helpers.php");
if (!strstr($_SERVER['REQUEST_URI'], "fckeditor")) {
    require(SITE_FS_PATH . "/includes/arrays.inc.php");
    require(SITE_FS_PATH . '/includes/phpmailer/class.phpmailer.php');
}
$CURRENT_SUB_PATH = str_replace(SITE_FS_PATH, '/', dirname($_SERVER['PHP_SELF']));
$CURRENT_SUB_PATH = str_replace(SITE_FS_PATH, '/', dirname($_SERVER['PHP_SELF']));
$CURRENT_PLUGIN = '';
if (defined('PLUGINS_DIR')) {
    $plugin_pos = strpos($_SERVER['PHP_SELF'], '/' . PLUGINS_DIR . '/');
    if ($plugin_pos !== false) {
        $CURRENT_PLUGIN = substr($_SERVER['PHP_SELF'], $plugin_pos + strlen(PLUGINS_DIR) + 2);
        $slash_pos = strpos($CURRENT_PLUGIN, '/');
        if ($slash_pos !== false) {
            $CURRENT_PLUGIN = substr($CURRENT_PLUGIN, 0, $slash_pos);
        }
    }
    define('CURRENT_PLUGIN', $CURRENT_PLUGIN);
}
if (!defined('SCRIPT_START_TIME')) {
    function getmicrotime()
    {
        return microtime(true);
    }
    define('SCRIPT_START_TIME', getmicrotime());
}
$_GET = ms_trim($_GET);
$_POST = ms_trim($_POST);
$_COOKIE = ms_trim($_COOKIE);
// Using @extract is discouraged for security reasons but keeping for backward compatibility
@extract($_GET);
@extract($_POST);
// Magic quotes runtime was removed in PHP 7.0.0, so this code is no longer needed
// DI NONAKTIFKAN SEMENTARA
if (defined('PLUGINS_DIR')) {
    if ($handle = opendir(SITE_FS_PATH . '/' . PLUGINS_DIR)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                $curr_dir = SITE_FS_PATH . '/' . PLUGINS_DIR . '/' . $file;
                if (is_dir($curr_dir)) {
                    if (file_exists($curr_dir . '/midas_plugin.php')) {
                        require_once($curr_dir . '/midas_plugin.php');
                    }
                }
            }
        }
        closedir($handle);
    }
}
$BASENAME = basename($_SERVER['PHP_SELF']);
$PHP_SELF = $_SERVER['PHP_SELF'];
if (defined('AGENT_ADMIN_DIR')) {
    $agent_pos = strpos($PHP_SELF, '/');
    if ($agent_pos !== false && !strstr($_SERVER['PHP_SELF'], "/voucher/")) {
        if (function_exists('protect_agent_page')) {
            protect_agent_page();
        }
    }
}
if (empty($_SESSION['user_currency'])) {
    $_SESSION['user_currency'] = "EURO";
}
if (empty($_SESSION['commission_method'])) {
    define('PRICE_COMMISSION_METHOD', 'CLIENT');
} else {
    define('PRICE_COMMISSION_METHOD', $_SESSION['commission_method']);
}
define('CUSTOM_TRANSFER_EMAIL', 'travel@medvisits.com');
define('TRANSFER_COUNTRY_NAME', 'Italy');
define('TRANSFER_COUNTRY_CODE', '105');
if (!strstr($_SERVER['REQUEST_URI'], "fckeditor")) {
    $daylight_setting = date('I', time()) == '1' ? '-07:00' : '-08:00';
    db_query("SET time_zone = '$daylight_setting';");
}
//phpinfo();