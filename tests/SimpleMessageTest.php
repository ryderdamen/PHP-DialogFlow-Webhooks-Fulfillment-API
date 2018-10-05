<?php
/**
 * Tests for SimpleMessage Class
 */
use PHPUnit\Framework\TestCase;
require_once(dirname(__FILE__) . '/WebhookTestBase.php');
require_once(dirname(__FILE__) . '/../src/responses/SimpleMessage.php');
require_once(dirname(__FILE__) . '/../src/Webhook.php');


/**
 * Test Class for SimpleMessage Object
 */
class SimpleMessageTest extends WebhookTestBase {

    /**
     * Asserts the new simple message class returns the same array
     * as the old simpleResponse method
     *
     * @return void
     */
    public function test_simpleMessageRegression() {

        // New SimpleMessage Object
        $args = [
            'textToSpeech' => 'This should be spoken',
            'displayText' => 'This should be displayed',
        ];
        $sm = new SimpleMessage($args);
        $new = $sm->render();

        // Old SimpleResponse Method
        $wh = new Webhook($this->setup_environment());
        $wh->build_simpleResponse($args['textToSpeech'], $args['displayText']);
        $old = $wh->items[0];
        $this->assertJsonStringEqualsJsonString(json_encode($old), json_encode($new));

    }

}