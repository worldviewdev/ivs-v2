<?php
class FileRepository
{
    public function getFiles($start, $length, $orderBy, $orderDir, $searchValue, $agentId, $statusFilter = '', $dateFilter = '', $dateFrom = '', $dateTo = '')
    {
        $db = Database::conn();

        $sql = "SELECT f.*,
                   CONCAT(e.emp_first_name,' ',e.emp_last_name) AS active_staff_name,
                   CONCAT(c.client_first_name,' ',c.client_last_name) AS client_name,
                   CONCAT(a.agent_first_name,' ',a.agent_last_name) AS agent_name
                FROM mv_files f
                LEFT JOIN mv_employee e ON f.file_active_staff = e.emp_id
                LEFT JOIN mv_client c ON f.fk_client_id = c.client_id
                LEFT JOIN mv_agent a ON f.fk_agent_id = a.agent_id
                WHERE f.file_status != 'Delete'
                  AND f.file_admin_type = 'Admin'
                  AND (f.file_primary_staff = :agentId OR f.file_active_staff = :agentId)
                  AND f.is_package_file = 'No'";

        if ($searchValue) {
            $sql .= " AND (
                f.file_code LIKE :search OR
                CONCAT(c.client_first_name,' ',c.client_last_name) LIKE :search OR
                CONCAT(a.agent_first_name,' ',a.agent_last_name) LIKE :search OR
                CONCAT(e.emp_first_name,' ',e.emp_last_name) LIKE :search
            )";
        }

        // Add status filter
        if ($statusFilter) {
            $sql .= " AND f.file_current_status = :status_filter";
        }

        // Add date filter
        if ($dateFilter) {
            $sql .= " AND DATE(f.file_arrival_date) = :date_filter";
        }
        
        // Add date range filter
        if ($dateFrom && $dateTo) {
            $sql .= " AND DATE(f.file_arrival_date) BETWEEN :date_from AND :date_to";
        } elseif ($dateFrom) {
            $sql .= " AND DATE(f.file_arrival_date) >= :date_from";
        } elseif ($dateTo) {
            $sql .= " AND DATE(f.file_arrival_date) <= :date_to";
        }

