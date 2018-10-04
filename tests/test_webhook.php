<?php
/**
 * Tests for Webhook Class
 */
use PHPUnit\Framework\TestCase;
require_once(dirname(__FILE__) . '/../src/Webhook.php');


function test_environment_setup() {
    $_SERVER['REQUEST_METHOD'] = 'POST';
    return [
        'projectId' => 'test-project',
        'inputStream' => dirname(__FILE__) . '/data/sample_request.json',
    ];
}

/**
 * Test Class for Webhook Object
 */
class WebhookTest extends TestCase {

    public function test_instantiationAndInputParse() {
        $webhook = new Webhook(test_environment_setup());

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