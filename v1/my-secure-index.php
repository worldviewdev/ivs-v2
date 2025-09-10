<?php
require_once('header.inc.php');
$arr_file_owners = array("0" => "IVS", "7" => "Visits Italy", "8" => "Wine Tours Italia");

$sql_add = "";
$sql_add .= "and (file_primary_staff='" . $_SESSION['sess_agent_id'] . "' or file_active_staff='" . $_SESSION['sess_agent_id'] . "')";

//$sql = "select f.*, concat(emp_first_name,' ',emp_last_name) as active_staff_name,concat(client_first_name,' ',client_last_name) as client_name,concat(agent_first_name,' ',agent_last_name) as agent_name  from mv_files f left join mv_employee e on f.file_active_staff=e.emp_id  left join mv_client c on f.fk_client_id=c.client_id left join mv_agent a on f.fk_agent_id=a.agent_id left join mv_agency ag on ag.agency_id=a.fk_agency_id where file_status!='Delete' and file_admin_type = 'Admin' $sql_add ORDER BY file_id desc LIMIT 7";
//and file_return_date >= curdate()
$sql_latest_sales = "select f.*, concat(emp_first_name,' ',emp_last_name) as active_staff_name,concat(client_first_name,' ',client_last_name) as client_name,concat(agent_first_name,' ',agent_last_name) as agent_name  from mv_files f left join mv_employee e on f.file_active_staff=e.emp_id  left join mv_client c on f.fk_client_id=c.client_id left join mv_agent a on f.fk_agent_id=a.agent_id left join mv_agency ag on ag.agency_id=a.fk_agency_id where file_status!='Delete' and file_admin_type = 'Admin' $sql_add ORDER BY file_id desc LIMIT 7";
$sql_nolimit = "select f.*, concat(emp_first_name,' ',emp_last_name) as active_staff_name,concat(client_first_name,' ',client_last_name) as client_name,concat(agent_first_name,' ',agent_last_name) as agent_name  from mv_files f left join mv_employee e on f.file_active_staff=e.emp_id  left join mv_client c on f.fk_client_id=c.client_id left join mv_agent a on f.fk_agent_id=a.agent_id left join mv_agency ag on ag.agency_id=a.fk_agency_id where file_status!='Delete' and file_admin_type = 'Admin' $sql_add ORDER BY file_id desc";

$sql_sales_paid = "select f.*, concat(emp_first_name,' ',emp_last_name) as active_staff_name,concat(client_first_name,' ',client_last_name) as client_name,concat(agent_first_name,' ',agent_last_name) as agent_name  from mv_files f left join mv_employee e on f.file_active_staff=e.emp_id  left join mv_client c on f.fk_client_id=c.client_id left join mv_agent a on f.fk_agent_id=a.agent_id left join mv_agency ag on ag.agency_id=a.fk_agency_id where file_status!='Delete' and file_admin_type = 'Admin' and file_current_status in (2,3) $sql_add ORDER BY file_id desc LIMIT 7";
$result = db_query($sql_latest_sales);
$result_nolimit = db_query($sql_nolimit);
$result_sales_paid = db_query($sql_sales_paid);
?>

