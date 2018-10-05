<?php
/**
 * Tests for Webhook Class
 */
use PHPUnit\Framework\TestCase;
require(dirname(__FILE__) . '/WebhookTestBase.php');
require_once(dirname(__FILE__) . '/../src/responses/Image.php');
require_once(dirname(__FILE__) . '/../src/Webhook.php');


/**
 * Test Class for Image Object
 */
class ImageTest extends WebhookTestBase {

    /**
     * Tests that when an image is instantiated without its required properties
     * it throws an exception indicating it needs more properties in the constructor
     * 
     * @expectedException Exception
     * @return void
     */
    public function test_imageWontRenderWithoutRequiredProperties() {
        $args = ['url' => 'https://example.com/image.jpeg']; // Missing accessibilityText
        $image = new Image($args);
        $image->render();
    }


    /**
     * Asserts that the new image object still returns the same
     * dictionary as the old image method
     *
     * @return void
     */
    public function test_imageRegression() {
        $args = [
            'url' => 'https://example.com/image.jpeg',
            'accessibilityText' => 'This is a photo of something',
        ];
        $image = new Image($args);
        $new = $image->render();
        $wh = new Webhook($this->setup_environment());
        $old = $wh->build_image($args['url'], $args['accessibilityText']);
        $this->assertJsonStringEqualsJsonString(json_encode($old), json_encode($new));
    }


}