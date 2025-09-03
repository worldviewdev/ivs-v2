// AI Inline Assistant for TinyMCE
// Handles AI-powered text generation with inline interface

// Create AI inline interface
function createAIInline(editor) {
    const aiId = 'tinymce-ai-inline';
    
    // Remove existing AI interface if any
    const existingAI = document.getElementById(aiId);
    if (existingAI) {
        existingAI.remove();
    }

    const aiContainer = document.createElement('div');
    aiContainer.id = aiId;
    aiContainer.className = 'ai-inline-container';
    aiContainer.innerHTML = `
        <div class="ai-inline-header">
            <span class="ai-inline-title">AI Assistant</span>
            <button class="ai-inline-close" onclick="closeAIInline()">&times;</button>
        </div>
        <div class="ai-inline-body">
            <div id="ai-result-preview" class="ai-result-preview" style="display: none;">
                <div class="ai-result-content-wrapper">
                    <textarea id="ai-result-content" class="ai-result-content" placeholder="AI generated content will appear here..."></textarea>
                </div>
                <div class="ai-result-actions">
                    <div class="ai-result-action-btn">
                    <button class="ai-action-btn ai-insert-btn" onclick="insertAIResult()">Insert</button>
                    <button class="ai-action-btn ai-retry-btn" onclick="retryAIRequest()">Try again</button>
                    <button class="ai-action-btn ai-stop-btn" onclick="stopAIRequest()">Stop</button>
                    </div>
                    <div class="ai-disclaimer">AI responses can be inaccurate</div>
                </div>
            </div>
        </div>
        <div class="ai-inline-input-container">
            <input type="text" id="ai-inline-prompt" placeholder="Ask AI to edit or generate..." />
            <button id="ai-inline-submit" onclick="processAIPrompt()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                </svg>
            </button>
        </div>
    `;

    // Append directly to document body for guaranteed visibility
    document.body.appendChild(aiContainer);
    
    console.log('AI container added to body:', aiContainer);
    
    return aiContainer;
}

// Open AI inline interface
function openAIModal(editor) {
    console.log('🤖 Opening AI Assistant');
    
    // Store editor reference
    window.currentAIEditor = editor;
    
    // Create and show inline interface
    const aiContainer = createAIInline(editor);
    
    // Get selected text if any
    const selectedText = editor.selection.getContent({format: 'text'});
    if (selectedText) {
        window.selectedAIText = selectedText;
        document.getElementById('ai-inline-prompt').placeholder = `Edit selected text: "${selectedText.substring(0, 50)}${selectedText.length > 50 ? '...' : ''}"`;
    }
    
    // Focus on input and add keyboard support
    setTimeout(() => {
        const input = document.getElementById('ai-inline-prompt');
        input.focus();
        
        // Add Enter key support
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                processAIPrompt();
            }
            if (e.key === 'Escape') {
                closeAIInline();
            }
        });
    }, 100);
}

// Close AI inline interface
function closeAIInline() {
    const aiContainer = document.getElementById('tinymce-ai-inline');
    if (aiContainer) {
        aiContainer.remove();
    }
    window.currentAIEditor = null;
    window.selectedAIText = null;
}

// Set AI prompt from suggestion
function setAIPrompt(prompt) {
    document.getElementById('ai-inline-prompt').value = prompt;
    document.getElementById('ai-inline-prompt').focus();
}

// Process AI prompt
function processAIPrompt() {
    const prompt = document.getElementById('ai-inline-prompt').value.trim();
    if (!prompt) return;
    
    const editor = window.currentAIEditor;
    if (!editor) return;
    
    // Disable submit button and show processing
    const submitBtn = document.getElementById('ai-inline-submit');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<div style="width:12px;height:12px;border:2px solid #fff;border-top:2px solid transparent;border-radius:50%;animation:spin 1s linear infinite;"></div>';
    
    // Get context
    let context = '';
    if (window.selectedAIText) {
        context = window.selectedAIText;
    } else {
        // Get some context from editor content
        const content = editor.getContent({format: 'text'});
        context = content.substring(0, 500);
    }
    
    // Make AI request
    makeAIRequest(prompt, context);
}

