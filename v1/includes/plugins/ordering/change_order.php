<?php
require_once("../../midas.inc.php");
//echo("<br>$source, $target, $table_name, $column_order, $column_id, $where_clause");
$source = (int)$_REQUEST['source'];
$target = (int)$_REQUEST['target']; // If target is not set, it will be the same as source
$table_name = $_REQUEST['table_name'];
$column_order = $_REQUEST['column_order'];
$column_id = $_REQUEST['column_id'];
$where_clause = $_REQUEST['where_clause'] ?? '';
$return_path = $_REQUEST['return_path'] ?? $_SERVER['REQUEST_URI'];
midas_ordering::change_order($source, $target, $table_name, $column_order, $column_id, stripslashes($where_clause ?? ''));
header("location: $return_path");