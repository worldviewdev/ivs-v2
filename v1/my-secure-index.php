<?php
require_once('header.inc.php');
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


                        <div class="d-flex align-items-center gap-2 gap-lg-3">
                            <a href="#" class="btn btn-sm btn-flex btn-transparent btn-hover-outline" data-bs-toggle="modal" data-bs-target="#kt_modal_create_campaign">Save</a>
                            <a href="" class="btn btn-sm btn-flex btn-outline btn-active-primary bg-body" data-bs-toggle="modal" data-bs-target="#kt_modal_view_users">
                                <i class="ki-outline ki-eye fs-4"></i>Preview</a>
                            <a href="" class="btn btn-sm btn-flex btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_create_campaign">
                                <i class="ki-outline ki-exit-up fs-4"></i>Push</a>
                        </div>

                    </div>

                </div>

            </div>


            <div id="kt_app_content" class="app-content flex-column-fluid">

                <div id="kt_app_content_container" class="app-container container-fluid">

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


                                        <span class="fs-6 fw-semibold text-gray-500">Etherium rate</span>

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


                                        <li class="nav-item mb-3">

                                            <a class="nav-link btn btn-flex flex-center btn-active-danger btn-color-gray-600 btn-active-color-white rounded-2 w-45px h-35px" data-bs-toggle="tab" id="kt_charts_widget_33_tab_2" href="#kt_charts_widget_33_tab_content_2">5d</a>

                                        </li>


                                        <li class="nav-item mb-3">

                                            <a class="nav-link btn btn-flex flex-center btn-active-danger btn-color-gray-600 btn-active-color-white rounded-2 w-45px h-35px" data-bs-toggle="tab" id="kt_charts_widget_33_tab_3" href="#kt_charts_widget_33_tab_content_3">1m</a>

                                        </li>


                                        <li class="nav-item mb-3">

                                            <a class="nav-link btn btn-flex flex-center btn-active-danger btn-color-gray-600 btn-active-color-white rounded-2 w-45px h-35px" data-bs-toggle="tab" id="kt_charts_widget_33_tab_4" href="#kt_charts_widget_33_tab_content_4">6m</a>

                                        </li>


                                        <li class="nav-item mb-3">

                                            <a class="nav-link btn btn-flex flex-center btn-active-danger btn-color-gray-600 btn-active-color-white rounded-2 w-45px h-35px" data-bs-toggle="tab" id="kt_charts_widget_33_tab_5" href="#kt_charts_widget_33_tab_content_5">1y</a>

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
                                        <span class="card-label fw-bold text-gray-800">Active Auctions</span>
                                        <span class="text-gray-500 mt-1 fw-semibold fs-6">Updated 37 minutes ago</span>
                                    </h3>


                                    <div class="card-toolbar">
                                        <a href="apps/ecommerce/catalog/add-product.html" class="btn btn-sm btn-light">History</a>
                                    </div>

                                </div>


                                <div class="card-body">

                                    <div class="table-responsive">

                                        <table class="table table-row-dashed align-middle gs-0 gy-3 my-0">

                                            <thead>
                                                <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                                    <th class="p-0 pb-3 min-w-175px text-start">ITEM</th>
                                                    <th class="p-0 pb-3 min-w-100px text-end">OPEN PRICE</th>
                                                    <th class="p-0 pb-3 min-w-100px text-end">YOUR OFFER</th>
                                                    <th class="p-0 pb-3 min-w-125px text-end">RECENT OFFER</th>
                                                    <th class="p-0 pb-3 min-w-100px text-end">TIME LEFT</th>
                                                    <th class="p-0 pb-3 w-80px text-end">VIEW</th>
                                                </tr>
                                            </thead>


                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="symbol symbol-50px me-3">
                                                                <img src="assets/media/stock/600x600/img-49.jpg" class="" alt="" />
                                                            </div>
                                                            <div class="d-flex justify-content-start flex-column">
                                                                <a href="#" class="text-gray-800 fw-bold text-hover-primary mb-1 fs-6">The Art</a>
                                                                <span class="text-gray-500 fw-semibold d-block fs-7">Jenny Wilson</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-end pe-0">
                                                        <span class="text-gray-600 fw-bold fs-6">0.054 ETH</span>
                                                    </td>
                                                    <td class="text-end pe-0">
                                                        <span class="text-gray-600 fw-bold fs-6">0.089 ETH</span>
                                                    </td>
                                                    <td class="pe-0">
                                                        <div class="d-flex align-items-center justify-content-end">
                                                            <div class="symbol symbol-30px me-3">
                                                                <img src="assets/media/avatars/300-13.jpg" class="" alt="" />
                                                            </div>
                                                            <span class="text-gray-600 fw-bold d-block fs-6">0.089 ETH</span>
                                                        </div>
                                                    </td>
                                                    <td class="text-end pe-0">
                                                        <span class="text-gray-600 fw-bold fs-6">1h 43m 52s</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-30px h-30px">
                                                            <i class="ki-outline ki-black-right fs-2 text-gray-500"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="symbol symbol-50px me-3">
                                                                <img src="assets/media/stock/600x600/img-43.jpg" class="" alt="" />
                                                            </div>
                                                            <div class="d-flex justify-content-start flex-column">
                                                                <a href="#" class="text-gray-800 fw-bold text-hover-primary mb-1 fs-6">Blue Bubble Art</a>
                                                                <span class="text-gray-500 fw-semibold d-block fs-7">Guy Hawkins</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-end pe-0">
                                                        <span class="text-gray-600 fw-bold fs-6">1.830 ETH</span>
                                                    </td>
                                                    <td class="text-end pe-0">
                                                        <span class="text-gray-600 fw-bold fs-6">2.451 ETH</span>
                                                    </td>
                                                    <td class="pe-0">
                                                        <div class="d-flex align-items-center justify-content-end">
                                                            <div class="symbol symbol-30px me-3">
                                                                <img src="assets/media/avatars/300-2.jpg" class="" alt="" />
                                                            </div>
                                                            <span class="text-gray-600 fw-bold d-block fs-6">3.083 ETH</span>
                                                        </div>
                                                    </td>
                                                    <td class="text-end pe-0">
                                                        <span class="text-gray-600 fw-bold fs-6">4h 28m 07s</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-30px h-30px">
                                                            <i class="ki-outline ki-black-right fs-2 text-gray-500"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="symbol symbol-50px me-3">
                                                                <img src="assets/media/stock/600x600/img-46.jpg" class="" alt="" />
                                                            </div>
                                                            <div class="d-flex justify-content-start flex-column">
                                                                <a href="#" class="text-gray-800 fw-bold text-hover-primary mb-1 fs-6">Color Face Art</a>
                                                                <span class="text-gray-500 fw-semibold d-block fs-7">Wade Warren</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-end pe-0">
                                                        <span class="text-gray-600 fw-bold fs-6">0.043 ETH</span>
                                                    </td>
                                                    <td class="text-end pe-0">
                                                        <span class="text-gray-600 fw-bold fs-6">0.124 ETH</span>
                                                    </td>
                                                    <td class="pe-0">
                                                        <div class="d-flex align-items-center justify-content-end">
                                                            <div class="symbol symbol-30px me-3">
                                                                <img src="assets/media/avatars/300-11.jpg" class="" alt="" />
                                                            </div>
                                                            <span class="text-gray-600 fw-bold d-block fs-6">1.058 ETH</span>
                                                        </div>
                                                    </td>
                                                    <td class="text-end pe-0">
                                                        <span class="text-gray-600 fw-bold fs-6">5h 09m 23s</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-30px h-30px">
                                                            <i class="ki-outline ki-black-right fs-2 text-gray-500"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="symbol symbol-50px me-3">
                                                                <img src="assets/media/stock/600x600/img-54.jpg" class="" alt="" />
                                                            </div>
                                                            <div class="d-flex justify-content-start flex-column">
                                                                <a href="#" class="text-gray-800 fw-bold text-hover-primary mb-1 fs-6">Blue to Orange Art</a>
                                                                <span class="text-gray-500 fw-semibold d-block fs-7">Jane Cooper</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-end pe-0">
                                                        <span class="text-gray-600 fw-bold fs-6">0.092 ETH</span>
                                                    </td>
                                                    <td class="text-end pe-0">
                                                        <span class="text-gray-600 fw-bold fs-6">0.158 ETH</span>
                                                    </td>
                                                    <td class="pe-0">
                                                        <div class="d-flex align-items-center justify-content-end">
                                                            <div class="symbol symbol-30px me-3">
                                                                <img src="assets/media/avatars/300-10.jpg" class="" alt="" />
                                                            </div>
                                                            <span class="text-gray-600 fw-bold d-block fs-6">0.403 ETH</span>
                                                        </div>
                                                    </td>
                                                    <td class="text-end pe-0">
                                                        <span class="text-gray-600 fw-bold fs-6">7h 23m 16s</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-30px h-30px">
                                                            <i class="ki-outline ki-black-right fs-2 text-gray-500"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="symbol symbol-50px me-3">
                                                                <img src="assets/media/stock/600x600/img-45.jpg" class="" alt="" />
                                                            </div>
                                                            <div class="d-flex justify-content-start flex-column">
                                                                <a href="#" class="text-gray-800 fw-bold text-hover-primary mb-1 fs-6">Awesome Bird Art</a>
                                                                <span class="text-gray-500 fw-semibold d-block fs-7">Jacob Jones</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-end pe-0">
                                                        <span class="text-gray-600 fw-bold fs-6">0.824 ETH</span>
                                                    </td>
                                                    <td class="text-end pe-0">
                                                        <span class="text-gray-600 fw-bold fs-6">1.072 ETH</span>
                                                    </td>
                                                    <td class="pe-0">
                                                        <div class="d-flex align-items-center justify-content-end">
                                                            <div class="symbol symbol-30px me-3">
                                                                <img src="assets/media/avatars/300-9.jpg" class="" alt="" />
                                                            </div>
                                                            <span class="text-gray-600 fw-bold d-block fs-6">1.094 ETH</span>
                                                        </div>
                                                    </td>
                                                    <td class="text-end pe-0">
                                                        <span class="text-gray-600 fw-bold fs-6">36h 18m 42s</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-30px h-30px">
                                                            <i class="ki-outline ki-black-right fs-2 text-gray-500"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            </tbody>

                                        </table>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">

                        <div class="col-xl-4">

                            <div class="card card-flush h-xl-100">

                                <div class="card-header pt-7">

                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bold text-gray-800">Lading Teams</span>
                                        <span class="text-gray-500 mt-1 fw-semibold fs-6">8k social visitors</span>
                                    </h3>


                                    <div class="card-toolbar"></div>

                                </div>


                                <div class="card-body pt-5">

                                    <div class="">

                                        <div class="d-flex flex-stack">

                                            <div class="d-flex align-items-center me-5">

                                                <img src="assets/media/svg/brand-logos/atica.svg" class="me-4 w-30px" style="border-radius: 4px" alt="" />


                                                <div class="me-5">

                                                    <a href="#" class="text-gray-800 fw-bold text-hover-primary fs-6">Abstergo Ltd.</a>


                                                    <span class="text-gray-500 fw-semibold fs-7 d-block text-start ps-0">Community</span>

                                                </div>

                                            </div>


                                            <div class="d-flex align-items-center">

                                                <span class="text-gray-800 fw-bold fs-4 me-3">579</span>


                                                <div class="m-0">

                                                    <span class="badge badge-light-success fs-base">
                                                        <i class="ki-outline ki-arrow-up fs-5 text-success ms-n1"></i>2.6%</span>

                                                </div>

                                            </div>

                                        </div>


                                        <div class="separator separator-dashed my-3"></div>


                                        <div class="d-flex flex-stack">

                                            <div class="d-flex align-items-center me-5">

                                                <img src="assets/media/svg/brand-logos/telegram-2.svg" class="me-4 w-30px" style="border-radius: 4px" alt="" />


                                                <div class="me-5">

                                                    <a href="#" class="text-gray-800 fw-bold text-hover-primary fs-6">Binford Ltd.</a>


                                                    <span class="text-gray-500 fw-semibold fs-7 d-block text-start ps-0">Social Media</span>

                                                </div>

                                            </div>


                                            <div class="d-flex align-items-center">

                                                <span class="text-gray-800 fw-bold fs-4 me-3">2,588</span>


                                                <div class="m-0">

                                                    <span class="badge badge-light-danger fs-base">
                                                        <i class="ki-outline ki-arrow-down fs-5 text-danger ms-n1"></i>0.4%</span>

                                                </div>

                                            </div>

                                        </div>


                                        <div class="separator separator-dashed my-3"></div>


                                        <div class="d-flex flex-stack">

                                            <div class="d-flex align-items-center me-5">

                                                <img src="assets/media/svg/brand-logos/balloon.svg" class="me-4 w-30px" style="border-radius: 4px" alt="" />


                                                <div class="me-5">

                                                    <a href="#" class="text-gray-800 fw-bold text-hover-primary fs-6">Barone LLC.</a>


                                                    <span class="text-gray-500 fw-semibold fs-7 d-block text-start ps-0">Messanger</span>

                                                </div>

                                            </div>


                                            <div class="d-flex align-items-center">

                                                <span class="text-gray-800 fw-bold fs-4 me-3">794</span>


                                                <div class="m-0">

                                                    <span class="badge badge-light-success fs-base">
                                                        <i class="ki-outline ki-arrow-up fs-5 text-success ms-n1"></i>0.2%</span>

                                                </div>

                                            </div>

                                        </div>


                                        <div class="separator separator-dashed my-3"></div>


                                        <div class="d-flex flex-stack">

                                            <div class="d-flex align-items-center me-5">

                                                <img src="assets/media/svg/brand-logos/kickstarter.svg" class="me-4 w-30px" style="border-radius: 4px" alt="" />


                                                <div class="me-5">

                                                    <a href="#" class="text-gray-800 fw-bold text-hover-primary fs-6">Abstergo Ltd.</a>


                                                    <span class="text-gray-500 fw-semibold fs-7 d-block text-start ps-0">Video Channel</span>

                                                </div>

                                            </div>


                                            <div class="d-flex align-items-center">

                                                <span class="text-gray-800 fw-bold fs-4 me-3">1,578</span>


                                                <div class="m-0">

                                                    <span class="badge badge-light-success fs-base">
                                                        <i class="ki-outline ki-arrow-up fs-5 text-success ms-n1"></i>4.1%</span>

                                                </div>

                                            </div>

                                        </div>


                                        <div class="separator separator-dashed my-3"></div>


                                        <div class="d-flex flex-stack">

                                            <div class="d-flex align-items-center me-5">

                                                <img src="assets/media/svg/brand-logos/vimeo.svg" class="me-4 w-30px" style="border-radius: 4px" alt="" />


                                                <div class="me-5">

                                                    <a href="#" class="text-gray-800 fw-bold text-hover-primary fs-6">Biffco Enterprises</a>


                                                    <span class="text-gray-500 fw-semibold fs-7 d-block text-start ps-0">Social Network</span>

                                                </div>

                                            </div>


                                            <div class="d-flex align-items-center">

                                                <span class="text-gray-800 fw-bold fs-4 me-3">3,458</span>


                                                <div class="m-0">

                                                    <span class="badge badge-light-success fs-base">
                                                        <i class="ki-outline ki-arrow-up fs-5 text-success ms-n1"></i>8.3%</span>

                                                </div>

                                            </div>

                                        </div>


                                        <div class="separator separator-dashed my-3"></div>


                                        <div class="d-flex flex-stack">

                                            <div class="d-flex align-items-center me-5">

                                                <img src="assets/media/svg/brand-logos/plurk.svg" class="me-4 w-30px" style="border-radius: 4px" alt="" />


                                                <div class="me-5">

                                                    <a href="#" class="text-gray-800 fw-bold text-hover-primary fs-6">Big Kahuna Burger</a>


                                                    <span class="text-gray-500 fw-semibold fs-7 d-block text-start ps-0">Social Network</span>

                                                </div>

                                            </div>


                                            <div class="d-flex align-items-center">

                                                <span class="text-gray-800 fw-bold fs-4 me-3">2,047</span>


                                                <div class="m-0">

                                                    <span class="badge badge-light-success fs-base">
                                                        <i class="ki-outline ki-arrow-up fs-5 text-success ms-n1"></i>1.9%</span>

                                                </div>

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
                                        <span class="card-label fw-bold text-gray-800">Most Popular Sellers</span>
                                        <span class="text-gray-500 mt-1 fw-semibold fs-6">Total 424,567 deliveries</span>
                                    </h3>


                                    <div class="card-toolbar">

                                        <div data-kt-daterangepicker="true" data-kt-daterangepicker-opens="left" class="btn btn-sm btn-light d-flex align-items-center px-4">

                                            <div class="text-gray-600 fw-bold">Loading date range...</div>

                                            <i class="ki-outline ki-calendar-8 text-gray-500 lh-0 fs-2 ms-2 me-0"></i>
                                        </div>

                                    </div>

                                </div>


                                <div class="card-body pt-3 pb-4">

                                    <div class="table-responsive">

                                        <table class="table table-row-dashed align-middle gs-0 gy-4 my-0">

                                            <thead>
                                                <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                                    <th class="p-0 min-w-200px"></th>
                                                    <th class="p-0 min-w-150px"></th>
                                                    <th class="p-0 min-w-125px"></th>
                                                    <th class="p-0 min-w-125px"></th>
                                                    <th class="p-0 w-100px"></th>
                                                </tr>
                                            </thead>


                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="symbol symbol- symbol-40px me-3">
                                                                <img src="assets/media/avatars/300-1.jpg" class="" alt="" />
                                                            </div>
                                                            <div class="d-flex justify-content-start flex-column">
                                                                <a href="account/overview.html" class="text-gray-800 fw-bold text-hover-primary mb-1 fs-6">Brooklyn Simmons</a>
                                                                <span class="text-gray-500 fw-semibold d-block fs-7">Zuid Area</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-end">
                                                        <span class="text-gray-800 fw-bold d-block mb-1 fs-6">1,240</span>
                                                        <span class="fw-semibold text-gray-500 d-block">Deliveries</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="text-gray-800 fw-bold text-hover-primary d-block mb-1 fs-6">$5,400</a>
                                                        <span class="text-gray-500 fw-semibold d-block fs-7">Earnings</span>
                                                    </td>
                                                    <td class="float-end text-end border-0">
                                                        <div class="rating">
                                                            <div class="rating-label checked">
                                                                <i class="ki-outline ki-star fs-6"></i>
                                                            </div>
                                                            <div class="rating-label checked">
                                                                <i class="ki-outline ki-star fs-6"></i>
                                                            </div>
                                                            <div class="rating-label checked">
                                                                <i class="ki-outline ki-star fs-6"></i>
                                                            </div>
                                                            <div class="rating-label checked">
                                                                <i class="ki-outline ki-star fs-6"></i>
                                                            </div>
                                                            <div class="rating-label checked">
                                                                <i class="ki-outline ki-star fs-6"></i>
                                                            </div>
                                                        </div>
                                                        <span class="text-gray-500 fw-semibold d-block fs-7 mt-1">Rating</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-25px h-25px">
                                                            <i class="ki-outline ki-black-right fs-2 text-gray-500"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="symbol symbol- symbol-40px me-3">
                                                                <img src="assets/media/avatars/300-2.jpg" class="" alt="" />
                                                            </div>
                                                            <div class="d-flex justify-content-start flex-column">
                                                                <a href="account/overview.html" class="text-gray-800 fw-bold text-hover-primary mb-1 fs-6">Annette Black</a>
                                                                <span class="text-gray-500 fw-semibold d-block fs-7">Zuid Area</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-end">
                                                        <span class="text-gray-800 fw-bold d-block mb-1 fs-6">6,074</span>
                                                        <span class="fw-semibold text-gray-500 d-block">Deliveries</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="text-gray-800 fw-bold text-hover-primary d-block mb-1 fs-6">$174,074</a>
                                                        <span class="text-gray-500 fw-semibold d-block fs-7">Earnings</span>
                                                    </td>
                                                    <td class="float-end text-end border-0">
                                                        <div class="rating">
                                                            <div class="rating-label checked">
                                                                <i class="ki-outline ki-star fs-6"></i>
                                                            </div>
                                                            <div class="rating-label checked">
                                                                <i class="ki-outline ki-star fs-6"></i>
                                                            </div>
                                                            <div class="rating-label checked">
                                                                <i class="ki-outline ki-star fs-6"></i>
                                                            </div>
                                                            <div class="rating-label checked">
                                                                <i class="ki-outline ki-star fs-6"></i>
                                                            </div>
                                                            <div class="rating-label checked">
                                                                <i class="ki-outline ki-star fs-6"></i>
                                                            </div>
                                                        </div>
                                                        <span class="text-gray-500 fw-semibold d-block fs-7 mt-1">Rating</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-25px h-25px">
                                                            <i class="ki-outline ki-black-right fs-2 text-gray-500"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="symbol symbol- symbol-40px me-3">
                                                                <img src="assets/media/avatars/300-12.jpg" class="" alt="" />
                                                            </div>
                                                            <div class="d-flex justify-content-start flex-column">
                                                                <a href="account/overview.html" class="text-gray-800 fw-bold text-hover-primary mb-1 fs-6">Esther Howard</a>
                                                                <span class="text-gray-500 fw-semibold d-block fs-7">Zuid Area</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-end">
                                                        <span class="text-gray-800 fw-bold d-block mb-1 fs-6">357</span>
                                                        <span class="fw-semibold text-gray-500 d-block">Deliveries</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="text-gray-800 fw-bold text-hover-primary d-block mb-1 fs-6">$2,737</a>
                                                        <span class="text-gray-500 fw-semibold d-block fs-7">Earnings</span>
                                                    </td>
                                                    <td class="float-end text-end border-0">
                                                        <div class="rating">
                                                            <div class="rating-label checked">
                                                                <i class="ki-outline ki-star fs-6"></i>
                                                            </div>
                                                            <div class="rating-label checked">
                                                                <i class="ki-outline ki-star fs-6"></i>
                                                            </div>
                                                            <div class="rating-label checked">
                                                                <i class="ki-outline ki-star fs-6"></i>
                                                            </div>
                                                            <div class="rating-label checked">
                                                                <i class="ki-outline ki-star fs-6"></i>
                                                            </div>
                                                            <div class="rating-label checked">
                                                                <i class="ki-outline ki-star fs-6"></i>
                                                            </div>
                                                        </div>
                                                        <span class="text-gray-500 fw-semibold d-block fs-7 mt-1">Rating</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-25px h-25px">
                                                            <i class="ki-outline ki-black-right fs-2 text-gray-500"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="symbol symbol- symbol-40px me-3">
                                                                <img src="assets/media/avatars/300-11.jpg" class="" alt="" />
                                                            </div>
                                                            <div class="d-flex justify-content-start flex-column">
                                                                <a href="account/overview.html" class="text-gray-800 fw-bold text-hover-primary mb-1 fs-6">Guy Hawkins</a>
                                                                <span class="text-gray-500 fw-semibold d-block fs-7">Zuid Area</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-end">
                                                        <span class="text-gray-800 fw-bold d-block mb-1 fs-6">2,954</span>
                                                        <span class="fw-semibold text-gray-500 d-block">Deliveries</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="text-gray-800 fw-bold text-hover-primary d-block mb-1 fs-6">$59,634</a>
                                                        <span class="text-gray-500 fw-semibold d-block fs-7">Earnings</span>
                                                    </td>
                                                    <td class="float-end text-end border-0">
                                                        <div class="rating">
                                                            <div class="rating-label checked">
                                                                <i class="ki-outline ki-star fs-6"></i>
                                                            </div>
                                                            <div class="rating-label checked">
                                                                <i class="ki-outline ki-star fs-6"></i>
                                                            </div>
                                                            <div class="rating-label checked">
                                                                <i class="ki-outline ki-star fs-6"></i>
                                                            </div>
                                                            <div class="rating-label checked">
                                                                <i class="ki-outline ki-star fs-6"></i>
                                                            </div>
                                                            <div class="rating-label">
                                                                <i class="ki-outline ki-star fs-6"></i>
                                                            </div>
                                                        </div>
                                                        <span class="text-gray-500 fw-semibold d-block fs-7 mt-1">Rating</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-25px h-25px">
                                                            <i class="ki-outline ki-black-right fs-2 text-gray-500"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="symbol symbol- symbol-40px me-3">
                                                                <img src="assets/media/avatars/300-3.jpg" class="" alt="" />
                                                            </div>
                                                            <div class="d-flex justify-content-start flex-column">
                                                                <a href="account/overview.html" class="text-gray-800 fw-bold text-hover-primary mb-1 fs-6">Marvin McKinney</a>
                                                                <span class="text-gray-500 fw-semibold d-block fs-7">Zuid Area</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-end">
                                                        <span class="text-gray-800 fw-bold d-block mb-1 fs-6">822</span>
                                                        <span class="fw-semibold text-gray-500 d-block">Deliveries</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="text-gray-800 fw-bold text-hover-primary d-block mb-1 fs-6">$19,842</a>
                                                        <span class="text-gray-500 fw-semibold d-block fs-7">Earnings</span>
                                                    </td>
                                                    <td class="float-end text-end border-0">
                                                        <div class="rating">
                                                            <div class="rating-label checked">
                                                                <i class="ki-outline ki-star fs-6"></i>
                                                            </div>
                                                            <div class="rating-label checked">
                                                                <i class="ki-outline ki-star fs-6"></i>
                                                            </div>
                                                            <div class="rating-label checked">
                                                                <i class="ki-outline ki-star fs-6"></i>
                                                            </div>
                                                            <div class="rating-label checked">
                                                                <i class="ki-outline ki-star fs-6"></i>
                                                            </div>
                                                            <div class="rating-label checked">
                                                                <i class="ki-outline ki-star fs-6"></i>
                                                            </div>
                                                        </div>
                                                        <span class="text-gray-500 fw-semibold d-block fs-7 mt-1">Rating</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-25px h-25px">
                                                            <i class="ki-outline ki-black-right fs-2 text-gray-500"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            </tbody>

                                        </table>
                                    </div>

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

                                    <table class="table align-middle table-row-dashed fs-6 gy-3" id="kt_table_widget_4_table">

                                        <thead>

                                            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                                <th class="min-w-100px">Order ID</th>
                                                <th class="text-end min-w-100px">Created</th>
                                                <th class="text-end min-w-125px">Customer</th>
                                                <th class="text-end min-w-100px">Total</th>
                                                <th class="text-end min-w-100px">Profit</th>
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