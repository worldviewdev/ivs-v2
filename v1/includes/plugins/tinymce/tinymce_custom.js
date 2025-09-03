// TinyMCE Custom Upload Functions

// Initialize TinyMCE with custom buttons
function initializeTinyMCE(selector, config) {
    tinymce.init({
        selector: selector,
        license_key: "gpl",
        menubar: false,
        statusbar: false,
        height: config.height || 500,
        width: config.width || "100%",
        plugins: [
            "advlist", "autolink", "lists", "link", "image", "charmap", "preview",
            "anchor", "searchreplace", "visualblocks", "code", "fullscreen",
            "insertdatetime", "media", "table", "help", "wordcount"
        ],
        toolbar: "undo redo | blocks | bold italic forecolor backcolor | alignleft aligncenter alignright alignjustify | customai customimage custommultiimage customvideo | bullist numlist outdent indent | removeformat | link media table | code fullscreen help",
        branding: false,
        promotion: false,
        automatic_uploads: false,
        file_picker_types: 'image',
        file_picker_callback: function(callback, value, meta) {
            if (meta.filetype === 'image') {
                openImageModal(tinymce.activeEditor, 'picker', callback);
            }
        },
        images_upload_handler: function(blobInfo, success, failure) {
            var xhr, formData;
            xhr = new XMLHttpRequest();
            xhr.withCredentials = false;
            xhr.open('POST', tinymceUploadUrl);
            
            xhr.onload = function() {
                var json;
                if (xhr.status !== 200) {
                    failure('HTTP Error: ' + xhr.status);
                    return;
                }
                
                json = JSON.parse(xhr.responseText);
                if (!json || typeof json.location !== "string") {
                    failure("Invalid JSON: " + xhr.responseText);
                    return;
                }
                
                success(json.location);
            };
            
            formData = new FormData();
            formData.append("file", blobInfo.blob(), blobInfo.filename());
            
            xhr.send(formData);
        },
        setup: function(editor) {
            // Custom Image Upload Button
            editor.ui.registry.addButton('customimage', {
                text: 'Image',
                icon: 'image',
                tooltip: 'Upload Image',
                onAction: function() {
                    openImageModal(editor, 'single');
                }
            });
            
            // Custom Multi-Image Gallery Button
            editor.ui.registry.addButton('custommultiimage', {
                text: 'Gallery',
                icon: 'gallery',
                tooltip: 'Upload Image Gallery',
                onAction: function() {
                    openImageModal(editor, 'multiple');
                }
            });
            
            // Custom Video Button
            editor.ui.registry.addButton('customvideo', {
                text: 'Video',
                icon: 'embed',
                tooltip: 'Insert Video',
                onAction: function() {
                    openVideoModal(editor);
                }
            });
            
            // AI Assistant Button
            editor.ui.registry.addButton('customai', {
                text: 'AI',
                icon: 'ai-prompt',
                tooltip: 'AI Assistant - Generate, Improve, Translate',
                onAction: function() {
                    openAIModal(editor);
                }
            });
        }
    });
}

// Check for missing thumbnails
function checkMissingThumbnails() {
    console.log('🔍 DEBUG: Checking missing thumbnails');
    
    var folder = document.getElementById('thumbFolderSelect').value;
    var resultsDiv = document.getElementById('thumbnailResults');
    
    resultsDiv.innerHTML = '<div class="loading">🔍 Checking for missing thumbnails...</div>';
    
    var apiUrl = '/ivsportal/includes/plugins/tinymce/api.php?action=check_thumbnails';
    if (folder) {
        apiUrl += '&folder=' + encodeURIComponent(folder);
    }
    
    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            console.log('📊 DEBUG: Thumbnail check result:', data);
            
            if (data.success) {
                var html = '<div class="thumbnail-check-results">';
                html += '<h5>Thumbnail Check Results</h5>';
                html += '<p><strong>' + data.images_without_thumbnails + '</strong> images missing thumbnails</p>';
                
                if (data.images.length > 0) {
                    html += '<div class="missing-thumbnails-list">';
                    html += '<h6>Images without thumbnails:</h6>';
                    html += '<ul>';
                    data.images.forEach(function(image) {
                        html += '<li>' + image.name + ' (' + Math.round(image.size/1024) + ' KB)</li>';
                    });
                    html += '</ul>';
                    html += '</div>';
                } else {
                    html += '<p class="success-message">✅ All images have thumbnails!</p>';
                }
                
                html += '</div>';
                resultsDiv.innerHTML = html;
            } else {
                resultsDiv.innerHTML = '<div class="error">❌ Error: ' + data.error + '</div>';
            }
        })
        .catch(error => {
            console.error('❌ DEBUG: Error checking thumbnails:', error);
            resultsDiv.innerHTML = '<div class="error">❌ Failed to check thumbnails</div>';
        });
}

// Generate missing thumbnails
function generateMissingThumbnails() {
    console.log('🔧 DEBUG: Generating missing thumbnails');
    
    var folder = document.getElementById('thumbFolderSelect').value;
    var resultsDiv = document.getElementById('thumbnailResults');
    
    resultsDiv.innerHTML = '<div class="loading">🔧 Generating missing thumbnails...</div>';
    
    var apiUrl = '/ivsportal/includes/plugins/tinymce/api.php?action=generate_thumbnails';
    if (folder) {
        apiUrl += '&folder=' + encodeURIComponent(folder);
    }
    
    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            console.log('🔧 DEBUG: Thumbnail generation result:', data);
            
            if (data.success) {
                var html = '<div class="thumbnail-generation-results">';
                html += '<h5>Thumbnail Generation Results</h5>';
                html += '<p><strong>Processed:</strong> ' + data.total_processed + ' images</p>';
                html += '<p><strong>Generated:</strong> ' + data.generated + ' thumbnails</p>';
                
                if (data.failed > 0) {
                    html += '<p><strong>Failed:</strong> ' + data.failed + ' images</p>';
                }
                
                html += '<p class="success-message">✅ ' + data.message + '</p>';
                html += '</div>';
                resultsDiv.innerHTML = html;
                
                // Refresh gallery if it's loaded
                if (window.currentTinyMCEFolder !== undefined) {
                    loadMediaGallery(window.currentTinyMCEFolder, 1, false);
                }
            } else {
                resultsDiv.innerHTML = '<div class="error">❌ Error: ' + data.error + '</div>';
            }
        })
        .catch(error => {
            console.error('❌ DEBUG: Error generating thumbnails:', error);
            resultsDiv.innerHTML = '<div class="error">❌ Failed to generate thumbnails</div>';
        });
}

