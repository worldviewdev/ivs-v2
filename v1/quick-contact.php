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

function lead_view_modal($id) {
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

                                

                            </div>

                        </div>


                        <div class="card-body pt-0">

                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_ecommerce_sales_table">
                                <thead>
                                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                        <th class="text-start w-10px pe-2">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_ecommerce_sales_table .form-check-input" value="1" />
                                            </div>
                                        </th>
                                        <th class="min-w-100px">File ID</th>
                                        <th class="ext-end min-w-70px">Name</th>
                                        <th class="text-center min-w-70px">Email</th>
                                        <th class="text-end min-w-100px">Contact</th>
                                        <th class="text-end min-w-100px">Travel Date</th>
                                        <th class="text-end min-w-100px">No of Adult</th>
                                        <th class="text-end min-w-100px">No of Child</th>
                                        <th class="text-end min-w-100px">Query/Comment</th>
                                        <th class="text-end min-w-100px">Date & Time</th>
                                        <th class="text-end min-w-100px">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                    <?php $i = 1; while ($row = mysqli_fetch_assoc($result)) { ?> 
                                        <tr>
                                            <td class="text-start">
                                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="checkbox" value="1" />
                                                </div>
                                            </td>
                                            <td class="text-start" data-kt-ecommerce-order-filter="order_id">
                                                <?php if ($row['file_code'] != '') { ?>
                                                    <a href="file/file_summary_general.php?id=<?= $row['fk_file_id'] ?>"><?php echo $row['file_code']; ?></a>
                                                <?php } else { ?>
                                                    <a href="<?php echo SITE_URL; ?>/quick-contact.php?act=cf&id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Create File</a>
                                                <?php } ?>
                                            </td>
                                            <td class="text-start">
                                                <span class="fw-bold"><?php echo $row['name']; ?></span>
                                            </td>
                                            <td class="text-center pe-0" data-order="Refunded">

                                                <span class="fw-bold"><?php echo $row['email']; ?></span>

                                            </td>
                                            <td class="text-end pe-0">
                                                <span class="fw-bold"><?php echo $row['phone']; ?></span>
                                            </td>
                                            <td class="text-end">
                                                <span class="fw-bold"><?php echo $row['dates_for_travel']; ?></span>
                                            </td>
                                            <td class="text-end">
                                                <span class="fw-bold"><?php echo $row['adults'] ?? 0; ?></span>
                                            </td>
                                            <td class="text-end">
                                                <span class="fw-bold"><?php echo $row['children'] ?? 0; ?></span>
                                            </td>
                                            <td class="text-end">
                                                <span class="fw-bold"> <?php echo substr($row['message'], 0, 25); ?>...</span>
                                            </td>
                                            <td class="text-end">
                                                <span class="fw-bold"> <?= date('j M Y H:i:s', strtotime($row['added_on'])) ?></span>
                                            </td>
                                            <td class="text-end">
                                                <a href="#" class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                                    <i class="ki-outline ki-down fs-5 ms-1"></i></a>

                                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">

                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3 view-lead-btn" data-lead-id="<?= $row['id'] ?>" data-bs-toggle="modal" data-bs-target="#kt_modal_new_target">View</a>
                                                    </div>

                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3" data-kt-ecommerce-order-filter="delete_row">Delete</a>
                                                    </div>

                                                </div>

                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>

                            <?php $pager->show_pager(); ?>

                        </div>

                    </div>

                </div>

            </div>

        <!-- </div>


        <div id="kt_app_footer" class="app-footer">

            <div class="app-container container-fluid d-flex flex-column flex-md-row flex-center flex-md-stack py-3">

                <div class="text-gray-900 order-2 order-md-1">
                    <span class="text-muted fw-semibold me-1">2025&copy;</span>
                    <a href="https://keenthemes.com" target="_blank" class="text-gray-800 text-hover-primary">Keenthemes</a>
                </div>


                <ul class="menu menu-gray-600 menu-hover-primary fw-semibold order-1">
                    <li class="menu-item">
                        <a href="https://keenthemes.com" target="_blank" class="menu-link px-2">About</a>
                    </li>
                    <li class="menu-item">
                        <a href="https://devs.keenthemes.com" target="_blank" class="menu-link px-2">Support</a>
                    </li>
                    <li class="menu-item">
                        <a href="https://1.envato.market/EA4JP" target="_blank" class="menu-link px-2">Purchase</a>
                    </li>
                </ul>

            </div>

        </div>

    </div>

    <div class="modal fade" id="kt_modal_new_target" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered mw-650px">

            <div class="modal-content rounded">

                <div class="modal-header pb-0 border-0 justify-content-end">

                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <i class="ki-outline ki-cross fs-1"></i>
                    </div>

                </div>


                <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">

                    <div class="mb-13 text-center">
                        <h1 class="mb-3">Lead Details</h1>
                        <div class="text-muted fw-semibold fs-5">Complete information about the selected lead</div>
                    </div>

                    <div id="lead-details-content">
                        <!-- Lead details will be loaded here via JavaScript -->
                        <!-- <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3">Loading lead details...</p>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div> -->

<!-- <script src="../assets/js/lead-modal.js"></script> --> 

<?php
require_once('footer.inc.php');
?>