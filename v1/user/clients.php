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
            </div>
            <div class="card-body pt-0">
                <table id="kt_datatable_vertical_scroll" class="table table-row-bordered gy-5 dataTable gs-7">
                    <thead>
                        <tr class="fw-semibold fs-6 text-gray-800">
                            <th>Title</th>
                            <th>Name</th>
                            <th>Surname</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Code</th>
                            <th>Status</th>
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
        // Initialize DataTable for Clients using reusable component
        const clientsTable = createDataTable('clients', {
            ajax: {
                url: '../api/users/list?action=get_all_clients'
            },
            order: [[0, 'desc']], // Default sort by client_added_date desc
            filters: {
                search: '[data-kt-ecommerce-order-filter="search"]'
            }
        });
    });

    // Edit Client function
    function editClient(clientId) {
        // TODO: Implement edit functionality
        console.log('Edit client:', clientId);
        alert('Edit client functionality will be implemented');
    }

    // Delete Client function
    function deleteClient(clientId) {
        if (confirm('Are you sure you want to delete this client?')) {
            // TODO: Implement delete functionality
            console.log('Delete client:', clientId);
            alert('Delete client functionality will be implemented');
        }
    }
</script>
<?php
require_once('../footer.inc.php');
?>