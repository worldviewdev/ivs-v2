<?php
require_once __DIR__ . '/../core/StatusHelper.php';

class FileController
{
    public function index()
    {
        // Check if this is a request for latest files
        if (isset($_GET['action']) && $_GET['action'] === 'get_latest_files') {
            $this->getLatestFiles();
            return;
        }

        // Check if this is a request for sales paid files
        if (isset($_GET['action']) && $_GET['action'] === 'get_sales_paid') {
            $this->getSalesPaid();
            return;
        }

        if (isset($_GET['action']) && $_GET['action'] === 'get_motivation_files') {
            $this->getMotivationFiles();
            return;
        }

        if (isset($_GET['action']) && $_GET['action'] === 'get_current_year_files') {
            $this->getCurrentYearFiles();
            return;
        }

        if (isset($_GET['action']) && $_GET['action'] === 'get_paid_in_full_files') {
            $this->getPaidInFullFiles();
            return;
        }

        if (isset($_GET['action']) && $_GET['action'] === 'get_abandoned_files') {
            $this->getAbandonedFiles();
            return;
        }

        if (isset($_GET['action']) && $_GET['action'] === 'get_archived_files') {
            $this->getArchivedFiles();
            return;
        }

        $draw = (int)($_GET['draw'] ?? 1);
        $start = (int)($_GET['start'] ?? 0);
        $length = (int)($_GET['length'] ?? 10);
        $searchValue = $_GET['search']['value'] ?? '';
        $orderColumn = $_GET['order'][0]['column'] ?? 0;
        $orderDir = $_GET['order'][0]['dir'] ?? 'desc';

        $columns = ['file_code', 'file_arrival_date', 'client_name', 'agent_name', 'active_staff_name', 'file_current_status', 'file_type', 'file_type_desc', 'file_added_on'];
        $orderBy = $columns[$orderColumn] ?? 'file_added_on';

        $agentId = $_SESSION['sess_agent_id'] ?? 1;

        // Get filter parameters
        $statusFilter = $_GET['status_filter'] ?? '';
        $dateFilter = $_GET['date_filter'] ?? '';
        $dateFrom = $_GET['date_from'] ?? '';
        $dateTo = $_GET['date_to'] ?? '';

        $repo = new FileRepository();
        $files = $repo->getFiles($start, $length, $orderBy, $orderDir, $searchValue, $agentId, $statusFilter, $dateFilter, $dateFrom, $dateTo);
        $total = $repo->countFiles($searchValue, $agentId, true, $statusFilter, $dateFilter, $dateFrom, $dateTo);

        // Process files to add status info
        $processedFiles = [];
        foreach ($files as $file) {
            $statusInfo = StatusHelper::getStatusInfo($file['file_current_status'], $file['file_type']);
            
            $processedFiles[] = [
                'file_code' => $file['file_code'],
                'file_arrival_date' => $file['file_arrival_date'],
                'client_name' => $file['client_name'],
                'agent_name' => $file['agent_name'],
                'active_staff_name' => $file['active_staff_name'],
                'status' => [
                    'text' => $statusInfo['text'],
                    'class' => $statusInfo['class'],
                    'bg_color' => $statusInfo['bg_color']
                ],
                'file_type' => $statusInfo['file_type_text'],
                'file_type_desc' => $file['file_type_desc'],
                'file_added_on' => $file['file_added_on'],
                'row_class' => $statusInfo['class'],
                'row_bg_color' => $statusInfo['bg_color'],
                // Include original data for reference
                'file_current_status' => $file['file_current_status'],
                'file_type_id' => $file['file_type']
            ];
        }

        Response::json([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $processedFiles
        ]);
    }

