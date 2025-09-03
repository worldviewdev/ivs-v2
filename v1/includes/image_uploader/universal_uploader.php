<?php
/**
 * Universal Image Uploader and Cropper
 *
 * This file provides a universal image upload and cropping functionality using FilePond and CropperJS.
 * It handles image uploads, cropping, and saves the final result to /uploaded_files/temp/final.
 * After cropping is complete, data is sent to the parent window using postMessage.
 *
 * Created by : IVS DEV TEAM
 * Email : devteam@italyvacationspecialists.com
 */
// Include required files
require_once '../midas.inc.php';
// Define constants for file paths
define('UPLOAD_DIR', UP_FILES_FS_PATH . '/temp');
define('FINAL_DIR', UPLOAD_DIR . '/final');
define('WEB_PATH', UP_FILES_WS_PATH . '/temp/final');
/**
 * Helper function to clean URL paths
 */
function cleanUrl($url) {
	// Remove double slashes and normalize path
	$url = preg_replace('#/+#', '/', $url);
	return rtrim($url, '/');
}
/**
 * Create required directories if they don't exist
 */
if (!file_exists(UPLOAD_DIR)) {
	@mkdir(UPLOAD_DIR, 0755, true);
}
if (!file_exists(FINAL_DIR)) {
	@mkdir(FINAL_DIR, 0755, true);
}
// Get request parameters with input validation
$handler = isset($_GET['handler']) ? htmlspecialchars(strip_tags($_GET['handler']), ENT_QUOTES, 'UTF-8') : '';
$goto = isset($_GET['goto']) ? filter_var($_GET['goto'], FILTER_SANITIZE_URL) : '';
$type = isset($_GET['type']) ? htmlspecialchars(strip_tags($_GET['type']), ENT_QUOTES, 'UTF-8') : 'photo';
$debug = isset($_GET['debug']) ? htmlspecialchars(strip_tags($_GET['debug']), ENT_QUOTES, 'UTF-8') : '';
// Debug endpoint to test file access
if ($debug === 'test_path') {
	echo '<h3>Path Debug Information</h3>';
	echo '<p><strong>SITE_FS_PATH:</strong> ' . SITE_FS_PATH . '</p>';
	echo '<p><strong>SITE_WS_PATH:</strong> ' . SITE_WS_PATH . '</p>';
	echo '<p><strong>UP_FILES_FS_PATH:</strong> ' . UP_FILES_FS_PATH . '</p>';
	echo '<p><strong>UP_FILES_WS_PATH:</strong> ' . UP_FILES_WS_PATH . '</p>';
	echo '<p><strong>UPLOAD_DIR:</strong> ' . UPLOAD_DIR . '</p>';
	echo '<p><strong>WEB_PATH:</strong> ' . WEB_PATH . '</p>';
	echo '<p><strong>cleanUrl(UP_FILES_WS_PATH):</strong> ' . cleanUrl(UP_FILES_WS_PATH) . '</p>';
	echo '<h4>Test Files in Temp Directory:</h4>';
	$files = glob(UPLOAD_DIR . '/img_*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
	$count = 0;
	foreach ($files as $file) {
		if ($count >= 5) break; // Show only first 5 files
		$filename = basename($file);
		$webUrl = UP_FILES_WS_PATH . '/temp/' . $filename;
		echo '<p><strong>' . $filename . '</strong><br>';
		echo 'File exists: ' . (file_exists($file) ? 'Yes' : 'No') . '<br>';
		echo 'File readable: ' . (is_readable($file) ? 'Yes' : 'No') . '<br>';
		echo 'File size: ' . filesize($file) . ' bytes<br>';
		echo 'Web URL: <a href="' . $webUrl . '" target="_blank">' . $webUrl . '</a></p>';
		$count++;
	}
	exit;
}
// Validate handler parameter
$allowedHandlers = ['upload_filepond', 'revert_filepond'];
if ($handler && !in_array($handler, $allowedHandlers)) {
	$handler = '';
}
/**
 * Handle FilePond upload requests
 * Saves uploaded file to temporary directory and returns filename
 */
if ($handler === 'upload_filepond') {
	$tmp_name = $_FILES['file']['tmp_name'] ?? '';
	$name = $_FILES['file']['name'] ?? '';
	// Validate file upload
	if (!$tmp_name || !$name || !is_uploaded_file($tmp_name)) {
		http_response_code(400);
		echo 'Invalid file upload';
		exit;
	}
	// Get and validate file extension
	$ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
	$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
	if (!in_array($ext, $allowedExtensions)) {
		http_response_code(400);
		echo 'File type not allowed';
		exit;
	}
	// Validate MIME type
	$finfo = finfo_open(FILEINFO_MIME_TYPE);
	$mimeType = finfo_file($finfo, $tmp_name);
	finfo_close($finfo);
	$allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
	if (!in_array($mimeType, $allowedMimes)) {
		http_response_code(400);
		echo 'Invalid file type';
		exit;
	}
	// Generate secure filename
	$filename = uniqid('img_', true) . '.' . $ext;
	$targetPath = UPLOAD_DIR . '/' . $filename;
	if (move_uploaded_file($tmp_name, $targetPath)) {
		// Verify file was actually created and is readable
		if (file_exists($targetPath) && is_readable($targetPath)) {
			// Set proper permissions to ensure web access
			@chmod($targetPath, 0644);
			echo $filename;
		} else {
			http_response_code(500);
			echo 'File created but not readable';
		}
	} else {
		http_response_code(500);
		echo 'Failed to save file';
	}
	exit;
}
/**
 * Handle FilePond revert requests
 * Deletes the temporary file when upload is cancelled
 */
if ($handler === 'revert_filepond') {
	$filename = trim(file_get_contents('php://input'));
	// Sanitize filename to prevent path traversal
	$filename = basename($filename);
	$filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
	// Validate filename format (should match our upload pattern with more entropy)
	if (preg_match('/^img_[a-f0-9.]+\.[a-z]+$/', $filename)) {
		$filePath = UPLOAD_DIR . '/' . $filename;
		if (file_exists($filePath)) {
			unlink($filePath);
		}
	}
	echo 'reverted';
	exit;
}
/**
 * Handle cropped image upload and processing
 * Saves the cropped image, adds watermark, and manages session data
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['croppedImage'])) {
	try {
		$blob = $_FILES['croppedImage']['tmp_name'];
		$targetField = isset($_GET['targetField']) ? htmlspecialchars(strip_tags($_GET['targetField']), ENT_QUOTES, 'UTF-8') : '';
		// Validate uploaded file
		if (!$blob || !is_uploaded_file($blob)) {
			throw new Exception('Invalid file upload');
		}
		// Validate file is actually an image
		$imageInfo = getimagesize($blob);
		if ($imageInfo === false) {
			throw new Exception('Invalid image file');
		}
		// Generate unique filename with consistent format
		$timestamp = time();
		$uniqueId = uniqid('', true);
		$filename = "crop_{$timestamp}_{$uniqueId}.jpg";
		$targetPath = FINAL_DIR . '/' . $filename;
		if (move_uploaded_file($blob, $targetPath)) {
			// Verify file was created successfully
			if (file_exists($targetPath)) {
				// Add watermark using existing function (if needed)
				if (function_exists('image_watermark')) {
					image_watermark($targetPath);
				}
			}
			$imagePath = WEB_PATH . '/' . $filename;
			// Initialize session array if not exists
			if (!isset($_SESSION['new_image_arr'])) {
				$_SESSION['new_image_arr'] = array();
			}
			$_SESSION['new_image_arr'][] = $filename;
			// Always set existing_images with current image
			$_SESSION['existing_images'] = implode(',', $_SESSION['new_image_arr']);
			// Initialize org_images if not exists
			if (!isset($_SESSION['org_images'])) {
				$_SESSION['org_images'] = array();
			}
			// Add data for current image
			$_SESSION['org_images'][$filename] = array(
				'title' => '',  // Can be filled with default title if available
				'type' => $type // Use type from parameter
			);
			// Clear new_image_arr after processing
			unset($_SESSION['new_image_arr']);
			// Prepare redirect URL with proper sanitization
			$redirectUrl = '';
			if ($goto) {
				$separator = (strpos($goto, '?') !== false) ? '&' : '?';
				$redirectUrl = $goto . $separator . "image_name=" . urlencode($filename) . "&type=" . urlencode($type);
			}
			// Send JSON response
			header('Content-Type: application/json');
			echo json_encode([
				'success' => true,
				'image' => $imagePath,
				'target' => $targetField,
				'filename' => $filename,
				'redirect' => $redirectUrl
			]);
			exit;
		} else {
			throw new Exception('Failed to save file');
		}
	} catch (Exception $e) {
		http_response_code(500);
		echo json_encode(['error' => $e->getMessage()]);
	}
	exit;
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Universal Uploader</title>
	<link href="https://unpkg.com/filepond/dist/filepond.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
	<style>
		* {
			box-sizing: border-box;
		}
		body {
			font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
			background: #f9f9fb;
			margin: 0;
			padding: 30px;
			display: flex;
			justify-content: center;
			align-items: flex-start;
			min-height: 100vh;
		}
		.container {
			max-width: 100%;
			width: 100%;
		}
		h3 {
			margin-top: 0;
			font-size: 22px;
			color: #333;
			text-align: center;
			border-bottom: 1px solid #e0e0e0;
			padding-bottom: 10px;
		}
		input[type="file"] {
			width: 100%;
			height: 100px;
			margin-bottom: 20px;
		}
		#crop-image {
			width: 100%;
			max-height: 400px;
			object-fit: contain;
			border: 1px solid #ccc;
			border-radius: 6px;
			display: none;
			margin-top: 20px;
		}
		button[type="submit"] {
			background: #333;
			color: #fff;
			border: none;
			padding: 12px 20px;
			font-size: 15px;
			border-radius: 8px;
			cursor: pointer;
			transition: background 0.3s ease;
			margin-top: 10px;
			margin-left: 10px;
			font-weight: 500;
		}
		button[type="submit"]:hover {
			color: #000;
		}
		button[type="submit"]:hover {
			background: #c6c6c6;
		}
		select {
			padding: 5px;
			border: 1px solid #ccc;
			border-radius: 6px;
			font-size: 15px;
			margin-top: 10px;
		}
		label {
			font-size: 15px;
			font-style: italic;
			color: #333;
			margin-right: 10px;
		}
		@media screen and (max-width: 480px) {
			.container {
				padding: 20px;
			}
			h3 {
				font-size: 18px;
			}
			button[type="submit"] {
				font-size: 14px;
			}
		}
	</style>
</head>
<body>
	<div class="container">
		<img src="<?php echo SITE_WS_PATH; ?>/images/logo2.png" alt="Logo" class="logo" style="margin-bottom: 30px;">
		<h3></h3>
		<form id="crop-form" method="post" enctype="multipart/form-data">
			<input type="file" id="filepond" name="file" />
			<img id="crop-image" />
			<div style="margin-top: 20px; text-align: left;">
				<label for="image-type">Select Image Type:</label>
				<select id="image-type" name="image_type">
					<option value="free">Free Size (Max 1024px)</option>
					<option value="tile">Tile Image (Max Width 300px)</option>
					<option value="banner">Banner (Must be 800x390)</option>
				</select>
				<button type="submit">Crop & Save</button>
			</div>
		</form>
	</div>
	<script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
	<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>
	<script src="https://unpkg.com/filepond-plugin-image-resize/dist/filepond-plugin-image-resize.min.js"></script>
	<script src="https://unpkg.com/filepond-plugin-image-transform/dist/filepond-plugin-image-transform.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
	<script>
		/**
		 * Initialize FilePond and Cropper functionality
		 * Handles file upload, preview, and cropping operations
		 */
		document.addEventListener('DOMContentLoaded', function() {
			let cropper;
			const input = document.getElementById('filepond');
			const img = document.getElementById('crop-image');
			const imageType = document.querySelector('#image-type');
			// Register FilePond plugins
			FilePond.registerPlugin(
				FilePondPluginImagePreview,
				FilePondPluginImageResize,
				FilePondPluginImageTransform
			);
			// Initialize FilePond with configuration
			const pond = FilePond.create(input, {
				allowImagePreview: false,
				allowImageCrop: false,
				allowFileTypeValidation: true,
				acceptedFileTypes: ['image/*'],
				// Configure image resize settings with maximum quality
				imageResizeTargetWidth: 1024,
				imageResizeTargetHeight: 1024,
				imageResizeMode: 'contain',
				imageResizeUpscale: true,
				imageTransformOutputMimeType: 'image/jpeg',
				// Preserve original file information
				preserveFileNames: true,
				// Configure server endpoints
				server: {
					process: 'universal_uploader.php?handler=upload_filepond',
					revert: 'universal_uploader.php?handler=revert_filepond'
				}
			});
			// Function to update resize options based on selected image type
			function updateResizeOptions() {
				const value = imageType.value;
				if (value === 'free') {
					pond.setOptions({
						imageResizeTargetWidth: 1024,
						imageResizeTargetHeight: 1024,
						imageResizeMode: 'contain',
						imageResizeUpscale: true,
						imageTransformOutputMimeType: 'image/jpeg',
					});
				} else if (value === 'tile') {
					pond.setOptions({
						imageResizeTargetWidth: 300,
						imageResizeTargetHeight: 200,
						imageResizeMode: 'cover',
						imageResizeUpscale: true,
						imageTransformOutputMimeType: 'image/jpeg',
					});
				} else if (value === 'banner') {
					pond.setOptions({
						imageResizeTargetWidth: 800,
						imageResizeTargetHeight: 390,
						imageResizeMode: 'cover',
						imageResizeUpscale: true,
						imageTransformOutputMimeType: 'image/jpeg',
					});
				}
			}
			// Initialize resize options based on current selection
			updateResizeOptions();
			// Trigger resize options update on image type change
			imageType.addEventListener('change', updateResizeOptions);
			// Apply resize options when file is added
			pond.on('addfile', (error, file) => {
				if (!error) {
					// Update resize options for the new file
					setTimeout(() => {
						updateResizeOptions();
					}, 100);
				}
			});
			/**
			 * Handle successful file upload
			 * Initialize Cropper with uploaded image
			 */
			pond.on('processfile', (err, file) => {
				if (!err) {
					// Clean the filename to remove any unwanted characters
					const cleanFilename = file.serverId.trim();
					const basePath = '<?php echo cleanUrl(UP_FILES_WS_PATH); ?>';
					// Try multiple URL variations to find the working one
					const urlVariations = [
						basePath + '/temp/' + cleanFilename,
						basePath + '/temp/' + encodeURIComponent(cleanFilename),
						'<?php echo SITE_WS_PATH; ?>/uploaded_files/temp/' + cleanFilename
					];
					let imageLoaded = false;
					let currentUrlIndex = 0;
					function tryNextUrl() {
						if (currentUrlIndex >= urlVariations.length) {
							alert('Failed to load uploaded image. File uploaded successfully but cannot be displayed for cropping.');
							return;
						}
						const currentUrl = urlVariations[currentUrlIndex];
						img.onload = () => {
							imageLoaded = true;
							img.style.display = 'block';
							if (cropper) cropper.destroy();
							cropper = new Cropper(img, {
								viewMode: 1,
								zoomable: true,
								responsive: true,
								restore: false,
								guides: false,
								center: false,
								highlight: false,
								cropBoxMovable: true,
								cropBoxResizable: true,
								toggleDragModeOnDblclick: false
							});
						};
						img.onerror = () => {
							currentUrlIndex++;
							tryNextUrl();
						};
						img.src = currentUrl;
					}
					// Start trying URLs
					tryNextUrl();
				} else {
					console.error('FilePond upload error:', err);
				}
			});
			/**
			 * Handle form submission
			 * Process cropped image and send to server
			 */
			document.getElementById('crop-form').addEventListener('submit', function(e) {
				e.preventDefault();
				if (!cropper) return alert('Please upload and select crop first');
				cropper.getCroppedCanvas().toBlob(blob => {
					const formData = new FormData();
					formData.append('croppedImage', blob);
					// Get target field from URL parameters
					const urlParams = new URLSearchParams(window.location.search);
					const targetField = urlParams.get('targetField');
					const uploadUrl = `${location.href}${targetField ? '&targetField=' + targetField : ''}`;
					// Send cropped image to server
					fetch(uploadUrl, {
							method: 'POST',
							body: formData
						})
						.then(response => {
							if (!response.ok) {
								throw new Error('Network response was not ok');
							}
							return response.json();
						})
						.then(data => {
							if (!data.success) {
								throw new Error(data.error || 'Error occurred while saving image');
							}
							// Send message to parent window
							window.parent.postMessage({
								type: 'uploadComplete',
								image: data.image,
								target: data.target,
								filename: data.filename
							}, '*');
							// Redirect if specified
							if (data.redirect) {
								window.location.href = data.redirect;
							}
						})
						.catch(error => {
							console.error('Error:', error);
							alert('Error occurred while saving image: ' + error.message);
						});
				}, 'image/jpeg', 1.0); // Use maximum quality (1.0 = 100%)
			});
		});
	</script>
</body>
</html>