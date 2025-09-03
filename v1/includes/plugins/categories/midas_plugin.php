<?php
$plugin_version = '0.1';
class midas_categories {
	function midas_categories($category_type) {
	}
	function get_category_path($category_type, $category_id) {
		global $arr_get_category_path;
		$sql = "select * from tbl_categories where category_type='$category_type' and category_id='$category_id' ";
		$result = db_query($sql);
		if ($line = mysql_fetch_array($result)) {
			if($line['category_parent_id']!=0) {
				$path .= midas_categories::get_category_path($category_type, $line['category_parent_id']);
			}
		}
		$arr_get_category_path[] = $category_id;
		return $arr_get_category_path;
	}
	/*
	function recursive_dropdown($category_type, $name, $sel_value, $skip='', $extra='', $choose_one='', $parent_id=0, $level=0) {
		return recursive_dropdown( 'category_id', 'category_name', 'category_parent_id', 'category_order', 'tbl_categories', "category_type='$category_type'",  $name , $sel_value, $skip, $extra, $choose_one, $parent_id, $level);
	}
	*/
	function checkboxes_2level($category_type, $checkname, $checksel, $category_parent_id=0, $cols=4, $missit='', $style	= '', $tableattr = '') {
		$sql = "select category_id, category_name from tbl_categories where category_parent_id='$category_parent_id' order by category_order";
		$arr_tmp = sql_to_assoc_array($sql);
		$str = '';
		foreach($arr_tmp as $id=>$name) {
			$str .= "<br><b>".$name."</b><br>";
			$str .= midas_categories::checkboxes($category_type, $checkname, $checksel, $id, $cols=4, $missit='', $style	= '', $tableattr = '');
		}
		return $str;
	}
	function checkboxes($category_type, $checkname, $checksel, $category_parent_id=0, $cols=4, $missit='', $style	= '', $tableattr = '') {
		$sql = "select category_id, category_name from tbl_categories where category_parent_id='$category_parent_id' order by category_order";
		$arr_tmp = sql_to_assoc_array($sql);
		return make_checkboxes($arr_tmp, $checkname, $checksel, $cols,	$missit, $style, $tableattr);
	}
	function item_category_ids($category_type, $c2i_item_id) {
		$sql = "select c2i_category_id from tbl_categories_to_items where c2i_item_id='$c2i_item_id' and c2i_type='$category_type'";
		return sql_to_index_array($sql);
	}
	function item_category_names($category_type, $c2i_item_id) {
		$sql = "select category_name from tbl_categories_to_items
		inner join tbl_categories on c2i_category_id = category_id
		where c2i_item_id='$c2i_item_id' and c2i_type='$category_type' order by category_name";
		return sql_to_index_array($sql);
	}
	function item_categories($category_type, $c2i_item_id) {
		$sql = "select category_id, category_name from tbl_categories_to_items
		inner join tbl_categories on c2i_category_id = category_id
		where c2i_item_id='$c2i_item_id' and c2i_type='$category_type'  order by category_name";
		return sql_to_assoc_array($sql);
	}
	function insert($category_ids, $category_type, $c2i_item_id) {
		if(is_array($category_ids) && count($category_ids)>0) {
			$sql = "delete from tbl_categories_to_items where c2i_type = '$category_type' and c2i_item_id='$c2i_item_id'";
			db_query($sql);
			foreach($category_ids as $category_id ) {
				$sql = "insert into tbl_categories_to_items set c2i_item_id='$c2i_item_id', c2i_category_id='$category_id', c2i_type = '$category_type' ";
				db_query($sql);
			}
		}
	}
	function recursive_dropdown($category_type, $name, $item_id, $skip='', $extra='', $choose_one='', $parent_id=0, $level=0) {
		return recursive_dropdown( 'category_id', 'category_name', 'category_parent_id', 'category_order', 'tbl_categories', "category_type='$category_type'",  $name , midas_categories::item_category_ids($category_type, $item_id), $skip, $extra, $choose_one, $parent_id, $level);
	}
	function dropdown($category_type, $name, $item_id, $skip='', $extra='', $choose_one='', $parent_id=0, $level=0) {
		$sql = "select category_id, category_name from tbl_categories where category_parent_id='$parent_id' and category_type='$category_type' order by category_order";
		return sql_dropdown($sql, $name, midas_categories::item_category_ids($category_type, $item_id), $extra, $choose_one);
	}
}
?>