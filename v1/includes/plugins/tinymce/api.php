<?php
/**
 * TinyMCE Image Management API for IVS Portal
 * Handles upload, gallery, and file management operations
 */
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Include necessary files
require_once(dirname(__FILE__) . '/../../midas.inc.php');
// Check authentication - temporarily disabled for testing
// if (!isset($_SESSION['admin_id']) && !isset($_SESSION['agent_id'])) {
//     http_response_code(403);
//     echo json_encode(['error' => 'Unauthorized']);
//     exit;
// }
// Get the action from request
$action = $_GET['action'] ?? $_POST['action'] ?? '';
// Set content type to JSON
header('Content-Type: application/json');
// Handle different actions
switch ($action) {
    case 'gallery':
        handleGallery();
        break;
    case 'upload':
        handleUpload();
        break;
    case 'delete':
        handleDelete();
        break;
    case 'create_folder':
        handleCreateFolder();
        break;
    case 'check_thumbnails':
        handleCheckThumbnails();
        break;
    case 'generate_thumbnails':
        handleGenerateThumbnails();
        break;
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        break;
}
/**
 * Handle file upload with comprehensive validation
 */
function handleUpload() {
    // Check if file was uploaded
    if (!isset($_FILES['file'])) {
        http_response_code(400);
        echo json_encode(['error' => 'No file uploaded']);
        return;
    }
    $file = $_FILES['file'];
    $watermark_type = $_POST['watermark'] ?? 'default';
    $folder = $_POST['folder'] ?? '';
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error_messages = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
        ];
        $error_msg = $error_messages[$file['error']] ?? 'Unknown upload error';
        http_response_code(400);
        echo json_encode(['error' => $error_msg]);
        return;
    }
    // Validate file type using multiple methods
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    // Check file extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $allowed_extensions)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid file extension. Only JPG, PNG, GIF, and WebP are allowed.']);
        return;
    }
    // Check MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if (!in_array($mime_type, $allowed_mime_types)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid file type detected.']);
        return;
    }
    // Check file size (max 10MB)
    $max_size = 10 * 1024 * 1024; // 10MB
    if ($file['size'] > $max_size) {
        http_response_code(400);
        echo json_encode(['error' => 'File too large. Maximum size is 10MB.']);
        return;
    }
    // Validate image dimensions and content
    $image_info = getimagesize($file['tmp_name']);
    if ($image_info === false) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid image file.']);
        return;
    }
    // Create upload directory structure using helper functions
    $upload_dir = buildTinyMCEPath($folder, '', 'fs');
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create upload directory']);
            return;
        }
    }
    // Generate unique filename to prevent conflicts
    $original_name = pathinfo($file['name'], PATHINFO_FILENAME);
    $safe_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $original_name);
    $filename = $safe_name . '_' . time() . '.' . $extension;
    // Ensure filename is unique
    $counter = 1;
    $base_filename = $safe_name . '_' . time();
    while (file_exists($upload_dir . $filename)) {
        $filename = $base_filename . '_' . $counter . '.' . $extension;
        $counter++;
    }
    $upload_path = $upload_dir . $filename;
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to save file']);
        return;
    }
    // Set proper file permissions
    chmod($upload_path, 0644);
    // Apply watermark if requested and function exists
    if ($watermark_type !== 'none' && function_exists('image_watermark')) {
        image_watermark($upload_path, $watermark_type);
    }
    // Generate thumbnail
    generateThumbnail($upload_path);
    // Build file URL using helper functions
    $file_url = buildTinyMCEPath($folder, $filename, 'ws');
    // Return success response
    echo json_encode([
        'success' => true,
        'location' => $file_url,
        'filename' => $filename,
        'folder' => $folder,
        'size' => $file['size'],
        'dimensions' => $image_info[0] . 'x' . $image_info[1],
        'watermark' => $watermark_type
    ]);
}
/**
 * Handle gallery listing with folder support
 */