function openImageModal(editor, mode, callback) {
    var modalId = 'tinymce-modal-' + Date.now();
    
    // Store references globally
    window.currentTinyMCEEditor = editor;
    window.currentTinyMCECallback = callback;
    window.currentTinyMCEMode = mode;
    window.selectedFiles = null;
    window.selectedMediaItems = [];
    
    // Create modern modal structure
    var modal = document.createElement('div');
    modal.id = modalId;
    modal.className = 'tinymce-modal';
    
    var modalContent = document.createElement('div');
    modalContent.className = 'tinymce-modal-content';
    
    // Header
    var header = document.createElement('div');
    header.className = 'tinymce-modal-header';
    
    var title = document.createElement('h2');
    title.className = 'tinymce-modal-title';
    title.textContent = mode === 'multiple' ? 'Select Featured Images' : 'Insert Image to Editor';
    
    var closeBtn = document.createElement('button');
    closeBtn.className = 'tinymce-modal-close';
    closeBtn.innerHTML = '×';
    closeBtn.onclick = function() { closeModal(modalId); };
    
    header.appendChild(title);
    header.appendChild(closeBtn);
    
    // Tabs
    var tabs = document.createElement('div');
    tabs.className = 'tinymce-modal-tabs';
    
    var uploadTab = document.createElement('button');
    uploadTab.className = 'tinymce-tab active';
    uploadTab.textContent = '📤 Upload New';
    uploadTab.onclick = function() { switchTab('upload'); };
    
    var galleryTab = document.createElement('button');
    galleryTab.className = 'tinymce-tab';
    galleryTab.textContent = '🖼️ Media Gallery';
    galleryTab.onclick = function() { switchTab('gallery'); };
    
    var thumbnailsTab = document.createElement('button');
    thumbnailsTab.className = 'tinymce-tab';
    thumbnailsTab.textContent = '🔧 Thumbnails';
    thumbnailsTab.onclick = function() { switchTab('thumbnails'); };
    
    tabs.appendChild(uploadTab);
    tabs.appendChild(galleryTab);
    tabs.appendChild(thumbnailsTab);
    
    // Body
    var body = document.createElement('div');
    body.className = 'tinymce-modal-body';
    
    // Upload Tab Content
    var uploadContent = document.createElement('div');
    uploadContent.className = 'tinymce-tab-content active';
    uploadContent.id = 'upload-tab';
    
    // Upload info
    var uploadInfo = document.createElement('div');
    uploadInfo.className = 'tinymce-upload-info';
    uploadInfo.innerHTML = 
        '<h4>Upload Images</h4>' +
        '<p>Drag and drop multiple images or click to browse. Maximum 10 files, up to 5MB each.</p>';

    var uploadArea = document.createElement('div');
    uploadArea.className = 'tinymce-upload-area';
    uploadArea.innerHTML = 
        '<div class="tinymce-upload-icon">📁</div>' +
        '<div class="tinymce-upload-text">Drop your files here</div>' +
        '<div class="tinymce-upload-subtext">or click to browse files</div>';
    
    var fileInput = document.createElement('input');
    fileInput.type = 'file';
    fileInput.accept = 'image/*';
    fileInput.multiple = true; // Always allow multiple files
    fileInput.style.display = 'none';
    
    var previewGrid = document.createElement('div');
    previewGrid.className = 'tinymce-preview-grid';
    previewGrid.id = 'upload-preview';
    
    // Add drag and drop functionality
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        uploadArea.classList.add('drag-over');
    });
    
    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        uploadArea.classList.remove('drag-over');
    });
    
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        uploadArea.classList.remove('drag-over');
        
        var files = Array.from(e.dataTransfer.files);
        handleFileSelection(files);
    });
    
    uploadArea.onclick = function() { fileInput.click(); };
    
    // Handle file input change
    fileInput.addEventListener('change', function(e) {
        var files = Array.from(e.target.files);
        handleFileSelection(files);
    });
    
    uploadContent.appendChild(uploadInfo);
    uploadContent.appendChild(uploadArea);
    uploadContent.appendChild(fileInput);
    uploadContent.appendChild(previewGrid);
    
    // Gallery Tab Content
    var galleryContent = document.createElement('div');
    galleryContent.className = 'tinymce-tab-content';
    galleryContent.id = 'gallery-tab';
    
    var mediaGrid = document.createElement('div');
    mediaGrid.className = 'tinymce-media-grid';
    mediaGrid.id = 'mediaGrid';
    
    // Add folder navigation
    var folderNav = document.createElement('div');
    folderNav.id = 'folderNavigation';
    folderNav.className = 'folder-navigation';
    
    galleryContent.appendChild(folderNav);
    galleryContent.appendChild(mediaGrid);
    
    // Thumbnails Tab Content
    var thumbnailsContent = document.createElement('div');
    thumbnailsContent.className = 'tinymce-tab-content';
    thumbnailsContent.id = 'thumbnails-tab';
    thumbnailsContent.innerHTML = 
        '<div class="thumbnails-controls">' +
        '<h4>Thumbnail Management</h4>' +
        '<p>Check and generate missing thumbnails for your images.</p>' +
        '<div class="control-group">' +
        '<label for="thumbFolderSelect">Folder:</label>' +
        '<select id="thumbFolderSelect" class="tinymce-select">' +
        '<option value="">All Folders</option>' +
        '<option value="tours">Tours</option>' +
        '<option value="hotels">Hotels</option>' +
        '<option value="destinations">Destinations</option>' +
        '</select>' +
        '</div>' +
        '<div class="thumbnail-actions">' +
        '<button type="button" class="tinymce-btn tinymce-btn-secondary" onclick="checkMissingThumbnails()">🔍 Check Missing</button>' +
        '<button type="button" class="tinymce-btn tinymce-btn-primary" onclick="generateMissingThumbnails()">🔧 Generate All</button>' +
        '</div>' +
        '</div>' +
        '<div id="thumbnailResults" class="thumbnail-results"></div>';
    
    body.appendChild(uploadContent);
    body.appendChild(galleryContent);
    body.appendChild(thumbnailsContent);
    
    // Footer
    var footer = document.createElement('div');
    footer.className = 'tinymce-modal-footer';
    
    var selectedCount = document.createElement('span');
    selectedCount.id = 'selected-count';
    selectedCount.textContent = '0 of 1 selected';
    selectedCount.style.marginRight = 'auto';
    selectedCount.style.color = '#666';
    
    var uploadBtn = document.createElement('button');
    uploadBtn.className = 'tinymce-btn tinymce-btn-primary';
    uploadBtn.id = 'upload-btn';
    uploadBtn.textContent = 'Upload Images';
    uploadBtn.style.display = 'none';
    uploadBtn.onclick = function() { uploadImages(modalId); };
    
    var cancelBtn = document.createElement('button');
    cancelBtn.className = 'tinymce-btn tinymce-btn-secondary';
    cancelBtn.textContent = 'Cancel';
    cancelBtn.onclick = function() { closeModal(modalId); };
    
    var insertBtn = document.createElement('button');
    insertBtn.className = 'tinymce-btn tinymce-btn-primary';
    insertBtn.textContent = 'Insert';
    insertBtn.disabled = true;
    insertBtn.id = 'insert-btn';
    
    footer.appendChild(selectedCount);
    footer.appendChild(uploadBtn);
    footer.appendChild(cancelBtn);
    footer.appendChild(insertBtn);
    
    // Assemble modal
    modalContent.appendChild(header);
    modalContent.appendChild(tabs);
    modalContent.appendChild(body);
    modalContent.appendChild(footer);
    modal.appendChild(modalContent);
    document.body.appendChild(modal);
    
    // Event handlers
    insertBtn.addEventListener('click', handleInsert);
    
    // Load existing media
    // File selection handler
    function handleFileSelection(files) {
        console.log('📁 DEBUG: Files selected:', files.length);
        
        // Filter image files only
        var imageFiles = files.filter(function(file) {
            return file.type.startsWith('image/');
        });
        
        // Limit to 10 files
        if (imageFiles.length > 10) {
            alert('Maximum 10 files allowed. Only first 10 files will be processed.');
            imageFiles = imageFiles.slice(0, 10);
        }
        
        // Validate file sizes
        var validFiles = [];
        var maxSize = 5 * 1024 * 1024; // 5MB
        
        imageFiles.forEach(function(file) {
            if (file.size > maxSize) {
                alert('File "' + file.name + '" is too large. Maximum size is 5MB.');
            } else {
                validFiles.push(file);
            }
        });
        
        if (validFiles.length === 0) {
            return;
        }
        
        window.selectedFiles = validFiles;
        displayFilePreview(validFiles);
        
        // Show upload button
        var uploadBtn = document.getElementById('upload-btn');
        if (uploadBtn) {
            uploadBtn.style.display = 'inline-block';
            uploadBtn.textContent = 'Upload ' + validFiles.length + ' file(s)';
        }
    }
    
    // Display file preview
    function displayFilePreview(files) {
        var previewGrid = document.getElementById('upload-preview');
        previewGrid.innerHTML = '';
        
        files.forEach(function(file, index) {
            var previewItem = document.createElement('div');
            previewItem.className = 'tinymce-preview-item';
            
            var img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.onload = function() {
                URL.revokeObjectURL(img.src);
            };
            
            var info = document.createElement('div');
            info.className = 'tinymce-preview-info';
            info.innerHTML = 
                '<div class="file-name">' + file.name + '</div>' +
                '<div class="file-size">' + Math.round(file.size / 1024) + ' KB</div>';
            
            var removeBtn = document.createElement('button');
            removeBtn.className = 'tinymce-remove-file';
            removeBtn.innerHTML = '×';
            removeBtn.onclick = function() {
                removeFile(index);
            };
            
            previewItem.appendChild(img);
            previewItem.appendChild(info);
            previewItem.appendChild(removeBtn);
            previewGrid.appendChild(previewItem);
        });
    }
    
    // Remove file from selection
    function removeFile(index) {
        window.selectedFiles.splice(index, 1);
        displayFilePreview(window.selectedFiles);
        
        var uploadBtn = document.getElementById('upload-btn');
        if (uploadBtn) {
            if (window.selectedFiles.length === 0) {
                uploadBtn.style.display = 'none';
            } else {
                uploadBtn.textContent = 'Upload ' + window.selectedFiles.length + ' file(s)';
            }
        }
    }
    
    console.log('🚀 DEBUG: About to call loadMediaGallery from modal setup');
    loadMediaGallery();
    
    // Tab switching function
    function switchTab(tabName) {
        document.querySelectorAll('.tinymce-tab').forEach(function(tab) {
            tab.classList.remove('active');
        });
        document.querySelectorAll('.tinymce-tab-content').forEach(function(content) {
            content.classList.remove('active');
        });
        
        if (tabName === 'upload') {
            uploadTab.classList.add('active');
            uploadContent.classList.add('active');
        } else if (tabName === 'gallery') {
            galleryTab.classList.add('active');
            galleryContent.classList.add('active');
        } else if (tabName === 'thumbnails') {
            thumbnailsTab.classList.add('active');
            thumbnailsContent.classList.add('active');
        }
    }
    
    // Close on backdrop click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal(modalId);
        }
    });
}