    public function getLatestFiles()
    {
        $limit = (int)($_GET['limit'] ?? 7);
        $agentId = $_GET['agent_id'] ?? $_SESSION['sess_agent_id'] ?? 1;

        $repo = new FileRepository();
        $files = $repo->getLatestFiles($agentId, $limit);

        // Process files to add status info
        $processedFiles = [];
        foreach ($files as $file) {
            $statusInfo = StatusHelper::getStatusInfo($file['file_current_status'], $file['file_type']);
            
            $processedFiles[] = [
                'file_id' => $file['file_id'],
                'file_code' => $file['file_code'],
                'file_arrival_date' => $file['file_arrival_date'],
                'client_name' => $file['client_name'],
                'agent_name' => $file['agent_name'],
                'active_staff_name' => $file['active_staff_name'],
                'status' => [
                    'text' => $statusInfo['text'],
                    'class' => $statusInfo['class'],
                    'bg_color' => $statusInfo['bg_color']
                ],
                'file_type' => $statusInfo['file_type_text'],
                'file_type_desc' => $file['file_type_desc'],
                'notes' => '', // Add empty notes field
                'row_class' => $statusInfo['class'],
                'row_bg_color' => $statusInfo['bg_color'],
                // Include original data for reference
                'file_current_status' => $file['file_current_status'],
                'file_type_id' => $file['file_type']
            ];
        }

        Response::json([
            'data' => $processedFiles
        ]);
    }

    public function getSalesPaid()
    {
        $limit = (int)($_GET['limit'] ?? 7);
        $agentId = $_GET['agent_id'] ?? $_SESSION['sess_agent_id'] ?? 1;

        $repo = new FileRepository();
        $files = $repo->getSalesPaid($agentId, $limit);

        // Process files to add status info
        $processedFiles = [];
        foreach ($files as $file) {
            $statusInfo = StatusHelper::getStatusInfo($file['file_current_status'], $file['file_type']);
            
            $processedFiles[] = [
                'file_id' => $file['file_id'],
                'file_code' => $file['file_code'],
                'file_arrival_date' => $file['file_departure_date'], // Use departure_date as arrival_date for consistency
                'client_name' => $file['client_name'],
                'agent_name' => $file['agent_name'],
                'active_staff_name' => $file['active_staff_name'],
                'status' => [
                    'text' => $statusInfo['text'],
                    'class' => $statusInfo['class'],
                    'bg_color' => $statusInfo['bg_color']
                ],
                'file_type' => $statusInfo['file_type_text'],
                'file_type_desc' => $file['file_type_desc'],
                'notes' => '', // Add empty notes field for consistency
                'row_class' => $statusInfo['class'],
                'row_bg_color' => $statusInfo['bg_color'],
                // Include original data for reference
                'file_current_status' => $file['file_current_status'],
                'file_type_id' => $file['file_type']
            ];
        }

        Response::json([
            'data' => $processedFiles
        ]);
    }

    public function getMotivationFiles()
    {
        $limit = (int)($_GET['limit'] ?? 10);
        
        $repo = new FileRepository();
        $files = $repo->getMotivationFiles($limit);

        // Process files to add status info
        $processedFiles = [];
        foreach ($files as $file) {
            $statusInfo = StatusHelper::getStatusInfo($file['file_current_status'], $file['file_type']);
            
            $processedFiles[] = [
                'file_id' => $file['file_id'],
                'file_code' => $file['file_code'],
                'file_arrival_date' => $file['file_arrival_date'],
                'client_name' => $file['client_name'],
                'agent_name' => $file['agent_name'],
                'active_staff_name' => $file['active_staff_name'],
                'status' => [
                    'text' => $statusInfo['text'],
                    'class' => $statusInfo['class'],
                    'bg_color' => $statusInfo['bg_color']
                ],
                'file_type' => $statusInfo['file_type_text'],
                'file_type_desc' => $file['file_type_desc'],
                'notes' => '', // Add empty notes field for consistency
                'row_class' => $statusInfo['class'],
                'row_bg_color' => $statusInfo['bg_color'],
                // Include original data for reference
                'file_current_status' => $file['file_current_status'],
                'file_type_id' => $file['file_type']
            ];
        }

        Response::json([
            'data' => $processedFiles
        ]);
    }

