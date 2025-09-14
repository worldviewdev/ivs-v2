<?php
require_once('../includes/midas.inc.php');

// Set header untuk JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    // Ambil parameter dari DataTable
    $draw = isset($_GET['draw']) ? intval($_GET['draw']) : 1;
    $start = isset($_GET['start']) ? intval($_GET['start']) : 0;
    $length = isset($_GET['length']) ? intval($_GET['length']) : 10;
    $searchValue = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';
    $orderColumn = isset($_GET['order'][0]['column']) ? intval($_GET['order'][0]['column']) : 0;
    $orderDir = isset($_GET['order'][0]['dir']) ? $_GET['order'][0]['dir'] : 'desc';
    
    // Mapping kolom untuk ordering
    $columns = [
        'file_code',
        'file_arrival_date', 
        'client_name',
        'agent_name',
        'active_staff_name',
        'file_current_status',
        'file_type',
        'file_type_desc'
    ];
    
    $orderBy = isset($columns[$orderColumn]) ? $columns[$orderColumn] : 'file_id';
    if ($orderDir == 'desc') {
        $orderBy .= ' DESC';
    } else {
        $orderBy .= ' ASC';
    }
    $sql_add = " AND (file_primary_staff='" . $_SESSION['sess_agent_id'] . "' OR file_active_staff='" . $_SESSION['sess_agent_id'] . "')";
    // Query utama
    $baseSql = "SELECT f.*, 
                CONCAT(emp_first_name,' ',emp_last_name) as active_staff_name,
                CONCAT(client_first_name,' ',client_last_name) as client_name,
                CONCAT(agent_first_name,' ',agent_last_name) as agent_name  
                FROM mv_files f 
                LEFT JOIN mv_employee e ON f.file_active_staff=e.emp_id  
                LEFT JOIN mv_client c ON f.fk_client_id=c.client_id 
                LEFT JOIN mv_agent a ON f.fk_agent_id=a.agent_id 
                LEFT JOIN mv_agency ag ON ag.agency_id=a.fk_agency_id 
                WHERE file_status!='Delete' 
                AND file_admin_type = 'Admin' 
                $sql_add
                AND is_package_file='No'";
    
    // Tambahkan search filter
    if (!empty($searchValue)) {
        $baseSql .= " AND (f.file_code LIKE '%$searchValue%' 
                          OR CONCAT(client_first_name,' ',client_last_name) LIKE '%$searchValue%'
                          OR CONCAT(agent_first_name,' ',agent_last_name) LIKE '%$searchValue%'
                          OR CONCAT(emp_first_name,' ',emp_last_name) LIKE '%$searchValue%'
                          OR f.file_arrival_date LIKE '%$searchValue%')";
    }
    
    // Query untuk total records
    $countSql = "SELECT COUNT(*) as total FROM ($baseSql) as count_table";
    $countResult = db_query($countSql);
    $totalRecords = mysqli_fetch_assoc($countResult)['total'];
    
    // Query dengan pagination dan ordering
    $sql = $baseSql . " ORDER BY $orderBy LIMIT $start, $length";
    $result = db_query($sql);
    
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $status = $row['file_current_status'];
        $statusText = $arr_file_status[$status] ?? '-';
        
        // Mapping status classes dan colors
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
        
        $data[] = [
            'file_code' => $row['file_code'],
            'file_arrival_date' => $row['file_arrival_date'],
            'client_name' => $row['client_name'],
            'agent_name' => $row['agent_name'],
            'active_staff_name' => $row['active_staff_name'],
            'status' => [
                'text' => $statusText,
                'class' => $cls,
                'bg_color' => $bgColor
            ],
            'file_type' => $arr_file_types[$row['file_type']] ?? '-',
            'file_type_desc' => $row['file_type_desc'],
            'row_class' => $cls,
            'row_bg_color' => $bgColor
        ];
    }
    
    // Response untuk DataTable
    $response = [
        'draw' => $draw,
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $totalRecords,
        'data' => $data
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Server Error',
        'message' => $e->getMessage()
    ]);
}
?>