// Handle file selection for upload
function handleFileSelection(e) {
    console.log('📁 DEBUG: handleFileSelection called with', e.target.files.length, 'files');
    
    window.selectedFiles = Array.from(e.target.files);
    console.log('📋 DEBUG: Selected files:', window.selectedFiles.map(f => f.name));
    
    // Validate files
    var validFiles = [];
    var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    var maxSize = 5 * 1024 * 1024; // 5MB
    
    window.selectedFiles.forEach(function(file) {
        if (!allowedTypes.includes(file.type)) {
            alert('File "' + file.name + '" is not a valid image type. Only JPEG, PNG, GIF, and WebP are allowed.');
            return;
        }
        
        if (file.size > maxSize) {
            alert('File "' + file.name + '" is too large. Maximum size is 5MB.');
            return;
        }
        
        validFiles.push(file);
    });
    
    window.selectedFiles = validFiles;
    console.log('✅ DEBUG: Valid files after filtering:', validFiles.length);
    
    updateUploadPreview();
    updateInsertButton();
}

function updateUploadPreview() {
    var previewGrid = document.getElementById('upload-preview');
    previewGrid.innerHTML = '';
        
    if (!window.selectedFiles || window.selectedFiles.length === 0) return;
        
    window.selectedFiles.forEach(function(file, index) {
        var item = document.createElement('div');
        item.className = 'tinymce-media-item';
        item.dataset.url = file.name;
        item.dataset.name = file.name;
            
        var img = document.createElement('img');
        img.className = 'tinymce-media-thumbnail';
        img.src = URL.createObjectURL(file);
        img.alt = file.name;
        img.onerror = function() {
            console.warn('🖼️ DEBUG: Failed to load image:', file.name);
            this.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiBmaWxsPSIjZjhmOWZhIi8+CjxwYXRoIGQ9Ik0zMCA3MEw0MCA1MEw2MCA3MEg3MFY3MEgzMFoiIGZpbGw9IiNkZGQiLz4KPGNpcmNsZSBjeD0iMzUiIGN5PSIzNSIgcj0iNSIgZmlsbD0iI2RkZCIvPgo8L3N2Zz4K';
        };
            
        // Add image info overlay
        var info = document.createElement('div');
        info.className = 'tinymce-media-info';
            
        var name = document.createElement('div');
        name.className = 'tinymce-media-name';
        name.textContent = file.name;
            
        var details = document.createElement('div');
        details.className = 'tinymce-media-details';
        details.textContent = 'Unknown size';
            
        info.appendChild(name);
        info.appendChild(details);
            
        item.appendChild(img);
        item.appendChild(info);
        previewGrid.appendChild(item);
    });
}

