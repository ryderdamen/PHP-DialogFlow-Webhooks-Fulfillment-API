<?php
use PHPUnit\Framework\TestCase;


if ( ! class_exists('WebhookTestBase')) {

    /**
     * Base class for Webhook Tests
     */
    class WebhookTestBase extends TestCase {


        /**
         * Sets up the test environment
         * by setting the request method
         * and input stream to 
         *
         * @return array - protectId and inputStream path
         */
        protected function setup_environment() {
            $_SERVER['REQUEST_METHOD'] = 'POST';
            return [
                'projectId' => 'test-project',
                'inputStream' => dirname(__FILE__) . '/data/sample_request.json',
                'isTest' => true,
            ];
        }


        /**
         * Strips whitespace for the purpose of the tests
         * Not to be used on actual HTML - strips genuinely all whitespace
         *
         * @param [string] $input
         * @return string
         */
        protected function dangerously_strip_whitespace($input) {
            $input = str_replace(array("\r", "\n"), '', $input); // Remove Line Breaks
            $input = preg_replace('/\s+/', '', $input); // Remove whitespace
            return $input;
        }


    }


}