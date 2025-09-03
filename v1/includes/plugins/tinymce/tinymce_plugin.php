<?php
/**
 * TinyMCE Plugin for IVS Portal
 * Replaces legacy CKEditor with modern TinyMCE editor
 */
// Include required files
require_once(dirname(__FILE__) . '/../../midas.inc.php');
function get_tinymce_editor($control_name, $value = '', $width = '100%', $height = '500')
{
    // Generate unique editor ID
    $editor_id = 'tinymce_' . $control_name . '_' . uniqid();
    // Parse height to numeric value
    $height_numeric = is_numeric($height) ? $height : 500;
    // Output the textarea
    echo '<textarea id="' . $editor_id . '" name="' . $control_name . '">' . htmlspecialchars($value) . '</textarea>';
    // Include external CSS file
    include_tinymce_css();
    // Set up JavaScript variables and initialize editor
    $upload_url = SITE_SUB_PATH . '/includes/plugins/tinymce/upload.php';
    $js_path = SITE_SUB_PATH . '/includes/plugins/tinymce/tinymce_custom.js';
    echo '<script>';
    echo 'var tinymceUploadUrl = "' . $upload_url . '";';
    echo 'document.addEventListener("DOMContentLoaded", function() {';
    echo '  if (typeof initializeTinyMCE === "function") {';
    echo '    initializeTinyMCE("#' . $editor_id . '", {';
    echo '      height: ' . $height_numeric . ',';
    echo '      width: "' . $width . '"';
    echo '    });';
    echo '  }';
    echo '});';
    echo '</script>';
    // Include the external JavaScript files
    echo '<script src="' . $js_path . '"></script>';
    echo '<script src="' . SITE_SUB_PATH . '/includes/plugins/tinymce/ai_modal.js"></script>';
}
function include_tinymce_cdn()
{
    static $included = false;
    if (!$included) {
        $tinymce_path = SITE_SUB_PATH . '/includes/plugins/tinymce/js/tinymce/tinymce.min.js';
        echo '<script src="' . $tinymce_path . '"></script>';
        $included = true;
    }
}
function include_tinymce_css()
{
    static $included = false;
    if (!$included) {
        $css_path = SITE_SUB_PATH . '/includes/plugins/tinymce/tinymce_custom.css';
        echo '<link rel="stylesheet" type="text/css" href="' . $css_path . '">';
        $included = true;
    }
}
function get_tinymce_config($control_name, $config = array())
{
    $default_config = array(
        'height' => 500,
        'width' => '100%',
        'plugins' => [
            "advlist", "autolink", "lists", "link", "image", "charmap", "preview",
            "anchor", "searchreplace", "visualblocks", "code", "fullscreen",
            "insertdatetime", "media", "table", "help", "wordcount"
        ],
        'toolbar' => "undo redo | blocks | bold italic forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | customimage custommultiimage customvideo | link media table | code fullscreen help",
        'branding' => false,
        'promotion' => false
    );
    return array_merge($default_config, $config);
}
?>