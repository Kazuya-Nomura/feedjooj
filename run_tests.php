<?php
/**
 * Simple test runner for Twitter OAuth tests
 * This script will run the tests without requiring PHPUnit to be installed
 */

// Include the bootstrap file
require_once __DIR__ . '/tests/bootstrap.php';

// Define test classes
require_once __DIR__ . '/tests/Feature/OAuth/TwitterOAuthTest.php';
require_once __DIR__ . '/tests/Feature/OAuth/TwitterAuthenticationTest.php';

use Tests\Feature\OAuth\TwitterOAuthTest;
use Tests\Feature\OAuth\TwitterAuthenticationTest;

// Simple test runner
class SimpleTestRunner {
    private $passed = 0;
    private $failed = 0;
    private $failures = [];
    
    public function run() {
        echo "Running Twitter OAuth Tests...\n\n";
        
        // Run TwitterOAuthTest
        $this->runTestClass(new TwitterOAuthTest());
        
        // Run TwitterAuthenticationTest
        $this->runTestClass(new TwitterAuthenticationTest());
        
        // Print results
        echo "\n";
        echo "Results: " . ($this->passed + $this->failed) . " tests, " . 
             $this->passed . " passed, " . $this->failed . " failed\n";
        
        if (!empty($this->failures)) {
            echo "\nFailures:\n";
            foreach ($this->failures as $failure) {
                echo "- " . $failure . "\n";
            }
        }
        
        return $this->failed === 0;
    }
    
    private function runTestClass($testClass) {
        $class = new ReflectionClass($testClass);
        $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
        
        echo "Running " . $class->getShortName() . ":\n";
        
        foreach ($methods as $method) {
            $name = $method->getName();
            
            // Only run test methods
            if (strpos($name, 'test') === 0) {
                echo "  - " . $name . "... ";
                
                try {
                    $testClass->$name();
                    echo "PASSED\n";
                    $this->passed++;
                } catch (Exception $e) {
                    echo "FAILED\n";
                    $this->failed++;
                    $this->failures[] = $class->getShortName() . "::" . $name . " - " . $e->getMessage();
                }
            }
        }
    }
}

// Run the tests
$runner = new SimpleTestRunner();
$success = $runner->run();

exit($success ? 0 : 1);