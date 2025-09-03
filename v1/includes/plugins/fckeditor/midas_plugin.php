<?php
function get_fck_editor($control_name, $value = '', $width = '100%', $height = '500')
{
	require_once(dirname(__FILE__) . "/ckeditor.php");
	$replace_text = '"' . SITE_WS_PATH . '/uploaded_files/fck';
	$value = str_replace("\"/uploaded_files/fck", $replace_text, (string)$value);
	$sBasePath = plugin_sub_path('fckeditor') . '/';
	$oFCKeditor = new CKEditor($control_name);
	$oFCKeditor->basePath = $sBasePath;
	$oFCKeditor->config['enterMode'] = 2;
	$oFCKeditor->config['shiftEnterMode'] = 2;
	$oFCKeditor->config['height'] = $height;
	$oFCKeditor->config['filebrowserBrowseUrl']	= $sBasePath . 'kcfinder/browse.php?type=files';
	$oFCKeditor->config['filebrowserImageBrowseUrl']	= $sBasePath . 'kcfinder/browse.php?type=images';
	$oFCKeditor->config['filebrowserFlashBrowseUrl']	= $sBasePath . 'kcfinder/browse.php?type=flash';
	$oFCKeditor->config['filebrowserUploadUrl']	= $sBasePath . 'kcfinder/upload.php?type=files';
	$oFCKeditor->config['filebrowserImageUploadUrl']	= $sBasePath . 'kcfinder/upload.php?type=images';
	$oFCKeditor->config['filebrowserFlashUploadUrl']	= $sBasePath . 'kcfinder/upload.php?type=flash';
	$oFCKeditor->editor($control_name, $value);
}