function handleGallery() {
    error_log("🔍 DEBUG API: handleGallery called");
    $folder = $_GET['folder'] ?? '';
    $page = intval($_GET['page'] ?? 1);
    $limit = intval($_GET['limit'] ?? 20);
    $offset = ($page - 1) * $limit;
    error_log("📁 DEBUG API: folder='$folder', page=$page, limit=$limit");
    // Use helper function for directory scanning
    $scan_dir = buildTinyMCEPath($folder, '', 'fs');
    error_log("📂 DEBUG API: scan_dir='$scan_dir'");
    error_log("📂 DEBUG API: directory exists: " . (is_dir($scan_dir) ? 'YES' : 'NO'));
    if (!is_dir($scan_dir)) {
        error_log("❌ DEBUG API: Directory does not exist, returning empty result");
        echo json_encode([
            'success' => true,
            'images' => [],
            'folders' => [],
            'total' => 0,
            'page' => $page,
            'has_more' => false,
            'debug_info' => [
                'scan_dir' => $scan_dir,
                'directory_exists' => false,
                'UP_FILES_FS_PATH' => UP_FILES_FS_PATH,
                'UP_FILES_WS_PATH' => UP_FILES_WS_PATH
            ]
        ]);
        return;
    }
    $files = scandir($scan_dir);
    error_log("📋 DEBUG API: Found " . count($files) . " files in directory");
    error_log("📋 DEBUG API: Files: " . implode(', ', $files));
    $images = [];
    $folders = [];
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    foreach ($files as $file) {
        if ($file === '.' || $file === '..' || $file === '.thumbs') {
            continue;
        }
        $file_path = $scan_dir . $file;
        error_log("🔍 DEBUG API: Processing file: $file (path: $file_path)");
        if (is_dir($file_path)) {
            error_log("📁 DEBUG API: Found directory: $file");
            $folders[] = [
                'name' => $file,
                'path' => !empty($folder) ? $folder . '/' . $file : $file,
                'count' => countImagesInFolder($file_path)
            ];
        } else {
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            error_log("🖼️ DEBUG API: Found file: $file (extension: $extension)");
            if (in_array($extension, $allowed_extensions)) {
                error_log("✅ DEBUG API: Valid image file: $file");
                $file_url = buildTinyMCEPath($folder, $file, 'ws');
                error_log("🌐 DEBUG API: File URL: $file_url");
                $thumb_url = getThumbnailUrl($file_url);
                $file_info = getimagesize($file_path);
                $images[] = [
                    'name' => $file,
                    'url' => $file_url,
                    'thumb' => $thumb_url,
                    'size' => filesize($file_path),
                    'modified' => filemtime($file_path),
                    'dimensions' => $file_info ? $file_info[0] . 'x' . $file_info[1] : 'unknown'
                ];
            } else {
                error_log("❌ DEBUG API: Invalid extension for: $file");
            }
        }
    }
    // Sort images by modification time (newest first)
    usort($images, function($a, $b) {
        return $b['modified'] - $a['modified'];
    });
    // Apply pagination
    $total_images = count($images);
    $images = array_slice($images, $offset, $limit);
    error_log("📊 DEBUG API: Final results - Images: " . count($images) . ", Folders: " . count($folders));
    error_log("📊 DEBUG API: Total images before pagination: $total_images");
    $response = [
        'success' => true,
        'images' => $images,
        'folders' => $folders,
        'total' => $total_images,
        'page' => $page,
        'has_more' => ($offset + $limit) < $total_images,
        'current_folder' => $folder,
        'debug_info' => [
            'scan_dir' => $scan_dir,
            'directory_exists' => true,
            'files_found' => count($files),
            'images_found' => count($images),
            'folders_found' => count($folders),
            'UP_FILES_FS_PATH' => UP_FILES_FS_PATH,
            'UP_FILES_WS_PATH' => UP_FILES_WS_PATH
        ]
    ];
    error_log("📤 DEBUG API: Sending response: " . json_encode($response));
    echo json_encode($response);
}
/**
 * Handle file deletion
 */
function handleDelete() {
    $filename = $_POST['filename'] ?? '';
    $folder = $_POST['folder'] ?? '';
    if (empty($filename)) {
        http_response_code(400);
        echo json_encode(['error' => 'Filename required']);
        return;
    }
    // Sanitize inputs
    $filename = basename($filename); // Prevent directory traversal
    $folder = sanitizeFolderName($folder);
    $file_path = buildTinyMCEPath($folder, $filename, 'fs');
    // Check if file exists and is within allowed directory
    if (!file_exists($file_path) || !is_file($file_path)) {
        http_response_code(404);
        echo json_encode(['error' => 'File not found']);
        return;
    }
    // Verify the file is within the upload directory (security check)
    $paths = getTinyMCEPaths();
    $real_base = realpath($paths['base_fs_path']);
    $real_file = realpath($file_path);
    if (!$real_file || strpos($real_file, $real_base) !== 0) {
        http_response_code(403);
        echo json_encode(['error' => 'Access denied']);
        return;
    }
    // Delete the file
    if (unlink($file_path)) {
        // Also delete thumbnail if exists
        $thumb_path = buildTinyMCEThumbPath($folder, $filename, 'fs');
        if (file_exists($thumb_path)) {
            unlink($thumb_path);
        }
        echo json_encode(['success' => true, 'message' => 'File deleted successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete file']);
    }
}
/**
 * Handle checking thumbnails status
 */
