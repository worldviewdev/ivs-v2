<?php
return [
    'GET' => [
        '/files' => ['FileController', 'index'],
        '/files/all' => ['FileController', 'allFiles'],
        '/files/{id}' => ['FileController', 'show'],
    ],
    'POST' => [
        '/files' => ['FileController', 'store'],
    ],
    'PUT' => [
        '/files/{id}' => ['FileController', 'update'],
    ],
    'DELETE' => [
        '/files/{id}' => ['FileController', 'destroy'],
    ]
];
