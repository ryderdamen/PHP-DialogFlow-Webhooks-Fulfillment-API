<?php
/**
 * Tests for Webhook Class
 */
use PHPUnit\Framework\TestCase;
require_once(dirname(__FILE__) . '/WebhookTestBase.php');
require_once(dirname(__FILE__) . '/../src/Webhook.php');


/**
 * Test Class for Webhook Object
 */
class WebhookTest extends WebhookTestBase {


    public function test_instantiationAndInputParse() {
        $webhook = new Webhook($this->setup_environment());

        $this->assertJsonStringEqualsJsonString(
            json_encode($webhook->decoded_webhook),
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
    
        $this->assertEquals($webhook->decoded_webhook, null);
    }


    /**
     * Tests the get_decoded_webhook method of the webhook class
     */
    public function test_get_decoded_webhook() {
        $wh = new Webhook($this->setup_environment());
        $this->assertEquals(count($wh->get_decoded_webhook()), 7);
    }


    /**
     * Tests the get_intent method of the webhook class
     */
    public function test_get_intent() {
        $wh = new Webhook($this->setup_environment());
        $this->assertEquals($wh->get_intent(), 'get_weather');
    }


    /**
     * Tests the get_language method of the webhook class
     */
    public function test_get_language() {
        $wh = new Webhook($this->setup_environment());
        $this->assertEquals($wh->get_language(), 'en-ca');
    }


    /**
     * Tests the get_timestamp method of the webhook class
     * returns a string when requested
     */
    public function test_get_timestamp_string() {
        $wh = new Webhook($this->setup_environment());
        $this->assertEquals($wh->get_timestamp(true), '2018-03-28T22:07:23.168Z');
    }


    /**
     * Tests the get_timestamp method of the webhook class
     * returns a unix timestamp
     */
    public function test_get_timestamp() {
        $wh = new Webhook($this->setup_environment());
        $this->assertEquals($wh->get_timestamp(), 1522274843);
    }


    /**
     * Tests the get_query method of the webhook class
     */
    public function test_get_query() {
        $wh = new Webhook($this->setup_environment());
        $this->assertEquals($wh->get_query(), 'cyxu');
    }

    /**
     * Tests the get_parameters method of the webhook class
     */
    public function test_get_parameters() {
        $wh = new Webhook($this->setup_environment());
        $expected = [
            "airport" => [
                "name" => "London",
                "city" => "London",
                "country" => "Canada",
                "IATA" => "YXU",
                "ICAO" => "CYXU"
            ]
        ];
        $this->assertEquals($wh->get_parameters(), $expected);
    }


    /**
     * Tests that a specific parameter can be retrieved from the webhook
     */
    public function test_get_parameter() {
        $wh = new Webhook($this->setup_environment());
        $this->assertEquals(
            $wh->get_parameter('airport'),
            [
                "name" => "London",
                "city" => "London",
                "country" => "Canada",
                "IATA" => "YXU",
                "ICAO" => "CYXU"
            ]
        );
    }


    /**
     * Tests that a parameter not found in the webhook returns false
     */
    public function test_get_parameter_returnsFalseWhenNoParam() {
        $wh = new Webhook($this->setup_environment());
        $this->assertEquals($wh->get_parameter('ketchup'), false);
    }



}