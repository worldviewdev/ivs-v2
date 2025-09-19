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
        ],
        '/quick-contact/all' => [
            'controller' => 'QuickContactController',
            'action' => 'all',
            'auth' => true,
            'permission' => 'read_quick_contact'
        ],
        '/quick-contact/{id}' => [
            'controller' => 'QuickContactController',
            'action' => 'show',
            'auth' => true,
            'permission' => 'read_quick_contact'
        ],
        '/users/list' => [
            'controller' => 'UserController',
            'action' => 'index',
            'auth' => true,
            'permission' => 'read_users'
        ]
    ],
    'POST' => [
        '/files' => [
            'controller' => 'FileController',
            'action' => 'store',
            'auth' => true,
            'permission' => 'write_files'
        ],
        '/users' => [
            'controller' => 'UserController',
            'action' => 'store',
            'auth' => true,
            'permission' => 'write_users'
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
        ],
        '/quick-contact/{id}' => [
            'controller' => 'QuickContactController',
            'action' => 'destroy',
            'auth' => true,
            'permission' => 'delete_quick_contact'
        ]
    ]
];
