<?php
/**
 * Tests for Carousel Class
 */
use PHPUnit\Framework\TestCase;
require_once(dirname(__FILE__) . '/WebhookTestBase.php');
require_once(dirname(__FILE__) . '/../src/responses/CarouselItem.php');
require_once(dirname(__FILE__) . '/../src/responses/Carousel.php');
require_once(dirname(__FILE__) . '/../src/Webhook.php');


/**
 * Test Class for SimpleMessage Object
 */
class CarouselTest extends WebhookTestBase {

    /**
     * Ensures basic rendering of title and description
     *
     * @return void
     */
    public function test_titleAndDescriptionItem() {
        $args = [
            'title' => 'Hello World',
            'description' => 'How are you doing?',
        ];
        $item = new CarouselItem($args);
        $rendered = $item->render();
        $expected = '
            {
                "optionInfo": {
                    "key":null,
                    "synonyms":null
                },
                "title": "Hello World",
                "description": "How are you doing?",
                "image": {
                    "imageUri": null,
                    "accessibilityText": null
                }
            }';
        $this->assertEquals(
            $this->dangerously_strip_whitespace(json_encode($rendered)),
            $this->dangerously_strip_whitespace($expected)
        );
    }

}