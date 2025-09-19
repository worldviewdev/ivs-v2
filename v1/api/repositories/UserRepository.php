<?php

class UserRepository
{
    private $db;

    public function __construct()
    {
        $this->db = Database::conn();
    }

    /**
     * Get all clients with DataTable support
     */
    public function getAllClients($params = [])
    {
        try {
            // Get DataTable parameters
            $draw = intval($params['draw'] ?? 1);
            $start = intval($params['start'] ?? 0);
            $length = intval($params['length'] ?? 10);
            $searchValue = $params['search']['value'] ?? '';
            $orderColumn = intval($params['order'][0]['column'] ?? 0);
            $orderDir = $params['order'][0]['dir'] ?? 'asc';
            
            // Define column mapping for ordering
            $columns = [
                'client_added_date',  // Column 0 - hidden, used for default sorting
                'client_salutation',  // Column 1
                'client_first_name',  // Column 2
                'client_last_name',   // Column 3
                'client_email',       // Column 4
                'client_phone',       // Column 5
                'client_code',        // Column 6
                'client_status'       // Column 7
            ];
            
            $orderBy = $columns[$orderColumn] ?? 'client_added_date';
            $orderDirection = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';
            
            // Default sort by client_added_date DESC if no specific order is provided
            if ($orderBy === 'client_added_date' && empty($params['order'])) {
                $orderDirection = 'DESC';
            }
            
            // Build search condition
            $searchCondition = '';
            if (!empty($searchValue)) {
                $searchCondition = "WHERE (client_salutation LIKE '%$searchValue%' OR 
                                        client_first_name LIKE '%$searchValue%' OR 
                                        client_last_name LIKE '%$searchValue%' OR 
                                        client_email LIKE '%$searchValue%' OR 
                                        client_phone LIKE '%$searchValue%' OR 
                                        client_code LIKE '%$searchValue%')";
            }
            
            // Get total records
            $totalRecordsQuery = "SELECT COUNT(*) as total FROM mv_client";
            $stmt = $this->db->prepare($totalRecordsQuery);
            $stmt->execute();
            $totalRecords = $stmt->fetch()['total'];
            
            // Get filtered records count
            $filteredRecordsQuery = "SELECT COUNT(*) as total FROM mv_client $searchCondition";
            $stmt = $this->db->prepare($filteredRecordsQuery);
            $stmt->execute();
            $filteredRecords = $stmt->fetch()['total'];
            
            // Get data with pagination
            $dataQuery = "SELECT * FROM mv_client $searchCondition 
                         ORDER BY $orderBy $orderDirection 
                         LIMIT :start, :length";
            
            $stmt = $this->db->prepare($dataQuery);
            $stmt->bindValue(':start', $start, PDO::PARAM_INT);
            $stmt->bindValue(':length', $length, PDO::PARAM_INT);
            $stmt->execute();
            $clients = $stmt->fetchAll();
            
            // Format data for DataTable
            $formattedData = [];
            foreach ($clients as $client) {
                $formattedData[] = [
                    'client_added_date' => $client['client_added_date'] ?? '',
                    'client_id' => $client['client_id'] ?? '',
                    'title' => $client['client_salutation'] ?? '',
                    'name' => $client['client_first_name'] ?? '',
                    'surname' => $client['client_last_name'] ?? '',
                    'email' => $client['client_email'] ?? '',
                    'phone' => $client['client_phone'] ?? '',
                    'code' => $client['client_code'] ?? '',
                    'status' => [
                        'text' => $client['client_status'] ?? 'Active',
                        'class' => 'success'
                    ],
                    'actions' => '' // Empty, will be rendered by DataTable
                ];
            }
            
            // Return DataTable response
            return [
                'draw' => $draw,
                'recordsTotal' => intval($totalRecords),
                'recordsFiltered' => intval($filteredRecords),
                'data' => $formattedData
            ];
            
        } catch (Exception $e) {
            return [
                'draw' => intval($params['draw'] ?? 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Error retrieving clients: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get simple clients list (for backward compatibility)
     */
    public function getClients()
    {
        $stmt = $this->db->prepare("SELECT * FROM mv_client");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get client by ID
     */
    public function getClientById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM mv_client WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Create new client
     */
    public function createClient($data)
    {
        $sql = "INSERT INTO mv_client (title, name, surname, email, phone, code, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $params = [
            $data['title'] ?? '',
            $data['name'] ?? '',
            $data['surname'] ?? '',
            $data['email'] ?? '',
            $data['phone'] ?? '',
            $data['code'] ?? '',
            $data['status'] ?? 'Active'
        ];
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Update client
     */
    public function updateClient($id, $data)
    {
        $sql = "UPDATE mv_client SET title = ?, name = ?, surname = ?, email = ?, phone = ?, code = ?, status = ? WHERE id = ?";
        $params = [
            $data['title'] ?? '',
            $data['name'] ?? '',
            $data['surname'] ?? '',
            $data['email'] ?? '',
            $data['phone'] ?? '',
            $data['code'] ?? '',
            $data['status'] ?? 'Active',
            $id
        ];
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Delete client
     */
    public function deleteClient($id)
    {
        $stmt = $this->db->prepare("DELETE FROM mv_client WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
