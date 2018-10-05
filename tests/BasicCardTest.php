<?php
/**
 * Tests for Webhook Class
 */
use PHPUnit\Framework\TestCase;
require_once(dirname(__FILE__) . '/WebhookTestBase.php');
require_once(dirname(__FILE__) . '/../src/responses/BasicCard.php');
require_once(dirname(__FILE__) . '/../src/Webhook.php');


/**
 * Test Class for Webhook Object
 */
class BasicCardTest extends WebhookTestBase {

    public function test_argsAddedToClass() {
        $args = [
            'title' => "Hello World",
            'subtitle' => "It's great to be here",
            'formattedText' => "",
            'imageObject' => [],
            'buttons' => [],
        ];
        $card = new BasicCard($args);
        $this->assertEquals($card->subtitle, $args['subtitle']);
    }

    public function test_weirdArgsNotAddedToClass() {
        $args = [
            'horses' => 'are fun',
        ];
        $card = new BasicCard($args);
        $this->assertFalse(property_exists($card, 'horses'));
    }

    /**
     * Ensures the new object card system does not lose any attributes
     * of the old method
     *
     * @return void
     */
    public function test_basicCardRegression() {
        $args = [
            'title' => "Hello World",
            'subtitle' => "It's great to be here",
            'formattedText' => "",
            'imageObject' => [],
            'buttons' => [],
        ];
        $card = new BasicCard($args);
        $new = $card->render();
        $wh = new Webhook($this->setup_environment());
        $wh->build_basicCard(
            "",
            $args['title'],
            $args['subtitle'],
            $args['formattedText'],
            $args['imageObject'],
            $args['buttons']
        );
        $old = $wh->items[1];
        $this->assertJsonStringEqualsJsonString(
            json_encode($old),
            json_encode($new)
        );
    }

}