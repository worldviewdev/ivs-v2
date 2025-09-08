<?php
require_once('header.inc.php');
$arr_file_owners = array("0" => "IVS", "7" => "Visits Italy", "8" => "Wine Tours Italia");
$request_vars =([
    'freekeyword' => '',
    'file_search_option' => '',
    'file_current_status' => '',
    'staff_id' => '',
    'keyword' => '',
]);


$sql_add = "";

if ($_SESSION['sess_super_admin'] != 'SuperAdmin') {
  $sql_add .= "and (file_primary_staff='" . $_SESSION['sess_agent_id'] . "' or file_active_staff='" . $_SESSION['sess_agent_id'] . "')";
}


$sql = "select f.*, concat(emp_first_name,' ',emp_last_name) as active_staff_name,concat(client_first_name,' ',client_last_name) as client_name,concat(agent_first_name,' ',agent_last_name) as agent_name  from mv_files f left join mv_employee e on f.file_active_staff=e.emp_id  left join mv_client c on f.fk_client_id=c.client_id left join mv_agent a on f.fk_agent_id=a.agent_id left join mv_agency ag on ag.agency_id=a.fk_agency_id where file_status!='Delete' and file_admin_type = 'Admin' $sql_add ORDER BY file_id desc LIMIT 7";
//and file_return_date >= curdate()



$result = db_query($sql);
?>

