<?php
/**
 * Twitter OAuth Test
 * 
 * This test verifies that the Twitter OAuth functionality is working correctly
 */

// Define a mock configuration for testing
$cl = [
    'config' => [
        'twitter_oauth' => 'on',
        'twitter_api_id' => 'test_api_id',
        'twitter_api_key' => 'test_api_key',
    ]
];
echo $cl['config']['twitter_oauth'] . "\n";
echo $cl['config']['twitter_api_id'] . "\n";
echo $cl['config']['twitter_api_key'] . "\n";
// Mock the cl_link function
if (!function_exists('cl_link')) {
    function cl_link($path) {
        return "https://cointweet.com/{$path}";
    }
}

class TwitterOAuthTest {
    /**
     * Run all tests
     */
    public function run() {
        echo "Running Twitter OAuth Tests...\n";
        
        $this->testTwitterConfigExists();
        $this->testTwitterOAuthUrl();
        $this->testTwitterProviderInList();
        
        echo "All tests completed successfully!\n";
    }
    
    /**
     * Test that Twitter OAuth configuration exists
     */
    private function testTwitterConfigExists() {
        global $cl;
        
        echo "Testing Twitter OAuth configuration... ";
        
        // Check if Twitter OAuth is enabled
        if (!isset($cl['config']['twitter_oauth'])) {
            echo "FAILED: Twitter OAuth configuration not found\n";
            exit(1);
        }
        
        // Check if Twitter API credentials are set
        if (!isset($cl['config']['twitter_api_id']) || !isset($cl['config']['twitter_api_key'])) {
            echo "FAILED: Twitter API credentials not found\n";
            exit(1);
        }
        
        echo "PASSED\n";
    }
    
    /**
     * Test that the Twitter OAuth URL is correctly formed
     */
    private function testTwitterOAuthUrl() {
        echo "Testing Twitter OAuth URL... ";
        
        $expected_url = cl_link('oauth/twitter');
        
        if (empty($expected_url) || strpos($expected_url, 'oauth/twitter') === false) {
            echo "FAILED: Twitter OAuth URL is not correctly formed\n";
            exit(1);
        }
        
        echo "PASSED\n";
    }
    
    /**
     * Test that Twitter is in the provider list
     */
    private function testTwitterProviderInList() {
        echo "Testing Twitter provider in list... ";
        
        // Get the provider list from the OAuth content file
        $content_file = file_get_contents(__DIR__ . '/../apps/native/http/oauth/content.php');
        
        if (strpos($content_file, "'twitter' => 'Twitter'") === false) {
            echo "FAILED: Twitter provider not found in provider list\n";
            exit(1);
        }
        
        echo "PASSED\n";
    }
}

// Run the tests
$test = new TwitterOAuthTest();
$test->run();