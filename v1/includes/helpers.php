<?php

/**
 * get value from $_POST with default
 */
function post($key, $default = '') {
    return isset($_POST[$key]) ? $_POST[$key] : $default;
}

/**
 * get value from $_GET with default
 */
function get($key, $default = '') {
    return isset($_GET[$key]) ? $_GET[$key] : $default;
}

/**
 * get multiple fields from $_POST with defaults
 */
function posts(array $fields) {
    $result = [];
    foreach ($fields as $field => $default) {
        $result[$field] = isset($_POST[$field]) ? $_POST[$field] : $default;
    }
    return $result;
}

function requests(array $fields) {
    $result = [];
    foreach ($fields as $field => $default) {
        $result[$field] = isset($_REQUEST[$field]) ? $_REQUEST[$field] : $default;
    }
    return $result;
}
?>