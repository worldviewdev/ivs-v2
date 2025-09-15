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

// DataTable will handle data loading via AJAX
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
                            <th>File ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Travel Date</th>
                            <th>No of Adult</th>
                            <th>No of Child</th>
                            <th>Query/Comment</th>
                            <th>Date & Time</th>
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

<script>
    $(document).ready(function() {
        // Initialize DataTable for Quick Contact using reusable component
        const quickContactTable = createDataTable('quick_contact', {
            ajax: {
                url: '../api/quick-contact/all'
            },
            columns: [{
                    data: 'file_code',
                    render: function(data, type, row) {
                        if (row.file_code && row.file_code !== '') {
                            return '<a href="file/file_summary_general.php?id=' + row.fk_file_id + '">' + row.file_code + '</a>';
                        } else {
                            return '<a href="quick-contact.php?act=cf&id=' + row.id + '" class="btn btn-sm btn-primary">Create File</a>';
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
                    data: 'dates_for_travel'
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
                        return data ? data.substring(0, 25) + '...' : '';
                    }
                },
                {
                    data: 'added_on',
                    render: function(data) {
                        return new Date(data).toLocaleDateString('en-GB', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit'
                        });
                    }
                },
                {
                    data: 'id',
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                            <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                Actions
                                <i class="ki-outline ki-down fs-5 ms-1"></i>
                            </a>
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3 view-lead-btn" data-lead-id="${data}" data-bs-toggle="modal" data-bs-target="#kt_modal_new_target">View</a>
                                </div>
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3 delete-lead-btn" data-lead-id="${data}">Delete</a>
                                </div>
                            </div>
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
                url: 'quick-contact.php?act=get_lead&id=' + leadId,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.error) {
                        alert('Error: ' + response.error);
                        return;
                    }

                    // Populate modal with lead data
                    const modalContent = `
                        <div class="row mb-5">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Name:</label>
                                <p>${response.name || 'N/A'}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email:</label>
                                <p>${response.email || 'N/A'}</p>
                            </div>
                        </div>
                        <div class="row mb-5">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Phone:</label>
                                <p>${response.phone || 'N/A'}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Travel Date:</label>
                                <p>${response.dates_for_travel || 'N/A'}</p>
                            </div>
                        </div>
                        <div class="row mb-5">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Adults:</label>
                                <p>${response.adults || 0}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Children:</label>
                                <p>${response.children || 0}</p>
                            </div>
                        </div>
                        <div class="row mb-5">
                            <div class="col-12">
                                <label class="form-label fw-bold">Message/Query:</label>
                                <p>${response.message || 'N/A'}</p>
                            </div>
                        </div>
                        <div class="row mb-5">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">File Code:</label>
                                <p>${response.file_code || 'Not created yet'}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Date Added:</label>
                                <p>${new Date(response.added_on).toLocaleString()}</p>
                            </div>
                        </div>
                    `;

                    $('#lead-details-content').html(modalContent);
                },
                error: function() {
                    alert('Error loading lead details');
                }
            });
        });

        // Handle delete lead button click
        $(document).on('click', '.delete-lead-btn', function(e) {
            e.preventDefault();
            const leadId = $(this).data('lead-id');

            if (confirm('Are you sure you want to delete this lead?')) {
                window.location.href = 'quick-contact.php?act=delete&id=' + leadId;
            }
        });
    });
</script>

<?php
require_once('footer.inc.php');
?>