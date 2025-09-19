<?php
/**
 * FileRepository - Repository class for managing file data
 * 
 * This class handles all database operations related to files including
 * CRUD operations, filtering, and file search.
 */
class FileRepository
{
    /**
     * Get list of files with pagination and filtering
     * 
     * @param int $start Offset for pagination
     * @param int $length Number of records per page
     * @param string $orderBy Column for sorting
     * @param string $orderDir Sorting direction (ASC/DESC)
     * @param string $searchValue Search value
     * @param int $agentId Agent ID
     * @param string $statusFilter Filter by status
     * @param string $dateFilter Filter by specific date
     * @param string $dateFrom Start date range
     * @param string $dateTo End date range
     * @return array
     */
    public function getFiles($start, $length, $orderBy, $orderDir, $searchValue, $agentId, $statusFilter = '', $dateFilter = '', $dateFrom = '', $dateTo = '')
    {
        $db = Database::conn();

        $sql = $this->buildBaseFileQuery();
        $sql .= " WHERE f.file_status != 'Delete'
                  AND f.file_admin_type = 'Admin'
                  AND (f.file_primary_staff = :agentId OR f.file_active_staff = :agentId)
                  AND f.is_package_file = 'No'";

        $sql .= $this->buildSearchCondition($searchValue);
        $sql .= $this->buildStatusFilter($statusFilter);
        $sql .= $this->buildDateFilters($dateFilter, $dateFrom, $dateTo);
        
        // Default sort order by file_added_on DESC if not specified
        if (empty($orderBy) || $orderBy == 'file_id') {
            $orderBy = 'f.file_added_on';
            $orderDir = 'DESC';
        }
        
        $sql .= " ORDER BY $orderBy $orderDir LIMIT :start, :length";

        $stmt = $db->prepare($sql);
        $this->bindFileQueryParams($stmt, $agentId, $searchValue, $statusFilter, $dateFilter, $dateFrom, $dateTo, $start, $length);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Build base query for files with JOIN to related tables
     * 
     * @return string
     */
    private function buildBaseFileQuery()
    {
        return "SELECT f.*,
                   CONCAT(e.emp_first_name,' ',e.emp_last_name) AS active_staff_name,
                   CONCAT(c.client_first_name,' ',c.client_last_name) AS client_name,
                   CONCAT(a.agent_first_name,' ',a.agent_last_name) AS agent_name
                FROM mv_files f
                LEFT JOIN mv_employee e ON f.file_active_staff = e.emp_id
                LEFT JOIN mv_client c ON f.fk_client_id = c.client_id
                LEFT JOIN mv_agent a ON f.fk_agent_id = a.agent_id";
    }

    /**
     * Build search condition for query
     * 
     * @param string $searchValue
     * @return string
     */
    private function buildSearchCondition($searchValue)
    {
        if (!$searchValue) {
            return '';
        }

        return " AND (
            f.file_code LIKE :search OR
            CONCAT(c.client_first_name,' ',c.client_last_name) LIKE :search OR
            CONCAT(a.agent_first_name,' ',a.agent_last_name) LIKE :search OR
            CONCAT(e.emp_first_name,' ',e.emp_last_name) LIKE :search
        )";
    }

    /**
     * Build search condition with multiple keywords
     * 
     * @param string $searchValue
     * @return string
     */
    private function buildMultiKeywordSearchCondition($searchValue)
    {
        if (!$searchValue) {
            return '';
        }

        $keywords = explode(' ', trim($searchValue));
        $searchConditions = [];
        
        foreach ($keywords as $keyword) {
            $searchConditions[] = "(a.agent_first_name LIKE :search_" . count($searchConditions) . " 
                OR a.agent_last_name LIKE :search_" . count($searchConditions) . " 
                OR c.client_first_name LIKE :search_" . count($searchConditions) . " 
                OR c.client_last_name LIKE :search_" . count($searchConditions) . " 
                OR f.file_id = :search_id_" . count($searchConditions) . " 
                OR f.file_code LIKE :search_" . count($searchConditions) . " 
                OR e.emp_first_name LIKE :search_" . count($searchConditions) . " 
                OR e.emp_last_name LIKE :search_" . count($searchConditions) . ")";
        }
        