// Global variables for infinite scroll
window.galleryState = {
    currentFolder: '',
    currentPage: 1,
    isLoading: false,
    hasMore: true,
    totalLoaded: 0
};

// Load media gallery with API integration
function loadMediaGallery(folder = '', page = 1, append = false) {
    console.log('🔍 DEBUG: loadMediaGallery called with folder:', folder, 'page:', page, 'append:', append);
    
    var mediaGrid = document.getElementById('mediaGrid');
    if (!mediaGrid) {
        console.error('❌ DEBUG: mediaGrid element not found!');
        return;
    }
    
    // Update gallery state
    if (!append) {
        window.galleryState.currentFolder = folder;
        window.galleryState.currentPage = 1;
        window.galleryState.totalLoaded = 0;
        window.galleryState.hasMore = true;
    }
    
    if (window.galleryState.isLoading) {
        console.log('⏳ DEBUG: Already loading, skipping request');
        return;
    }
    
    window.galleryState.isLoading = true;
    
    console.log('✅ DEBUG: mediaGrid found, setting loading state');
    if (!append) {
        mediaGrid.innerHTML = '<div class="loading">Loading images...</div>';
    } else {
        // Add loading indicator for infinite scroll
        var loadingDiv = document.createElement('div');
        loadingDiv.className = 'loading-more';
        loadingDiv.innerHTML = '<div class="loading">Loading more images...</div>';
        mediaGrid.appendChild(loadingDiv);
    }
    
    var params = new URLSearchParams({
        action: 'gallery',
        folder: folder,
        page: page,
        limit: 20
    });
    
    var apiUrl = '/ivsportal/includes/plugins/tinymce/api.php?' + params.toString();
    console.log('🌐 DEBUG: Making API request to:', apiUrl);
    
    fetch(apiUrl)
    .then(function(response) {
        console.log('📡 DEBUG: API response status:', response.status);
        console.log('📡 DEBUG: API response headers:', response.headers);
        
        if (!response.ok) {
            throw new Error('HTTP ' + response.status + ': ' + response.statusText);
        }
        
        return response.json();
    })
    .then(function(data) {
        console.log('📦 DEBUG: API response data:', data);
        
        window.galleryState.isLoading = false;
        
        // Remove loading indicator
        var loadingMore = document.querySelector('.loading-more');
        if (loadingMore) {
            loadingMore.remove();
        }
        
        if (data.success) {
            console.log('✅ DEBUG: API success, images count:', data.images ? data.images.length : 0);
            console.log('✅ DEBUG: API success, folders count:', data.folders ? data.folders.length : 0);
            
            // Update pagination state
            window.galleryState.hasMore = data.has_more;
            window.galleryState.totalLoaded += data.images ? data.images.length : 0;
            
            if (append) {
                window.galleryState.currentPage = page;
                appendGalleryContent(data);
            } else {
                displayGalleryContent(data, data.current_folder);
                setupInfiniteScroll();
            }
        } else {
            console.error('❌ DEBUG: API returned error:', data.error);
            if (!append) {
                mediaGrid.innerHTML = '<div class="error">Failed to load gallery: ' + (data.error || 'Unknown error') + '</div>';
            }
        }
    })
    .catch(function(error) {
        console.error('💥 DEBUG: Gallery load error:', error);
        console.error('💥 DEBUG: Error stack:', error.stack);
        window.galleryState.isLoading = false;
        
        // Remove loading indicator
        var loadingMore = document.querySelector('.loading-more');
        if (loadingMore) {
            loadingMore.remove();
        }
        
        if (!append) {
            mediaGrid.innerHTML = '<div class="error">Failed to load gallery: ' + error.message + '</div>';
        }
    });
}

