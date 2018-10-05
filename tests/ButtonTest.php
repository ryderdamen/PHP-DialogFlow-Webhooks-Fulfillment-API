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
     * Asserts the new Button class returns the same array
     * as the old build_button method
     *
     * @return void
     */
    public function test_buttonRegression() {

        // New Button Object
        $args = [
            'title' => 'The Website',
            'url' => 'https://example.com',
        ];
        $sm = new Button($args);
        $new = $sm->render();

        // Old Button Method
        $wh = new Webhook($this->setup_environment());
        $old = $wh->build_button($args['title'], $args['url']);
        $this->assertJsonStringEqualsJsonString(json_encode($old), json_encode($new));

    }


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