        return " AND (" . implode(' AND ', $searchConditions) . ")";
    }

    /**
     * Build status filter for query
     * 
     * @param string $statusFilter
     * @return string
     */
    private function buildStatusFilter($statusFilter)
    {
        if (!$statusFilter) {
            return '';
        }

        if ($statusFilter == "0") {
            return " AND f.file_current_status != '7' AND f.file_current_status != '8'";
        } elseif ($statusFilter != "99") {
            return " AND f.file_current_status = :status_filter";
        }

        return '';
    }

    /**
     * Build date filter for query
     * 
     * @param string $dateFilter
     * @param string $dateFrom
     * @param string $dateTo
     * @return string
     */
    private function buildDateFilters($dateFilter, $dateFrom, $dateTo)
    {
        $dateCondition = '';

        if ($dateFilter) {
            $dateCondition .= " AND DATE(f.file_arrival_date) = :date_filter";
        } elseif ($dateFrom && $dateTo) {
            $dateCondition .= " AND DATE(f.file_arrival_date) BETWEEN :date_from AND :date_to";
        } elseif ($dateFrom) {
            $dateCondition .= " AND DATE(f.file_arrival_date) >= :date_from";
        } elseif ($dateTo) {
            $dateCondition .= " AND DATE(f.file_arrival_date) <= :date_to";
        }

        return $dateCondition;
    }

    /**
     * Build date filter with special range
     * 
     * @param string $fromDate
     * @param string $toDate
     * @param string $dateFilter
     * @param string $dateFrom
     * @param string $dateTo
     * @return string
     */
    private function buildDateRangeFilters($fromDate, $toDate, $dateFilter, $dateFrom, $dateTo)
    {
        $dateCondition = '';

        if ($fromDate && $toDate) {
            $dateCondition .= " AND f.file_arrival_date >= :from_date AND f.file_arrival_date <= :to_date";
        } elseif ($dateFilter) {
            $dateCondition .= " AND DATE(f.file_arrival_date) = :date_filter";
        } elseif ($dateFrom && $dateTo) {
            $dateCondition .= " AND DATE(f.file_arrival_date) BETWEEN :date_from AND :date_to";
        } elseif ($dateFrom) {
            $dateCondition .= " AND DATE(f.file_arrival_date) >= :date_from";
        } elseif ($dateTo) {
            $dateCondition .= " AND DATE(f.file_arrival_date) <= :date_to";
        }

        return $dateCondition;
    }

    /**
     * Build agent restriction condition
     * 
     * @param bool $isSuperAdmin
     * @return string
     */
    private function buildAgentRestriction($isSuperAdmin)
    {
        if ($isSuperAdmin) {
            return '';
        }

        return " AND (f.file_primary_staff = :agentId OR f.file_active_staff = :agentId)";
    }

    /**
     * Build staff filter condition
     * 
     * @param int $staffId
     * @return string
     */
    private function buildStaffFilter($staffId)
    {
        if (!$staffId || $staffId <= 0) {
            return '';
        }

        return " AND (f.file_active_staff = :staff_id OR f.file_primary_staff = :staff_id)";
    }

    /**
     * Bind parameters for standard file query
     * 
     * @param PDOStatement $stmt
     * @param int $agentId
     * @param string $searchValue
     * @param string $statusFilter
     * @param string $dateFilter
     * @param string $dateFrom
     * @param string $dateTo
     * @param int $start
     * @param int $length
     * @param bool $isSuperAdmin
     */
    private function bindFileQueryParams($stmt, $agentId, $searchValue, $statusFilter, $dateFilter, $dateFrom, $dateTo, $start, $length, $isSuperAdmin = false)
    {
        if (!$isSuperAdmin) {
            $stmt->bindValue(':agentId', $agentId, PDO::PARAM_INT);
        }
        
        if ($searchValue) {
            $stmt->bindValue(':search', "%$searchValue%", PDO::PARAM_STR);
        }
        
        if ($statusFilter && $statusFilter != "0" && $statusFilter != "99") {
            $stmt->bindValue(':status_filter', $statusFilter, PDO::PARAM_INT);
        }
        
        if ($dateFilter) {
            $stmt->bindValue(':date_filter', $dateFilter, PDO::PARAM_STR);
        }
        
        if ($dateFrom) {
            $stmt->bindValue(':date_from', $dateFrom, PDO::PARAM_STR);
        }
        
        if ($dateTo) {
            $stmt->bindValue(':date_to', $dateTo, PDO::PARAM_STR);
        }
        
        $stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
        $stmt->bindValue(':length', (int)$length, PDO::PARAM_INT);
    }

    /**
     * Bind parameters for multi-keyword search
     * 
     * @param PDOStatement $stmt
     * @param string $searchValue
     */
    private function bindMultiKeywordSearchParams($stmt, $searchValue)
    {
        if (!$searchValue) {
            return;
        }

        $keywords = explode(' ', trim($searchValue));
        foreach ($keywords as $index => $keyword) {
            $stmt->bindValue(':search_' . $index, "%$keyword%", PDO::PARAM_STR);
            $stmt->bindValue(':search_id_' . $index, $keyword, PDO::PARAM_STR);
        }
    }

    /**
     * Get order by based on displayBy
     * 
     * @param int $displayBy
     * @param string $orderBy
     * @return string
     */
    private function getDisplayByOrder($displayBy, $orderBy)
    {
        if ($orderBy == 'file_id') {
            switch ($displayBy) {
                case 1:
                    return "f.file_departure_date";
                case 2:
                    return "f.file_id";
                case 3:
                    return "f.file_arrival_date";
                default:
                    return "f.file_id";
            }
        }
        
        // Default sort by file_added_on if no specific order provided
        if (empty($orderBy) || $orderBy == 'file_id') {
            return "f.file_added_on";
        }
        
        return $orderBy;
    }

    /**
     * Get order direction based on displayBy
     * 
     * @param int $displayBy
     * @param string $orderBy
     * @param string $orderDir
     * @return string
     */
    private function getDisplayByOrderDir($displayBy, $orderBy, $orderDir)
    {
        if ($orderBy == 'file_id') {
            switch ($displayBy) {
                case 1:
                    return "asc";
                case 2:
                    return "desc";
                case 3:
                    return "asc";
                default:
                    return "desc";
            }
        }
        
        // Default sort direction is DESC for file_added_on
        if (empty($orderDir) || $orderDir == 'file_id') {
            return "DESC";
        }
        
        return $orderDir;
    }

    /**
     * Get latest files based on agent
     * 
     * @param int $agentId
     * @param int $limit
     * @return array
     */
    public function getLatestFiles($agentId, $limit = 7)
    {
        $db = Database::conn();

        $sql = $this->buildBaseFileQuery();
        $sql .= " WHERE f.file_status != 'Delete'
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

    /**
     * Get motivation files with specific limit
     * 
     * @param int $limit
     * @return array
     */
    public function getMotivationFiles($limit = 10)
    {
        $db = Database::conn();
        
        $sql = $this->buildBaseFileQuery();
        $sql .= " WHERE f.file_status != 'Delete'
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

    /**
     * Get abandoned files with filtering
     * 
     * @param int $start
     * @param int $length
     * @param string $orderBy
     * @param string $orderDir
     * @param string $searchValue
     * @param int $agentId
     * @param string $statusFilter
     * @param string $dateFilter
     * @param string $dateFrom
     * @param string $dateTo
     * @param bool $isSuperAdmin
     * @param string $fileType
     * @param string $staffId
     * @return array
     */
    public function getAbandonedFiles($start, $length, $orderBy, $orderDir, $searchValue, $agentId, $statusFilter = '', $dateFilter = '', $dateFrom = '', $dateTo = '', $isSuperAdmin = false, $fileType = '', $staffId = '')
    {
        $db = Database::conn();
        
        $sql = $this->buildBaseFileQuery();
        $sql .= " LEFT JOIN mv_agency ag ON ag.agency_id = a.fk_agency_id";
        $sql .= " WHERE f.file_status != 'Delete'
                  AND f.is_online = 'No'";
        
        $sql .= $this->buildAgentRestriction($isSuperAdmin);
        $sql .= " AND f.file_current_status = '8'"; // Status abandoned
        
        // Handle file type filter
        if ($fileType != "") {
            $sql .= " AND f.is_package_file = 'Yes'";
        } else {
            $sql .= " AND f.is_package_file = 'No'";
        }
        
        $sql .= $this->buildSearchCondition($searchValue);
        $sql .= $this->buildDateFilters($dateFilter, $dateFrom, $dateTo);
        $sql .= $this->buildStaffFilter($staffId);
        
        // Default sort order by file_added_on DESC if not specified
        if (empty($orderBy) || $orderBy == 'file_id') {
            $orderBy = 'f.file_added_on';
            $orderDir = 'DESC';
        }
        
        $sql .= " ORDER BY $orderBy $orderDir LIMIT :start, :length";

        $stmt = $db->prepare($sql);
        
        if (!$isSuperAdmin) {
            $stmt->bindValue(':agentId', $agentId, PDO::PARAM_INT);
        }
        
        if ($searchValue) {
            $stmt->bindValue(':search', "%$searchValue%", PDO::PARAM_STR);
            $stmt->bindValue(':search_id', $searchValue, PDO::PARAM_STR);
        }
        
        if ($dateFilter) {
            $stmt->bindValue(':date_filter', $dateFilter, PDO::PARAM_STR);
        }
        if ($dateFrom) {
            $stmt->bindValue(':date_from', $dateFrom, PDO::PARAM_STR);
        }
        if ($dateTo) {
            $stmt->bindValue(':date_to', $dateTo, PDO::PARAM_STR);
        }
        if ($staffId && $staffId > 0) {
            $stmt->bindValue(':staff_id', $staffId, PDO::PARAM_INT);
        }
        
        $stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
        $stmt->bindValue(':length', (int)$length, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get archived files with filtering
     * 
     * @param int $start
     * @param int $length
     * @param string $orderBy
     * @param string $orderDir
     * @param string $searchValue
     * @param int $agentId
     * @param string $statusFilter
     * @param string $dateFilter
     * @param string $dateFrom
     * @param string $dateTo
     * @param bool $isSuperAdmin
     * @param string $fileType
     * @param string $staffId
     * @return array
     */
    public function getArchivedFiles($start, $length, $orderBy, $orderDir, $searchValue, $agentId, $statusFilter = '', $dateFilter = '', $dateFrom = '', $dateTo = '', $isSuperAdmin = false, $fileType = '', $staffId = '')
    {
        $db = Database::conn();
        
        $sql = $this->buildBaseFileQuery();
        $sql .= " LEFT JOIN mv_agency ag ON ag.agency_id = a.fk_agency_id";
        $sql .= " WHERE 1=1";
        
        $sql .= $this->buildAgentRestriction($isSuperAdmin);
        $sql .= " AND f.file_status = 'Delete'"; // Archived files
        $sql .= " AND f.is_package_file = 'No'";

        // Handle file type filter for ftype=10
        if (isset($_GET['ftype']) && $_GET['ftype'] == 10) {
            $sql .= " AND f.file_type = '10'";
        } else {
            $sql .= " AND f.file_type <> '10'";
        }

        $sql .= $this->buildStatusFilter($statusFilter);
        
        // Handle file type filter for file_type
        if ($fileType) {
            if ($fileType == "0") {
                $sql .= " AND f.file_type != '7' AND f.file_type != '8'";
            } else {
                $sql .= " AND f.file_type = :file_type";
            }
        }

        $sql .= $this->buildMultiKeywordSearchCondition($searchValue);
        $sql .= $this->buildDateFilters($dateFilter, $dateFrom, $dateTo);
        $sql .= $this->buildStaffFilter($staffId);
        
        // Default sort order by file_added_on DESC if not specified
        if (empty($orderBy) || $orderBy == 'file_id') {
            $orderBy = 'f.file_added_on';
            $orderDir = 'DESC';
        }
        
        $sql .= " ORDER BY $orderBy $orderDir LIMIT :start, :length";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
        $stmt->bindValue(':length', (int)$length, PDO::PARAM_INT);
        
        if (!$isSuperAdmin) {
            $stmt->bindValue(':agentId', $agentId, PDO::PARAM_INT);
        }
        
        $this->bindMultiKeywordSearchParams($stmt, $searchValue);
        
        if ($statusFilter && $statusFilter != "0" && $statusFilter != "99") {
            $stmt->bindValue(':status_filter', $statusFilter, PDO::PARAM_STR);
        }
        
        if ($fileType && $fileType != "0") {
            $stmt->bindValue(':file_type', $fileType, PDO::PARAM_STR);
        }
        
        if ($dateFilter) {
            $stmt->bindValue(':date_filter', $dateFilter, PDO::PARAM_STR);
        }
        if ($dateFrom) {
            $stmt->bindValue(':date_from', $dateFrom, PDO::PARAM_STR);
        }
        if ($dateTo) {
            $stmt->bindValue(':date_to', $dateTo, PDO::PARAM_STR);
        }
        if ($staffId && $staffId > 0) {
            $stmt->bindValue(':staff_id', $staffId, PDO::PARAM_INT);
        }
        
        $stmt->execute();

        return $stmt->fetchAll();
    }
    /**
     * Count abandoned files
     * 
     * @param string $searchValue
     * @param int $agentId
     * @param bool $isSuperAdmin
     * @param string $fileType
     * @param string $staffId
     * @param string $dateFilter
     * @param string $dateFrom
     * @param string $dateTo
     * @return int
     */
    public function countAbandonedFiles($searchValue, $agentId, $isSuperAdmin = false, $fileType = '', $staffId = '', $dateFilter = '', $dateFrom = '', $dateTo = '')
    {
        $db = Database::conn();

        $sql = "SELECT COUNT(*) as total
                FROM mv_files f
                LEFT JOIN mv_client c ON f.fk_client_id = c.client_id
                LEFT JOIN mv_agent a ON f.fk_agent_id = a.agent_id
                LEFT JOIN mv_employee e ON f.file_active_staff = e.emp_id
                LEFT JOIN mv_agency ag ON ag.agency_id = a.fk_agency_id
                WHERE f.file_status != 'Delete'
                  AND f.is_online = 'No'";

        $sql .= $this->buildAgentRestriction($isSuperAdmin);
        $sql .= " AND f.file_current_status = '8'"; // Status abandoned

        // Handle file type filter
        if ($fileType != "") {
            $sql .= " AND f.is_package_file = 'Yes'";
        } else {
            $sql .= " AND f.is_package_file = 'No'";
        }

        $sql .= $this->buildSearchCondition($searchValue);
        $sql .= $this->buildDateFilters($dateFilter, $dateFrom, $dateTo);
        $sql .= $this->buildStaffFilter($staffId);

        $stmt = $db->prepare($sql);
        
        if (!$isSuperAdmin) {
            $stmt->bindValue(':agentId', $agentId, PDO::PARAM_INT);
        }
        
        if ($searchValue) {
            $stmt->bindValue(':search', "%$searchValue%", PDO::PARAM_STR);
            $stmt->bindValue(':search_id', $searchValue, PDO::PARAM_STR);
        }
        
        if ($dateFilter) {
            $stmt->bindValue(':date_filter', $dateFilter, PDO::PARAM_STR);
        }
        if ($dateFrom) {
            $stmt->bindValue(':date_from', $dateFrom, PDO::PARAM_STR);
        }
        if ($dateTo) {
            $stmt->bindValue(':date_to', $dateTo, PDO::PARAM_STR);
        }
        if ($staffId && $staffId > 0) {
            $stmt->bindValue(':staff_id', $staffId, PDO::PARAM_INT);
        }
        
        $stmt->execute();

        return $stmt->fetch()['total'];
    }
    
    /**
     * Get current year files with filtering
     * 
     * @param int $start
     * @param int $length
     * @param string $orderBy
     * @param string $orderDir
     * @param string $searchValue
     * @param int $agentId
     * @param string $statusFilter
     * @param string $dateFilter
     * @param string $dateFrom
     * @param string $dateTo
     * @param bool $isSuperAdmin
     * @param string $fileType
     * @param string $staffId
     * @return array
     */
    public function getCurrentYearFiles($start, $length, $orderBy, $orderDir, $searchValue, $agentId, $statusFilter = '', $dateFilter = '', $dateFrom = '', $dateTo = '', $isSuperAdmin = false, $fileType = '', $staffId = '')
    {
        $db = Database::conn();
        
        $sql = $this->buildBaseFileQuery();
        $sql .= " LEFT JOIN mv_agency ag ON ag.agency_id = a.fk_agency_id";
        $sql .= " WHERE f.file_status != 'Delete'
                  AND f.is_online = 'No'
                  AND f.file_admin_type = 'Admin'
                  AND f.file_type != '7'
                  AND YEAR(f.file_arrival_date) = YEAR(CURDATE())";

        $sql .= $this->buildAgentRestriction($isSuperAdmin);
        $sql .= $this->buildStatusFilter($statusFilter);

        // Handle file type filter
        if ($fileType != "") {
            $sql .= " AND f.is_package_file = 'Yes'";
        } else {
            $sql .= " AND f.is_package_file = 'No'";
        }

        $sql .= $this->buildSearchCondition($searchValue);
        $sql .= $this->buildDateFilters($dateFilter, $dateFrom, $dateTo);
        $sql .= $this->buildStaffFilter($staffId);
        
        // Default sort order by file_added_on DESC if not specified
        if (empty($orderBy) || $orderBy == 'file_id') {
            $orderBy = 'f.file_added_on';
            $orderDir = 'DESC';
        }
        
        $sql .= " ORDER BY $orderBy $orderDir LIMIT :start, :length";

        $stmt = $db->prepare($sql);
        
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
        
        if ($dateFilter) {
            $stmt->bindValue(':date_filter', $dateFilter, PDO::PARAM_STR);
        }
        if ($dateFrom) {
            $stmt->bindValue(':date_from', $dateFrom, PDO::PARAM_STR);
        }
        if ($dateTo) {
            $stmt->bindValue(':date_to', $dateTo, PDO::PARAM_STR);
        }
        if ($staffId && $staffId > 0) {
            $stmt->bindValue(':staff_id', $staffId, PDO::PARAM_INT);
        }
        
        $stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
        $stmt->bindValue(':length', (int)$length, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get all files with filtering
     * 
     * @param int $start
     * @param int $length
     * @param string $orderBy
     * @param string $orderDir
     * @param string $searchValue
     * @param int $agentId
     * @param bool $myFiles
     * @param string $statusFilter
     * @param string $dateFilter
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    public function getAllFiles($start, $length, $orderBy, $orderDir, $searchValue, $agentId, $myFiles = true, $statusFilter = '', $dateFilter = '', $dateFrom = '', $dateTo = '')
    {
        $db = Database::conn();

        $sql = $this->buildBaseFileQuery();
        $sql .= " WHERE f.file_status != 'Delete'
                  AND f.file_admin_type = 'Admin'
                  AND f.is_package_file = 'No'";

        // If myFiles = true, only show files related to the logged-in agent
        if ($myFiles) {
            $sql .= " AND (f.file_primary_staff = :agentId OR f.file_active_staff = :agentId)";
        }

        $sql .= $this->buildSearchCondition($searchValue);
        $sql .= $this->buildStatusFilter($statusFilter);
        $sql .= $this->buildDateFilters($dateFilter, $dateFrom, $dateTo);
        
        // Default sort order by file_added_on DESC if not specified
        if (empty($orderBy) || $orderBy == 'file_id') {
            $orderBy = 'f.file_added_on';
            $orderDir = 'DESC';
        }
        
        $sql .= " ORDER BY $orderBy $orderDir LIMIT :start, :length";

        $stmt = $db->prepare($sql);
        
        // Only bind agentId if myFiles = true
        if ($myFiles) {
            $stmt->bindValue(':agentId', $agentId, PDO::PARAM_INT);
        }
        
        if ($searchValue) {
            $stmt->bindValue(':search', "%$searchValue%", PDO::PARAM_STR);
        }
        if ($statusFilter) {
            $stmt->bindValue(':status_filter', $statusFilter, PDO::PARAM_INT);
        }
        if ($dateFilter) {
            $stmt->bindValue(':date_filter', $dateFilter, PDO::PARAM_STR);
        }
        if ($dateFrom) {
            $stmt->bindValue(':date_from', $dateFrom, PDO::PARAM_STR);
        }
        if ($dateTo) {
            $stmt->bindValue(':date_to', $dateTo, PDO::PARAM_STR);
        }
        
        $stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
        $stmt->bindValue(':length', (int)$length, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Count files with filtering
     * 
     * @param string $searchValue
     * @param int $agentId
     * @param bool $myFiles
     * @param string $statusFilter
     * @param string $dateFilter
     * @param string $dateFrom
     * @param string $dateTo
     * @param bool $isSuperAdmin
     * @param string $fileType
     * @param string $staffId
     * @return int
     */
    public function countFiles($searchValue, $agentId, $myFiles = true, $statusFilter = '', $dateFilter = '', $dateFrom = '', $dateTo = '', $isSuperAdmin = false, $fileType = '', $staffId = '')
    {
        $db = Database::conn();

        $sql = "SELECT COUNT(*) as total
                FROM mv_files f
                LEFT JOIN mv_client c ON f.fk_client_id = c.client_id
                LEFT JOIN mv_agent a ON f.fk_agent_id = a.agent_id
                LEFT JOIN mv_employee e ON f.file_active_staff = e.emp_id
                LEFT JOIN mv_agency ag ON ag.agency_id = a.fk_agency_id
                WHERE f.file_status != 'Delete'
                  AND f.file_admin_type = 'Admin'
                  AND f.is_package_file = 'No'";

        // If myFiles = true and not super admin, only count files related to the logged-in agent
        if ($myFiles && !$isSuperAdmin) {
            $sql .= " AND (f.file_primary_staff = :agentId OR f.file_active_staff = :agentId)";
        }

        $sql .= $this->buildStatusFilter($statusFilter);

        $sql .= $this->buildSearchCondition($searchValue);
        $sql .= $this->buildDateFilters($dateFilter, $dateFrom, $dateTo);
        $sql .= $this->buildStaffFilter($staffId);

        $stmt = $db->prepare($sql);
        
        // Only bind agentId if myFiles = true and not super admin
        if ($myFiles && !$isSuperAdmin) {
            $stmt->bindValue(':agentId', $agentId, PDO::PARAM_INT);
        }
        
        if ($searchValue) {
            $stmt->bindValue(':search', "%$searchValue%", PDO::PARAM_STR);
        }
        
        if ($statusFilter && $statusFilter != "0" && $statusFilter != "99") {
            $stmt->bindValue(':status_filter', $statusFilter, PDO::PARAM_INT);
        }
        
        if ($dateFilter) {
            $stmt->bindValue(':date_filter', $dateFilter, PDO::PARAM_STR);
        }
        if ($dateFrom) {
            $stmt->bindValue(':date_from', $dateFrom, PDO::PARAM_STR);
        }
        if ($dateTo) {
            $stmt->bindValue(':date_to', $dateTo, PDO::PARAM_STR);
        }
        if ($staffId && $staffId > 0) {
            $stmt->bindValue(':staff_id', $staffId, PDO::PARAM_INT);
        }
        
        $stmt->execute();

        return $stmt->fetch()['total'];
    }

    /**
     * Count current year files with filtering
     * 
     * @param string $searchValue
     * @param int $agentId
     * @param bool $isSuperAdmin
     * @param string $fileType
     * @param string $staffId
     * @param string $statusFilter
     * @param string $dateFilter
     * @param string $dateFrom
     * @param string $dateTo
     * @return int
     */
    public function countCurrentYearFiles($searchValue, $agentId, $isSuperAdmin = false, $fileType = '', $staffId = '', $statusFilter = '', $dateFilter = '', $dateFrom = '', $dateTo = '')
    {
        $db = Database::conn();

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
                  AND YEAR(f.file_arrival_date) = YEAR(CURDATE())";

        $sql .= $this->buildAgentRestriction($isSuperAdmin);
        $sql .= $this->buildStatusFilter($statusFilter);

        // Handle file type filter
        if ($fileType != "") {
            $sql .= " AND f.is_package_file = 'Yes'";
        } else {
            $sql .= " AND f.is_package_file = 'No'";
        }

        $sql .= $this->buildSearchCondition($searchValue);
        $sql .= $this->buildDateFilters($dateFilter, $dateFrom, $dateTo);
        $sql .= $this->buildStaffFilter($staffId);

        $stmt = $db->prepare($sql);
        
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
        
        if ($dateFilter) {
            $stmt->bindValue(':date_filter', $dateFilter, PDO::PARAM_STR);
        }
        if ($dateFrom) {
            $stmt->bindValue(':date_from', $dateFrom, PDO::PARAM_STR);
        }
        if ($dateTo) {
            $stmt->bindValue(':date_to', $dateTo, PDO::PARAM_STR);
        }
        if ($staffId && $staffId > 0) {
            $stmt->bindValue(':staff_id', $staffId, PDO::PARAM_INT);
        }
        
        $stmt->execute();

        return $stmt->fetch()['total'];
    }

    /**
     * Get file by ID
     * 
     * @param int $id
     * @return array|false
     */
    public function getFileById($id)
    {
        $db = Database::conn();

        $sql = $this->buildBaseFileQuery();
        $sql .= " WHERE f.file_id = :id
                  AND f.file_status != 'Delete'";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Create new file
     * 
     * @param array $data
     * @return array|false
     */
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

    /**
     * Update file by ID
     * 
     * @param int $id
     * @param array $data
     * @return array|false
     */
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

    /**
     * Get files that have been paid (sales paid)
     * 
     * @param int $agentId
     * @param int $limit
     * @return array
     */
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

    /**
     * Get files that have been paid in full
     * 
     * @param int $start
     * @param int $length
     * @param string $orderBy
     * @param string $orderDir
     * @param string $searchValue
     * @param int $agentId
     * @param string $statusFilter
     * @param string $dateFilter
     * @param string $dateFrom
     * @param string $dateTo
     * @param bool $isSuperAdmin
     * @param string $fileType
     * @param string $staffId
     * @param int $displayBy
     * @param string $fromDate
     * @param string $toDate
     * @return array
     */
    public function getPaidInFullFiles($start, $length, $orderBy, $orderDir, $searchValue, $agentId, $statusFilter = '', $dateFilter = '', $dateFrom = '', $dateTo = '', $isSuperAdmin = false, $fileType = '', $staffId = '', $displayBy = 0, $fromDate = '', $toDate = '')
    {
        $db = Database::conn();
        
        $sql = $this->buildBaseFileQuery();
        $sql .= " LEFT JOIN mv_agency ag ON ag.agency_id = a.fk_agency_id";
        $sql .= " WHERE f.file_status != 'Delete'";

        $sql .= $this->buildAgentRestriction($isSuperAdmin);
        $sql .= " AND f.file_current_status = '3'"; // Status Paid in Full by Credit Card
        $sql .= " AND f.is_package_file = 'No'";

        // Handle file type filter
        if ($fileType == "0") {
            $sql .= " AND f.file_type NOT IN ('7','8','10')";
        } elseif ($fileType != "") {
            $sql .= " AND f.file_type = :file_type";
        }

        $sql .= $this->buildMultiKeywordSearchCondition($searchValue);
        $sql .= $this->buildDateRangeFilters($fromDate, $toDate, $dateFilter, $dateFrom, $dateTo);
        $sql .= $this->buildStaffFilter($staffId);

        // Handle displayBy ordering
        $orderBy = $this->getDisplayByOrder($displayBy, $orderBy);
        $orderDir = $this->getDisplayByOrderDir($displayBy, $orderBy, $orderDir);

        $sql .= " ORDER BY $orderBy $orderDir LIMIT :start, :length";

        $stmt = $db->prepare($sql);
        
        if (!$isSuperAdmin) {
            $stmt->bindValue(':agentId', $agentId, PDO::PARAM_INT);
        }
        
        $this->bindMultiKeywordSearchParams($stmt, $searchValue);
        
        if ($fileType && $fileType != "0") {
            $stmt->bindValue(':file_type', $fileType, PDO::PARAM_STR);
        }
        
        if ($fromDate && $toDate) {
            $stmt->bindValue(':from_date', $fromDate, PDO::PARAM_STR);
            $stmt->bindValue(':to_date', $toDate, PDO::PARAM_STR);
        }
        
        if ($dateFilter) {
            $stmt->bindValue(':date_filter', $dateFilter, PDO::PARAM_STR);
        } elseif ($dateFrom && $dateTo) {
            $stmt->bindValue(':date_from', $dateFrom, PDO::PARAM_STR);
            $stmt->bindValue(':date_to', $dateTo, PDO::PARAM_STR);
        } elseif ($dateFrom) {
            $stmt->bindValue(':date_from', $dateFrom, PDO::PARAM_STR);
        } elseif ($dateTo) {
            $stmt->bindValue(':date_to', $dateTo, PDO::PARAM_STR);
        }
        
        if ($staffId && $staffId > 0) {
            $stmt->bindValue(':staff_id', $staffId, PDO::PARAM_INT);
        }
        
        $stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
        $stmt->bindValue(':length', (int)$length, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Count files that have been paid in full
     * 
     * @param string $searchValue
     * @param int $agentId
     * @param bool $isSuperAdmin
     * @param string $fileType
     * @param string $staffId
     * @param string $dateFilter
     * @param string $dateFrom
     * @param string $dateTo
     * @param int $displayBy
     * @param string $fromDate
     * @param string $toDate
     * @return int
     */
    public function countPaidInFullFiles($searchValue, $agentId, $isSuperAdmin = false, $fileType = '', $staffId = '', $dateFilter = '', $dateFrom = '', $dateTo = '', $displayBy = 0, $fromDate = '', $toDate = '')
    {
        $db = Database::conn();

        $sql = "SELECT COUNT(*) as total
                FROM mv_files f
                LEFT JOIN mv_client c ON f.fk_client_id = c.client_id
                LEFT JOIN mv_agent a ON f.fk_agent_id = a.agent_id
                LEFT JOIN mv_employee e ON f.file_active_staff = e.emp_id
                LEFT JOIN mv_agency ag ON ag.agency_id = a.fk_agency_id
                WHERE f.file_status != 'Delete'";

        $sql .= $this->buildAgentRestriction($isSuperAdmin);
        $sql .= " AND f.file_current_status = '3'"; // Status Paid in Full by Credit Card
        $sql .= " AND f.is_package_file = 'No'";

        // Handle file type filter
        if ($fileType == "0") {
            $sql .= " AND f.file_type NOT IN ('7','8','10')";
        } elseif ($fileType != "") {
            $sql .= " AND f.file_type = :file_type";
        }

        $sql .= $this->buildMultiKeywordSearchCondition($searchValue);
        $sql .= $this->buildDateRangeFilters($fromDate, $toDate, $dateFilter, $dateFrom, $dateTo);
        $sql .= $this->buildStaffFilter($staffId);

        $stmt = $db->prepare($sql);
        
        if (!$isSuperAdmin) {
            $stmt->bindValue(':agentId', $agentId, PDO::PARAM_INT);
        }
        
        $this->bindMultiKeywordSearchParams($stmt, $searchValue);
        
        if ($fileType && $fileType != "0") {
            $stmt->bindValue(':file_type', $fileType, PDO::PARAM_STR);
        }
        
        if ($fromDate && $toDate) {
            $stmt->bindValue(':from_date', $fromDate, PDO::PARAM_STR);
            $stmt->bindValue(':to_date', $toDate, PDO::PARAM_STR);
        }
        
        if ($dateFilter) {
            $stmt->bindValue(':date_filter', $dateFilter, PDO::PARAM_STR);
        } elseif ($dateFrom && $dateTo) {
            $stmt->bindValue(':date_from', $dateFrom, PDO::PARAM_STR);
            $stmt->bindValue(':date_to', $dateTo, PDO::PARAM_STR);
        } elseif ($dateFrom) {
            $stmt->bindValue(':date_from', $dateFrom, PDO::PARAM_STR);
        } elseif ($dateTo) {
            $stmt->bindValue(':date_to', $dateTo, PDO::PARAM_STR);
        }
        
        if ($staffId && $staffId > 0) {
            $stmt->bindValue(':staff_id', $staffId, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total'];
    }

    /**
     * Delete file (soft delete)
     * 
     * @param int $id
     * @return bool
     */
    public function deleteFile($id)
    {
        $db = Database::conn();

        $sql = "UPDATE mv_files SET file_status = 'Delete' WHERE file_id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Count archived files
     * 
     * @param string $searchValue
     * @param int $agentId
     * @param string $statusFilter
     * @param string $dateFilter
     * @param string $dateFrom
     * @param string $dateTo
     * @param bool $isSuperAdmin
     * @param string $fileType
     * @param string $staffId
     * @return int
     */
    public function countArchivedFiles($searchValue, $agentId, $statusFilter = '', $dateFilter = '', $dateFrom = '', $dateTo = '', $isSuperAdmin = false, $fileType = '', $staffId = '')
    {
        $db = Database::conn();

        $sql = "SELECT COUNT(*) as total
                FROM mv_files f
                LEFT JOIN mv_client c ON f.fk_client_id = c.client_id
                LEFT JOIN mv_agent a ON f.fk_agent_id = a.agent_id
                LEFT JOIN mv_employee e ON f.file_active_staff = e.emp_id
                LEFT JOIN mv_agency ag ON ag.agency_id = a.fk_agency_id
                WHERE 1=1";

        $sql .= $this->buildAgentRestriction($isSuperAdmin);
        $sql .= " AND f.file_status = 'Delete'"; // Archived files
        $sql .= " AND f.is_package_file = 'No'";

        // Handle file type filter for ftype=10
        if (isset($_GET['ftype']) && $_GET['ftype'] == 10) {
            $sql .= " AND f.file_type = '10'";
        } else {
            $sql .= " AND f.file_type <> '10'";
        }

        $sql .= $this->buildStatusFilter($statusFilter);
        
        // Handle file type filter for file_type
        if ($fileType) {
            if ($fileType == "0") {
                $sql .= " AND f.file_type != '7' AND f.file_type != '8'";
            } else {
                $sql .= " AND f.file_type = :file_type";
            }
        }

        $sql .= $this->buildMultiKeywordSearchCondition($searchValue);
        $sql .= $this->buildDateFilters($dateFilter, $dateFrom, $dateTo);
        $sql .= $this->buildStaffFilter($staffId);

        $stmt = $db->prepare($sql);
        
        if (!$isSuperAdmin) {
            $stmt->bindValue(':agentId', $agentId, PDO::PARAM_INT);
        }
        
        $this->bindMultiKeywordSearchParams($stmt, $searchValue);
        
        if ($statusFilter && $statusFilter != "0" && $statusFilter != "99") {
            $stmt->bindValue(':status_filter', $statusFilter, PDO::PARAM_STR);
        }
        
        if ($fileType && $fileType != "0") {
            $stmt->bindValue(':file_type', $fileType, PDO::PARAM_STR);
        }
        
        if ($dateFilter) {
            $stmt->bindValue(':date_filter', $dateFilter, PDO::PARAM_STR);
        } elseif ($dateFrom && $dateTo) {
            $stmt->bindValue(':date_from', $dateFrom, PDO::PARAM_STR);
            $stmt->bindValue(':date_to', $dateTo, PDO::PARAM_STR);
        } elseif ($dateFrom) {
            $stmt->bindValue(':date_from', $dateFrom, PDO::PARAM_STR);
        } elseif ($dateTo) {
            $stmt->bindValue(':date_to', $dateTo, PDO::PARAM_STR);
        }

        if ($staffId && $staffId > 0) {
            $stmt->bindValue(':staff_id', $staffId, PDO::PARAM_INT);
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total'];
    }
}
