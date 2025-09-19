<?php
require_once __DIR__ . '/../core/StatusHelper.php';

class FileController
{
    /**
     * Process files data to add status information
     */
    private function processFilesData($files, $useDepartureDate = false)
    {
        $processedFiles = [];
        foreach ($files as $file) {
            $statusInfo = StatusHelper::getStatusInfo($file['file_current_status'], $file['file_type']);
            
            // Handle special case for sales paid files that use departure_date
            $arrivalDate = $useDepartureDate ? 
                ($file['file_departure_date'] ?? $file['file_arrival_date'] ?? '') : 
                ($file['file_arrival_date'] ?? '');
            
            // Handle file_type_desc truncation for abandoned files
            $fileTypeDesc = $file['file_type_desc'] ?? '';
            if (isset($file['is_abandoned']) && $file['is_abandoned'] && $fileTypeDesc) {
                $fileTypeDesc = mb_substr($fileTypeDesc, 0, 50) . '...';
            }
            
            $processedFiles[] = [
                'file_id' => $file['file_id'],
                'id' => $file['file_id'], // Add id field for compatibility
                'file_code' => $file['file_code'],
                'file_arrival_date' => $arrivalDate,
                'client_name' => $file['client_name'],
                'agent_name' => $file['agent_name'],
                'active_staff_name' => $file['active_staff_name'],
                'status' => [
                    'text' => $statusInfo['text'],
                    'class' => $statusInfo['class'],
                    'bg_color' => $statusInfo['bg_color']
                ],
                'file_type' => $statusInfo['file_type_text'],
                'file_type_desc' => $fileTypeDesc,
                'file_added_on' => $file['file_added_on'] ?? '',
                'notes' => '', // Add empty notes field for consistency
                'row_class' => $statusInfo['class'],
                'row_bg_color' => $statusInfo['bg_color'],
                // Include original data for reference
                'file_current_status' => $file['file_current_status'],
                'file_type_id' => $file['file_type']
            ];
        }
        
        return $processedFiles;
    }

    /**
     * Get common pagination parameters from request
     */
    private function getPaginationParams()
    {
        return [
            'draw' => (int)($_GET['draw'] ?? 1),
            'start' => (int)($_GET['start'] ?? 0),
            'length' => (int)($_GET['length'] ?? 10),
            'limit' => (int)($_GET['limit'] ?? 10)
        ];
    }

    /**
     * Get common search and ordering parameters from request
     */
    private function getSearchAndOrderParams()
    {
        $searchValue = $_GET['search']['value'] ?? '';
        $orderColumn = $_GET['order'][0]['column'] ?? 8;  // Default to file_added_on column
        $orderDir = $_GET['order'][0]['dir'] ?? 'desc';
        
        $columns = ['file_code', 'file_arrival_date', 'client_name', 'agent_name', 'active_staff_name', 'file_current_status', 'file_type', 'file_type_desc', 'file_added_on'];
        $orderBy = $columns[$orderColumn] ?? 'file_added_on';
        
        return [
            'searchValue' => $searchValue,
            'orderBy' => $orderBy,
            'orderDir' => $orderDir
        ];
    }

    /**
     * Get common filter parameters from request
     */
    private function getFilterParams()
    {
        return [
            'statusFilter' => $_GET['status_filter'] ?? '',
            'dateFilter' => $_GET['date_filter'] ?? '',
            'dateFrom' => $_GET['date_from'] ?? '',
            'dateTo' => $_GET['date_to'] ?? '',
            'fileType' => $_GET['file_type'] ?? '',
            'staffId' => $_GET['staff_id'] ?? '',
            'isSuperAdmin' => ($_SESSION['sess_super_admin'] ?? '') == 'SuperAdmin'
        ];
    }

    /**
     * Get agent ID from session or request
     */
    private function getAgentId()
    {
        return $_GET['agent_id'] ?? $_SESSION['sess_agent_id'] ?? 1;
    }

    /**
     * Send JSON response with common structure
     */
    private function sendJsonResponse($data, $total = null, $draw = 1)
    {
        $response = [
            'draw' => $draw,
            'data' => $data
        ];
        
        if ($total !== null) {
            $response['recordsTotal'] = $total;
            $response['recordsFiltered'] = $total;
        }
        
        Response::json($response);
    }

    /**
     * Get file ID from URL path
     */
    private function getFileIdFromPath()
    {
        $path = strtok($_SERVER['REQUEST_URI'], '?');
        $path = str_replace('/v1/api', '', $path);
        return str_replace('/files/', '', $path);
    }

