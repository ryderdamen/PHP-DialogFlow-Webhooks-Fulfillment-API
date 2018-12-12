<?php

/**
 * Ask a question of the user, expect a response
 */

include('../src/Webhook.php');
$wh = new Webhook([
    'projectId' => 'test-project-id'
]);
$wh->ask("What are you looking to do?");
