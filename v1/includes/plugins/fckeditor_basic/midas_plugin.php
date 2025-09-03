<?php
function get_fck_editor_frontend($control_name, $value='', $width='100%', $height='500')
{
	require_once(dirname(__FILE__)."/ckeditor.php") ;
	$sBasePath = plugin_sub_path('fckeditor_basic').'/';
	$oFCKeditor = new CKEditor($control_name) ;
	$oFCKeditor->basePath = $sBasePath;
	$oFCKeditor->config['enterMode'] = 2;
	$oFCKeditor->config['shiftEnterMode'] = 2;
	$oFCKeditor->Value = $value;
	$oFCKeditor->config['height']= $height;
	$oFCKeditor->editor($control_name, $value) ;
}
if (!function_exists('get_fck_editor_newsletter')) {
	function get_fck_editor_newsletter($control_name, $value='', $width='100%', $height='500')
	{
		require_once(dirname(__FILE__)."/ckeditor.php") ;
		$sBasePath = plugin_sub_path('fckeditor_basic').'/';
		$oFCKeditor = new CKEditor($control_name) ;
		$oFCKeditor->basePath = $sBasePath;
		$oFCKeditor->config['enterMode'] = 2;
		$oFCKeditor->config['shiftEnterMode'] = 2;
		$oFCKeditor->Value = $value;
		$oFCKeditor->config['height']= $height;
		$oFCKeditor->editor($control_name, $value) ;
	}
}
?>