function handleCheckThumbnails() {
    $folder = $_GET['folder'] ?? '';
    try {
        $images_without_thumbs = getImagesWithoutThumbnails($folder);
        echo json_encode([
            'success' => true,
            'folder' => $folder,
            'images_without_thumbnails' => count($images_without_thumbs),
            'images' => $images_without_thumbs
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to check thumbnails: ' . $e->getMessage()]);
    }
}
/**
 * Handle generating missing thumbnails
 */
function handleGenerateThumbnails() {
    $folder = $_GET['folder'] ?? '';
    try {
        $result = generateMissingThumbnails($folder);
        echo json_encode([
            'success' => true,
            'folder' => $folder,
            'total_processed' => $result['total_processed'],
            'generated' => $result['generated'],
            'failed' => $result['failed'],
            'message' => "Generated {$result['generated']} thumbnails, {$result['failed']} failed"
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to generate thumbnails: ' . $e->getMessage()]);
    }
}
/**
 * Handle folder creation
 */
function handleCreateFolder() {
    $folder_name = $_POST['folder_name'] ?? '';
    $parent_folder = $_POST['parent_folder'] ?? '';
    if (empty($folder_name)) {
        http_response_code(400);
        echo json_encode(['error' => 'Folder name required']);
        return;
    }
    $folder_name = sanitizeFolderName($folder_name);
    $parent_folder = sanitizeFolderName($parent_folder);
    if (empty($folder_name)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid folder name']);
        return;
    }
    $new_folder_path = buildTinyMCEPath($parent_folder, $folder_name, 'fs');
    if (file_exists($new_folder_path)) {
        http_response_code(409);
        echo json_encode(['error' => 'Folder already exists']);
        return;
    }
    if (mkdir($new_folder_path, 0755, true)) {
        echo json_encode([
            'success' => true,
            'message' => 'Folder created successfully',
            'folder_path' => !empty($parent_folder) ? $parent_folder . '/' . $folder_name : $folder_name
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create folder']);
    }
}
/**
 * Get standardized paths for TinyMCE file operations
 */
function getTinyMCEPaths() {
    return [
        'base_fs_path' => UP_FILES_FS_PATH . '/fck/images/',
        'base_ws_path' => UP_FILES_WS_PATH . '/fck/images/',
        'thumb_fs_path' => UP_FILES_FS_PATH . '/fck/.thumbs/images/',
        'thumb_ws_path' => UP_FILES_WS_PATH . '/fck/.thumbs/images/'
    ];
}
/**
 * Build file system path for TinyMCE operations
 */
function buildTinyMCEPath($folder = '', $filename = '', $type = 'fs') {
    $paths = getTinyMCEPaths();
    $base_path = ($type === 'fs') ? $paths['base_fs_path'] : $paths['base_ws_path'];
    if (!empty($folder)) {
        $folder = sanitizeFolderName($folder);
        $base_path .= $folder . '/';
    }
    if (!empty($filename)) {
        $base_path .= $filename;
    }
    return $base_path;
}
/**
 * Build thumbnail path for TinyMCE operations
 */
function buildTinyMCEThumbPath($folder = '', $filename = '', $type = 'fs') {
    $paths = getTinyMCEPaths();
    $base_path = ($type === 'fs') ? $paths['thumb_fs_path'] : $paths['thumb_ws_path'];
    if (!empty($folder)) {
        $folder = sanitizeFolderName($folder);
        $base_path .= $folder . '/';
    }
    if (!empty($filename)) {
        $base_path .= $filename;
    }
    return $base_path;
}
/**
 * Sanitize folder name to prevent directory traversal and invalid characters
 */
function sanitizeFolderName($folder) {
    if (empty($folder)) {
        return '';
    }
    // Remove any directory traversal attempts
    $folder = str_replace(['../', '../', '..\\', '..'], '', $folder);
    // Remove leading/trailing slashes and spaces
    $folder = trim($folder, '/\\ ');
    // Replace invalid characters with underscores
    $folder = preg_replace('/[^a-zA-Z0-9_\-\/]/', '_', $folder);
    // Remove multiple consecutive slashes
    $folder = preg_replace('/\/+/', '/', $folder);
    return $folder;
}
/**
 * Generate thumbnail for uploaded image
 */
function generateThumbnail($image_path) {
    $thumb_dir = dirname($image_path) . '/../.thumbs/' . basename(dirname($image_path)) . '/';
    if (!file_exists($thumb_dir)) {
        mkdir($thumb_dir, 0755, true);
    }
    $thumb_path = $thumb_dir . basename($image_path);
    // Skip if thumbnail already exists and is newer than original
    if (file_exists($thumb_path) && filemtime($thumb_path) >= filemtime($image_path)) {
        return true;
    }
    $image_info = getimagesize($image_path);
    if (!$image_info) {
        return false;
    }
    $thumb_width = 150;
    $thumb_height = 150;
    // Create image resource based on type
    switch ($image_info[2]) {
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($image_path);
            break;
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($image_path);
            break;
        case IMAGETYPE_GIF:
            $source = imagecreatefromgif($image_path);
            break;
        case IMAGETYPE_WEBP:
            $source = imagecreatefromwebp($image_path);
            break;
        default:
            return false;
    }
    if (!$source) {
        return false;
    }
    $orig_width = imagesx($source);
    $orig_height = imagesy($source);
    // Calculate thumbnail dimensions maintaining aspect ratio
    $ratio = min($thumb_width / $orig_width, $thumb_height / $orig_height);
    $new_width = intval($orig_width * $ratio);
    $new_height = intval($orig_height * $ratio);
    // Create thumbnail
    $thumbnail = imagecreatetruecolor($new_width, $new_height);
    // Preserve transparency for PNG and GIF
    if ($image_info[2] == IMAGETYPE_PNG || $image_info[2] == IMAGETYPE_GIF) {
        imagealphablending($thumbnail, false);
        imagesavealpha($thumbnail, true);
        $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
        imagefill($thumbnail, 0, 0, $transparent);
    }
    imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, $new_width, $new_height, $orig_width, $orig_height);
    // Save thumbnail
    $result = false;
    switch ($image_info[2]) {
        case IMAGETYPE_JPEG:
            $result = imagejpeg($thumbnail, $thumb_path, 90);
            break;
        case IMAGETYPE_PNG:
            $result = imagepng($thumbnail, $thumb_path, 9);
            break;
        case IMAGETYPE_GIF:
            $result = imagegif($thumbnail, $thumb_path);
            break;
        case IMAGETYPE_WEBP:
            $result = imagewebp($thumbnail, $thumb_path, 90);
            break;
    }
    imagedestroy($source);
    imagedestroy($thumbnail);
    return $result;
}
/**
 * Check if image has thumbnail
 */
function hasValidThumbnail($image_path) {
    $thumb_dir = dirname($image_path) . '/../.thumbs/' . basename(dirname($image_path)) . '/';
    $thumb_path = $thumb_dir . basename($image_path);
    // Check if thumbnail exists and is newer than original
    return file_exists($thumb_path) && filemtime($thumb_path) >= filemtime($image_path);
}
/**
 * Get images without thumbnails in a directory
 */
function getImagesWithoutThumbnails($folder = '') {
    $paths = getTinyMCEPaths();
    $base_path = $paths['base_fs_path'];
    if (!empty($folder)) {
        $folder = sanitizeFolderName($folder);
        $base_path .= $folder . '/';
    }
    if (!is_dir($base_path)) {
        return [];
    }
    $files = scandir($base_path);
    $images_without_thumbs = [];
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $file_path = $base_path . $file;
        if (is_file($file_path)) {
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($extension, $allowed_extensions)) {
                if (!hasValidThumbnail($file_path)) {
                    $images_without_thumbs[] = [
                        'name' => $file,
                        'path' => $file_path,
                        'folder' => $folder,
                        'size' => filesize($file_path),
                        'modified' => filemtime($file_path)
                    ];
                }
            }
        }
    }
    return $images_without_thumbs;
}
/**
 * Generate thumbnails for all images without them
 */
function generateMissingThumbnails($folder = '') {
    $images_without_thumbs = getImagesWithoutThumbnails($folder);
    $generated_count = 0;
    $failed_count = 0;
    foreach ($images_without_thumbs as $image) {
        if (generateThumbnail($image['path'])) {
            $generated_count++;
            error_log("Generated thumbnail for: " . $image['name']);
        } else {
            $failed_count++;
            error_log("Failed to generate thumbnail for: " . $image['name']);
        }
    }
    return [
        'total_processed' => count($images_without_thumbs),
        'generated' => $generated_count,
        'failed' => $failed_count,
        'images' => $images_without_thumbs
    ];
}
/**
 * Get thumbnail URL for an image
 */
function getThumbnailUrl($image_url) {
    // Extract folder and filename from URL
    $paths = getTinyMCEPaths();
    $relative_path = str_replace($paths['base_ws_path'], '', $image_url);
    $path_parts = pathinfo($relative_path);
    $folder = ($path_parts['dirname'] !== '.') ? $path_parts['dirname'] : '';
    $filename = $path_parts['basename'];
    return buildTinyMCEThumbPath($folder, $filename, 'ws');
}
/**
 * Count images in a folder
 */
function countImagesInFolder($folder_path) {
    if (!is_dir($folder_path)) {
        return 0;
    }
    $files = scandir($folder_path);
    $count = 0;
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $file_path = $folder_path . '/' . $file;
        if (is_file($file_path)) {
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($extension, $allowed_extensions)) {
                $count++;
            }
        }
    }
    return $count;
}
?>