// Display gallery content
function displayGalleryContent(data, currentFolder) {
    console.log('🎨 DEBUG: displayGalleryContent called with data:', data);
    console.log('🎨 DEBUG: currentFolder:', currentFolder);
    
    var mediaGrid = document.getElementById('mediaGrid');
    if (!mediaGrid) {
        console.error('❌ DEBUG: mediaGrid not found in displayGalleryContent');
        return;
    }
    
    var html = '';

    // Show folders first
    if (data.folders && data.folders.length > 0) {
        console.log('📁 DEBUG: Adding', data.folders.length, 'folders to display');
        html += '<div class="folder-section">';
        data.folders.forEach(function(folder) {
            console.log('📁 DEBUG: Adding folder:', folder);
            html += '<div class="folder-item" onclick="loadMediaGallery(\'' + folder.path + '\')">' +
                    '<div class="folder-icon">📁</div>' +
                    '<div class="folder-name">' + folder.name + '</div>' +
                    '<div class="folder-count">' + folder.count + ' images</div>' +
                    '</div>';
        });
        html += '</div>';
    } else {
        console.log('📁 DEBUG: No folders to display');
    }

    // Show images
    if (data.images && data.images.length > 0) {
        console.log('🖼️ DEBUG: Adding', data.images.length, 'images to display');
        data.images.forEach(function(image, index) {
            console.log('🖼️ DEBUG: Adding image', index + 1, ':', image);
            html += '<div class="tinymce-media-item" data-url="' + image.url + '" data-name="' + image.name + '" onclick="toggleImageSelection(this)">' +
                    '<img class="tinymce-media-thumbnail" src="' + (image.thumb || image.url) + '" alt="' + image.name + '" loading="lazy" onerror="console.error(\'❌ DEBUG: Failed to load image:\', this.src)">' +
                    '<div class="tinymce-media-info">' +
                    '<div class="tinymce-media-name">' + image.name + '</div>' +
                    '<div class="tinymce-media-details">' + (image.dimensions || 'Unknown size') + '</div>' +
                    '</div>' +
                    '</div>';
        });
    } else {
        console.log('🖼️ DEBUG: No images to display');
    }

    if ((!data.folders || data.folders.length === 0) && (!data.images || data.images.length === 0)) {
        console.log('🚫 DEBUG: No content found, showing empty message');
        html = '<div class="no-images">No images found in this folder.</div>';
    }

    console.log('📝 DEBUG: Final HTML length:', html.length);
    console.log('📝 DEBUG: Final HTML content:', html.substring(0, 200) + '...');
    
    mediaGrid.innerHTML = html;
    console.log('✅ DEBUG: Gallery content updated in DOM');
}

// Append new content for infinite scroll
function appendGalleryContent(data) {
    console.log('➕ DEBUG: appendGalleryContent called with data:', data);
    
    var mediaGrid = document.getElementById('mediaGrid');
    if (!mediaGrid) {
        console.error('❌ DEBUG: mediaGrid not found in appendGalleryContent');
        return;
    }
    
    var html = '';
    
    // Only append images (folders are only shown on first load)
    if (data.images && data.images.length > 0) {
        console.log('🖼️ DEBUG: Appending', data.images.length, 'images');
        data.images.forEach(function(image, index) {
            console.log('🖼️ DEBUG: Appending image', index + 1, ':', image);
            html += '<div class="tinymce-media-item" data-url="' + image.url + '" data-name="' + image.name + '" onclick="toggleImageSelection(this)">' +
                    '<img class="tinymce-media-thumbnail" src="' + (image.thumb || image.url) + '" alt="' + image.name + '" loading="lazy" onerror="console.error(\'❌ DEBUG: Failed to load image:\', this.src)">' +
                    '<div class="tinymce-media-info">' +
                    '<div class="tinymce-media-name">' + image.name + '</div>' +
                    '<div class="tinymce-media-details">' + (image.dimensions || 'Unknown size') + '</div>' +
                    '</div>' +
                    '</div>';
        });
        
        // Create temporary container and append to existing content
        var tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        
        while (tempDiv.firstChild) {
            mediaGrid.appendChild(tempDiv.firstChild);
        }
        
        console.log('✅ DEBUG: New images appended to gallery');
    }
}

