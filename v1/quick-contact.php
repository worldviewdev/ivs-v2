<?php
// Initialize variables from GET or POST to avoid undefined variable warnings
$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';
$start = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
$pagesize = isset($_REQUEST['pagesize']) ? intval($_REQUEST['pagesize']) : 0;

if ($act == "get_lead") {
    require_once('includes/midas.inc.php');
} else {
    require_once('header.inc.php');
}

if ($_SESSION['sess_super_admin'] != 'SuperAdmin') {
    header("Location: my-secure-index.php");
    die;
}



if ($act == "export") {
    print_r($_REQUEST);
    die;
}

if ($act == "delete" && $id != "" && $_SESSION['sess_agent_type'] == "Admin") {
    $sql = "delete from ivs_quick_contact where id='" . $id . "'";
    db_query($sql);
    $_SESSION['agent_msg'] = '<li>Selected Lead deleted successfully.</li>';
    $_SESSION['agent_msg_class'] = 'sucess_msg';
    header("Location: quick-contact.php");
    exit;
}

if ($act == "cf") {
    error_log($id);
    create_file_by_qc($id);
    $_SESSION['agent_msg'] = '<li>File has been created successfully.</li>';
    $_SESSION['agent_msg_class'] = 'sucess_msg';
    header("Location: quick-contact.php");
    exit;
}

if ($act == "get_lead" && $id != "") {
    $sql = "SELECT q.*, f.file_code FROM ivs_quick_contact q LEFT JOIN mv_files f ON f.file_id = q.fk_file_id WHERE q.id = '" . intval($id) . "'";
    $result = db_query($sql);
    error_log(print_r($result, true));
    $lead = mysqli_fetch_assoc($result);
    error_log(print_r($lead, true));
    if ($lead) {
        header('Content-Type: application/json');
        echo json_encode($lead);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Lead not found']);
    }
    exit;
}

function lead_view_modal($id)
{
    error_log($id);
    $sql = "SELECT q.*, f.file_code FROM ivs_quick_contact q LEFT JOIN mv_files f ON f.file_id = q.fk_file_id WHERE q.id = '" . intval($id) . "'";
    $result = db_query($sql);
    error_log(print_r($result, true));
    $lead = mysqli_fetch_assoc($result);
    error_log(print_r($lead, true));

    if (!$lead) {
        return false;
    }

    return $lead;
}

