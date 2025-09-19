<?php

require_once __DIR__ . '/../repositories/UserRepository.php';

class UserController
{
    private $db;
    private $userRepository;

    public function __construct()
    {
        $this->db = Database::conn();
        $this->userRepository = new UserRepository();
    }

    public function index()
    {
        if (isset($_GET['action']) && $_GET['action'] === 'get_clients') {
            $this->getClients();
            return;
        }

        if (isset($_GET['action']) && $_GET['action'] === 'get_all_clients') {
            $this->getAllClients();
            return;
        }

        if (!isset($_GET['action']) || $_GET['action'] === '') {
            $this->sendJsonResponse(['message' => 'Unknown action']);
            return;
        }
    }

    public function getClients()
    {
        $clients = $this->userRepository->getClients();
        $this->sendJsonResponse($clients);
    }

    public function getAllClients()
    {
        $response = $this->userRepository->getAllClients($_GET);
        $this->sendJsonResponse($response);
    }

    private function sendJsonResponse($data)
    {
        Response::json($data);
    }
}
