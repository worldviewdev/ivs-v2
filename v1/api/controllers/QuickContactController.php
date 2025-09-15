<?php

// require_once('../core/Database.php');
// require_once('../core/Response.php');

class QuickContactController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::conn();
    }

    /**
     * Get all quick contact leads with pagination and filtering
     */
    public function all()
    {
        try {
            // Get request parameters
            $start = isset($_GET['start']) ? (int)$_GET['start'] : 0;
            $length = isset($_GET['length']) ? (int)$_GET['length'] : 10;
            $search = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';
            $orderColumn = isset($_GET['order'][0]['column']) ? (int)$_GET['order'][0]['column'] : 0;
            $orderDir = isset($_GET['order'][0]['dir']) ? $_GET['order'][0]['dir'] : 'desc';
            
            // Column mapping for ordering
            $columns = [
                'file_code',
                'name', 
                'email',
                'phone',
                'dates_for_travel',
                'adults',
                'children',
                'message',
                'added_on',
                'id'
            ];
            
            $orderBy = isset($columns[$orderColumn]) ? $columns[$orderColumn] : 'id';
            $orderDirection = strtoupper($orderDir) === 'ASC' ? 'ASC' : 'DESC';
            
            // Build base query
            $sql = "SELECT q.*, f.file_code 
                    FROM ivs_quick_contact q 
                    LEFT JOIN mv_files f ON f.file_id = q.fk_file_id 
                    WHERE 1=1";
            
            $params = [];
            
            // Add search condition
            if (!empty($search)) {
                $sql .= " AND (q.name LIKE ? OR q.email LIKE ? OR q.phone LIKE ? OR q.message LIKE ?)";
                $searchTerm = "%{$search}%";
                $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            }
            
            // Add date range filter if provided
            if (isset($_GET['date_from']) && !empty($_GET['date_from'])) {
                $sql .= " AND DATE(q.added_on) >= ?";
                $params[] = $_GET['date_from'];
            }
            
            if (isset($_GET['date_to']) && !empty($_GET['date_to'])) {
                $sql .= " AND DATE(q.added_on) <= ?";
                $params[] = $_GET['date_to'];
            }
            
            // Get total count for pagination
            $countSql = "SELECT COUNT(*) as total FROM ivs_quick_contact q WHERE 1=1";
            $countParams = [];
            
            if (!empty($search)) {
                $countSql .= " AND (q.name LIKE ? OR q.email LIKE ? OR q.phone LIKE ? OR q.message LIKE ?)";
                $searchTerm = "%{$search}%";
                $countParams = array_merge($countParams, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            }
            
            if (isset($_GET['date_from']) && !empty($_GET['date_from'])) {
                $countSql .= " AND DATE(q.added_on) >= ?";
                $countParams[] = $_GET['date_from'];
            }
            
            if (isset($_GET['date_to']) && !empty($_GET['date_to'])) {
                $countSql .= " AND DATE(q.added_on) <= ?";
                $countParams[] = $_GET['date_to'];
            }
            
            $countStmt = $this->db->prepare($countSql);
            $countStmt->execute($countParams);
            $totalRecords = $countStmt->fetch()['total'];
            
            // Add ordering and pagination
            $sql .= " ORDER BY {$orderBy} {$orderDirection}";
            $sql .= " LIMIT {$start}, {$length}";
            
            // Execute query
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $data = $stmt->fetchAll();
            
            // Format data for DataTable
            $formattedData = [];
            foreach ($data as $row) {
                $formattedData[] = [
                    'id' => $row['id'],
                    'file_code' => $row['file_code'],
                    'fk_file_id' => $row['fk_file_id'],
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'phone' => $row['phone'],
                    'dates_for_travel' => $row['dates_for_travel'],
                    'adults' => $row['adults'],
                    'children' => $row['children'],
                    'message' => $row['message'],
                    'added_on' => $row['added_on']
                ];
            }
            
            // Return DataTable format
            Response::json([
                'draw' => isset($_GET['draw']) ? (int)$_GET['draw'] : 1,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $formattedData
            ]);
            
        } catch (Exception $e) {
            Response::json(['error' => 'Failed to fetch quick contact data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get single quick contact lead by ID
     */
    public function show()
    {
        try {
            $id = isset($_GET['id']) ? $_GET['id'] : '';
            
            if (empty($id)) {
                Response::json(['error' => 'ID is required'], 400);
                return;
            }
            
            $sql = "SELECT q.*, f.file_code 
                    FROM ivs_quick_contact q 
                    LEFT JOIN mv_files f ON f.file_id = q.fk_file_id 
                    WHERE q.id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $lead = $stmt->fetch();
            
            if (!$lead) {
                Response::json(['error' => 'Lead not found'], 404);
                return;
            }
            
            Response::json($lead);
            
        } catch (Exception $e) {
            Response::json(['error' => 'Failed to fetch lead: ' . $e->getMessage()], 500);
        }
    }
}
