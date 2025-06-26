<?php
/**
 * Run Twitter OAuth Test
 */

// Include the test file
require_once __DIR__ . '/tests/TwitterOAuthTest.php';

// Run the test
$test = new TwitterOAuthTest();
$test->run();