// Make AI request
function makeAIRequest(prompt, context = '') {
    const formData = new FormData();
    formData.append('action', 'generate');
    formData.append('prompt', prompt);
    formData.append('context', context);
    
    // Use absolute path from document root
    const aiHelperPath = '/ivsportal/includes/plugins/tinymce/ai_helper.php';
    
    fetch(aiHelperPath, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.text();
    })
    .then(text => {
        console.log('Response text:', text);
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('JSON parse error:', e);
            throw new Error('Invalid JSON response: ' + text.substring(0, 200));
        }
        return data;
    })
    .then(data => {
        // Reset submit button
        const submitBtn = document.getElementById('ai-inline-submit');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>';
        
        if (data.success) {
            // Show AI result in preview area
            document.getElementById('ai-result-content').value = data.content;
            document.getElementById('ai-result-preview').style.display = 'block';
            
            // Store result for later use
            window.currentAIResult = data.content;
        } else {
            alert('AI Error: ' + (data.error || 'An error occurred'));
        }
    })
    .catch(error => {
        // Reset submit button
        const submitBtn = document.getElementById('ai-inline-submit');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>';
        
        alert('Network error: ' + error.message);
    });
}

// Insert AI result into editor
function insertAIResult() {
    const editor = window.currentAIEditor;
    const content = document.getElementById('ai-result-content').value;
    
    if (editor && content) {
        if (window.selectedAIText) {
            // Replace selected text
            editor.selection.setContent(content);
        } else {
            // Insert at cursor position
            editor.insertContent(content);
        }
        
        // Close modal after insertion
        closeAIInline();
    }
}

// Retry AI request with same prompt
function retryAIRequest() {
    const prompt = document.getElementById('ai-inline-prompt').value.trim();
    if (prompt) {
        // Hide result preview
        document.getElementById('ai-result-preview').style.display = 'none';
        // Process prompt again
        processAIPrompt();
    }
}

// Stop AI request and close modal
function stopAIRequest() {
    closeAIInline();
}

// Show AI error
function showAIError(error) {
    document.getElementById('ai-inline-result-content').innerHTML = `<div class="ai-error">${error}</div>`;
    document.getElementById('ai-inline-result').style.display = 'block';
}

// Accept AI result
function acceptAIResult() {
    const editor = window.currentAIEditor;
    if (!editor || !window.lastAIResult) return;
    
    if (window.selectedAIText) {
        // Replace selected text
        editor.selection.setContent(window.lastAIResult);
    } else {
        // Insert at cursor
        editor.insertContent(window.lastAIResult);
    }
    
    closeAIInline();
}

// Reject AI result
function rejectAIResult() {
    document.getElementById('ai-inline-result').style.display = 'none';
    document.getElementById('ai-inline-prompt').focus();
}

// Copy AI result
function copyAIResult() {
    if (!window.lastAIResult) return;
    
    navigator.clipboard.writeText(window.lastAIResult.replace(/<[^>]*>/g, '')).then(() => {
        // Show brief feedback
        const copyBtn = document.querySelector('.ai-copy-btn');
        const originalText = copyBtn.textContent;
        copyBtn.textContent = 'Copied!';
        setTimeout(() => {
            copyBtn.textContent = originalText;
        }, 1000);
    });
}

// Show loading state
function showAILoading() {
    document.getElementById('ai-loading').style.display = 'block';
    document.getElementById('ai-result').style.display = 'none';
    
    // Disable all action buttons
    document.querySelectorAll('.ai-action-btn').forEach(btn => btn.disabled = true);
}

// Hide loading state
function hideAILoading() {
    document.getElementById('ai-loading').style.display = 'none';
    
    // Re-enable all action buttons
    document.querySelectorAll('.ai-action-btn').forEach(btn => btn.disabled = false);
}

// Show AI result
function showAIResult(content) {
    document.getElementById('ai-result-content').innerHTML = content.replace(/\n/g, '<br>');
    document.getElementById('ai-result').style.display = 'block';
    window.currentAIResult = content;
}

// Show AI error
function showAIError(error) {
    document.getElementById('ai-result-content').innerHTML = `<div class="ai-error">Error: ${error}</div>`;
    document.getElementById('ai-result').style.display = 'block';
    window.currentAIResult = null;
}