    public function getAbandonedFiles()
    {
        $agentId = $_GET['agent_id'] ?? $_SESSION['sess_agent_id'] ?? 1;
        $draw = (int)($_GET['draw'] ?? 1);

        $start = (int)($_GET['start'] ?? 0);
        $length = (int)($_GET['length'] ?? 10);
        $searchValue = $_GET['search']['value'] ?? '';
        $orderColumn = $_GET['order'][0]['column'] ?? 0;
        $orderDir = $_GET['order'][0]['dir'] ?? 'desc';
        $statusFilter = $_GET['status_filter'] ?? '';
        $dateFilter = $_GET['date_filter'] ?? '';
        $dateFrom = $_GET['date_from'] ?? '';
        $dateTo = $_GET['date_to'] ?? '';
        $fileType = $_GET['file_type'] ?? '';
        $staffId = $_GET['staff_id'] ?? '';
        $isSuperAdmin = ($_SESSION['sess_super_admin'] ?? '') == 'SuperAdmin';
        
        $columns = ['file_code', 'file_arrival_date', 'client_name', 'agent_name', 'active_staff_name', 'file_current_status', 'file_type', 'file_type_desc', 'file_added_on'];
        $orderBy = $columns[$orderColumn] ?? 'file_added_on';

        $repo = new FileRepository();
        $files = $repo->getAbandonedFiles($start, $length, $orderBy, $orderDir, $searchValue, $agentId, $statusFilter, $dateFilter, $dateFrom, $dateTo, $isSuperAdmin, $fileType, $staffId);
        $total = $repo->countAbandonedFiles($searchValue, $agentId, $isSuperAdmin, $fileType, $staffId, $dateFilter, $dateFrom, $dateTo);

        // Process files to add status info
        $processedFiles = [];
        foreach ($files as $file) {
            $statusInfo = StatusHelper::getStatusInfo($file['file_current_status'], $file['file_type']);
            
            $processedFiles[] = [
                'file_id' => $file['file_id'],
                'file_code' => $file['file_code'],
                'file_arrival_date' => $file['file_arrival_date'],
                'client_name' => $file['client_name'],
                'agent_name' => $file['agent_name'],
                'active_staff_name' => $file['active_staff_name'],
                'status' => [
                    'text' => $statusInfo['text'],
                    'class' => $statusInfo['class'],
                    'bg_color' => $statusInfo['bg_color']
                ],
                'file_type' => $statusInfo['file_type_text'],
                'file_type_desc' => $file['file_type_desc'] ? mb_substr($file['file_type_desc'], 0, 50) . '...' : '',
                'notes' => '', // Add empty notes field for consistency
                'row_class' => $statusInfo['class'],
                'row_bg_color' => $statusInfo['bg_color'],
                // Include original data for reference
                'file_current_status' => $file['file_current_status'],
                'file_type_id' => $file['file_type']
            ];
        }

        Response::json([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $processedFiles
        ]);
    }

    public function getArchivedFiles()
    {
        $agentId = $_GET['agent_id'] ?? $_SESSION['sess_agent_id'] ?? 1;
        $draw = (int)($_GET['draw'] ?? 1);
        
        $start = (int)($_GET['start'] ?? 0);
        $length = (int)($_GET['length'] ?? 10);
        $searchValue = $_GET['search']['value'] ?? '';
        $orderColumn = $_GET['order'][0]['column'] ?? 0;
        $orderDir = $_GET['order'][0]['dir'] ?? 'desc';
        $statusFilter = $_GET['status_filter'] ?? '';
        $dateFilter = $_GET['date_filter'] ?? '';
        $dateFrom = $_GET['date_from'] ?? '';
        $dateTo = $_GET['date_to'] ?? '';
        $fileType = $_GET['file_type'] ?? '';
        $staffId = $_GET['staff_id'] ?? '';
        $isSuperAdmin = ($_SESSION['sess_super_admin'] ?? '') == 'SuperAdmin';

        $columns = ['file_code', 'file_arrival_date', 'client_name', 'agent_name', 'active_staff_name', 'file_current_status', 'file_type', 'file_type_desc', 'file_added_on'];
        $orderBy = $columns[$orderColumn] ?? 'file_added_on';

        $repo = new FileRepository();
        $files = $repo->getArchivedFiles($start, $length, $orderBy, $orderDir, $searchValue, $agentId, $statusFilter, $dateFilter, $dateFrom, $dateTo, $isSuperAdmin, $fileType, $staffId);
        $total = $repo->countArchivedFiles($searchValue, $agentId, $statusFilter, $dateFilter, $dateFrom, $dateTo, $isSuperAdmin, $fileType, $staffId);

        // Process files to add status info
        $processedFiles = [];
        foreach ($files as $file) {
            $statusInfo = StatusHelper::getStatusInfo($file['file_current_status'], $file['file_type']);
            
            $processedFiles[] = [
                'file_id' => $file['file_id'],
                'id' => $file['file_id'], // Add id field for compatibility
                'file_code' => $file['file_code'],
                'file_arrival_date' => $file['file_arrival_date'],
                'client_name' => $file['client_name'],
                'agent_name' => $file['agent_name'],
                'active_staff_name' => $file['active_staff_name'],
                'status' => [
                    'text' => $statusInfo['text'],
                    'class' => $statusInfo['class'],
                    'bg_color' => $statusInfo['bg_color']
                ],
                'file_type' => $statusInfo['file_type_text'],
                'file_type_desc' => $file['file_type_desc'],
                'file_added_on' => $file['file_added_on'],
                'notes' => '', // Add empty notes field for consistency
                'row_class' => $statusInfo['class'],
                'row_bg_color' => $statusInfo['bg_color'],
                // Include original data for reference
                'file_current_status' => $file['file_current_status'],
                'file_type_id' => $file['file_type']
            ];
        }

        Response::json([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $processedFiles
        ]);
    }

