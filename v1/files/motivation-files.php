<?php
require_once('../header.inc.php');
$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';
$start = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
$pagesize = isset($_REQUEST['pagesize']) ? intval($_REQUEST['pagesize']) : 0;

// $pagesize = $pagesize == 0 ? DEF_PAGE_SIZE : $pagesize;
// $sql = "select f.*, concat(emp_first_name,' ',emp_last_name) as active_staff_name,concat(client_first_name,' ',client_last_name) as client_name,concat(agent_first_name,' ',agent_last_name) as agent_name  from mv_files f left join mv_employee e on f.file_active_staff=e.emp_id  left join mv_client c on f.fk_client_id=c.client_id left join mv_agent a on f.fk_agent_id=a.agent_id left join mv_agency ag on ag.agency_id=a.fk_agency_id where file_status!='Delete' and file_admin_type = 'Admin' and (file_primary_staff='80' or file_active_staff='80') and is_package_file='No'  order by file_id desc ";
// $pager = new midas_pager_sql($sql, $pagesize, $start, 'bootstrap');
// $sql .= " limit $start, $pagesize ";
// $result = db_query($sql);
?>

<div id="kt_app_content" class="app-content flex-column-fluid">

    <div id="kt_app_content_container" class="app-container container-fluid">

        <div class="card card-flush">

            <div class="card-header align-items-center py-5 gap-2 gap-md-5">

                <div class="card-title">

                    <div class="d-flex align-items-center position-relative my-1">
                        <i class="ki-outline ki-magnifier fs-3 position-absolute ms-4"></i>
                        <input type="text" data-kt-ecommerce-order-filter="search" class="form-control form-control-solid w-250px ps-12" placeholder="Search Order" />
                    </div>

                </div>


                <div class="card-toolbar flex-row-fluid justify-content-end gap-5">

                    <div class="input-group w-250px">
                        <input class="form-control form-control-solid rounded rounded-end-0" placeholder="Pick date range" id="kt_ecommerce_sales_flatpickr" />
                        <button class="btn btn-icon btn-light" id="kt_ecommerce_sales_flatpickr_clear">
                            <i class="ki-outline ki-cross fs-2"></i>
                        </button>
                    </div>

                    <div class="w-350px mw-350px">

                        <select name="status_filter" id="status_filter" class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Status" data-kt-ecommerce-order-filter="status">
                            <option></option>
                            <?php foreach($arr_file_status as $key => $value): ?>
                                <option style="font-size: 8px;" value="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($value) ?></option>
                            <?php endforeach; ?>
                            
                        </select>

                    </div>

                    

                </div>

            </div>


            <div class="card-body pt-0">

                <table id="kt_datatable_vertical_scroll" class="table table-row-bordered gy-5 dataTable gs-7">
                    <thead>
                        <tr class="fw-semibold fs-6 text-gray-800">
                            <th>File Code</th>
                            <th>Arrival Date</th>
                            <th>Client</th>
                            <th>Agent</th>
                            <th>Assigned To</th>
                            <th>Status</th>
                            <th>Lead</th>
                            <th>Notes</th>
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

<!-- DataTable Script -->

<script>
    $(document).ready(function() {
    // Initialize DataTable for All Files using reusable component
    const motivationFilesTable = createDataTable('files', {
        ajax: {
            tableId: '#kt_datatable_vertical_scroll',
            url: 'api/files',
            data: function(d) {
                return {
                    ...d,
                    action: 'get_motivation_files',
                    limit: 7,
                    agent_id: '<?= $_SESSION['sess_agent_id'] ?>'
                };
            },
            dataSrc: 'data' // Specify that data is in 'data' property
        },
        columns: [
            { 
                data: 'file_code',
                render: function(data, type, row) {
                    if (!row.id) return data;
                    return `<a href="files/file_summary_general.php?id=${row.id}" target="_blank">${data}</a>`;
                }
            },
            { data: 'file_arrival_date' },
            { data: 'client_name' },
            { data: 'agent_name' },
            { data: 'active_staff_name' },
            { 
                data: 'status',
                render: function(data, type, row) {
                    return '<span class="status-indicator"></span><span class="status-badge">' + data.text + '</span>';
                }
            },
            { data: 'file_type' },
            { data: 'file_type_desc',
                render: function(data, type, row) {
                    return data ? data.substring(0, 35) + '...' : '';
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
            onChange: function (selectedDates, dateStr, instance) {
                // Trigger DataTable refresh when date changes
                if (motivationFilesTable) {
                    allFilesTable.refresh();
                }
            },
        });
        
        // Handle clear button
        const clearButton = document.querySelector('#kt_ecommerce_sales_flatpickr_clear');
        if (clearButton) {
            clearButton.addEventListener('click', function() {
                flatpickr.clear();
                if (motivationFilesTable) {
                    allFilesTable.refresh();
                }
            });
        }
    }
});
</script>
<?php
require_once('../footer.inc.php');
?>