        $sql .= " ORDER BY $orderBy $orderDir LIMIT :start, :length";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':agentId', $agentId, PDO::PARAM_INT);
        if ($searchValue) $stmt->bindValue(':search', "%$searchValue%", PDO::PARAM_STR);
        if ($statusFilter) $stmt->bindValue(':status_filter', $statusFilter, PDO::PARAM_INT);
        if ($dateFilter) $stmt->bindValue(':date_filter', $dateFilter, PDO::PARAM_STR);
        if ($dateFrom) $stmt->bindValue(':date_from', $dateFrom, PDO::PARAM_STR);
        if ($dateTo) $stmt->bindValue(':date_to', $dateTo, PDO::PARAM_STR);
        $stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
        $stmt->bindValue(':length', (int)$length, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getLatestFiles($agentId, $limit = 7)
    {
        $db = Database::conn();

        $sql = "SELECT f.*,
                   CONCAT(e.emp_first_name,' ',e.emp_last_name) AS active_staff_name,
                   CONCAT(c.client_first_name,' ',c.client_last_name) AS client_name,
                   CONCAT(a.agent_first_name,' ',a.agent_last_name) AS agent_name
                FROM mv_files f
                LEFT JOIN mv_employee e ON f.file_active_staff = e.emp_id
                LEFT JOIN mv_client c ON f.fk_client_id = c.client_id
                LEFT JOIN mv_agent a ON f.fk_agent_id = a.agent_id
                WHERE f.file_status != 'Delete'
                  AND f.file_admin_type = 'Admin'
                  AND (f.file_primary_staff = :agentId OR f.file_active_staff = :agentId)
                  AND f.is_package_file = 'No'
                ORDER BY f.file_id DESC
                LIMIT :limit";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':agentId', $agentId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getMotivationFiles($limit = 10)
    {
        $db = Database::conn();
        
        $sql = "SELECT f.*,
                   CONCAT(e.emp_first_name,' ',e.emp_last_name) AS active_staff_name,
                   CONCAT(c.client_first_name,' ',c.client_last_name) AS client_name,
                   CONCAT(a.agent_first_name,' ',a.agent_last_name) AS agent_name
                FROM mv_files f
                LEFT JOIN mv_employee e ON f.file_active_staff = e.emp_id
                LEFT JOIN mv_client c ON f.fk_client_id = c.client_id
                LEFT JOIN mv_agent a ON f.fk_agent_id = a.agent_id
                WHERE f.file_status != 'Delete'
                  AND f.file_admin_type = 'Admin'
                  AND f.file_type = 10
                  AND f.is_package_file = 'No'
                ORDER BY f.file_id DESC
                LIMIT :limit";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getAbandonedFiles($start, $length, $orderBy, $orderDir, $searchValue, $agentId, $statusFilter = '', $dateFilter = '', $dateFrom = '', $dateTo = '', $isSuperAdmin = false, $fileType = '', $staffId = '')
    {
        $db = Database::conn();
        
        $sql_add = '';

        // Check if user is not super admin - add agent restriction
        if (!$isSuperAdmin) {
            $sql_add .= " AND (f.file_primary_staff = :agentId OR f.file_active_staff = :agentId)";
        }

        // Set status abandoned (8) secara eksplisit
        $sql_add .= " AND f.file_current_status = '8'";

        // Handle file type filter
        if ($fileType != "") {
            $sql_add .= " AND f.is_package_file = 'Yes'";
        } else {
            $sql_add .= " AND f.is_package_file = 'No'";
        }

        // Handle search value
        if ($searchValue) {
            $sql_add .= " AND (f.file_code LIKE :search OR CONCAT(c.client_first_name,' ',c.client_last_name) LIKE :search OR CONCAT(a.agent_first_name,' ',a.agent_last_name) LIKE :search OR CONCAT(e.emp_first_name,' ',e.emp_last_name) LIKE :search OR f.file_id = :search_id)";
        }
        
        if ($dateFilter) {
            $sql_add .= " AND DATE(f.file_arrival_date) = :date_filter";
        }
        
        if ($dateFrom && $dateTo) {
            $sql_add .= " AND DATE(f.file_arrival_date) BETWEEN :date_from AND :date_to";
        } elseif ($dateFrom) {
            $sql_add .= " AND DATE(f.file_arrival_date) >= :date_from";
        } elseif ($dateTo) {
            $sql_add .= " AND DATE(f.file_arrival_date) <= :date_to";
        }

        // Handle staff filter
        if ($staffId && $staffId > 0) {
            $sql_add .= " AND (f.file_active_staff = :staff_id OR f.file_primary_staff = :staff_id)";
        }

        $sql = "SELECT f.*,
                   CONCAT(e.emp_first_name,' ',e.emp_last_name) AS active_staff_name,
                   CONCAT(c.client_first_name,' ',c.client_last_name) AS client_name,
                   CONCAT(a.agent_first_name,' ',a.agent_last_name) AS agent_name
                FROM mv_files f
                LEFT JOIN mv_employee e ON f.file_active_staff = e.emp_id
                LEFT JOIN mv_client c ON f.fk_client_id = c.client_id
                LEFT JOIN mv_agent a ON f.fk_agent_id = a.agent_id
                LEFT JOIN mv_agency ag ON ag.agency_id = a.fk_agency_id
                WHERE f.file_status != 'Delete'
                  AND f.is_online = 'No'
                  $sql_add
                  ORDER BY $orderBy $orderDir
                  LIMIT :start, :length";

        $stmt = $db->prepare($sql);
        
        // Bind agent ID only if not super admin
        if (!$isSuperAdmin) {
            $stmt->bindValue(':agentId', $agentId, PDO::PARAM_INT);
        }
        
        if ($searchValue) {
            $stmt->bindValue(':search', "%$searchValue%", PDO::PARAM_STR);
            $stmt->bindValue(':search_id', $searchValue, PDO::PARAM_STR);
        }
        if ($dateFilter) $stmt->bindValue(':date_filter', $dateFilter, PDO::PARAM_STR);
        if ($dateFrom) $stmt->bindValue(':date_from', $dateFrom, PDO::PARAM_STR);
        if ($dateTo) $stmt->bindValue(':date_to', $dateTo, PDO::PARAM_STR);
        if ($staffId && $staffId > 0) {
            $stmt->bindValue(':staff_id', $staffId, PDO::PARAM_INT);
        }
        $stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
        $stmt->bindValue(':length', (int)$length, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function countAbandonedFiles($searchValue, $agentId, $isSuperAdmin = false, $fileType = '', $staffId = '', $dateFilter = '', $dateFrom = '', $dateTo = '')
    {
        $db = Database::conn();
        $sql_add = '';

        // If not super admin, only count files related to the logged-in agent
        if (!$isSuperAdmin) {
            $sql_add = "AND (f.file_primary_staff = :agentId OR f.file_active_staff = :agentId)";
        }

        // Set status abandoned (8) secara eksplisit
        $sql_add .= " AND f.file_current_status = '8'";

        // Handle file type filter
        if ($fileType != "") {
            $sql_add .= " AND f.is_package_file = 'Yes'";
        } else {
            $sql_add .= " AND f.is_package_file = 'No'";
        }

        $sql = "SELECT COUNT(*) as total
                FROM mv_files f
                LEFT JOIN mv_client c ON f.fk_client_id = c.client_id
                LEFT JOIN mv_agent a ON f.fk_agent_id = a.agent_id
                LEFT JOIN mv_employee e ON f.file_active_staff = e.emp_id
                LEFT JOIN mv_agency ag ON ag.agency_id = a.fk_agency_id
                WHERE f.file_status != 'Delete'
                  AND f.is_online = 'No'
                  $sql_add";

        if ($searchValue) {
            $sql .= " AND (
                f.file_code LIKE :search OR
                CONCAT(c.client_first_name,' ',c.client_last_name) LIKE :search OR
                CONCAT(a.agent_first_name,' ',a.agent_last_name) LIKE :search OR
                CONCAT(e.emp_first_name,' ',e.emp_last_name) LIKE :search OR
                f.file_id = :search_id
            )";
        }

        // Add date filter
        if ($dateFilter) {
            $sql .= " AND DATE(f.file_arrival_date) = :date_filter";
        }
        
        // Add date range filter
        if ($dateFrom && $dateTo) {
            $sql .= " AND DATE(f.file_arrival_date) BETWEEN :date_from AND :date_to";
        } elseif ($dateFrom) {
            $sql .= " AND DATE(f.file_arrival_date) >= :date_from";
        } elseif ($dateTo) {
            $sql .= " AND DATE(f.file_arrival_date) <= :date_to";
        }

        // Handle staff filter
        if ($staffId && $staffId > 0) {
            $sql .= " AND (f.file_active_staff = :staff_id OR f.file_primary_staff = :staff_id)";
        }

        $stmt = $db->prepare($sql);
        
        // Only bind agentId if not super admin
        if (!$isSuperAdmin) {
            $stmt->bindValue(':agentId', $agentId, PDO::PARAM_INT);
        }
        
        if ($searchValue) {
            $stmt->bindValue(':search', "%$searchValue%", PDO::PARAM_STR);
            $stmt->bindValue(':search_id', $searchValue, PDO::PARAM_STR);
        }
        if ($dateFilter) $stmt->bindValue(':date_filter', $dateFilter, PDO::PARAM_STR);
        if ($dateFrom) $stmt->bindValue(':date_from', $dateFrom, PDO::PARAM_STR);
        if ($dateTo) $stmt->bindValue(':date_to', $dateTo, PDO::PARAM_STR);
        if ($staffId && $staffId > 0) {
            $stmt->bindValue(':staff_id', $staffId, PDO::PARAM_INT);
        }
        $stmt->execute();

        return $stmt->fetch()['total'];
    }
    
    public function getCurrentYearFiles($start, $length, $orderBy, $orderDir, $searchValue, $agentId, $statusFilter = '', $dateFilter = '', $dateFrom = '', $dateTo = '', $isSuperAdmin = false, $fileType = '', $staffId = '')
    {
        $db = Database::conn();
        
        $sql_add = '';

        // Check if user is not super admin - add agent restriction
        if (!$isSuperAdmin) {
            $sql_add .= " AND (f.file_primary_staff = :agentId OR f.file_active_staff = :agentId)";
        }

        // Handle status filter
        if ($statusFilter) {
            if ($statusFilter == "0") {
                $sql_add .= " AND f.file_current_status != '7' AND f.file_current_status != '8'";
            } elseif ($statusFilter != "99") {
                $sql_add .= " AND f.file_current_status = :status_filter";
            }
        }

        // Handle file type filter
        if ($fileType != "") {
            $sql_add .= " AND f.is_package_file = 'Yes'";
        } else {
            $sql_add .= " AND f.is_package_file = 'No'";
        }

        // Handle search value
        if ($searchValue) {
            $sql_add .= " AND (f.file_code LIKE :search OR CONCAT(c.client_first_name,' ',c.client_last_name) LIKE :search OR CONCAT(a.agent_first_name,' ',a.agent_last_name) LIKE :search OR CONCAT(e.emp_first_name,' ',e.emp_last_name) LIKE :search OR f.file_id = :search_id)";
        }
        
        if ($dateFilter) {
            $sql_add .= " AND DATE(f.file_arrival_date) = :date_filter";
        }
        
        if ($dateFrom && $dateTo) {
            $sql_add .= " AND DATE(f.file_arrival_date) BETWEEN :date_from AND :date_to";
        } elseif ($dateFrom) {
            $sql_add .= " AND DATE(f.file_arrival_date) >= :date_from";
        } elseif ($dateTo) {
            $sql_add .= " AND DATE(f.file_arrival_date) <= :date_to";
        }

        // Handle staff filter
        if ($staffId && $staffId > 0) {
            $sql_add .= " AND (f.file_active_staff = :staff_id OR f.file_primary_staff = :staff_id)";
        }
        
        $sql = "SELECT f.*,
                   CONCAT(e.emp_first_name,' ',e.emp_last_name) AS active_staff_name,
                   CONCAT(c.client_first_name,' ',c.client_last_name) AS client_name,
                   CONCAT(a.agent_first_name,' ',a.agent_last_name) AS agent_name
                FROM mv_files f
                LEFT JOIN mv_employee e ON f.file_active_staff = e.emp_id
                LEFT JOIN mv_client c ON f.fk_client_id = c.client_id
                LEFT JOIN mv_agent a ON f.fk_agent_id = a.agent_id
                LEFT JOIN mv_agency ag ON ag.agency_id = a.fk_agency_id
                WHERE f.file_status != 'Delete'
                  AND f.is_online = 'No'
                  AND f.file_admin_type = 'Admin'
                  AND f.file_type != '7'
                  AND YEAR(f.file_arrival_date) = YEAR(CURDATE())
                  $sql_add
                  ORDER BY $orderBy $orderDir
                  LIMIT :start, :length";

        $stmt = $db->prepare($sql);
        
        // Bind agent ID only if not super admin
        if (!$isSuperAdmin) {
            $stmt->bindValue(':agentId', $agentId, PDO::PARAM_INT);
        }
        
        if ($searchValue) {
            $stmt->bindValue(':search', "%$searchValue%", PDO::PARAM_STR);
            $stmt->bindValue(':search_id', $searchValue, PDO::PARAM_STR);
        }
        if ($statusFilter && $statusFilter != "0" && $statusFilter != "99") {
            $stmt->bindValue(':status_filter', $statusFilter, PDO::PARAM_INT);
        }
        if ($dateFilter) $stmt->bindValue(':date_filter', $dateFilter, PDO::PARAM_STR);
        if ($dateFrom) $stmt->bindValue(':date_from', $dateFrom, PDO::PARAM_STR);
        if ($dateTo) $stmt->bindValue(':date_to', $dateTo, PDO::PARAM_STR);
        if ($staffId && $staffId > 0) {
            $stmt->bindValue(':staff_id', $staffId, PDO::PARAM_INT);
        }
        $stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
        $stmt->bindValue(':length', (int)$length, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getAllFiles($start, $length, $orderBy, $orderDir, $searchValue, $agentId, $myFiles = true, $statusFilter = '', $dateFilter = '', $dateFrom = '', $dateTo = '')
    {
        $db = Database::conn();
        $sql_add = '';

        // If myFiles = true, only show files related to the logged-in agent
        if ($myFiles) {
            $sql_add = "AND (f.file_primary_staff = :agentId OR f.file_active_staff = :agentId)";
        }

        $sql = "SELECT f.*,
                   CONCAT(e.emp_first_name,' ',e.emp_last_name) AS active_staff_name,
                   CONCAT(c.client_first_name,' ',c.client_last_name) AS client_name,
                   CONCAT(a.agent_first_name,' ',a.agent_last_name) AS agent_name
                FROM mv_files f
                LEFT JOIN mv_employee e ON f.file_active_staff = e.emp_id
                LEFT JOIN mv_client c ON f.fk_client_id = c.client_id
                LEFT JOIN mv_agent a ON f.fk_agent_id = a.agent_id
                WHERE f.file_status != 'Delete'
                  AND f.file_admin_type = 'Admin'
                  $sql_add
                  AND f.is_package_file = 'No'";

        if ($searchValue) {
            $sql .= " AND (
                f.file_code LIKE :search OR
                CONCAT(c.client_first_name,' ',c.client_last_name) LIKE :search OR
                CONCAT(a.agent_first_name,' ',a.agent_last_name) LIKE :search OR
                CONCAT(e.emp_first_name,' ',e.emp_last_name) LIKE :search
            )";
        }

        // Add status filter
        if ($statusFilter) {
            $sql .= " AND f.file_current_status = :status_filter";
        }

        // Add date filter
        if ($dateFilter) {
            $sql .= " AND DATE(f.file_arrival_date) = :date_filter";
        }
        
        // Add date range filter
        if ($dateFrom && $dateTo) {
            $sql .= " AND DATE(f.file_arrival_date) BETWEEN :date_from AND :date_to";
        } elseif ($dateFrom) {
            $sql .= " AND DATE(f.file_arrival_date) >= :date_from";
        } elseif ($dateTo) {
            $sql .= " AND DATE(f.file_arrival_date) <= :date_to";
        }

        $sql .= " ORDER BY $orderBy $orderDir LIMIT :start, :length";

        $stmt = $db->prepare($sql);
        // Only bind agentId if myFiles = true
        if ($myFiles) {
            $stmt->bindValue(':agentId', $agentId, PDO::PARAM_INT);
        }
        if ($searchValue) $stmt->bindValue(':search', "%$searchValue%", PDO::PARAM_STR);
        if ($statusFilter) $stmt->bindValue(':status_filter', $statusFilter, PDO::PARAM_INT);
        if ($dateFilter) $stmt->bindValue(':date_filter', $dateFilter, PDO::PARAM_STR);
        if ($dateFrom) $stmt->bindValue(':date_from', $dateFrom, PDO::PARAM_STR);
        if ($dateTo) $stmt->bindValue(':date_to', $dateTo, PDO::PARAM_STR);
        $stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
        $stmt->bindValue(':length', (int)$length, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function countFiles($searchValue, $agentId, $myFiles = true, $statusFilter = '', $dateFilter = '', $dateFrom = '', $dateTo = '', $isSuperAdmin = false, $fileType = '', $staffId = '')
    {
        $db = Database::conn();
        $sql_add = '';

        // If myFiles = true and not super admin, only count files related to the logged-in agent
        if ($myFiles && !$isSuperAdmin) {
            $sql_add = "AND (f.file_primary_staff = :agentId OR f.file_active_staff = :agentId)";
        }

        // Handle status filter
        if ($statusFilter) {
            if ($statusFilter == "0") {
                $sql_add .= " AND f.file_current_status != '7' AND f.file_current_status != '8'";
            } elseif ($statusFilter != "99") {
                $sql_add .= " AND f.file_current_status = :status_filter";
            }
        }

        // Handle file type filter
        if ($fileType != "") {
            $sql_add .= " AND f.is_package_file = 'Yes'";
        } else {
            $sql_add .= " AND f.is_package_file = 'No'";
        }

        $sql = "SELECT COUNT(*) as total
                FROM mv_files f
                LEFT JOIN mv_client c ON f.fk_client_id = c.client_id
                LEFT JOIN mv_agent a ON f.fk_agent_id = a.agent_id
                LEFT JOIN mv_employee e ON f.file_active_staff = e.emp_id
                LEFT JOIN mv_agency ag ON ag.agency_id = a.fk_agency_id
                WHERE f.file_status != 'Delete'
                  AND f.is_online = 'No'
                  AND f.file_admin_type = 'Admin'
                  AND f.file_type != '7'
                  AND YEAR(f.file_arrival_date) = YEAR(CURDATE())
                  $sql_add";

        if ($searchValue) {
            $sql .= " AND (
                f.file_code LIKE :search OR
                CONCAT(c.client_first_name,' ',c.client_last_name) LIKE :search OR
                CONCAT(a.agent_first_name,' ',a.agent_last_name) LIKE :search OR
                CONCAT(e.emp_first_name,' ',e.emp_last_name) LIKE :search OR
                f.file_id = :search_id
            )";
        }

        // Add date filter
        if ($dateFilter) {
            $sql .= " AND DATE(f.file_arrival_date) = :date_filter";
        }
        
        // Add date range filter
        if ($dateFrom && $dateTo) {
            $sql .= " AND DATE(f.file_arrival_date) BETWEEN :date_from AND :date_to";
        } elseif ($dateFrom) {
            $sql .= " AND DATE(f.file_arrival_date) >= :date_from";
        } elseif ($dateTo) {
            $sql .= " AND DATE(f.file_arrival_date) <= :date_to";
        }

        // Handle staff filter
        if ($staffId && $staffId > 0) {
            $sql .= " AND (f.file_active_staff = :staff_id OR f.file_primary_staff = :staff_id)";
        }

        $stmt = $db->prepare($sql);
        
        // Only bind agentId if myFiles = true and not super admin
        if ($myFiles && !$isSuperAdmin) {
            $stmt->bindValue(':agentId', $agentId, PDO::PARAM_INT);
        }
        
        if ($searchValue) {
            $stmt->bindValue(':search', "%$searchValue%", PDO::PARAM_STR);
            $stmt->bindValue(':search_id', $searchValue, PDO::PARAM_STR);
        }
        if ($statusFilter && $statusFilter != "0" && $statusFilter != "99") {
            $stmt->bindValue(':status_filter', $statusFilter, PDO::PARAM_INT);
        }
        if ($dateFilter) $stmt->bindValue(':date_filter', $dateFilter, PDO::PARAM_STR);
        if ($dateFrom) $stmt->bindValue(':date_from', $dateFrom, PDO::PARAM_STR);
        if ($dateTo) $stmt->bindValue(':date_to', $dateTo, PDO::PARAM_STR);
        if ($staffId && $staffId > 0) {
            $stmt->bindValue(':staff_id', $staffId, PDO::PARAM_INT);
        }
        $stmt->execute();

        return $stmt->fetch()['total'];
    }

    public function getFileById($id)
    {
        $db = Database::conn();

        $sql = "SELECT f.*,
                   CONCAT(e.emp_first_name,' ',e.emp_last_name) AS active_staff_name,
                   CONCAT(c.client_first_name,' ',c.client_last_name) AS client_name,
                   CONCAT(a.agent_first_name,' ',a.agent_last_name) AS agent_name
                FROM mv_files f
                LEFT JOIN mv_employee e ON f.file_active_staff = e.emp_id
                LEFT JOIN mv_client c ON f.fk_client_id = c.client_id
                LEFT JOIN mv_agent a ON f.fk_agent_id = a.agent_id
                WHERE f.file_id = :id
                  AND f.file_status != 'Delete'";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function createFile($data)
    {
        $db = Database::conn();

        $sql = "INSERT INTO mv_files (
                    file_code, fk_client_id, fk_agent_id, file_arrival_date, 
                    file_current_status, file_type, file_type_desc, file_admin_type,
                    file_primary_staff, file_active_staff, is_package_file, file_status
                ) VALUES (
                    :file_code, :fk_client_id, :fk_agent_id, :file_arrival_date,
                    :file_current_status, :file_type, :file_type_desc, 'Admin',
                    :file_primary_staff, :file_active_staff, 'No', 'Active'
                )";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':file_code', $data['file_code']);
        $stmt->bindValue(':fk_client_id', $data['fk_client_id'], PDO::PARAM_INT);
        $stmt->bindValue(':fk_agent_id', $data['fk_agent_id'], PDO::PARAM_INT);
        $stmt->bindValue(':file_arrival_date', $data['file_arrival_date'] ?? date('Y-m-d'));
        $stmt->bindValue(':file_current_status', $data['file_current_status'] ?? 2);
        $stmt->bindValue(':file_type', $data['file_type'] ?? 1);
        $stmt->bindValue(':file_type_desc', $data['file_type_desc'] ?? '');
        $stmt->bindValue(':file_primary_staff', $data['file_primary_staff'] ?? $data['fk_agent_id'], PDO::PARAM_INT);
        $stmt->bindValue(':file_active_staff', $data['file_active_staff'] ?? $data['fk_agent_id'], PDO::PARAM_INT);

        if ($stmt->execute()) {
            $fileId = $db->lastInsertId();
            return $this->getFileById($fileId);
        }

        return false;
    }

    public function updateFile($id, $data)
    {
        $db = Database::conn();

        $fields = [];
        $allowedFields = [
            'file_code',
            'fk_client_id',
            'fk_agent_id',
            'file_arrival_date',
            'file_current_status',
            'file_type',
            'file_type_desc',
            'file_primary_staff',
            'file_active_staff'
        ];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = :$field";
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE mv_files SET " . implode(', ', $fields) . " WHERE file_id = :id";
        $stmt = $db->prepare($sql);

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $stmt->bindValue(":$field", $data[$field]);
            }
        }
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $this->getFileById($id);
        }

        return false;
    }

    public function getSalesPaid($agentId, $limit = 7)
    {
        $db = Database::conn();

        $sql = "SELECT f.*, 
                CONCAT(emp_first_name,' ',emp_last_name) as active_staff_name,
                CONCAT(client_first_name,' ',client_last_name) as client_name,
                CONCAT(agent_first_name,' ',agent_last_name) as agent_name,
                f.file_type_desc
                FROM mv_files f 
                LEFT JOIN mv_employee e ON f.file_active_staff=e.emp_id  
                LEFT JOIN mv_client c ON f.fk_client_id=c.client_id 
                LEFT JOIN mv_agent a ON f.fk_agent_id=a.agent_id 
                LEFT JOIN mv_agency ag ON ag.agency_id=a.fk_agency_id 
                WHERE file_status!='Delete' 
                AND file_admin_type = 'Admin' 
                AND file_current_status IN (2,3) 
                AND (file_primary_staff=:agent_id OR file_active_staff=:agent_id) 
                ORDER BY file_id DESC 
                LIMIT :limit";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':agent_id', $agentId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteFile($id)
    {
        $db = Database::conn();

        $sql = "UPDATE mv_files SET file_status = 'Delete' WHERE file_id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
