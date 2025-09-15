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
        ]
    ]
];
