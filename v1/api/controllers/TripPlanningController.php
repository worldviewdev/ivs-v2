<?php

class TripPlanningController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::conn();
    }

    public function all()
    {
        try {
            // Get request parameters
            $start = isset($_GET['start']) ? (int)$_GET['start'] : 0;
            $length = isset($_GET['length']) ? (int)$_GET['length'] : 10;
            $search = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';
            $orderColumn = isset($_GET['order'][0]['column']) ? (int)$_GET['order'][0]['column'] : 8;
            $orderDir = isset($_GET['order'][0]['dir']) ? $_GET['order'][0]['dir'] : 'desc';
            
            // Date filtering parameters
            $dateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : '';
            $dateTo = isset($_GET['date_to']) ? $_GET['date_to'] : '';

            // Column mapping for ordering
            $columns = [
                'id',
                'name',
                'email',
                'phone',
                'dates_for_travel',
                'people_in_group',
                'how_many_week',
                'created_at',
                'comments'
            ];

            $orderBy = isset($columns[$orderColumn]) ? $columns[$orderColumn] : 'created_at';
            $orderDirection = strtoupper($orderDir) === 'ASC' ? 'ASC' : 'DESC';

            // Build WHERE conditions
            $whereConditions = [];
            $params = [];

            // Add search condition
            if (!empty($search)) {
                $whereConditions[] = "(name LIKE ? OR email LIKE ? OR phone LIKE ? OR comments LIKE ?)";
                $searchTerm = "%{$search}%";
                $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            }

            // Add date filtering
            if (!empty($dateFrom)) {
                $whereConditions[] = "dates_for_travel >= ?";
                $params[] = $dateFrom;
            }
            if (!empty($dateTo)) {
                $whereConditions[] = "dates_for_travel <= ?";
                $params[] = $dateTo;
            }

            // Build WHERE clause
            $whereClause = '';
            if (!empty($whereConditions)) {
                $whereClause = ' WHERE ' . implode(' AND ', $whereConditions);
            }

            // Get total records count (without filtering)
            $totalSql = "SELECT COUNT(*) as total 
                        FROM ivs_quick_contact qc
                        INNER JOIN ivs_questionnaire_form tqf ON qc.email = tqf.email
                        LEFT JOIN mv_files mf ON qc.fk_file_id = mf.file_id
                        WHERE qc.added_on = tqf.created_at";
            $totalStmt = $this->db->prepare($totalSql);
            $totalStmt->execute();
            $totalRecords = $totalStmt->fetch()['total'];

            // Get filtered records count
            $filteredSql = "SELECT COUNT(*) as filtered 
                           FROM ivs_quick_contact qc
                           INNER JOIN ivs_questionnaire_form tqf ON qc.email = tqf.email
                           LEFT JOIN mv_files mf ON qc.fk_file_id = mf.file_id
                           WHERE qc.added_on = tqf.created_at" . 
                           (strpos($whereClause, 'WHERE') !== false ? str_replace('WHERE', 'AND', $whereClause) : $whereClause);
            $filteredStmt = $this->db->prepare($filteredSql);
            $filteredStmt->execute($params);
            $filteredRecords = $filteredStmt->fetch()['filtered'];

            // Build main query
            $sql = "SELECT 
                        qc.fk_file_id, 
                        mf.file_code,
                        tqf.* 
                    FROM 
                        ivs_quick_contact qc
                    INNER JOIN 
                        ivs_questionnaire_form tqf
                        ON qc.email = tqf.email
                    LEFT JOIN 
                        mv_files mf
                        ON qc.fk_file_id = mf.file_id
                    WHERE
                        qc.added_on = tqf.created_at" . 
                    (strpos($whereClause, 'WHERE') !== false ? str_replace('WHERE', 'AND', $whereClause) : $whereClause);
            $sql .= " ORDER BY {$orderBy} {$orderDirection}";
            $sql .= " LIMIT {$start}, {$length}";

            // Execute main query
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $data = $stmt->fetchAll();

            // Format data for DataTable
            $formattedData = [];

            foreach ($data as $row) {
                $formattedData[] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'phone' => $row['phone'],
                    'dates_for_travel' => $row['dates_for_travel'],
                    'people_in_group' => $row['people_in_group'],
                    'how_many_week' => $row['how_many_week'],
                    'comments' => $row['comments'],
                    'created_at' => $row['created_at'],
                    // New fields from JOIN query
                    'fk_file_id' => $row['fk_file_id'] ?? null,
                    'file_code' => $row['file_code'] ?? null,
                    // Additional fields for detailed view using actual database field names
                    'flexible_on_date' => $row['flexible_on_date'] ?? null,
                    'adult_age_group' => $row['adult_age_group'] ?? null,
                    'travel_with_children' => $row['travel_with_children'] ?? null,
                    'no_of_children' => $row['no_of_children'] ?? null,
                    'children_age_group' => $row['children_age_group'] ?? null,
                    'type_of_travel' => $row['type_of_travel'] ?? null,
                    'level_of_accommodation' => $row['level_of_accommodation'] ?? null,
                    'other_services_needed' => $row['other_services_needed'] ?? null,
                    'per_person_budget' => $row['per_person_budget'] ?? null,
                    'exclud_international_flight' => $row['exclud_international_flight'] ?? null,
                    'budget_flexible' => $row['budget_flexible'] ?? null,
                    'stage_in_planning' => $row['stage_in_planning'] ?? null,
                    'country_of_residence' => $row['country_of_residence'] ?? null,
                    'terms_and_cond' => $row['terms_and_cond'] ?? null,
                    'time' => $row['time'] ?? null,
                    'timezone' => $row['timezone'] ?? null,
                    'updated_at' => $row['updated_at'] ?? null
                ];
            }

            // Return response
            Response::json([
                'draw' => isset($_GET['draw']) ? (int)$_GET['draw'] : 1,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $formattedData
            ]);
        } catch (Exception $e) {
            Response::json(['error' => 'Failed to fetch trip planning data: ' . $e->getMessage()], 500);
        }
    }

    public function show()
    {
        try {
            $id = $_GET['id'] ?? null;
            
            if (!$id) {
                Response::json(['error' => 'ID is required'], 400);
                return;
            }

            $sql = "SELECT * FROM ivs_questionnaire_form WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $data = $stmt->fetch();

            if (!$data) {
                Response::json(['error' => 'Trip planning entry not found'], 404);
                return;
            }

            Response::json($data);
        } catch (Exception $e) {
            Response::json(['error' => 'Failed to fetch trip planning details: ' . $e->getMessage()], 500);
        }
    }

    public function destroy()
    {
        try {
            $id = $_GET['id'] ?? null;
            
            if (!$id) {
                Response::json(['error' => 'ID is required'], 400);
                return;
            }

            $sql = "DELETE FROM ivs_questionnaire_form WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([$id]);

            if ($result) {
                Response::json(['message' => 'Trip planning entry deleted successfully']);
            } else {
                Response::json(['error' => 'Failed to delete trip planning entry'], 500);
            }
        } catch (Exception $e) {
            Response::json(['error' => 'Failed to delete trip planning entry: ' . $e->getMessage()], 500);
        }
    }
}
