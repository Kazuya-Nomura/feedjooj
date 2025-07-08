<?php

// Include the application initialization file
require_once __DIR__ . '/../core/settings.php';

// Define test constants
define('CL_TESTING', true);

// Mock functions that might be needed during testing
if (!function_exists('cl_redirect')) {
    function cl_redirect($url) {
        // In tests, we don't want to actually redirect
        return $url;
    }
}

// Setup test database or mock as needed
// This is a simple bootstrap file, you might need to expand it based on your application's needs