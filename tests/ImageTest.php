<?php
/**
 * Tests for Webhook Class
 */
use PHPUnit\Framework\TestCase;
require_once(dirname(__FILE__) . '/WebhookTestBase.php');
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
     * @expectedException PropertyIsRequiredException
     * @return void
     */
    public function test_imageWontRenderWithoutRequiredProperties() {
        $args = ['url' => 'https://example.com/image.jpeg']; // Missing accessibilityText
        $image = new Image($args);
        $image->render();
    }

}