<?php
/**
 * Tests for SimpleMessage Class
 */
use PHPUnit\Framework\TestCase;
require_once(dirname(__FILE__) . '/WebhookTestBase.php');
require_once(dirname(__FILE__) . '/../src/responses/Button.php');
require_once(dirname(__FILE__) . '/../src/Webhook.php');


/**
 * Test Class for Button Object
 */
class ButtonTest extends WebhookTestBase {

    /**
     * Ensures a button will throw an exception without a required property
     * 
     * @expectedException PropertyIsRequiredException
     * @return void
     */
    public function test_buttonWillNotRenderWithoutRequiredProperty() {

        $args = [
            'title' => 'The Website', // Missing 'url'
        ];
        $sm = new Button($args);
        $new = $sm->render();
    }

}