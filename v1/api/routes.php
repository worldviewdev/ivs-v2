<?php
return [
    'GET' => [
        '/files' => [
            'controller' => 'FileController',
            'action' => 'index',
            'auth' => true,
            'permission' => 'read_files'
        ],
        '/files/all' => [
            'controller' => 'FileController',
            'action' => 'allFiles',
            'auth' => true,
            'permission' => 'read_files'
        ],
        '/files/{id}' => [
            'controller' => 'FileController',
            'action' => 'show',
            'auth' => true,
            'permission' => 'read_files'
        ],
        '/auth/me' => [
            'controller' => 'AuthController',
            'action' => 'me',
            'auth' => true
        ],
        '/auth/logout' => [
            'controller' => 'AuthController',
            'action' => 'logout',
            'auth' => true
        ],
        '/auth/status' => [
            'controller' => 'AuthController',
            'action' => 'status',
            'auth' => false
        ],
        '/auth/refresh' => [
            'controller' => 'AuthController',
            'action' => 'refresh',
            'auth' => true
        ],
        '/trip-planning/all' => [
            'controller' => 'TripPlanningController',
            'action' => 'all',
            'auth' => true,
            'permission' => 'read_trip_planning'
        ],
        '/trip-planning/{id}' => [
            'controller' => 'TripPlanningController',
            'action' => 'show',
            'auth' => true,
            'permission' => 'read_trip_planning'
        ]
    ],
    'POST' => [
        '/files' => [
            'controller' => 'FileController',
            'action' => 'store',
            'auth' => true,
            'permission' => 'write_files'
        ],
        '/auth/login' => [
            'controller' => 'AuthController',
            'action' => 'login',
            'auth' => false
        ]
    ],
    'PUT' => [
        '/files/{id}' => [
            'controller' => 'FileController',
            'action' => 'update',
            'auth' => true,
            'permission' => 'write_files'
        ]
    ],
    'DELETE' => [
        '/files/{id}' => [
            'controller' => 'FileController',
            'action' => 'destroy',
            'auth' => true,
            'permission' => 'delete_files'
        ],
        '/trip-planning/{id}' => [
            'controller' => 'TripPlanningController',
            'action' => 'destroy',
            'auth' => true,
            'permission' => 'delete_trip_planning'
        ]
    ]
];