// Generate AI content
function generateAIContent() {
    const prompt = document.getElementById('ai-generate-prompt').value.trim();
    const context = document.getElementById('ai-generate-context').value.trim();
    
    if (!prompt) {
        alert('Please enter a prompt');
        return;
    }
    
    showAILoading();
    
    const formData = new FormData();
    formData.append('action', 'generate');
    formData.append('prompt', prompt);
    formData.append('context', context);
    
    fetch('/ivsportal/includes/plugins/tinymce/ai_helper.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideAILoading();
        if (data.success) {
            showAIResult(data.content);
        } else {
            showAIError(data.error);
        }
    })
    .catch(error => {
        hideAILoading();
        showAIError('Network error: ' + error.message);
    });
}

// Improve AI content
function improveAIContent() {
    const text = document.getElementById('ai-improve-text').value.trim();
    const instruction = document.getElementById('ai-improve-type').value;
    
    if (!text) {
        alert('Please enter text to improve');
        return;
    }
    
    showAILoading();
    
    const formData = new FormData();
    formData.append('action', 'improve');
    formData.append('text', text);
    formData.append('instruction', instruction);
    
    fetch('/ivsportal/includes/plugins/tinymce/ai_helper.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideAILoading();
        if (data.success) {
            showAIResult(data.content);
        } else {
            showAIError(data.error);
        }
    })
    .catch(error => {
        hideAILoading();
        showAIError('Network error: ' + error.message);
    });
}

// Translate AI content
function translateAIContent() {
    const text = document.getElementById('ai-translate-text').value.trim();
    const language = document.getElementById('ai-translate-language').value;
    
    if (!text) {
        alert('Please enter text to translate');
        return;
    }
    
    showAILoading();
    
    const formData = new FormData();
    formData.append('action', 'translate');
    formData.append('text', text);
    formData.append('language', language);
    
    fetch('/ivsportal/includes/plugins/tinymce/ai_helper.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideAILoading();
        if (data.success) {
            showAIResult(data.content);
        } else {
            showAIError(data.error);
        }
    })
    .catch(error => {
        hideAILoading();
        showAIError('Network error: ' + error.message);
    });
}

// Generate itinerary
function generateItinerary() {
    const destination = document.getElementById('ai-itinerary-destination').value.trim();
    const duration = document.getElementById('ai-itinerary-duration').value.trim();
    const activities = document.getElementById('ai-itinerary-activities').value.trim();
    
    if (!destination) {
        alert('Please enter a destination');
        return;
    }
    
    showAILoading();
    
    const formData = new FormData();
    formData.append('action', 'itinerary');
    formData.append('destination', destination);
    formData.append('duration', duration || '1 day');
    formData.append('activities', activities);
    
    fetch('/ivsportal/includes/plugins/tinymce/ai_helper.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideAILoading();
        if (data.success) {
            showAIResult(data.content);
        } else {
            showAIError(data.error);
        }
    })
    .catch(error => {
        hideAILoading();
        showAIError('Network error: ' + error.message);
    });
}

// Insert AI result to editor
function insertAIResult() {
    if (!window.currentAIResult || !window.currentAIEditor) {
        alert('No content to insert');
        return;
    }
    
    // Convert line breaks to HTML
    const htmlContent = window.currentAIResult.replace(/\n/g, '<br>');
    
    // Insert content to editor
    window.currentAIEditor.insertContent(htmlContent);
    
    // Close modal
    closeAIModal();
}

// Copy AI result to clipboard
function copyAIResult() {
    if (!window.currentAIResult) {
        alert('No content to copy');
        return;
    }
    
    navigator.clipboard.writeText(window.currentAIResult).then(() => {
        alert('Content copied to clipboard');
    }).catch(err => {
        console.error('Failed to copy: ', err);
        alert('Failed to copy content');
    });
}

// Clear AI result
function clearAIResult() {
    document.getElementById('ai-result').style.display = 'none';
    window.currentAIResult = null;
}

// Close modal on backdrop click
document.addEventListener('click', function(e) {
    const modal = document.getElementById('tinymce-ai-modal');
    if (modal && e.target === modal) {
        closeAIModal();
    }
});
