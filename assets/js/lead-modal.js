/**
 * Lead Modal Handler
 * Handles the display of lead details in a modal
 */
class LeadModal {
    constructor() {
        this.init();
    }

    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.bindEvents();
        });
    }

    bindEvents() {
        // Handle view lead button clicks
        document.querySelectorAll('.view-lead-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const leadId = button.getAttribute('data-lead-id');
                this.loadLeadDetails(leadId);
            });
        });
    }

    loadLeadDetails(leadId) {
        const contentDiv = document.getElementById('lead-details-content');
        
        if (!contentDiv) {
            console.error('Lead details content div not found');
            return;
        }

        // Show loading spinner
        this.showLoading(contentDiv);
        
        // Fetch lead data
        fetch(`quick-contact.php?act=get_lead&id=${leadId}`)
            .then(response => {
                console.log('Response status:', response);
                return response.json();
            })
            .then(data => {
                console.log('Lead data:', data);
                
                if (data.error) {
                    this.showError(contentDiv, data.error);
                    return;
                }
                
                this.displayLeadDetails(contentDiv, data);
            })
            .catch(error => {
                console.error('Fetch error:', error);
                this.showError(contentDiv, 'Failed to load lead details. Please try again.');
            });
    }

    showLoading(contentDiv) {
        contentDiv.innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3">Loading lead details...</p>
            </div>
        `;
    }

    showError(contentDiv, errorMessage) {
        contentDiv.innerHTML = `
            <div class="alert alert-danger">
                <strong>Error:</strong> ${errorMessage}
            </div>
        `;
    }

    displayLeadDetails(contentDiv, data) {
        const leadHtml = `
            <div class="row g-6">
                <div class="col-md-6">
                    <div class="d-flex flex-column mb-5">
                        <label class="fs-6 fw-semibold mb-2">Name</label>
                        <div class="form-control form-control-solid">${this.cleanValue(data.name)}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex flex-column mb-5">
                        <label class="fs-6 fw-semibold mb-2">Email</label>
                        <div class="form-control form-control-solid">${this.cleanValue(data.email)}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex flex-column mb-5">
                        <label class="fs-6 fw-semibold mb-2">Phone</label>
                        <div class="form-control form-control-solid">${this.cleanValue(data.phone)}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex flex-column mb-5">
                        <label class="fs-6 fw-semibold mb-2">File Code</label>
                        <div class="form-control form-control-solid">${this.cleanValue(data.file_code) || 'No File Created'}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex flex-column mb-5">
                        <label class="fs-6 fw-semibold mb-2">Travel Date</label>
                        <div class="form-control form-control-solid">${this.cleanValue(data.dates_for_travel)}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex flex-column mb-5">
                        <label class="fs-6 fw-semibold mb-2">Date Added</label>
                        <div class="form-control form-control-solid">${this.formatDate(data.added_on)}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex flex-column mb-5">
                        <label class="fs-6 fw-semibold mb-2">Adults</label>
                        <div class="form-control form-control-solid">${data.adults || '0'}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex flex-column mb-5">
                        <label class="fs-6 fw-semibold mb-2">Children</label>
                        <div class="form-control form-control-solid">${data.children || '0'}</div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="d-flex flex-column mb-5">
                        <label class="fs-6 fw-semibold mb-2">Tour Code</label>
                        <div class="form-control form-control-solid">${this.decodeHtmlEntities(data.tour_code) || 'N/A'}</div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="d-flex flex-column mb-5">
                        <label class="fs-6 fw-semibold mb-2">Message/Query</label>
                        <div class="form-control form-control-solid" style="min-height: 100px; white-space: pre-wrap;">${this.cleanValue(data.message)}</div>
                    </div>
                </div>
            </div>
        `;
        
        contentDiv.innerHTML = leadHtml;
    }

    formatDate(dateString) {
        if (!dateString) return 'N/A';
        
        try {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-GB', {
                day: 'numeric',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        } catch (error) {
            console.error('Date formatting error:', error);
            return dateString;
        }
    }

    cleanValue(value) {
        if (!value || value.trim() === '') return 'N/A';
        return this.escapeHtml(value.toString());
    }

    decodeHtmlEntities(text) {
        if (!text) return '';
        
        const textarea = document.createElement('textarea');
        textarea.innerHTML = text;
        return this.escapeHtml(textarea.value);
    }

    escapeHtml(text) {
        if (!text) return '';
        
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize the lead modal when the script loads
const leadModal = new LeadModal();
