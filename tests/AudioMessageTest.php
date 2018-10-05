<?php
/**
 * Tests for SimpleMessage Class
 */
use PHPUnit\Framework\TestCase;
require_once(dirname(__FILE__) . '/WebhookTestBase.php');
require_once(dirname(__FILE__) . '/../src/responses/AudioMessage.php');
require_once(dirname(__FILE__) . '/../src/Webhook.php');


/**
 * Test Class for AudioMessage Object
 */
class AudioMessageTest extends WebhookTestBase {


    public function test_audioMessageCanAcceptMultipleUrls() {
        $urls = [
            'https://example.com/1.mp3',
            'https://example.com/2.mp3',
            'https://example.com/3.mp3',
        ];
        $args = [
            'url' => $urls,
            'displayText' => 'Display this text'
        ];
        $audio = new AudioMessage($args);
        $actual = $audio->render()['simpleResponse']['ssml'];
        $expected = "
            <speak>
                <audio src = 'https://example.com/1.mp3' />
                <audio src = 'https://example.com/2.mp3' />
                <audio src = 'https://example.com/3.mp3' />
            </speak>
        ";
        $this->assertEquals(
            $this->dangerously_strip_whitespace($actual),
            $this->dangerously_strip_whitespace($expected)
        );

    }

    public function test_audioMessageCanAcceptOneUrl() {
        $args = [
            'url' => 'https://example.com/1.mp3',
            'displayText' => 'Display this text'
        ];
        $audio = new AudioMessage($args);
        $actual = $audio->render()['simpleResponse']['ssml'];
        $expected = "
            <speak>
                <audio src = 'https://example.com/1.mp3' />
            </speak>
        ";
        $this->assertEquals(
            $this->dangerously_strip_whitespace($actual),
            $this->dangerously_strip_whitespace($expected)
        );
    }

}