    public function getCurrentYearFiles()
    {
        $limit = (int)($_GET['limit'] ?? 10);
        $agentId = $_GET['agent_id'] ?? $_SESSION['sess_agent_id'] ?? 1;        
        $draw = (int)($_GET['draw'] ?? 1);

        $start = (int)($_GET['start'] ?? 0);
        $length = (int)($_GET['length'] ?? 10);
        $searchValue = $_GET['search']['value'] ?? '';
        $orderColumn = $_GET['order'][0]['column'] ?? 0;
        $orderDir = $_GET['order'][0]['dir'] ?? 'desc';
        $statusFilter = $_GET['status_filter'] ?? '';
        $dateFilter = $_GET['date_filter'] ?? '';
        $dateFrom = $_GET['date_from'] ?? '';
        $dateTo = $_GET['date_to'] ?? '';
        $fileType = $_GET['file_type'] ?? '';
        $staffId = $_GET['staff_id'] ?? '';
        $isSuperAdmin = ($_SESSION['sess_super_admin'] ?? '') == 'SuperAdmin';
        
        $columns = ['file_code', 'file_arrival_date', 'client_name', 'agent_name', 'active_staff_name', 'file_current_status', 'file_type', 'file_type_desc', 'file_added_on'];
        $orderBy = $columns[$orderColumn] ?? 'file_added_on';

        $repo = new FileRepository();
        $files = $repo->getCurrentYearFiles($start, $length, $orderBy, $orderDir, $searchValue, $agentId, $statusFilter, $dateFilter, $dateFrom, $dateTo, $isSuperAdmin, $fileType, $staffId);
        $total = $repo->countFiles($searchValue, $agentId, true, $statusFilter, $dateFilter, $dateFrom, $dateTo, $isSuperAdmin, $fileType, $staffId);
        // Process files to add status info
        $processedFiles = [];
        foreach ($files as $file) {
            $statusInfo = StatusHelper::getStatusInfo($file['file_current_status'], $file['file_type']);
            
            $processedFiles[] = [
                'file_id' => $file['file_id'],
                'file_code' => $file['file_code'],
                'file_arrival_date' => $file['file_arrival_date'],
                'client_name' => $file['client_name'],
                'agent_name' => $file['agent_name'],
                'active_staff_name' => $file['active_staff_name'],
                'status' => [
                    'text' => $statusInfo['text'],
                    'class' => $statusInfo['class'],
                    'bg_color' => $statusInfo['bg_color']
                ],
                'file_type' => $statusInfo['file_type_text'],
                'file_type_desc' => $file['file_type_desc'],
                'notes' => '', // Add empty notes field for consistency
                'row_class' => $statusInfo['class'],
                'row_bg_color' => $statusInfo['bg_color'],
                // Include original data for reference
                'file_current_status' => $file['file_current_status'],
                'file_type_id' => $file['file_type']
            ];
        }

        Response::json([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $processedFiles
        ]);
    }

