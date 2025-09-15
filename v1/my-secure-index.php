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

// Query untuk menghitung total sales dan profit dari awal bulan hingga hari ini
$curr_year = date("Y");
$curr_month = date("m");
$curr_day = date("d");
$payment_type = 3; // Balance

// Hitung tanggal awal bulan dan hari ini
$start_date = $curr_year . '-' . $curr_month . '-01';
$end_date = $curr_year . '-' . $curr_month . '-' . $curr_day;

$sql_sales_profit = "select f.*,p.payment_applied_on from mv_files f left join mv_file_payment p on f.file_id=p.fk_file_id where file_primary_staff='" . $_SESSION['sess_agent_id'] . "' and DATE(payment_applied_on) >= '$start_date' and DATE(payment_applied_on) <= '$end_date' and display_file='No' and payment_type='$payment_type' and payment_status='Active' and payment_made='Yes'";

$result = db_query($sql_latest_sales);
$result_nolimit = db_query($sql_nolimit);
$result_sales_paid = db_query($sql_sales_paid);
$result_sales_profit = db_query($sql_sales_profit);

// Hitung total sales dan profit
$total_sale = 0;
$total_profit = 0;

if (mysqli_num_rows($result_sales_profit) > 0) {
    while ($res = mysqli_fetch_array($result_sales_profit)) {
        if ($res['net_total_sc'] > 0) {
            $net_sc = $res['net_total_sc'];
        } else {
            update_file_total($res['file_id']);
            $net_sc = db_scalar("select net_total_sc from mv_files where file_id='" . $res['file_id'] . "'");
        }
        $profit = $res['gross_total_sc'] - $net_sc - $res['gross_agent_commission'] - $res['gross_tax'] - $res['additional_agent_comm'];
        $sale_cad = $res['gross_total_sc'] * file_exchnage_rates($res['file_id'], $res['file_currency'], "CAD");
        $sale_cad = ceil($sale_cad);
        $profit_cad = $profit * file_exchnage_rates($res['file_id'], $res['file_currency'], "CAD");
        $total_sale += $sale_cad;
        $total_profit += $profit_cad;
    }
}

// Hitung persentase
if ($total_sale > 0) {
    $profit_per = round((($total_profit * 100) / $total_sale), 1);
    $sales_per = 100 - $profit_per;
    $sales_per = round($sales_per, 1);
} else {
    // Jika sales 0, maka profit juga 0, tampilkan 0 semua
    $profit_per = 0;
    $sales_per = 0;
}
?>

<script src="//code.highcharts.com/highcharts.js"></script>
<script src="//code.highcharts.com/modules/exporting.js"></script>
<script src="<?php echo SITE_WS_PATH; ?>/assets/plugins/custom/datatables/datatables.bundle.js"></script>


<div id="kt_app_content" class="app-content flex-column-fluid">

    <div id="kt_app_content_container" class="app-container container-fluid">
        <div class="alert alert-success d-flex align-items-center p-5 mb-10">
            <i class="ki-duotone ki-shield-tick fs-2hx text-primary me-4"><span class="path1"></span><span class="path2"></span></i>
            <div class="d-flex flex-column">
                <h4 class="mb-1 text-primary">Wellcome back!! <?php echo $_SESSION['sess_agent_name']; ?></h4>
                <span class="text-primary">Today is <?php echo date('j F Y'); ?></span>
            </div>
        </div>
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">

            <div class="col-xl-4">

                <div class="card card-flush h-lg-100">


                    <div class="card-body d-flex align-items-end pt-6">

                        <div class="row align-items-center mx-0 w-100">

                            <div class="col-12 px-0">

                                <div class="d-flex flex-column content-justify-center">

                                    <div class="d-flex fs-6 fw-semibold align-items-center">

                                        <div id="container_sales" style="width: 100%; height: 400px; margin: 0 auto"></div>

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


                                <div class="ms-auto fw-bolder text-gray-700 text-end"><?= $sales_per ?>%</div>

                            </div>


                            <div class="d-flex fs-6 fw-semibold align-items-center my-4">

                                <div class="bullet bg-primary me-3" style="border-radius: 3px;width: 12px;height: 12px"></div>


                                <div class="fs-5 fw-bold text-gray-600 me-5">Profit:</div>


                                <div class="ms-auto fw-bolder text-gray-700 text-end"><?= $profit_per ?>%</div>

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

                    <table id="kt_datatable_vertical_scroll_2" class="table table-row-bordered gy-5 dataTable gs-7">
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

    </div>

</div>

<script>
$(function() {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container_sales',
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                backgroundColor: 'transparent',
                spacing: [20, 20, 20, 20]
            },
            title: {
                text: 'Sales vs Profit <?= date('F Y') ?> (1-<?= $curr_day ?>)',
                style: {
                    fontSize: '16px',
                    fontWeight: 'bold',
                    color: '#333333'
                }
            },
            tooltip: {
                formatter: function() {
                    return '<b>' + this.point.name + '</b>: ' + this.percentage + ' %';
                },
                backgroundColor: 'rgba(255, 255, 255, 0.95)',
                borderColor: '#cccccc',
                borderRadius: 5,
                shadow: true
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    size: '80%',
                    dataLabels: {
                        enabled: true,
                        color: '#ffffff',
                        style: {
                            fontSize: '14px',
                            fontWeight: 'bold',
                            textShadow: '1px 1px 2px rgba(0,0,0,0.5)'
                        },
                        connectorColor: '#333333',
                        formatter: function() {
                            return '<b>' + this.point.name + '</b><br/>' + this.percentage + ' %';
                        }
                    },
                    showInLegend: false
                }
            },
            colors: ['#28a745', '#007bff'], // Hijau untuk Sales, Biru untuk Profit
            series: [{
                type: 'pie',
                name: 'Sales vs Profit',
                data: [
                    <?php if ($total_sale > 0) { ?>
                    ['Sales', <?= $sales_per ?>],
                    ['Profit', <?= $profit_per ?>]
                    <?php } else { ?>
                    ['Sales', 0],
                    ['Profit', 0]
                    <?php } ?>
                ]
            }]
        });
    });
});
</script>

<script>
$(document).ready(function() {
    // Initialize DataTable for Latest Files using reusable component
    const latestFilesTable = createDataTable('files', {
        ajax: {
            tableId: '#kt_datatable_vertical_scroll',
            url: 'api/files',
            data: function(d) {
                return {
                    ...d,
                    action: 'get_latest_files',
                    limit: 7,
                    agent_id: '<?= $_SESSION['sess_agent_id'] ?>'
                };
            },
            dataSrc: 'data' // Specify that data is in 'data' property
        },
        pageLength: 7,
        lengthMenu: [[7], [7]],
        paging: false,
        searching: false,
        info: false,
        lengthChange: false,
        dom: 'rt',
        processing: false,
        serverSide: false
    });

    // Initialize DataTable for Sales Paid Files - exactly same as table 1
    const salesPaidTable = createDataTable('files', {
        tableId: '#kt_datatable_vertical_scroll_2',
        ajax: {
            url: 'api/files',
            data: function(d) {
                return {
                    ...d,
                    action: 'get_sales_paid',
                    limit: 7,
                    agent_id: '<?= $_SESSION['sess_agent_id'] ?>'
                };
            },
            dataSrc: 'data' // Specify that data is in 'data' property
        },
        pageLength: 7,
        lengthMenu: [[7], [7]],
        paging: false,
        searching: false,
        info: false,
        lengthChange: false,
        dom: 'rt',
        processing: false,
        serverSide: false
    });
});
</script>

<?php require_once('footer.inc.php'); ?>