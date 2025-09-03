<?php
/**
 * TinyMCE Image Upload Handler for IVS Portal
 */
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);
// Set content type to JSON
header('Content-Type: application/json');
// Simple test endpoint
if (isset($_GET['test'])) {
    echo json_encode(['success' => true, 'message' => 'Upload endpoint is working']);
    exit;
}
// Debug POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("TinyMCE Upload: POST request received");
    error_log("TinyMCE Upload: Content-Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'not set'));
    error_log("TinyMCE Upload: Content-Length: " . ($_SERVER['CONTENT_LENGTH'] ?? 'not set'));
    error_log("TinyMCE Upload: FILES array: " . print_r($_FILES, true));
    if (empty($_FILES)) {
        error_log("TinyMCE Upload: No files received in POST request");
        echo json_encode(['error' => 'No files received', 'success' => false, 'debug' => 'POST request but no FILES']);
        exit;
    }
}
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Include necessary files
try {
    require_once(dirname(__FILE__) . '/../../funcs_cur.inc.php');
} catch (Exception $e) {
    error_log("TinyMCE Upload: Failed to include funcs_cur.inc.php - " . $e->getMessage());
    echo json_encode(['error' => 'Configuration error', 'success' => false]);
    exit;
}
// Thumbnail generation function
function generateThumbnail($image_path) {
    $thumb_dir = dirname($image_path) . '/../.thumbs/' . basename(dirname($image_path)) . '/';
    if (!file_exists($thumb_dir)) {
        mkdir($thumb_dir, 0755, true);
    }
    $thumb_path = $thumb_dir . basename($image_path);
    if (file_exists($thumb_path) && filemtime($thumb_path) >= filemtime($image_path)) {
        return true;
    }
    $image_info = getimagesize($image_path);
    if (!$image_info) {
        return false;
    }
    $width = $image_info[0];
    $height = $image_info[1];
    $mime_type = $image_info['mime'];
    // Create image resource based on type
    switch ($mime_type) {
        case 'image/jpeg':
            $source = imagecreatefromjpeg($image_path);
            break;
        case 'image/png':
            $source = imagecreatefrompng($image_path);
            break;
        case 'image/gif':
            $source = imagecreatefromgif($image_path);
            break;
        case 'image/webp':
            $source = imagecreatefromwebp($image_path);
            break;
        default:
            return false;
    }
    if (!$source) {
        return false;
    }
    // Calculate thumbnail dimensions (150x150 max, maintain aspect ratio)
    $thumb_width = 150;
    $thumb_height = 150;
    if ($width > $height) {
        $thumb_height = intval(($height * $thumb_width) / $width);
    } else {
        $thumb_width = intval(($width * $thumb_height) / $height);
    }
    // Create thumbnail
    $thumbnail = imagecreatetruecolor($thumb_width, $thumb_height);
    // Preserve transparency for PNG and GIF
    if ($mime_type == 'image/png' || $mime_type == 'image/gif') {
        imagealphablending($thumbnail, false);
        imagesavealpha($thumbnail, true);
        $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
        imagefilledrectangle($thumbnail, 0, 0, $thumb_width, $thumb_height, $transparent);
    }
    imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, $thumb_width, $thumb_height, $width, $height);
    // Save thumbnail
    $result = false;
    switch ($mime_type) {
        case 'image/jpeg':
            $result = imagejpeg($thumbnail, $thumb_path, 90);
            break;
        case 'image/png':
            $result = imagepng($thumbnail, $thumb_path, 9);
            break;
        case 'image/gif':
            $result = imagegif($thumbnail, $thumb_path);
            break;
        case 'image/webp':
            $result = imagewebp($thumbnail, $thumb_path, 90);
            break;
    }
    imagedestroy($source);
    imagedestroy($thumbnail);
    return $result;
}
// Log the request for debugging
error_log("TinyMCE Upload: Request received");
error_log("TinyMCE Upload: REQUEST_METHOD - " . $_SERVER['REQUEST_METHOD']);
error_log("TinyMCE Upload: CONTENT_TYPE - " . ($_SERVER['CONTENT_TYPE'] ?? 'not set'));
error_log("TinyMCE Upload: FILES data - " . print_r($_FILES, true));
error_log("TinyMCE Upload: POST data - " . print_r($_POST, true));
// Check if user is logged in (admin or agent) - temporarily disabled for testing
// if (!isset($_SESSION['admin_id']) && !isset($_SESSION['agent_id'])) {
//     error_log("TinyMCE Upload: Authentication failed - no admin_id or agent_id in session");
//     error_log("TinyMCE Upload: Session data: " . print_r($_SESSION, true));
//     http_response_code(403);
//     echo json_encode(['error' => 'Unauthorized', 'success' => false]);
//     exit;
// }
// Check if file was uploaded
if (!isset($_FILES['file'])) {
    error_log("TinyMCE Upload: No file in _FILES array");
    http_response_code(400);
    echo json_encode(['error' => 'No file uploaded', 'success' => false]);
    exit;
}
$file = $_FILES['file'];
// Check for upload errors
if ($file['error'] !== UPLOAD_ERR_OK) {
    error_log("TinyMCE Upload: Upload error code - " . $file['error']);
    http_response_code(400);
    echo json_encode(['error' => 'Upload error: ' . $file['error'], 'success' => false]);
    exit;
}
// Validate file type
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);
if (!in_array($mime_type, $allowed_types)) {
    error_log("TinyMCE Upload: Invalid file type - " . $mime_type);
    http_response_code(400);
    echo json_encode(['error' => 'Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed.', 'success' => false]);
    exit;
}
// Check file size (max 5MB)
$max_size = 5 * 1024 * 1024; // 5MB
if ($file['size'] > $max_size) {
    error_log("TinyMCE Upload: File too large - " . $file['size'] . " bytes");
    http_response_code(400);
    echo json_encode(['error' => 'File too large. Maximum size is 5MB.', 'success' => false]);
    exit;
}
// Create upload directory if it doesn't exist
$upload_dir = dirname(__FILE__) . '/../../../uploaded_files/fck/images/';
if (!file_exists($upload_dir)) {
    if (!mkdir($upload_dir, 0755, true)) {
        error_log("TinyMCE Upload: Failed to create upload directory: " . $upload_dir);
        echo json_encode(['error' => 'Failed to create upload directory', 'success' => false]);
        exit;
    }
}
// Generate unique filename
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = uniqid('image_') . '.' . $extension;
$upload_path = $upload_dir . $filename;
// Thumbs will be created by generateThumbnail() function
// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $upload_path)) {
    // Return the URL for TinyMCE - use relative path if SITE_WS_PATH not defined
    if (defined('SITE_WS_PATH')) {
        $file_url = SITE_WS_PATH . '/uploaded_files/fck/images/' . $filename;
    } else {
        $file_url = '/ivsportal/uploaded_files/fck/images/' . $filename;
    }
    // Generate thumbnail using existing API function
    $thumb_created = generateThumbnail($upload_path);
    $thumb_url = null;
    if ($thumb_created) {
        // Generate thumbnail URL manually
        $thumb_url = str_replace('/uploaded_files/fck/images/', '/uploaded_files/fck/.thumbs/images/', $file_url);
        error_log("TinyMCE Upload: Thumbnail created - " . $thumb_url);
    } else {
        error_log("TinyMCE Upload: Failed to create thumbnail");
    }
    error_log("TinyMCE Upload: Success - File saved to: " . $upload_path);
    error_log("TinyMCE Upload: Success - URL: " . $file_url);
    $response = [
        'location' => $file_url,
        'filename' => $filename,
        'thumb' => $thumb_url,
        'success' => true
    ];
    echo json_encode($response);
    error_log("TinyMCE Upload: Response sent - " . json_encode($response));
} else {
    error_log("TinyMCE Upload: Failed to move file from " . $file['tmp_name'] . " to " . $upload_path);
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save file', 'success' => false]);
}
?>