    public function getPaidInFullFiles()
    {
        $agentId = $_GET['agent_id'] ?? $_SESSION['sess_agent_id'] ?? 1;
        $draw = (int)($_GET['draw'] ?? 1);
        
        $start = (int)($_GET['start'] ?? 0);
        $length = (int)($_GET['length'] ?? 10);
        $searchValue = $_GET['search']['value'] ?? '';
        $orderColumn = $_GET['order'][0]['column'] ?? 0;
        $orderDir = $_GET['order'][0]['dir'] ?? 'desc';
        $statusFilter = $_GET['status_filter'] ?? '';
        $dateFilter = $_GET['date_filter'] ?? '';
        $dateFrom = $_GET['date_from'] ?? '';
        $dateTo = $_GET['date_to'] ?? '';
        $fileType = $_GET['file_type'] ?? '';
        $staffId = $_GET['staff_id'] ?? '';
        $isSuperAdmin = ($_SESSION['sess_super_admin'] ?? '') == 'SuperAdmin';
        $displayBy = $_GET['displayBy'] ?? 0;
        $fromDate = $_GET['from_date'] ?? '';
        $toDate = $_GET['to_date'] ?? '';

        $columns = ['file_code', 'file_arrival_date', 'client_name', 'agent_name', 'active_staff_name', 'file_current_status', 'file_type', 'file_type_desc', 'file_added_on'];
        $orderBy = $columns[$orderColumn] ?? 'file_id';

        $repo = new FileRepository();
        $files = $repo->getPaidInFullFiles($start, $length, $orderBy, $orderDir, $searchValue, $agentId, $statusFilter, $dateFilter, $dateFrom, $dateTo, $isSuperAdmin, $fileType, $staffId, $displayBy, $fromDate, $toDate);
        $total = $repo->countPaidInFullFiles($searchValue, $agentId, $isSuperAdmin, $fileType, $staffId, $dateFilter, $dateFrom, $dateTo, $displayBy, $fromDate, $toDate);

        // Process files to add status info
        $processedFiles = [];
        foreach ($files as $file) {
            $statusInfo = StatusHelper::getStatusInfo($file['file_current_status'], $file['file_type']);
            
            $processedFiles[] = [
                'file_id' => $file['file_id'],
                'id' => $file['file_id'], // Add id field for compatibility
                'file_code' => $file['file_code'],
                'file_arrival_date' => $file['file_arrival_date'],
                'client_name' => $file['client_name'],
                'agent_name' => $file['agent_name'],
                'active_staff_name' => $file['active_staff_name'],
                'status' => [
                    'text' => $statusInfo['text'],
                    'class' => $statusInfo['class'],
                    'bg_color' => $statusInfo['bg_color']
                ],
                'file_type' => $statusInfo['file_type_text'],
                'file_type_desc' => $file['file_type_desc'],
                'file_added_on' => $file['file_added_on'],
                'notes' => '', // Add empty notes field for consistency
                'row_class' => $statusInfo['class'],
                'row_bg_color' => $statusInfo['bg_color'],
                // Include original data for reference
                'file_current_status' => $file['file_current_status'],
                'file_type_id' => $file['file_type']
            ];
        }

        Response::json([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $processedFiles
        ]);
    }

    public function show()
    {
        $path = strtok($_SERVER['REQUEST_URI'], '?');
        $path = str_replace('/v1/api', '', $path);
        $id = str_replace('/files/', '', $path);

        if (!is_numeric($id)) {
            Response::json(['error' => 'Invalid file ID'], 400);
            return;
        }

        $repo = new FileRepository();
        $file = $repo->getFileById($id);

        if (!$file) {
            Response::json(['error' => 'File not found'], 404);
            return;
        }

        Response::json(['data' => $file]);
    }