<div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">

    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">

        <div class="d-flex flex-column flex-column-fluid">

            <div id="kt_app_toolbar" class="app-toolbar pt-10 mb-0">

                <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">

                    <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">

                        <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">

                            <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">Bidding Dashboard</h1>


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
                    <i class="ki-duotone ki-shield-tick fs-2hx text-success me-4"><span class="path1"></span><span class="path2"></span></i>                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-success">Wellcome back!!  <?php echo $_SESSION['sess_agent_name']; ?></h4>
                        <span>Today is <?php echo date('j F Y'); ?></span>
                    </div>
                </div>
                    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">

                        <div class="col-xl-4">

                            <div class="card card-flush h-xl-100">

                                <div class="card-header pt-5 mb-6">

                                    <h3 class="card-title align-items-start flex-column">

                                        <div class="d-flex align-items-center mb-2">

                                            <span class="fs-3 fw-semibold text-gray-500 align-self-start me-1">$</span>


                                            <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1 ls-n2">3,274.94</span>


                                            <span class="badge badge-light-success fs-base">
                                                <i class="ki-outline ki-arrow-up fs-5 text-success ms-n1"></i>9.2%</span>

                                        </div>


                                        <span class="fs-6 fw-semibold text-gray-500">On <?php echo date('F'); ?></span>

                                    </h3>


                                    <div class="card-toolbar">

                                        <button class="btn btn-icon btn-color-gray-500 btn-active-color-primary justify-content-end" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-overflow="true">
                                            <i class="ki-outline ki-dots-square fs-1 text-gray-500 me-n1"></i>
                                        </button>

                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px" data-kt-menu="true">

                                            <div class="menu-item px-3">
                                                <div class="menu-content fs-6 text-gray-900 fw-bold px-3 py-4">Quick Actions</div>
                                            </div>


                                            <div class="separator mb-3 opacity-75"></div>


                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3">New Ticket</a>
                                            </div>


                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3">New Customer</a>
                                            </div>


                                            <div class="menu-item px-3" data-kt-menu-trigger="hover" data-kt-menu-placement="right-start">

                                                <a href="#" class="menu-link px-3">
                                                    <span class="menu-title">New Group</span>
                                                    <span class="menu-arrow"></span>
                                                </a>


                                                <div class="menu-sub menu-sub-dropdown w-175px py-4">

                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3">Admin Group</a>
                                                    </div>


                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3">Staff Group</a>
                                                    </div>


                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3">Member Group</a>
                                                    </div>

                                                </div>

                                            </div>


                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3">New Contact</a>
                                            </div>


                                            <div class="separator mt-3 opacity-75"></div>


                                            <div class="menu-item px-3">
                                                <div class="menu-content px-3 py-3">
                                                    <a class="btn btn-primary btn-sm px-4" href="#">Generate Reports</a>
                                                </div>
                                            </div>

                                        </div>


                                    </div>

                                </div>


                                <div class="card-body py-0 px-0">

                                    <ul class="nav d-flex justify-content-between mb-3 mx-9">

                                        <li class="nav-item mb-3">

                                            <a class="nav-link btn btn-flex flex-center btn-active-danger btn-color-gray-600 btn-active-color-white rounded-2 w-45px h-35px active" data-bs-toggle="tab" id="kt_charts_widget_33_tab_1" href="#kt_charts_widget_33_tab_content_1">1d</a>

                                        </li>


                                    </ul>


                                    <div class="tab-content mt-n6">

                                        <div class="tab-pane fade active show" id="kt_charts_widget_33_tab_content_1">

                                            <div id="kt_charts_widget_33_chart_1" data-kt-chart-color="info" class="min-h-auto h-200px ps-3 pe-6"></div>


                                            <div class="table-responsive mx-9 mt-n6">

                                                <table class="table align-middle gs-0 gy-4">

                                                    <thead>
                                                        <tr>
                                                            <th class="min-w-100px"></th>
                                                            <th class="min-w-100px text-end pe-0"></th>
                                                            <th class="text-end min-w-50px"></th>
                                                        </tr>
                                                    </thead>


                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <a href="#" class="text-gray-600 fw-bold fs-6">2:30 PM</a>
                                                            </td>
                                                            <td class="pe-0 text-end">
                                                                <span class="text-gray-800 fw-bold fs-6 me-1">$2,756.26</span>
                                                            </td>
                                                            <td class="pe-0 text-end">
                                                                <span class="fw-bold fs-6 text-danger">-139.34</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <a href="#" class="text-gray-600 fw-bold fs-6">3:10 PM</a>
                                                            </td>
                                                            <td class="pe-0 text-end">
                                                                <span class="text-gray-800 fw-bold fs-6 me-1">$3,207.03</span>
                                                            </td>
                                                            <td class="pe-0 text-end">
                                                                <span class="fw-bold fs-6 text-success">+576.24</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <a href="#" class="text-gray-600 fw-bold fs-6">3:55 PM</a>
                                                            </td>
                                                            <td class="pe-0 text-end">
                                                                <span class="text-gray-800 fw-bold fs-6 me-1">$3,274.94</span>
                                                            </td>
                                                            <td class="pe-0 text-end">
                                                                <span class="fw-bold fs-6 text-success">+124.03</span>
                                                            </td>
                                                        </tr>
                                                    </tbody>

                                                </table>

                                            </div>

                                        </div>


                                        <div class="tab-pane fade" id="kt_charts_widget_33_tab_content_2">

                                            <div id="kt_charts_widget_33_chart_2" data-kt-chart-color="info" class="min-h-auto h-200px ps-3 pe-6"></div>


                                            <div class="table-responsive mx-9 mt-n6">

                                                <table class="table align-middle gs-0 gy-4">

                                                    <thead>
                                                        <tr>
                                                            <th class="min-w-100px"></th>
                                                            <th class="min-w-100px text-end pe-0"></th>
                                                            <th class="text-end min-w-50px"></th>
                                                        </tr>
                                                    </thead>


                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <a href="#" class="text-gray-600 fw-bold fs-6">2:30 PM</a>
                                                            </td>
                                                            <td class="pe-0 text-end">
                                                                <span class="text-gray-800 fw-bold fs-6 me-1">$2,756.26</span>
                                                            </td>
                                                            <td class="pe-0 text-end">
                                                                <span class="fw-bold fs-6 text-success">+231.01</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <a href="#" class="text-gray-600 fw-bold fs-6">2:30 PM</a>
                                                            </td>
                                                            <td class="pe-0 text-end">
                                                                <span class="text-gray-800 fw-bold fs-6 me-1">$2,756.26</span>
                                                            </td>
                                                            <td class="pe-0 text-end">
                                                                <span class="fw-bold fs-6 text-primary">+233.07</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <a href="#" class="text-gray-600 fw-bold fs-6">2:30 PM</a>
                                                            </td>
                                                            <td class="pe-0 text-end">
                                                                <span class="text-gray-800 fw-bold fs-6 me-1">$2,145.55</span>
                                                            </td>
                                                            <td class="pe-0 text-end">
                                                                <span class="fw-bold fs-6 text-danger">+134.06</span>
                                                            </td>
                                                        </tr>
                                                    </tbody>

                                                </table>

                                            </div>

                                        </div>


                                        <div class="tab-pane fade" id="kt_charts_widget_33_tab_content_3">

                                            <div id="kt_charts_widget_33_chart_3" data-kt-chart-color="info" class="min-h-auto h-200px ps-3 pe-6"></div>


                                            <div class="table-responsive mx-9 mt-n6">

                                                <table class="table align-middle gs-0 gy-4">

                                                    <thead>
                                                        <tr>
                                                            <th class="min-w-100px"></th>
                                                            <th class="min-w-100px text-end pe-0"></th>
                                                            <th class="text-end min-w-50px"></th>
                                                        </tr>
                                                    </thead>


                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <a href="#" class="text-gray-600 fw-bold fs-6">12:40 AM</a>
                                                            </td>
                                                            <td class="pe-0 text-end">
                                                                <span class="text-gray-800 fw-bold fs-6 me-1">$2,346.25</span>
                                                            </td>
                                                            <td class="pe-0 text-end">
                                                                <span class="fw-bold fs-6 text-warning">+134.57</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <a href="#" class="text-gray-600 fw-bold fs-6">11:30 PM</a>
                                                            </td>
                                                            <td class="pe-0 text-end">
                                                                <span class="text-gray-800 fw-bold fs-6 me-1">$1,565.26</span>
                                                            </td>
                                                            <td class="pe-0 text-end">
                                                                <span class="fw-bold fs-6 text-danger">+155.03</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <a href="#" class="text-gray-600 fw-bold fs-6">4:25 PM</a>
                                                            </td>
                                                            <td class="pe-0 text-end">
                                                                <span class="text-gray-800 fw-bold fs-6 me-1">$2,756.26</span>
                                                            </td>
                                                            <td class="pe-0 text-end">
                                                                <span class="fw-bold fs-6 text-success">+124.03</span>
                                                            </td>
                                                        </tr>
                                                    </tbody>

                                                </table>

                                            </div>

                                        </div>


                                        <div class="tab-pane fade" id="kt_charts_widget_33_tab_content_4">

                                            <div id="kt_charts_widget_33_chart_4" data-kt-chart-color="info" class="min-h-auto h-200px ps-3 pe-6"></div>


                                            <div class="table-responsive mx-9 mt-n6">

                                                <table class="table align-middle gs-0 gy-4">

                                                    <thead>
                                                        <tr>
                                                            <th class="min-w-100px"></th>
                                                            <th class="min-w-100px text-end pe-0"></th>
                                                            <th class="text-end min-w-50px"></th>
                                                        </tr>
                                                    </thead>


                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <a href="#" class="text-gray-600 fw-bold fs-6">3:20 PM</a>
                                                            </td>
                                                            <td class="pe-0 text-end">
                                                                <span class="text-gray-800 fw-bold fs-6 me-1">$3,756.26</span>
                                                            </td>
                                                            <td class="pe-0 text-end">
                                                                <span class="fw-bold fs-6 text-danger">+234.03</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <a href="#" class="text-gray-600 fw-bold fs-6">10:30 AM</a>
                                                            </td>
                                                            <td class="pe-0 text-end">
                                                                <span class="text-gray-800 fw-bold fs-6 me-1">$1,474.04</span>
                                                            </td>
                                                            <td class="pe-0 text-end">
                                                                <span class="fw-bold fs-6 text-info">-134.03</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <a href="#" class="text-gray-600 fw-bold fs-6">1:30 AM</a>
                                                            </td>
                                                            <td class="pe-0 text-end">
                                                                <span class="text-gray-800 fw-bold fs-6 me-1">$2,756.26</span>
                                                            </td>
                                                            <td class="pe-0 text-end">
                                                                <span class="fw-bold fs-6 text-primary">+124.03</span>
                                                            </td>
                                                        </tr>
                                                    </tbody>

                                                </table>

                                            </div>

                                        </div>


                                        <div class="tab-pane fade" id="kt_charts_widget_33_tab_content_5">

                                            <div id="kt_charts_widget_33_chart_5" data-kt-chart-color="info" class="min-h-auto h-200px ps-3 pe-6"></div>


                                            <div class="table-responsive mx-9 mt-n6">

                                                <table class="table align-middle gs-0 gy-4">

                                                    <thead>
                                                        <tr>
                                                            <th class="min-w-100px"></th>
                                                            <th class="min-w-100px text-end pe-0"></th>
                                                            <th class="text-end min-w-50px"></th>
                                                        </tr>
                                                    </thead>


                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <a href="#" class="text-gray-600 fw-bold fs-6">3:30 PM</a>
                                                            </td>
                                                            <td class="pe-0 text-end">
                                                                <span class="text-gray-800 fw-bold fs-6 me-1">$1,756.25</span>
                                                            </td>
                                                            <td class="pe-0 text-end">
                                                                <span class="fw-bold fs-6 text-primary">+144.04</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <a href="#" class="text-gray-600 fw-bold fs-6">2:30 PM</a>
                                                            </td>
                                                            <td class="pe-0 text-end">
                                                                <span class="text-gray-800 fw-bold fs-6 me-1">$2,756.26</span>
                                                            </td>
                                                            <td class="pe-0 text-end">
                                                                <span class="fw-bold fs-6 text-danger">+124.03</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <a href="#" class="text-gray-600 fw-bold fs-6">12:30 AM</a>
                                                            </td>
                                                            <td class="pe-0 text-end">
                                                                <span class="text-gray-800 fw-bold fs-6 me-1">$2,034.65</span>
                                                            </td>
                                                            <td class="pe-0 text-end">
                                                                <span class="fw-bold fs-6 text-success">+184.05</span>
                                                            </td>
                                                        </tr>
                                                    </tbody>

                                                </table>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>


                        <div class="col-xl-8">

                            <div class="card card-flush h-xl-100">

                                <div class="card-header pt-7">

                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bold text-gray-800">My Sales in Details</span>
                                        <span class="text-gray-500 mt-1 fw-semibold fs-6">Avg. 57 orders per day</span>
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
                                            <?php $i = 1; while ($row = mysqli_fetch_assoc($result)) { ?> 
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
                                                    <span class="badge py-3 px-4 fs-7 badge-light-warning"><?= $arr_file_status[$row['file_current_status']] ?? '-' ?></span>
                                                </td>
                                            </tr>
                                            <?php $i++; } ?>
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
                                        <span class="card-label fw-bold text-gray-800">My Sales in Details</span>
                                        <span class="text-gray-500 mt-1 fw-semibold fs-6">Avg. 57 orders per day</span>
                                    </h3>


                                    <div class="card-toolbar">

                                        <div class="d-flex flex-stack flex-wrap gap-4">

                                            <div class="d-flex align-items-center fw-bold">

                                                <div class="text-gray-500 fs-7 me-2">Cateogry</div>


                                                <select class="form-select form-select-transparent text-graY-800 fs-base lh-1 fw-bold py-0 ps-3 w-auto" data-control="select2" data-hide-search="true" data-dropdown-css-class="w-150px" data-placeholder="Select an option">
                                                    <option></option>
                                                    <option value="Show All" selected="selected">Show All</option>
                                                    <option value="a">Category A</option>
                                                    <option value="b">Category A</option>
                                                </select>

                                            </div>


                                            <div class="d-flex align-items-center fw-bold">

                                                <div class="text-gray-500 fs-7 me-2">Status</div>


                                                <select class="form-select form-select-transparent text-gray-900 fs-7 lh-1 fw-bold py-0 ps-3 w-auto" data-control="select2" data-hide-search="true" data-dropdown-css-class="w-150px" data-placeholder="Select an option" data-kt-table-widget-4="filter_status">
                                                    <option></option>
                                                    <option value="Show All" selected="selected">Show All</option>
                                                    <option value="Shipped">Shipped</option>
                                                    <option value="Confirmed">Confirmed</option>
                                                    <option value="Rejected">Rejected</option>
                                                    <option value="Pending">Pending</option>
                                                </select>

                                            </div>


                                            <div class="position-relative my-1">
                                                <i class="ki-outline ki-magnifier fs-2 position-absolute top-50 translate-middle-y ms-4"></i>
                                                <input type="text" data-kt-table-widget-4="search" class="form-control w-150px fs-7 ps-12" placeholder="Search" />
                                            </div>

                                        </div>

                                    </div>

                                </div>


                                <div class="card-body pt-2">

                                    <table class="table align-middle table-row-dashed fs-6 gy-3">

                                        <thead>

                                            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                                <th class="min-w-100px">Order ID</th>
                                                <th class="text-end min-w-100px">Created</th>
                                                <th class="text-end min-w-125px">Customer</th>
                                                <th class="text-end min-w-100px">Agent</th>
                                                <th class="text-end min-w-100px">Type</th>
                                                <th class="text-end min-w-50px">Status</th>
                                                <th class="text-end"></th>
                                            </tr>

                                        </thead>


                                        <tbody class="fw-bold text-gray-600">
                                            <tr data-kt-table-widget-4="subtable_template" class="d-none">
                                                <td colspan="2">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <a href="#" class="symbol symbol-50px bg-secondary bg-opacity-25 rounded">
                                                            <img src="" data-kt-src-path="assets/media/stock/ecommerce/" alt="" data-kt-table-widget-4="template_image" />
                                                        </a>
                                                        <div class="d-flex flex-column text-muted">
                                                            <a href="#" class="text-gray-800 text-hover-primary fw-bold" data-kt-table-widget-4="template_name">Product name</a>
                                                            <div class="fs-7" data-kt-table-widget-4="template_description">Product description</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <div class="text-gray-800 fs-7">Cost</div>
                                                    <div class="text-muted fs-7 fw-bold" data-kt-table-widget-4="template_cost">1</div>
                                                </td>
                                                <td class="text-end">
                                                    <div class="text-gray-800 fs-7">Qty</div>
                                                    <div class="text-muted fs-7 fw-bold" data-kt-table-widget-4="template_qty">1</div>
                                                </td>
                                                <td class="text-end">
                                                    <div class="text-gray-800 fs-7">Total</div>
                                                    <div class="text-muted fs-7 fw-bold" data-kt-table-widget-4="template_total">name</div>
                                                </td>
                                                <td class="text-end">
                                                    <div class="text-gray-800 fs-7 me-3">On hand</div>
                                                    <div class="text-muted fs-7 fw-bold" data-kt-table-widget-4="template_stock">32</div>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="apps/ecommerce/catalog/edit-product.html" class="text-gray-800 text-hover-primary">#XGY-346</a>
                                                </td>
                                                <td class="text-end">7 min ago</td>
                                                <td class="text-end">
                                                    <a href="#" class="text-gray-600 text-hover-primary">Albert Flores</a>
                                                </td>
                                                <td class="text-end">$630.00</td>
                                                <td class="text-end">
                                                    <span class="text-gray-800 fw-bolder">$86.70</span>
                                                </td>
                                                <td class="text-end">
                                                    <span class="badge py-3 px-4 fs-7 badge-light-warning">Pending</span>
                                                </td>
                                                <td class="text-end">
                                                    <button type="button" class="btn btn-sm btn-icon btn-light btn-active-light-primary toggle h-25px w-25px" data-kt-table-widget-4="expand_row">
                                                        <i class="ki-outline ki-plus fs-4 m-0 toggle-off"></i>
                                                        <i class="ki-outline ki-minus fs-4 m-0 toggle-on"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="apps/ecommerce/catalog/edit-product.html" class="text-gray-800 text-hover-primary">#YHD-047</a>
                                                </td>
                                                <td class="text-end">52 min ago</td>
                                                <td class="text-end">
                                                    <a href="#" class="text-gray-600 text-hover-primary">Jenny Wilson</a>
                                                </td>
                                                <td class="text-end">$25.00</td>
                                                <td class="text-end">
                                                    <span class="text-gray-800 fw-bolder">$4.20</span>
                                                </td>
                                                <td class="text-end">
                                                    <span class="badge py-3 px-4 fs-7 badge-light-primary">Confirmed</span>
                                                </td>
                                                <td class="text-end">
                                                    <button type="button" class="btn btn-sm btn-icon btn-light btn-active-light-primary toggle h-25px w-25px" data-kt-table-widget-4="expand_row">
                                                        <i class="ki-outline ki-plus fs-4 m-0 toggle-off"></i>
                                                        <i class="ki-outline ki-minus fs-4 m-0 toggle-on"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="apps/ecommerce/catalog/edit-product.html" class="text-gray-800 text-hover-primary">#SRR-678</a>
                                                </td>
                                                <td class="text-end">1 hour ago</td>
                                                <td class="text-end">
                                                    <a href="#" class="text-gray-600 text-hover-primary">Robert Fox</a>
                                                </td>
                                                <td class="text-end">$1,630.00</td>
                                                <td class="text-end">
                                                    <span class="text-gray-800 fw-bolder">$203.90</span>
                                                </td>
                                                <td class="text-end">
                                                    <span class="badge py-3 px-4 fs-7 badge-light-warning">Pending</span>
                                                </td>
                                                <td class="text-end">
                                                    <button type="button" class="btn btn-sm btn-icon btn-light btn-active-light-primary toggle h-25px w-25px" data-kt-table-widget-4="expand_row">
                                                        <i class="ki-outline ki-plus fs-4 m-0 toggle-off"></i>
                                                        <i class="ki-outline ki-minus fs-4 m-0 toggle-on"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="apps/ecommerce/catalog/edit-product.html" class="text-gray-800 text-hover-primary">#PXF-534</a>
                                                </td>
                                                <td class="text-end">3 hour ago</td>
                                                <td class="text-end">
                                                    <a href="#" class="text-gray-600 text-hover-primary">Cody Fisher</a>
                                                </td>
                                                <td class="text-end">$119.00</td>
                                                <td class="text-end">
                                                    <span class="text-gray-800 fw-bolder">$12.00</span>
                                                </td>
                                                <td class="text-end">
                                                    <span class="badge py-3 px-4 fs-7 badge-light-success">Shipped</span>
                                                </td>
                                                <td class="text-end">
                                                    <button type="button" class="btn btn-sm btn-icon btn-light btn-active-light-primary toggle h-25px w-25px" data-kt-table-widget-4="expand_row">
                                                        <i class="ki-outline ki-plus fs-4 m-0 toggle-off"></i>
                                                        <i class="ki-outline ki-minus fs-4 m-0 toggle-on"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="apps/ecommerce/catalog/edit-product.html" class="text-gray-800 text-hover-primary">#XGD-249</a>
                                                </td>
                                                <td class="text-end">2 day ago</td>
                                                <td class="text-end">
                                                    <a href="#" class="text-gray-600 text-hover-primary">Arlene McCoy</a>
                                                </td>
                                                <td class="text-end">$660.00</td>
                                                <td class="text-end">
                                                    <span class="text-gray-800 fw-bolder">$52.26</span>
                                                </td>
                                                <td class="text-end">
                                                    <span class="badge py-3 px-4 fs-7 badge-light-success">Shipped</span>
                                                </td>
                                                <td class="text-end">
                                                    <button type="button" class="btn btn-sm btn-icon btn-light btn-active-light-primary toggle h-25px w-25px" data-kt-table-widget-4="expand_row">
                                                        <i class="ki-outline ki-plus fs-4 m-0 toggle-off"></i>
                                                        <i class="ki-outline ki-minus fs-4 m-0 toggle-on"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="apps/ecommerce/catalog/edit-product.html" class="text-gray-800 text-hover-primary">#SKP-035</a>
                                                </td>
                                                <td class="text-end">2 day ago</td>
                                                <td class="text-end">
                                                    <a href="#" class="text-gray-600 text-hover-primary">Eleanor Pena</a>
                                                </td>
                                                <td class="text-end">$290.00</td>
                                                <td class="text-end">
                                                    <span class="text-gray-800 fw-bolder">$29.00</span>
                                                </td>
                                                <td class="text-end">
                                                    <span class="badge py-3 px-4 fs-7 badge-light-danger">Rejected</span>
                                                </td>
                                                <td class="text-end">
                                                    <button type="button" class="btn btn-sm btn-icon btn-light btn-active-light-primary toggle h-25px w-25px" data-kt-table-widget-4="expand_row">
                                                        <i class="ki-outline ki-plus fs-4 m-0 toggle-off"></i>
                                                        <i class="ki-outline ki-minus fs-4 m-0 toggle-on"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="apps/ecommerce/catalog/edit-product.html" class="text-gray-800 text-hover-primary">#SKP-567</a>
                                                </td>
                                                <td class="text-end">7 min ago</td>
                                                <td class="text-end">
                                                    <a href="#" class="text-gray-600 text-hover-primary">Dan Wilson</a>
                                                </td>
                                                <td class="text-end">$590.00</td>
                                                <td class="text-end">
                                                    <span class="text-gray-800 fw-bolder">$50.00</span>
                                                </td>
                                                <td class="text-end">
                                                    <span class="badge py-3 px-4 fs-7 badge-light-success">Shipped</span>
                                                </td>
                                                <td class="text-end">
                                                    <button type="button" class="btn btn-sm btn-icon btn-light btn-active-light-primary toggle h-25px w-25px" data-kt-table-widget-4="expand_row">
                                                        <i class="ki-outline ki-plus fs-4 m-0 toggle-off"></i>
                                                        <i class="ki-outline ki-minus fs-4 m-0 toggle-on"></i>
                                                    </button>
                                                </td>
                                            </tr>
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