// Setup infinite scroll functionality
function setupInfiniteScroll() {
    console.log('♾️ DEBUG: Setting up infinite scroll');
    
    var mediaGrid = document.getElementById('mediaGrid');
    if (!mediaGrid) {
        console.error('❌ DEBUG: mediaGrid not found for infinite scroll setup');
        return;
    }
    
    // Remove existing scroll listener to prevent duplicates
    mediaGrid.removeEventListener('scroll', handleInfiniteScroll);
    
    // Add scroll event listener
    mediaGrid.addEventListener('scroll', handleInfiniteScroll);
    console.log('✅ DEBUG: Infinite scroll event listener added');
}

// Handle infinite scroll
function handleInfiniteScroll() {
    var mediaGrid = document.getElementById('mediaGrid');
    if (!mediaGrid) return;
    
    // Check if we're near the bottom (within 100px)
    var scrollTop = mediaGrid.scrollTop;
    var scrollHeight = mediaGrid.scrollHeight;
    var clientHeight = mediaGrid.clientHeight;
    var scrollThreshold = 100;
    
    var nearBottom = scrollTop + clientHeight >= scrollHeight - scrollThreshold;
    
    console.log('📊 DEBUG: Scroll position - scrollTop:', scrollTop, 'clientHeight:', clientHeight, 'scrollHeight:', scrollHeight, 'nearBottom:', nearBottom);
    
    if (nearBottom && window.galleryState.hasMore && !window.galleryState.isLoading) {
        console.log('🔄 DEBUG: Loading next page for infinite scroll');
        var nextPage = window.galleryState.currentPage + 1;
        loadMediaGallery(window.galleryState.currentFolder, nextPage, true);
    }
}

// Update folder navigation
function updateFolderNavigation(folders, currentFolder) {
    var folderNav = document.getElementById('folderNavigation');
    if (!folderNav) return;

    var html = '<div class="breadcrumb">';
    html += '<span class="breadcrumb-item" onclick="loadMediaGallery(\'\')">📁 Root</span>';
    
    if (currentFolder) {
        var parts = currentFolder.split('/');
        var path = '';
        parts.forEach(function(part, index) {
            path += (index > 0 ? '/' : '') + part;
            html += ' > <span class="breadcrumb-item" onclick="loadMediaGallery(\'' + path + '\')">' + part + '</span>';
        });
    }
    html += '</div>';

    folderNav.innerHTML = html;
}

// Toggle image selection in gallery
function toggleImageSelection(item) {
    console.log('🎯 DEBUG: toggleImageSelection called for item:', item);
    
    var isMultiple = window.currentTinyMCEMode === 'multiple';
    console.log('📊 DEBUG: Selection mode:', isMultiple ? 'multiple' : 'single');
    
    // Initialize selectedMediaItems if not exists
    if (!window.selectedMediaItems) {
        window.selectedMediaItems = [];
    }
    
    if (!isMultiple) {
        // Single selection - clear others
        document.querySelectorAll('.tinymce-media-item.selected').forEach(function(selected) {
            selected.classList.remove('selected');
        });
        window.selectedMediaItems = [];
        console.log('🧹 DEBUG: Cleared previous selections for single mode');
    }
    
    if (item.classList.contains('selected')) {
        // Deselect
        item.classList.remove('selected');
        window.selectedMediaItems = window.selectedMediaItems.filter(function(mediaItem) {
            return mediaItem.url !== item.dataset.url;
        });
        console.log('❌ DEBUG: Deselected item:', item.dataset.url);
    } else {
        // Select
        item.classList.add('selected');
        var imageData = {
            url: item.dataset.url,
            name: item.dataset.name || 'Unknown',
            element: item
        };
        window.selectedMediaItems.push(imageData);
        console.log('✅ DEBUG: Selected item:', imageData);
    }
    
    console.log('📋 DEBUG: Total selected items:', window.selectedMediaItems.length);
    updateInsertButton();
}

// Legacy function for backward compatibility
function toggleMediaSelection(item) {
    return toggleImageSelection(item);
}

// Update insert button state
function updateInsertButton() {
    var insertBtn = document.getElementById('insert-btn');
    var selectedCount = document.getElementById('selected-count');
    var hasUploadFiles = window.selectedFiles && window.selectedFiles.length > 0;
    var hasMediaSelection = window.selectedMediaItems && window.selectedMediaItems.length > 0;
    
    var totalSelected = (hasUploadFiles ? window.selectedFiles.length : 0) + 
                       (hasMediaSelection ? window.selectedMediaItems.length : 0);
    
    insertBtn.disabled = totalSelected === 0;
    
    var maxSelection = window.currentTinyMCEMode === 'multiple' ? 'multiple' : '1';
    selectedCount.textContent = totalSelected + ' of ' + maxSelection + ' selected';
}

// Handle insert action
function handleInsert() {
    var activeTab = document.querySelector('.tinymce-tab-content.active');
    
    if (activeTab.id === 'upload-tab' && window.selectedFiles && window.selectedFiles.length > 0) {
        // Upload new files
        var insertBtn = document.getElementById('insert-btn');
        insertBtn.textContent = 'Uploading...';
        insertBtn.disabled = true;
        uploadImages(document.querySelector('.tinymce-modal').id);
    } else if (activeTab.id === 'gallery-tab' && window.selectedMediaItems && window.selectedMediaItems.length > 0) {
        // Insert selected media
        insertSelectedMedia();
    }
}

