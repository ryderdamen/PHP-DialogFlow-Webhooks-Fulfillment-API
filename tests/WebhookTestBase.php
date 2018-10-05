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
            ];
        }


    }


}