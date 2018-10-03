<?php

// Webhook Test Project

// Includes
include('../src/Webhook.php');


// Initialize Webhook: Listen for incoming request
$wh = new Webhook('test-project-id');

// Retrieve variables
$intent = $wh->get_intent();

// Build an image for the card
$image = $wh->build_image('https://www.pakmen.com/wp-content/uploads/2016/09/canadian-flag.jpg', 'A photo of something', 200, 400);

// Build a button for the card
$button = $wh->build_button('more info', 'https://example.com');

// Build a structured card
$wh->build_basicCard('Say this out loud', 'Card Title', 'Card Subtitle', 'The paragraph text of the card', $image, $button, 'DEFAULT');

//$wh->build_simpleResponse('new say', 'new display');

//$ssml = '<speak> There is a three second pause here <break time="3s"/> then the speech continues. </speak> ';

// $wh->build_audioResponse( 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3', 'display this');
$wh->respond();

// Send a simple response back
//$wh->respond_simpleMessage("Hmm, looks like you were looking for {$intent}");





































function saveData($data) {
	$myfile = fopen( __DIR__ . "/response.txt", "w");
	fwrite($myfile, $data);
	fclose($myfile);
}

