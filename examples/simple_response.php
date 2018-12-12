<?php

/**
 * Send a simple response to the user, and do not expect a response
 */

include('../src/Webhook.php');
$wh = new Webhook([
    'projectId' => 'test-project-id'
]);
$wh->tell("I'm afraid I can't do that, Dave.");