    /**
     * Validate file ID
     */
    private function validateFileId($id)
    {
        if (!is_numeric($id)) {
            Response::json(['error' => 'Invalid file ID'], 400);
            return false;
        }
        return true;
    }

    /**
     * Get all files
     */
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

        // Get common parameters
        $pagination = $this->getPaginationParams();
        $searchOrder = $this->getSearchAndOrderParams();
        $filters = $this->getFilterParams();
        $agentId = $_SESSION['sess_agent_id'] ?? 1;

        $repo = new FileRepository();
        $files = $repo->getFiles(
            $pagination['start'], 
            $pagination['length'], 
            $searchOrder['orderBy'], 
            $searchOrder['orderDir'], 
            $searchOrder['searchValue'], 
            $agentId, 
            $filters['statusFilter'], 
            $filters['dateFilter'], 
            $filters['dateFrom'], 
            $filters['dateTo']
        );
        $total = $repo->countFiles(
            $searchOrder['searchValue'], 
            $agentId, 
            true, 
            $filters['statusFilter'], 
            $filters['dateFilter'], 
            $filters['dateFrom'], 
            $filters['dateTo']
        );

        $processedFiles = $this->processFilesData($files);
        $this->sendJsonResponse($processedFiles, $total, $pagination['draw']);
    }

    public function getLatestFiles()
    {
        $pagination = $this->getPaginationParams();
        $agentId = $this->getAgentId();

        $repo = new FileRepository();
        $files = $repo->getLatestFiles($agentId, $pagination['limit']);
        $total = count($files); // For latest files, total is the same as returned files

        $processedFiles = $this->processFilesData($files);
        $this->sendJsonResponse($processedFiles, $total, $pagination['draw']);
    }

    public function getSalesPaid()
    {
        $pagination = $this->getPaginationParams();
        $agentId = $this->getAgentId();

        $repo = new FileRepository();
        $files = $repo->getSalesPaid($agentId, $pagination['limit']);
        $total = count($files); // For sales paid files, total is the same as returned files

        $processedFiles = $this->processFilesData($files, true); // Use departure date for sales paid
        $this->sendJsonResponse($processedFiles, $total, $pagination['draw']);
    }

    public function getMotivationFiles()
    {
        $pagination = $this->getPaginationParams();
        
        $repo = new FileRepository();
        $files = $repo->getMotivationFiles($pagination['limit']);

        $processedFiles = $this->processFilesData($files);
        $this->sendJsonResponse($processedFiles);
    }

    public function getAbandonedFiles()
    {
        $pagination = $this->getPaginationParams();
        $searchOrder = $this->getSearchAndOrderParams();
        $filters = $this->getFilterParams();
        $agentId = $this->getAgentId();

        $repo = new FileRepository();
        $files = $repo->getAbandonedFiles(
            $pagination['start'], 
            $pagination['length'], 
            $searchOrder['orderBy'], 
            $searchOrder['orderDir'], 
            $searchOrder['searchValue'], 
            $agentId, 
            $filters['statusFilter'], 
            $filters['dateFilter'], 
            $filters['dateFrom'], 
            $filters['dateTo'], 
            $filters['isSuperAdmin'], 
            $filters['fileType'], 
            $filters['staffId']
        );
        $total = $repo->countAbandonedFiles(
            $searchOrder['searchValue'], 
            $agentId, 
            $filters['isSuperAdmin'], 
            $filters['fileType'], 
            $filters['staffId'], 
            $filters['dateFilter'], 
            $filters['dateFrom'], 
            $filters['dateTo']
        );

        // Mark files as abandoned for special processing
        foreach ($files as &$file) {
            $file['is_abandoned'] = true;
        }
        
        $processedFiles = $this->processFilesData($files);
        $this->sendJsonResponse($processedFiles, $total, $pagination['draw']);
    }

    public function getArchivedFiles()
    {
        $pagination = $this->getPaginationParams();
        $searchOrder = $this->getSearchAndOrderParams();
        $filters = $this->getFilterParams();
        $agentId = $this->getAgentId();

        $repo = new FileRepository();
        $files = $repo->getArchivedFiles(
            $pagination['start'], 
            $pagination['length'], 
            $searchOrder['orderBy'], 
            $searchOrder['orderDir'], 
            $searchOrder['searchValue'], 
            $agentId, 
            $filters['statusFilter'], 
            $filters['dateFilter'], 
            $filters['dateFrom'], 
            $filters['dateTo'], 
            $filters['isSuperAdmin'], 
            $filters['fileType'], 
            $filters['staffId']
        );
        $total = $repo->countArchivedFiles(
            $searchOrder['searchValue'], 
            $agentId, 
            $filters['statusFilter'], 
            $filters['dateFilter'], 
            $filters['dateFrom'], 
            $filters['dateTo'], 
            $filters['isSuperAdmin'], 
            $filters['fileType'], 
            $filters['staffId']
        );

        $processedFiles = $this->processFilesData($files);
        $this->sendJsonResponse($processedFiles, $total, $pagination['draw']);
    }

    public function getCurrentYearFiles()
    {
        $pagination = $this->getPaginationParams();
        $searchOrder = $this->getSearchAndOrderParams();
        $filters = $this->getFilterParams();
        $agentId = $this->getAgentId();

        $repo = new FileRepository();
        $files = $repo->getCurrentYearFiles(
            $pagination['start'], 
            $pagination['length'], 
            $searchOrder['orderBy'], 
            $searchOrder['orderDir'], 
            $searchOrder['searchValue'], 
            $agentId, 
            $filters['statusFilter'], 
            $filters['dateFilter'], 
            $filters['dateFrom'], 
            $filters['dateTo'], 
            $filters['isSuperAdmin'], 
            $filters['fileType'], 
            $filters['staffId']
        );
        $total = $repo->countCurrentYearFiles(
            $searchOrder['searchValue'], 
            $agentId, 
            $filters['isSuperAdmin'], 
            $filters['fileType'], 
            $filters['staffId'], 
            $filters['statusFilter'], 
            $filters['dateFilter'], 
            $filters['dateFrom'], 
            $filters['dateTo']
        );

        $processedFiles = $this->processFilesData($files);
        $this->sendJsonResponse($processedFiles, $total, $pagination['draw']);
    }

    public function getPaidInFullFiles()
    {
        $pagination = $this->getPaginationParams();
        $searchOrder = $this->getSearchAndOrderParams();
        $filters = $this->getFilterParams();
        $agentId = $this->getAgentId();
        
        // Additional parameters specific to this method
        $displayBy = $_GET['displayBy'] ?? 0;
        $fromDate = $_GET['from_date'] ?? '';
        $toDate = $_GET['to_date'] ?? '';

        $repo = new FileRepository();
        $files = $repo->getPaidInFullFiles(
            $pagination['start'], 
            $pagination['length'], 
            $searchOrder['orderBy'], 
            $searchOrder['orderDir'], 
            $searchOrder['searchValue'], 
            $agentId, 
            $filters['statusFilter'], 
            $filters['dateFilter'], 
            $filters['dateFrom'], 
            $filters['dateTo'], 
            $filters['isSuperAdmin'], 
            $filters['fileType'], 
            $filters['staffId'], 
            $displayBy, 
            $fromDate, 
            $toDate
        );
        $total = $repo->countPaidInFullFiles(
            $searchOrder['searchValue'], 
            $agentId, 
            $filters['isSuperAdmin'], 
            $filters['fileType'], 
            $filters['staffId'], 
            $filters['dateFilter'], 
            $filters['dateFrom'], 
            $filters['dateTo'], 
            $displayBy, 
            $fromDate, 
            $toDate
        );

        $processedFiles = $this->processFilesData($files);
        $this->sendJsonResponse($processedFiles, $total, $pagination['draw']);
    }

    public function show()
    {
        $id = $this->getFileIdFromPath();

        if (!$this->validateFileId($id)) {
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
        $pagination = $this->getPaginationParams();
        $searchOrder = $this->getSearchAndOrderParams();
        $filters = $this->getFilterParams();
        $agentId = $_SESSION['sess_agent_id'] ?? 1;

        // Determine if user can see all files or only their own
        $myFiles = !(isset($_SESSION['sess_super_admin']) && $_SESSION['sess_super_admin'] == 'SuperAdmin');

        $repo = new FileRepository();
        $files = $repo->getAllFiles(
            $pagination['start'], 
            $pagination['length'], 
            $searchOrder['orderBy'], 
            $searchOrder['orderDir'], 
            $searchOrder['searchValue'], 
            $agentId, 
            $myFiles, 
            $filters['statusFilter'], 
            $filters['dateFilter'], 
            $filters['dateFrom'], 
            $filters['dateTo']
        );
        $total = $repo->countFiles(
            $searchOrder['searchValue'], 
            $agentId, 
            $myFiles, 
            $filters['statusFilter'], 
            $filters['dateFilter'], 
            $filters['dateFrom'], 
            $filters['dateTo']
        );

        $processedFiles = $this->processFilesData($files);
        $this->sendJsonResponse($processedFiles, $total, $pagination['draw']);
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
        $id = $this->getFileIdFromPath();

        if (!$this->validateFileId($id)) {
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
        $id = $this->getFileIdFromPath();

        if (!$this->validateFileId($id)) {
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
