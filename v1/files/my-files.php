<?php
require_once('../header.inc.php');
$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';
$start = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
$pagesize = isset($_REQUEST['pagesize']) ? intval($_REQUEST['pagesize']) : 0;

$pagesize = $pagesize == 0 ? DEF_PAGE_SIZE : $pagesize;
$sql = "select f.*, concat(emp_first_name,' ',emp_last_name) as active_staff_name,concat(client_first_name,' ',client_last_name) as client_name,concat(agent_first_name,' ',agent_last_name) as agent_name  from mv_files f left join mv_employee e on f.file_active_staff=e.emp_id  left join mv_client c on f.fk_client_id=c.client_id left join mv_agent a on f.fk_agent_id=a.agent_id left join mv_agency ag on ag.agency_id=a.fk_agency_id where file_status!='Delete' and file_admin_type = 'Admin' and (file_primary_staff='80' or file_active_staff='80') and is_package_file='No'  order by file_id desc ";
$pager = new midas_pager_sql($sql, $pagesize, $start, 'bootstrap');
$sql .= " limit $start, $pagesize ";
$result = db_query($sql);
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

                    <div class="w-100 mw-150px">

                        <select class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Status" data-kt-ecommerce-order-filter="status">
                            <option></option>
                            <option value="all">All</option>
                            <option value="Cancelled">Cancelled</option>
                            <option value="Completed">Completed</option>
                            <option value="Denied">Denied</option>
                            <option value="Expired">Expired</option>
                            <option value="Failed">Failed</option>
                            <option value="Pending">Pending</option>
                            <option value="Processing">Processing</option>
                            <option value="Refunded">Refunded</option>
                            <option value="Delivered">Delivered</option>
                            <option value="Delivering">Delivering</option>
                        </select>

                    </div>

                    <a href="apps/ecommerce/catalog/add-product.html" class="btn btn-primary">Add Order</a>

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
                        <?php $i = 1;
                        while ($row = mysqli_fetch_assoc($result)) {
                            $status = $row['file_current_status'];
                            $statusText = $arr_file_status[$status] ?? '-';

                            // Mapping ke custom badge class
                            $statusClasses = [
                                2  => "status-warning",
                                3  => "status-warning",
                                9  => "status-success",
                                10 => "status-gray",
                                11 => "status-info",
                                12 => "status-blue",
                                13 => "status-green",
                                8  => "status-danger",
                                14 => "status-pink",
                                15 => "status-gold",
                                58 => "status-purple",
                            ];
                            $cls = $statusClasses[$status] ?? "status-default";
                            
                            // Inline style untuk background
                            $bgColors = [
                                2  => "#fff3cd",
                                3  => "#fff3cd",
                                9  => "#d4edda", 
                                10 => "#e9ecef",
                                11 => "#d1ecf1",
                                12 => "#cce5ff",
                                13 => "#d4edda",
                                8  => "#f8d7da",
                                14 => "#ffe6f0",
                                15 => "#fff8d1",
                                58 => "#ede7f6",
                            ];
                            $bgColor = $bgColors[$status] ?? "#f8f9fa";
                        ?>
                            <tr class="<?= $cls ?>" style="background-color: <?= $bgColor ?> !important;">
                                <td style="background-color: <?= $bgColor ?> !important;"><?= $row['file_code'] ?></td>
                                <td style="background-color: <?= $bgColor ?> !important;"><?= $row['file_arrival_date'] ?></td>
                                <td style="background-color: <?= $bgColor ?> !important;"><?= $row['client_name'] ?></td>
                                <td style="background-color: <?= $bgColor ?> !important;"><?= $row['agent_name'] ?></td>
                                <td style="background-color: <?= $bgColor ?> !important;"><?= $row['active_staff_name'] ?></td>
                                <td style="background-color: <?= $bgColor ?> !important;">
                                    <span class="status-indicator"></span>
                                    <span class="status-badge"><?= $arr_file_status[$row['file_current_status']] ?></span>
                                </td>
                                <td style="background-color: <?= $bgColor ?> !important;"><?= $arr_file_types[$row['file_type']] ?></td>
                                <td style="background-color: <?= $bgColor ?> !important;"><?= $row['file_type_desc'] ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <?php //$pager->show_pager(); 
                ?>

            </div>

        </div>

    </div>

</div>

<?php
require_once('../footer.inc.php');
?>