    public function allFiles()
    {
        $draw = (int)($_GET['draw'] ?? 1);
        $start = (int)($_GET['start'] ?? 0);
        $length = (int)($_GET['length'] ?? 10);
        $searchValue = $_GET['search']['value'] ?? '';
        $orderColumn = $_GET['order'][0]['column'] ?? 0;
        $orderDir = $_GET['order'][0]['dir'] ?? 'desc';
        $agentId = $_SESSION['sess_agent_id'] ?? 1;
        $columns = ['file_code', 'file_arrival_date', 'client_name', 'agent_name', 'active_staff_name', 'file_current_status', 'file_type', 'file_type_desc', 'file_added_on'];
        $orderBy = $columns[$orderColumn] ?? 'file_added_on';

        // Get filter parameters
        $statusFilter = $_GET['status_filter'] ?? '';
        $dateFilter = $_GET['date_filter'] ?? '';
        $dateFrom = $_GET['date_from'] ?? '';
        $dateTo = $_GET['date_to'] ?? '';

        if(isset($_SESSION['sess_super_admin']) && $_SESSION['sess_super_admin'] == 'SuperAdmin') {
            $myFiles = false;
        } else {
            $myFiles = true;
        }

        $repo = new FileRepository();
        $files = $repo->getAllFiles($start, $length, $orderBy, $orderDir, $searchValue, $agentId, $myFiles, $statusFilter, $dateFilter, $dateFrom, $dateTo);
        $total = $repo->countFiles($searchValue, $agentId, $myFiles, $statusFilter, $dateFilter, $dateFrom, $dateTo);

        // Process files to add status info
        $processedFiles = [];
        foreach ($files as $file) {
            $statusInfo = StatusHelper::getStatusInfo($file['file_current_status'], $file['file_type']);
            
            $processedFiles[] = [
                'id' => $file['file_id'], // Add id field for compatibility
                'file_code' => $file['file_code'],
                'file_arrival_date' => $file['file_arrival_date'],
                'client_name' => $file['client_name'],
                'agent_name' => $file['agent_name'],
                'active_staff_name' => $file['active_staff_name'],
                'status' => [
                    'text' => $statusInfo['text'],
                    'class' => $statusInfo['class'],
                    'bg_color' => $statusInfo['bg_color']
                ],
                'file_type' => $statusInfo['file_type_text'],
                'file_type_desc' => $file['file_type_desc'],
                'row_class' => $statusInfo['class'],
                'row_bg_color' => $statusInfo['bg_color'],
                // Include original data for reference
                'file_current_status' => $file['file_current_status'],
                'file_type_id' => $file['file_type']
            ];
        }

        Response::json([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $processedFiles
        ]);
    }


    public function store()
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            Response::json(['error' => 'Invalid JSON input'], 400);
            return;
        }

        // Validasi input yang diperlukan
        $required = ['file_code', 'fk_client_id', 'fk_agent_id'];
        foreach ($required as $field) {
            if (!isset($input[$field]) || empty($input[$field])) {
                Response::json(['error' => "Field $field is required"], 400);
                return;
            }
        }

        $repo = new FileRepository();
        $result = $repo->createFile($input);

        if ($result) {
            Response::json(['message' => 'File created successfully', 'data' => $result], 201);
        } else {
            Response::json(['error' => 'Failed to create file'], 500);
        }
    }

    public function update()
    {
        $path = strtok($_SERVER['REQUEST_URI'], '?');
        $path = str_replace('/v1/api', '', $path);
        $id = str_replace('/files/', '', $path);

        if (!is_numeric($id)) {
            Response::json(['error' => 'Invalid file ID'], 400);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            Response::json(['error' => 'Invalid JSON input'], 400);
            return;
        }

        $repo = new FileRepository();
        $result = $repo->updateFile($id, $input);

        if ($result) {
            Response::json(['message' => 'File updated successfully', 'data' => $result]);
        } else {
            Response::json(['error' => 'Failed to update file'], 500);
        }
    }

    public function destroy()
    {
        $path = strtok($_SERVER['REQUEST_URI'], '?');
        $path = str_replace('/v1/api', '', $path);
        $id = str_replace('/files/', '', $path);

        if (!is_numeric($id)) {
            Response::json(['error' => 'Invalid file ID'], 400);
            return;
        }

        $repo = new FileRepository();
        $result = $repo->deleteFile($id);

        if ($result) {
            Response::json(['message' => 'File deleted successfully']);
        } else {
            Response::json(['error' => 'Failed to delete file'], 500);
        }
    }
}
