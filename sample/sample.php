<?php
    include('../src/Webhook.php');
    $args = ['projectId' => 'test-project-id'];
    $wh = new Webhook($args);
    $wh->respond_simpleMessage('Say this out loud', 'Display this text on screen');