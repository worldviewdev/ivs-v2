<?php
require_once('../header.inc.php');
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
                            <th>Type</th>
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
    // Initialize DataTable for Archived Files using predefined configuration
    const archivedFilesTable = createDataTable('archived_files', {
        ajax: {
            url: '../api/files',
            data: function(d) {
                return {
                    ...d,
                    action: 'get_archived_files',
                    agent_id: '<?= $_SESSION['sess_agent_id'] ?>'
                };
            },
            dataSrc: 'data' // Specify that data is in 'data' property
        }
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
                if (archivedFilesTable) {
                    archivedFilesTable.refresh();
                }
            },
        });
        
        // Handle clear button
        const clearButton = document.querySelector('#kt_ecommerce_sales_flatpickr_clear');
        if (clearButton) {
            clearButton.addEventListener('click', function() {
                flatpickr.clear();
                if (archivedFilesTable) {
                    archivedFilesTable.refresh();
                }
            });
        }
    }
});
</script>
<?php
require_once('../footer.inc.php');
?>