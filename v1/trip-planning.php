<?php
require_once('header.inc.php');
?>
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        <div class="card card-flush">
            <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                    <div class="input-group w-250px">
                        <input class="form-control form-control-solid rounded rounded-end-0" placeholder="Pick date range" id="kt_ecommerce_sales_flatpickr" />
                        <button class="btn btn-icon btn-light" id="kt_ecommerce_sales_flatpickr_clear">
                            <i class="ki-outline ki-cross fs-2"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body pt-0">
                <table id="kt_datatable_vertical_scroll" class="table table-row-bordered gy-5 dataTable gs-7">
                    <thead>
                        <tr class="fw-semibold fs-6 text-gray-800">
                            <th>File Code</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Departure Date</th>
                            <th>No of Adult</th>
                            <th>Number of Nights</th>
                            <th>Comments</th>
                            <th>Date Added</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data akan diisi oleh DataTable AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal for viewing trip planning details -->
<div class="modal fade" id="kt_modal_new_target" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content shadow-lg border-0">
            <!-- Header -->
            <div class="modal-header bg-primary text-white border-0">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-40px me-3">
                        <div class="symbol-label bg-white bg-opacity-20">
                            <i class="ki-outline ki-calendar fs-2 text-white"></i>
                        </div>
                    </div>
                    <div>
                        <h2 class="fw-bold text-white mb-0">Trip Planning Details</h2>
                        <p class="text-white-75 mb-0 fs-7">Complete information about the trip planning request</p>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-icon btn-light btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="ki-outline ki-cross fs-2"></i>
                </button>
            </div>
            
            <!-- Body -->
            <div class="modal-body p-0">
                <div id="lead-details-content">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            
            <!-- Footer -->
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">
                    <i class="ki-outline ki-cross fs-5 me-1"></i>Close
                </button>
                <button type="button" class="btn btn-primary btn-sm">
                    <i class="ki-outline ki-printer fs-5 me-1"></i>Print Details
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        // Initialize DataTable for Trip Planning using reusable component
        const tripPlanningTable = createDataTable('trip_planning', {
            ajax: {
                url: '<?php echo SITE_WS_PATH; ?>/api/trip-planning/all'
            },
            columns: [
                {
                    data: 'file_code',
                    render: function(data, type, row) {
                        return `<a href="files/file_summary_general.php?id=${row.fk_file_id}" target="_blank" class="text-primary text-hover-primary fw-bold">${data}</a>`;
                    }
                },
                {
                    data: 'name'
                },
                {
                    data: 'email'
                },
                {
                    data: 'phone'
                },
                {
                    data: 'dates_for_travel',
                    render: function(data) {
                        return new Date(data).toLocaleDateString('en-US', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric'
                        });
                    }
                },
                {
                    data: 'people_in_group',
                    render: function(data) {
                        return data || 0;
                    }
                },
                {
                    data: 'how_many_week'
                },
                {
                    data: 'comments',
                    render: function(data) {
                        return data ? data.substring(0, 35) + '...' : '';
                    }
                },
                {
                    data: 'created_at',
                    render: function(data) {
                        return new Date(data).toLocaleDateString('en-US', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    }
                },
                {
                    data: 'id',
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                            <a href="#" class="btn btn-sm btn-icon btn-primary view-lead-btn me-2" title="Lihat" data-lead-id="${data}" data-bs-toggle="modal" data-bs-target="#kt_modal_new_target">
                                <i class="ki-outline ki-eye fs-5"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-icon btn-danger delete-lead-btn" title="Hapus" data-lead-id="${data}">
                                <i class="ki-outline ki-trash fs-5"></i>
                            </a>
                        `;
                    }
                }
            ]
        });
        // Initialize Flatpickr for date picker
        const flatpickrElement = document.querySelector('#kt_ecommerce_sales_flatpickr');
        if (flatpickrElement) {
            const flatpickr = $(flatpickrElement).flatpickr({
                altInput: true,
                altFormat: "d/m/Y",
                dateFormat: "Y-m-d",
                mode: "range",
                onChange: function(selectedDates, dateStr, instance) {
                    // Trigger DataTable refresh when date changes
                    if (tripPlanningTable) {
                        tripPlanningTable.refresh();
                    }
                },
            });
            // Handle clear button
            const clearButton = document.querySelector('#kt_ecommerce_sales_flatpickr_clear');
            if (clearButton) {
                clearButton.addEventListener('click', function() {
                    flatpickr.clear();
                    if (tripPlanningTable) {
                        tripPlanningTable.refresh();
                    }
                });
            }
        }
        // Handle view lead button click
        $(document).on('click', '.view-lead-btn', function(e) {
            e.preventDefault();
            const leadId = $(this).data('lead-id');
            // Load lead details via AJAX
            $.ajax({
                url: '<?php echo SITE_WS_PATH; ?>/api/trip-planning/' + leadId,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.error) {
                        alert('Error: ' + response.error);
                        return;
                    }
                    // Format travel date
                    const travelDate = response.dates_for_travel ? 
                        new Date(response.dates_for_travel).toLocaleDateString('en-US', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric'
                        }) : 'N/A';
                    
                    // Format created date
                    const createdDate = response.created_at ? 
                        new Date(response.created_at).toLocaleString('en-US', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        }) : 'N/A';
                    
                    // Function to get travel type label
                    function getTravelTypeLabel(typeValue) {
                        const travelTypeMap = {
                            '1': "Custom Trip Package: Be on your own schedule. Activities or day tours can be private or shared.",
                            '2': "Scheduled Group Tour: Join a multi-day, guided group tour with fixed departure dates.",
                            '3': "I would like my Travel Specialists to make suggestions based on my interests."
                        };
                        
                        if (!typeValue) return 'N/A';
                        
                        // Handle multiple values separated by comma
                        if (typeValue.includes(',')) {
                            const values = typeValue.split(',').map(v => v.trim());
                            const labels = values.map(val => travelTypeMap[val] || val);
                            return labels.join('<br>• ');
                        }
                        
                        // Handle single value
                        return travelTypeMap[typeValue] || typeValue;
                    }
                    
                    // Populate modal with clean, modern design
                    const modalContent = `
                        <div class="p-8">
                            <!-- Header Info Card -->
                            <div class="card bg-gradient-primary text-white mb-6">
                                <div class="card-body p-6">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-50px me-4">
                                            <div class="symbol-label bg-primary bg-opacity-20">
                                                <i class="ki-outline ki-calendar fs-2x text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h3 class="fw-bold mb-1">Trip Planning Request</h3>
                                            <p class="text-primary mb-0">Entry ID: #${response.id || 'N/A'} • Submitted: ${createdDate}</p>
                                        </div>
                                        <div class="text-end">
                                            <div class="badge badge-light-success fs-7 fw-bold">Active</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Trip Information -->
                            <div class="row g-6 mb-6">
                                <div class="col-lg-8">
                                    <div class="card h-100">
                                        <div class="card-header border-0 pt-6">
                                            <h4 class="card-title fw-bold text-gray-800">Trip Information</h4>
                                        </div>
                                        <div class="card-body pt-0">
                                            <div class="row g-4">
                                                <div class="col-md-6">
                                                    <div class="d-flex flex-column">
                                                        <span class="text-muted fs-7 fw-semibold mb-2">DEPARTURE DATE</span>
                                                        <span class="fs-6 fw-bold text-gray-800">${travelDate}</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="d-flex flex-column">
                                                        <span class="text-muted fs-7 fw-semibold mb-2">DURATION</span>
                                                        <span class="fs-6 fw-bold text-gray-800">${response.how_many_week || 'N/A'}</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="d-flex flex-column">
                                                        <span class="text-muted fs-7 fw-semibold mb-2">FLEXIBLE ON DATE</span>
                                                        <span class="fs-6 fw-bold text-gray-800">${response.flexible_on_date || 'N/A'}</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="d-flex flex-column">
                                                        <span class="text-muted fs-7 fw-semibold mb-2">TRAVEL TYPE</span>
                                                        <div class="fs-6 fw-bold text-gray-800">${getTravelTypeLabel(response.type_of_travel)}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="card h-100">
                                        <div class="card-header border-0 pt-6">
                                            <h4 class="card-title fw-bold text-gray-800">Travelers</h4>
                                        </div>
                                        <div class="card-body pt-0">
                                            <div class="d-flex flex-column">
                                                <div class="d-flex align-items-center mb-4">
                                                    <div class="symbol symbol-40px me-3">
                                                        <div class="symbol-label bg-primary bg-opacity-10">
                                                            <i class="ki-outline ki-user fs-2 text-primary"></i>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span class="text-muted fs-7 fw-semibold">ADULTS</span>
                                                        <div class="fs-4 fw-bold text-gray-800">${response.people_in_group || 0}</div>
                                                        <span class="text-muted fs-8">${response.adult_age_group || ''}</span>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <div class="symbol symbol-40px me-3">
                                                        <div class="symbol-label bg-warning bg-opacity-10">
                                                            <i class="ki-outline ki-user-square fs-2 text-warning"></i>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span class="text-muted fs-7 fw-semibold">CHILDREN</span>
                                                        <div class="fs-4 fw-bold text-gray-800">${response.no_of_children || 0}</div>
                                                        <span class="text-muted fs-8">${response.children_age_group || ''}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Accommodation & Budget -->
                            <div class="row g-6 mb-6">
                                <div class="col-lg-6">
                                    <div class="card h-100">
                                        <div class="card-header border-0 pt-6">
                                            <h4 class="card-title fw-bold text-gray-800">Accommodation</h4>
                                        </div>
                                        <div class="card-body pt-0">
                                            <div class="d-flex align-items-center mb-4">
                                                <div class="symbol symbol-40px me-3">
                                                    <div class="symbol-label bg-success bg-opacity-10">
                                                        <i class="ki-outline ki-star fs-2 text-success"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <span class="text-muted fs-7 fw-semibold">LEVEL</span>
                                                    <div class="fs-5 fw-bold text-gray-800">${response.level_of_accommodation || 'N/A'}</div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-40px me-3">
                                                    <div class="symbol-label bg-info bg-opacity-10">
                                                        <i class="ki-outline ki-setting-2 fs-2 text-info"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <span class="text-muted fs-7 fw-semibold">OTHER SERVICES</span>
                                                    <div class="fs-6 fw-bold text-gray-800">${response.other_services_needed || 'N/A'}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="card h-100">
                                        <div class="card-header border-0 pt-6">
                                            <h4 class="card-title fw-bold text-gray-800">Budget</h4>
                                        </div>
                                        <div class="card-body pt-0">
                                            <div class="d-flex align-items-center mb-4">
                                                <div class="symbol symbol-40px me-3">
                                                    <div class="symbol-label bg-success bg-opacity-10">
                                                        <i class="ki-outline ki-dollar fs-2 text-success"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <span class="text-muted fs-7 fw-semibold">PER PERSON</span>
                                                    <div class="fs-5 fw-bold text-gray-800">${response.per_person_budget || 'N/A'}</div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-40px me-3">
                                                    <div class="symbol-label bg-warning bg-opacity-10">
                                                        <i class="ki-outline ki-gear fs-2 text-warning"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <span class="text-muted fs-7 fw-semibold">FLEXIBLE</span>
                                                    <div class="fs-6 fw-bold text-gray-800">${response.budget_flexible || 'N/A'}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Comments Section -->
                            <div class="card mb-6">
                                <div class="card-header border-0 pt-6">
                                    <h4 class="card-title fw-bold text-gray-800">Special Requests</h4>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="bg-light-primary p-6 rounded">
                                        <div class="d-flex align-items-start">
                                            <i class="ki-outline ki-quote fs-2x text-primary me-4"></i>
                                            <div class="flex-grow-1">
                                                <p class="text-gray-900 fs-6 lh-lg mb-0">${response.comments || 'No specific requests provided.'}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="card">
                                <div class="card-header border-0 pt-6">
                                    <h4 class="card-title fw-bold text-gray-800">Contact Information</h4>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-40px me-3">
                                                    <div class="symbol-label bg-primary bg-opacity-10">
                                                        <i class="ki-outline ki-user fs-2 text-primary"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <span class="text-muted fs-7 fw-semibold">FULL NAME</span>
                                                    <div class="fs-6 fw-bold text-gray-800">${response.name || 'N/A'}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-40px me-3">
                                                    <div class="symbol-label bg-success bg-opacity-10">
                                                        <i class="ki-outline ki-sms fs-2 text-success"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <span class="text-muted fs-7 fw-semibold">EMAIL</span>
                                                    <div class="fs-6 fw-bold text-gray-800">
                                                        <a href="mailto:${response.email}" class="text-primary text-hover-primary">${response.email || 'N/A'}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-40px me-3">
                                                    <div class="symbol-label bg-info bg-opacity-10">
                                                        <i class="ki-outline ki-phone fs-2 text-info"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <span class="text-muted fs-7 fw-semibold">PHONE</span>
                                                    <div class="fs-6 fw-bold text-gray-800">
                                                        <a href="tel:${response.phone}" class="text-primary text-hover-primary">${response.phone || 'N/A'}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-40px me-3">
                                                    <div class="symbol-label bg-warning bg-opacity-10">
                                                        <i class="ki-outline ki-time fs-2 text-warning"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <span class="text-muted fs-7 fw-semibold">BEST TIME TO CALL</span>
                                                    <div class="fs-6 fw-bold text-gray-800">${response.time || 'N/A'}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-40px me-3">
                                                    <div class="symbol-label bg-danger bg-opacity-10">
                                                        <i class="ki-outline ki-geolocation fs-2 text-danger"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <span class="text-muted fs-7 fw-semibold">COUNTRY</span>
                                                    <div class="fs-6 fw-bold text-gray-800">${response.country_of_residence || 'N/A'}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-40px me-3">
                                                    <div class="symbol-label bg-dark bg-opacity-10">
                                                        <i class="ki-outline ki-tag fs-2 text-dark"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <span class="text-muted fs-7 fw-semibold">TIMEZONE</span>
                                                    <div class="fs-6 fw-bold text-gray-800">${response.timezone || 'N/A'}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    $('#lead-details-content').html(modalContent);
                },
                error: function() {
                    alert('Error loading trip planning details');
                }
            });
        });
        // Handle delete lead button click
        $(document).on('click', '.delete-lead-btn', function(e) {
            e.preventDefault();
            const leadId = $(this).data('lead-id');
            if (confirm('Are you sure you want to delete this trip planning entry?')) {
                $.ajax({
                    url: '<?php echo SITE_WS_PATH; ?>/api/trip-planning/' + leadId,
                    type: 'DELETE',
                    dataType: 'json',
                    success: function(response) {
                        if (response.error) {
                            alert('Error: ' + response.error);
                            return;
                        }
                        alert('Trip planning entry deleted successfully');
                        // Refresh the table
                        if (tripPlanningTable) {
                            tripPlanningTable.refresh();
                        }
                    },
                    error: function() {
                        alert('Error deleting trip planning entry');
                    }
                });
            }
        });
    });
</script>
<?php
require_once('footer.inc.php');
?>