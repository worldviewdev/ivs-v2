<?php
require_once("../../midas.inc.php");
// initialize variables
$path = isset($_GET['path']) ? $_GET['path'] : '';
$cache_file = isset($_GET['cache_file']) ? $_GET['cache_file'] : '';
$width = isset($_GET['width']) ? intval($_GET['width']) : 0;
$height = isset($_GET['height']) ? intval($_GET['height']) : 0;
$ratio_type = isset($_GET['ratio_type']) ? $_GET['ratio_type'] : null;
$method = isset($_GET['method']) ? $_GET['method'] : null;
$path = stripslashes($path);
$cache_file = stripslashes($cache_file);
$width = intval($width);
$height = intval($height);
midas_thumb::make_thumb(absolute_to_fs($path), SITE_FS_PATH."/".THUMB_CACHE_DIR."/".$cache_file, $width, $height, $ratio_type, $method);
header("location: ".fs_to_absolute(SITE_FS_PATH."/".THUMB_CACHE_DIR."/".$cache_file));