// Insert selected media items
function insertSelectedMedia() {
    console.log('🚀 DEBUG: insertSelectedMedia called');
    console.log('📊 DEBUG: selectedMediaItems:', window.selectedMediaItems);
    
    var editor = window.currentTinyMCEEditor;
    var callback = window.currentTinyMCECallback;
    var mode = window.currentTinyMCEMode;
    
    console.log('🎯 DEBUG: Insert mode:', mode);
    console.log('📝 DEBUG: Editor:', editor);
    console.log('📞 DEBUG: Callback:', callback);
    
    if (!window.selectedMediaItems || window.selectedMediaItems.length === 0) {
        console.error('❌ DEBUG: No media items selected');
        return;
    }
    
    if (mode === "picker" && callback) {
        // File picker mode
        console.log('📁 DEBUG: Using picker mode');
        callback(window.selectedMediaItems[0].url);
    } else if (mode === "multiple") {
        // Gallery mode
        console.log('🖼️ DEBUG: Using multiple gallery mode');
        var galleryDiv = document.createElement("div");
        galleryDiv.className = "image-gallery";
        
        window.selectedMediaItems.forEach(function(media, index) {
            console.log('🖼️ DEBUG: Adding image', index + 1, ':', media.url);
            var imgElement = document.createElement("img");
            imgElement.src = media.url;
            imgElement.alt = media.name || 'Image';
            imgElement.style.maxWidth = '200px';
            imgElement.style.height = 'auto';
            imgElement.style.margin = '5px';
            galleryDiv.appendChild(imgElement);
        });
        
        console.log('📝 DEBUG: Inserting gallery HTML:', galleryDiv.outerHTML);
        editor.insertContent(galleryDiv.outerHTML);
    } else {
        // Single image mode
        console.log('🖼️ DEBUG: Using single image mode');
        var media = window.selectedMediaItems[0];
        var imgHtml = '<img src="' + media.url + '" alt="' + (media.name || 'Image') + '" style="max-width: 100%; height: auto;" />';
        console.log('📝 DEBUG: Inserting single image HTML:', imgHtml);
        editor.insertContent(imgHtml);
    }
    
    console.log('✅ DEBUG: Content inserted successfully');
    closeModal(document.querySelector('.tinymce-modal').id);
}

