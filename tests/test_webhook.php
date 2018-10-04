<?php
/**
 * Tests for Webhook Class
 */
use PHPUnit\Framework\TestCase;
require(dirname(__FILE__) . '/WebhookTestBase.php');
require_once(dirname(__FILE__) . '/../src/Webhook.php');


/**
 * Test Class for Webhook Object
 */
class WebhookTest extends WebhookTestBase {

    public function test_instantiationAndInputParse() {
        $webhook = new Webhook($this->setup_environment());

        $this->assertJsonStringEqualsJsonString(
            json_encode($webhook->decodedWebhook),
            file_get_contents(dirname(__FILE__) . '/data/sample_request.json')
        );
    }

    public function test_whenRequestIsGetNothingIsProcessed() {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $args = [
            'projectId' => 'test-project',
            'inputStream' => dirname(__FILE__) . '/data/sample_request.json',
        ];
        $webhook = new Webhook($args);
    
        $this->assertEquals($webhook->decodedWebhook, null);
    }

    // TODO: FIX
    // public function test_getIntent() {
    //     $webhook = new Webhook(test_environment_setup());
    //     $this->assertEquals('get_weather', $webhook->get_intent());
    // }

}