<div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">

    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">

        <div class="d-flex flex-column flex-column-fluid">

            <div id="kt_app_toolbar" class="app-toolbar pt-10 mb-0">

                <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">

                    <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">

                        <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">

                            <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">Dashboard</h1>


                            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">

                                <li class="breadcrumb-item text-muted">
                                    <a href="index.html" class="text-muted text-hover-primary">Home</a>
                                </li>


                                <li class="breadcrumb-item">
                                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                                </li>


                                <li class="breadcrumb-item text-muted">Dashboards</li>

                            </ul>

                        </div>

                    </div>

                </div>

            </div>


            <div id="kt_app_content" class="app-content flex-column-fluid">

                <div id="kt_app_content_container" class="app-container container-fluid">
                    <div class="alert alert-success d-flex align-items-center p-5 mb-10">
                        <i class="ki-duotone ki-shield-tick fs-2hx text-success me-4"><span class="path1"></span><span class="path2"></span></i>
                        <div class="d-flex flex-column">
                            <h4 class="mb-1 text-success">Wellcome back!! <?php echo $_SESSION['sess_agent_name']; ?></h4>
                            <span>Today is <?php echo date('j F Y'); ?></span>
                        </div>
                    </div>
                    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">

                        <div class="col-xl-4">

                            <div class="card card-flush h-lg-100">

                                <div class="card-header pt-5">

                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bold text-gray-900">Performance</span>
                                        <span class="text-gray-500 mt-1 fw-semibold fs-6">1,046 Inbound Calls today</span>
                                    </h3>


                                    <div class="card-toolbar">

                                        <span class="badge badge-light-danger fs-base mt-n3">
                                            <i class="ki-outline ki-arrow-down fs-5 text-danger ms-n1"></i>7.4%</span>

                                    </div>

                                </div>

                                <div class="card-body d-flex align-items-end pt-6">

                                    <div class="row align-items-center mx-0 w-100">

                                        <div class="col-12 px-0">

                                            <div class="d-flex flex-column content-justify-center">

                                                <div class="d-flex fs-6 fw-semibold align-items-center">

                                                    <div id="kt_card_widget_19_chart" class="min-h-auto" data-kt-size="250" data-kt-line="25"><span></span></div>

                                                </div>


                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <div class="card-footer pt-0 pb-5 mt-n5">

                                    <div class="d-flex flex-column content-justify-center">

                                        <div class="d-flex fs-6 fw-semibold align-items-center">

                                            <div class="bullet bg-success me-3" style="border-radius: 3px;width: 12px;height: 12px"></div>


                                            <div class="fs-5 fw-bold text-gray-600 me-5">Sales:</div>


                                            <div class="ms-auto fw-bolder text-gray-700 text-end">72.56%</div>

                                        </div>


                                        <div class="d-flex fs-6 fw-semibold align-items-center my-4">

                                            <div class="bullet bg-primary me-3" style="border-radius: 3px;width: 12px;height: 12px"></div>


                                            <div class="fs-5 fw-bold text-gray-600 me-5">Profit:</div>


                                            <div class="ms-auto fw-bolder text-gray-700 text-end">29.34%</div>

                                        </div>

                                    </div>


                                </div>
                            </div>

                        </div>


                        <div class="col-xl-8">

                            <div class="card card-flush h-xl-100">

                                <div class="card-header pt-7">

                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bold text-gray-800">Latest Sales</span>
                                        <span class="text-gray-500 mt-1 fw-semibold fs-6">Avg. <?= $result_nolimit->num_rows ?> Total Leads</span>
                                    </h3>

                                </div>


                                <div class="card-body pt-2">

                                    <table class="table align-middle table-row-dashed fs-6 gy-3">

                                        <thead>

                                            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                                <th class="min-w-100px">Order ID</th>
                                                <th class="text-center min-w-100px">Created</th>
                                                <th class="text-center min-w-125px">Customer</th>
                                                <th class="text-center min-w-100px">Agent</th>
                                                <th class="text-center min-w-100px">Type</th>
                                                <th class="text-center min-w-50px">Status</th>
                                            </tr>

                                        </thead>


                                        <tbody class="fw-bold text-gray-600">
                                            <?php $i = 1;
                                            while ($row = mysqli_fetch_assoc($result)) { ?>
                                                <tr>
                                                    <td>
                                                        <a href="apps/ecommerce/catalog/edit-product.html" class="text-primary text-hover-primary"><?= $row['file_code'] ?></a>
                                                    </td>
                                                    <td class="text-center"><?= date('j F Y', strtotime($row['file_departure_date'])) ?></td>
                                                    <td class="text-center">
                                                        <a href="#" class="text-gray-600 text-hover-primary"><?= $row['client_name'] ?></a>
                                                    </td>
                                                    <td class="text-center"><?= $row['agent_name'] ?? '-' ?></td>
                                                    <td class="text-center">
                                                        <span class="text-gray-800 fw-bolder"><?= $arr_file_types[$row['file_type']] ?? '-' ?></span>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php
                                                        $status = $row['file_current_status'];
                                                        $statusText = $arr_file_status[$status] ?? '-';

                                                        // Mapping ke custom badge class
                                                        $statusClasses = [
                                                            2  => "badge-status-warning",
                                                            3  => "badge-status-warning",
                                                            9  => "badge-status-success",
                                                            10 => "badge-status-gray",
                                                            11 => "badge-status-info",
                                                            12 => "badge-status-blue",
                                                            13 => "badge-status-green",
                                                            8  => "badge-status-danger",
                                                            14 => "badge-status-pink",
                                                            15 => "badge-status-gold",
                                                            58 => "badge-status-purple",
                                                        ];
                                                        $cls = $statusClasses[$status] ?? "badge-status-default";
                                                        ?>
                                                        <span class="badge py-3 px-4 fs-7 <?= $cls ?>">
                                                            <?= $statusText ?>
                                                        </span>
                                                    </td>

                                                </tr>
                                            <?php $i++;
                                            } ?>
                                        </tbody>

                                    </table>

                                </div>

                            </div>

                        </div>

                    </div>


                    <div class="row g-5 g-xl-10">

                        <div class="col-xl-4">

                            <div class="card h-md-100" dir="ltr">

                                <div class="card-body d-flex flex-column flex-center">

                                    <div class="mb-2">

                                        <h1 class="fw-semibold text-gray-800 text-center lh-lg">Have your tried
                                            <br />new
                                            <span class="fw-bolder">Invoice Manager?</span>
                                        </h1>


                                        <div class="py-10 text-center">
                                            <img src="assets/media/svg/illustrations/easy/2.svg" class="theme-light-show w-200px" alt="" />
                                            <img src="assets/media/svg/illustrations/easy/2-dark.svg" class="theme-dark-show w-200px" alt="" />
                                        </div>

                                    </div>


                                    <div class="text-center mb-1">

                                        <a class="btn btn-sm btn-primary me-2" data-bs-target="#kt_modal_create_app" data-bs-toggle="modal">Try Now</a>


                                        <a class="btn btn-sm btn-light" href="account/settings.html">Learn More</a>

                                    </div>

                                </div>

                            </div>

                        </div>


                        <div class="col-xl-8">

                            <div class="card card-flush h-xl-100">

                                <div class="card-header pt-7">

                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bold text-gray-800">Latest Sales</span>
                                        <span class="text-gray-500 mt-1 fw-semibold fs-6">Avg. <?= $result_sales_paid->num_rows ?> Total Leads</span>
                                    </h3>

                                </div>


                                <div class="card-body pt-2">

                                    <table class="table align-middle table-row-dashed fs-6 gy-3">

                                        <thead>

                                            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                                <th class="min-w-100px">Order ID</th>
                                                <th class="text-center min-w-100px">Created</th>
                                                <th class="text-center min-w-125px">Customer</th>
                                                <th class="text-center min-w-100px">Agent</th>
                                                <th class="text-center min-w-100px">Type</th>
                                                <th class="text-center min-w-50px">Status</th>
                                            </tr>

                                        </thead>


                                        <tbody class="fw-bold text-gray-600">
                                            <?php $i = 1;
                                            while ($row = mysqli_fetch_assoc($result_sales_paid)) { ?>
                                                <tr>
                                                    <td>
                                                        <a href="apps/ecommerce/catalog/edit-product.html" class="text-primary text-hover-primary"><?= $row['file_code'] ?></a>
                                                    </td>
                                                    <td class="text-center"><?= date('j F Y', strtotime($row['file_departure_date'])) ?></td>
                                                    <td class="text-center">
                                                        <a href="#" class="text-gray-600 text-hover-primary"><?= $row['client_name'] ?></a>
                                                    </td>
                                                    <td class="text-center"><?= $row['agent_name'] ?? '-' ?></td>
                                                    <td class="text-center">
                                                        <span class="text-gray-800 fw-bolder"><?= $arr_file_types[$row['file_type']] ?? '-' ?></span>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php
                                                        $status = $row['file_current_status'];
                                                        $statusText = $arr_file_status[$status] ?? '-';

                                                        // Mapping ke custom badge class
                                                        $statusClasses = [
                                                            2  => "badge-status-warning",
                                                            3  => "badge-status-warning",
                                                            9  => "badge-status-success",
                                                            10 => "badge-status-gray",
                                                            11 => "badge-status-info",
                                                            12 => "badge-status-blue",
                                                            13 => "badge-status-green",
                                                            8  => "badge-status-danger",
                                                            14 => "badge-status-pink",
                                                            15 => "badge-status-gold",
                                                            58 => "badge-status-purple",
                                                        ];
                                                        $cls = $statusClasses[$status] ?? "badge-status-default";
                                                        ?>
                                                        <span class="badge py-3 px-4 fs-7 <?= $cls ?>">
                                                            <?= $statusText ?>
                                                        </span>
                                                    </td>

                                                </tr>
                                            <?php $i++;
                                            } ?>
                                        </tbody>

                                    </table>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


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

</div>

<?php require_once('footer.inc.php'); ?>