function uploadImages(modalId) {
    console.log('📤 DEBUG: uploadImages called with modalId:', modalId);
    
    if (!window.selectedFiles || window.selectedFiles.length === 0) {
        console.error('❌ DEBUG: No files selected for upload');
        return;
    }
    
    console.log('📁 DEBUG: Uploading', window.selectedFiles.length, 'files');
    
    var uploadPromises = window.selectedFiles.map(function(file, index) {
        return new Promise(function(resolve, reject) {
            console.log('📤 DEBUG: Starting upload for file', index + 1, ':', file.name);
            
            var formData = new FormData();
            formData.append('file', file); // Changed from 'image' to 'file' to match upload.php
            
            // No additional form data needed - just upload the file
            
            var xhr = new XMLHttpRequest();
            
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    var percentComplete = (e.loaded / e.total) * 100;
                    console.log('📊 DEBUG: Upload progress for', file.name, ':', percentComplete.toFixed(1) + '%');
                }
            });
            
            xhr.addEventListener('load', function() {
                console.log('📡 DEBUG: Upload response status:', xhr.status);
                console.log('📡 DEBUG: Upload response text:', xhr.responseText);
                console.log('📡 DEBUG: Response headers:', xhr.getAllResponseHeaders());
                
                if (xhr.status === 200) {
                    try {
                        // Check if response is empty or contains HTML error
                        if (!xhr.responseText || xhr.responseText.trim() === '') {
                            console.error('❌ DEBUG: Empty response from server');
                            reject(new Error('Empty response from server'));
                            return;
                        }
                        
                        // Check if response starts with HTML (error page)
                        if (xhr.responseText.trim().startsWith('<')) {
                            console.error('❌ DEBUG: Server returned HTML instead of JSON');
                            console.error('❌ DEBUG: HTML response:', xhr.responseText.substring(0, 200));
                            reject(new Error('Server error: HTML response instead of JSON'));
                            return;
                        }
                        
                        var response = JSON.parse(xhr.responseText);
                        console.log('📦 DEBUG: Parsed response:', response);
                        
                        if (response.error) {
                            console.error('❌ DEBUG: Server returned error:', response.error);
                            reject(new Error('Server error: ' + response.error));
                        } else if (response.location) {
                            console.log('✅ DEBUG: Upload successful:', response.location);
                            resolve({
                                url: response.location,
                                filename: response.filename || file.name,
                                title: file.name,
                                alt: file.name
                            });
                        } else {
                            console.error('❌ DEBUG: No location in response:', response);
                            reject(new Error('Invalid response: no location field'));
                        }
                    } catch (e) {
                        console.error('❌ DEBUG: Failed to parse JSON response:', e);
                        console.error('❌ DEBUG: Raw response:', xhr.responseText);
                        reject(new Error('Invalid JSON response: ' + e.message));
                    }
                } else {
                    console.error('❌ DEBUG: Upload failed with status:', xhr.status);
                    console.error('❌ DEBUG: Status text:', xhr.statusText);
                    reject(new Error('Upload failed: HTTP ' + xhr.status + ' - ' + xhr.statusText));
                }
            });
            
            xhr.addEventListener('error', function() {
                console.error('❌ DEBUG: Upload network error for:', file.name);
                reject(new Error('Network error during upload'));
            });
            
            xhr.open('POST', '/ivsportal/includes/plugins/tinymce/upload.php');
            xhr.send(formData);
        });
    });
    
    Promise.all(uploadPromises).then(function(results) {
        console.log('🎉 DEBUG: All uploads completed successfully');
        console.log('📊 DEBUG: Upload results:', results);
        
        var editor = window.currentTinyMCEEditor;
        var callback = window.currentTinyMCECallback;
        var mode = window.currentTinyMCEMode;
        
        console.log('🎯 DEBUG: Processing results for mode:', mode);
        
        if (mode === "picker" && callback) {
            // File picker mode - return single image
            console.log('📁 DEBUG: Using picker mode, returning:', results[0].url);
            callback(results[0].url);
        } else if (mode === "multiple") {
            // Gallery mode - insert multiple images
            console.log('🖼️ DEBUG: Creating gallery with', results.length, 'images');
            var galleryDiv = document.createElement("div");
            galleryDiv.className = "image-gallery";
            
            results.forEach(function(img, index) {
                console.log('🖼️ DEBUG: Adding image', index + 1, 'to gallery:', img);
                
                if (img.title) {
                    var imgContainer = document.createElement("div");
                    imgContainer.className = "image-with-title";
                    
                    var imgElement = document.createElement("img");
                    imgElement.src = img.url;
                    imgElement.alt = img.alt || img.title;
                    imgElement.style.maxWidth = '200px';
                    imgElement.style.height = 'auto';
                    imgElement.style.margin = '5px';
                    
                    var titleSpan = document.createElement("span");
                    titleSpan.className = "image-title";
                    titleSpan.textContent = img.title;
                    
                    imgContainer.appendChild(imgElement);
                    imgContainer.appendChild(titleSpan);
                    galleryDiv.appendChild(imgContainer);
                } else {
                    var imgElement = document.createElement("img");
                    imgElement.src = img.url;
                    imgElement.alt = img.alt || img.filename;
                    imgElement.style.maxWidth = '200px';
                    imgElement.style.height = 'auto';
                    imgElement.style.margin = '5px';
                    galleryDiv.appendChild(imgElement);
                }
            });
            
            console.log('📝 DEBUG: Inserting gallery HTML:', galleryDiv.outerHTML);
            editor.insertContent(galleryDiv.outerHTML);
        } else {
            // Single image mode - but if multiple files uploaded, insert all
            if (results.length > 1) {
                console.log('🖼️ DEBUG: Multiple images uploaded in single mode, inserting all');
                results.forEach(function(img, index) {
                    var imageHtml = '<img src="' + img.url + '" alt="' + (img.alt || img.filename) + '" style="max-width: 100%; height: auto; margin: 5px;" />';
                    console.log('📝 DEBUG: Inserting image', index + 1, ':', imageHtml);
                    editor.insertContent(imageHtml);
                });
            } else {
                console.log('🖼️ DEBUG: Inserting single image');
                var img = results[0];
                if (img.title) {
                    var imageHtml = '<div class="image-with-title">';
                    imageHtml += '<img src="' + img.url + '" alt="' + img.alt + '" title="' + img.title + '" style="max-width: 100%; height: auto;" />';
                    imageHtml += '<span class="image-title">' + img.title + '</span>';
                    imageHtml += '</div>';
                    console.log('📝 DEBUG: Inserting titled image HTML:', imageHtml);
                    editor.insertContent(imageHtml);
                } else {
                    var imageHtml = '<img src="' + img.url + '" alt="' + img.alt + '" style="max-width: 100%; height: auto;" />';
                    console.log('📝 DEBUG: Inserting simple image HTML:', imageHtml);
                    editor.insertContent(imageHtml);
                }
            }
        }
        
        console.log('✅ DEBUG: Upload and insert completed successfully');
        closeModal(modalId);
        
        // Reset upload state
        window.selectedFiles = [];
        var previewGrid = document.getElementById('upload-preview');
        if (previewGrid) {
            previewGrid.innerHTML = '';
        }
        
        // Reset insert button
        var insertBtn = document.getElementById('insert-btn');
        if (insertBtn) {
            insertBtn.textContent = 'Insert';
            insertBtn.disabled = true;
        }
        
    }).catch(function(error) {
        console.error('💥 DEBUG: Upload failed:', error);
        alert("Upload failed: " + error.message);
        
        // Reset button state
        var insertBtn = document.getElementById('insert-btn');
        if (insertBtn) {
            insertBtn.textContent = 'Insert';
            insertBtn.disabled = false;
        }
    });
}

function openVideoModal(editor) {
    editor.windowManager.open({
        title: "Insert Video",
        body: {
            type: "panel",
            items: [
                {
                    type: "urlinput",
                    name: "source",
                    label: "Video URL (MP4)",
                    placeholder: "https://example.com/video.mp4"
                },
                {
                    type: "urlinput",
                    name: "poster",
                    label: "Poster Image URL (Optional)",
                    placeholder: "https://example.com/poster.jpg"
                },
                {
                    type: "input",
                    name: "width",
                    label: "Width",
                    placeholder: "560"
                },
                {
                    type: "input",
                    name: "height",
                    label: "Height", 
                    placeholder: "315"
                }
            ]
        },
        buttons: [
            {
                type: "cancel",
                text: "Cancel"
            },
            {
                type: "submit",
                text: "Insert Video",
                primary: true
            }
        ],
        onSubmit: function(api) {
            var data = api.getData();
            if (data.source) {
                var width = data.width || "560";
                var height = data.height || "315";
                var poster = data.poster ? ' poster="' + data.poster + '"' : "";
                
                var videoHtml = '<video width="' + width + '" height="' + height + '"' + poster + ' controls style="max-width: 100%; height: auto; margin: 10px 0;">' +
                    '<source src="' + data.source + '" type="video/mp4" />' +
                    'Your browser does not support the video tag.' +
                    '</video>';
                
                editor.insertContent(videoHtml);
            }
            api.close();
        }
    });
}

function closeModal(modalId) {
    var modal = document.getElementById(modalId);
    if (modal) {
        modal.remove();
    }
    // Clean up global variables
    window.currentTinyMCEEditor = null;
    window.currentTinyMCECallback = null;
    window.currentTinyMCEMode = null;
    window.selectedFiles = null;
}