$pagesize = $pagesize == 0 ? DEF_PAGE_SIZE : $pagesize;
$sql = "select q.*, f.file_code from ivs_quick_contact q left join mv_files f on f.file_id = q.fk_file_id ";
$sql .= " where 1 order by id desc "; //f.file_id is null // and date(added_on) > '2019-02-15'
$pager = new midas_pager_sql($sql, $pagesize, $start, 'bootstrap');
$sql .= " limit $start, $pagesize ";
$result = db_query($sql);
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
                            <th>Travel Date</th>
                            <th>Adults</th>
                            <th>Children</th>
                            <th>Message</th>
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
                        <h2 class="fw-bold text-white mb-0">Quick Contact Details</h2>
                        <p class="text-white-75 mb-0 fs-7">Complete information about the quick contact inquiry</p>
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
        // Initialize DataTable for Quick Contact using reusable component
        const quickContactTable = createDataTable('quick_contact', {
            ajax: {
                url: '<?php echo SITE_WS_PATH; ?>/api/quick-contact/all'
            },
            columns: [{
                    data: 'file_code',
                    render: function(data, type, row) {
                        if (row.fk_file_id == null) {
                            return `<a href="<?php echo SITE_URL; ?>/quick-contact.php?act=cf&id=${row.id}" class="btn btn-sm btn-primary btn-icon">
                            <i class="ki-outline ki-plus fs-5"></i>
                            </a>`;
                        } else {
                            return `<a href="files/file_summary_general.php?id=${row.fk_file_id}" class="text-primary text-hover-primary fw-bold">${data}</a>`;
                        }
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
                        return data ? new Date(data).toLocaleDateString('en-US', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric'
                        }) : 'N/A';
                    }
                },
                {
                    data: 'adults',
                    render: function(data) {
                        return data || 0;
                    }
                },
                {
                    data: 'children',
                    render: function(data) {
                        return data || 0;
                    }
                },
                {
                    data: 'message',
                    render: function(data) {
                        return data ? data.substring(0, 35) + '...' : '';
                    }
                },
                {
                    data: 'added_on',
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
                            <a href="#" class="btn btn-sm btn-light-primary btn-icon view-lead-btn me-2" title="Lihat" data-lead-id="${data}" data-bs-toggle="modal" data-bs-target="#kt_modal_new_target">
                                <i class="ki-outline ki-eye fs-5"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-icon btn-light-danger delete-lead-btn" title="Hapus" data-lead-id="${data}">
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
                    if (quickContactTable) {
                        quickContactTable.refresh();
                    }
                },
            });
            // Handle clear button
            const clearButton = document.querySelector('#kt_ecommerce_sales_flatpickr_clear');
            if (clearButton) {
                clearButton.addEventListener('click', function() {
                    flatpickr.clear();
                    if (quickContactTable) {
                        quickContactTable.refresh();
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
                url: '<?php echo SITE_WS_PATH; ?>/api/quick-contact/' + leadId,
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
                    const createdDate = response.added_on ?
                        new Date(response.added_on).toLocaleString('en-US', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        }) : 'N/A';

                    // Populate modal with clean, modern design for Quick Contact
                    const modalContent = `
                        <div class="p-8">
                            <!-- Header Info Card -->
                            <div class="card bg-gradient-primary text-white mb-6">
                                <div class="card-body p-6">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-50px me-4">
                                            <div class="symbol-label bg-primary bg-opacity-20">
                                                <i class="ki-outline ki-message-text-2 fs-2x text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h3 class="fw-bold mb-1">Quick Contact Inquiry</h3>
                                            <p class="text-primary mb-0">Entry ID: #${response.id || 'N/A'} • Submitted: ${createdDate}</p>
                                        </div>
                                        <div class="text-end">
                                            <div class="badge badge-light-success fs-7 fw-bold">New Inquiry</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Travel Information -->
                            <div class="row g-6 mb-6">
                                <div class="col-lg-8">
                                    <div class="card h-100">
                                        <div class="card-header border-0 pt-6">
                                            <h4 class="card-title fw-bold text-gray-800">Travel Information</h4>
                                        </div>
                                        <div class="card-body pt-0">
                                            <div class="row g-4">
                                                <div class="col-md-6">
                                                    <div class="d-flex flex-column">
                                                        <span class="text-muted fs-7 fw-semibold mb-2">TRAVEL DATE</span>
                                                        <span class="fs-6 fw-bold text-gray-800">${travelDate}</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="d-flex flex-column">
                                                        <span class="text-muted fs-7 fw-semibold mb-2">FILE CODE</span>
                                                        <span class="fs-6 fw-bold text-gray-800">${response.file_code || 'N/A'}</span>
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
                                                        <div class="fs-4 fw-bold text-gray-800">${response.adults || 0}</div>
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
                                                        <div class="fs-4 fw-bold text-gray-800">${response.children || 0}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Message Section -->
                            <div class="card mb-6">
                                <div class="card-header border-0 pt-6">
                                    <h4 class="card-title fw-bold text-gray-800">Inquiry Message</h4>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="bg-light-primary p-6 rounded">
                                        <div class="d-flex align-items-start">
                                            <i class="ki-outline ki-quote fs-2x text-primary me-4"></i>
                                            <div class="flex-grow-1">
                                                <p class="text-gray-900 fs-6 lh-lg mb-0">${response.message || 'No message provided.'}</p>
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
                                                    <span class="text-muted fs-7 fw-semibold">SUBMITTED ON</span>
                                                    <div class="fs-6 fw-bold text-gray-800">${createdDate}</div>
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
                    alert('Error loading quick contact details');
                }
            });
        });
        // Handle delete lead button click
        $(document).on('click', '.delete-lead-btn', function(e) {
            e.preventDefault();
            const leadId = $(this).data('lead-id');
            const $row = $(this).closest('tr');
            
            Swal.fire({
                title: "Konfirmasi Hapus",
                text: "Apakah Anda yakin ingin menghapus data quick contact ini?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn fw-bold btn-danger",
                    cancelButton: "btn fw-bold btn-active-light-primary"
                }
            }).then(function(result) {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: "Menghapus...",
                        text: "Sedang menghapus data, mohon tunggu...",
                        icon: "info",
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                            de
                        }
                    });
                    
                    // Perform delete via AJAX to API
                    $.ajax({
                        url: '<?php echo SITE_WS_PATH; ?>/api/quick-contact/' + leadId,
                        type: 'DELETE',
                        dataType: 'json',
                        success: function(response) {
                            if (response.error) {
                                Swal.fire({
                                    title: "Error!",
                                    text: "Error: " + response.error,
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "OK",
                                    customClass: {
                                        confirmButton: "btn fw-bold btn-primary"
                                    }
                                });
                                return;
                            }
                            Swal.fire({
                                title: "Berhasil!",
                                text: "Data quick contact berhasil dihapus.",
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "OK",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary"
                                }
                            }).then(function() {
                                // Refresh the table
                                if (quickContactTable) {
                                    quickContactTable.refresh();
                                }
                            });
                        },
                        error: function() {
                            Swal.fire({
                                title: "Error!",
                                text: "Terjadi kesalahan saat menghapus data.",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "OK",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary"
                                }
                            });
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Dibatalkan",
                        text: "Data tidak dihapus.",
                        icon: "info",
                        buttonsStyling: false,
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary"
                        }
                    });
                }
            });
        });
    });
</script>
<?php
require_once('footer.inc.php');
?>