[![CircleCI](https://circleci.com/gh/ryderdamen/PHP-DialogFlow-Webhooks-Fulfillment-API.svg?style=svg)](https://circleci.com/gh/ryderdamen/PHP-DialogFlow-Webhooks-Fulfillment-API)
# PHP Dialogflow Webhook Fulfillment
A simple API for handling webhook requests and responses from DialogFlow in PHP.

## Getting Started

Everything you need should be in the Webhook.php file with the Webhook class. Here are a few examples of getting set up.


### Sending a simple response to all platforms
To send a simple response to all platforms, use the following code

`````php
include('Webhook.php');
$args = ['projectId' => 'test-project-id'];
$wh = new Webhook($args);
$wh->respond_simpleMessage('Say this out loud', 'Display this text on screen');

`````

## Retrieving Data
Data is posted from the DialogFlow service to a location of your choosing (in the fulfillment tab). To retrieve it, use any of the below API calls.

### Get All Decoded Data
`````php
// Instantiate
include('Webhook.php');
$args = ['projectId' => 'test-project-id'];
$wh = new Webhook($args);

// Get all data as an array
$allData = $wh->getDecodedWebhook();
`````

### Get the intent of the conversation
`````php
// Instantiate
include('Webhook.php');
$args = ['projectId' => 'test-project-id'];
$wh = new Webhook($args);

// Get the intent name of the conversation
$intentName = $wh->get_intent();
`````

### Get any parameters DialogFlow has filtered out for you
`````php
// Instantiate
include('Webhook.php');
$args = ['projectId' => 'test-project-id'];
$wh = new Webhook($args);

// Get all parameters as an array
$parametersArray = $wh->getParameters();

// OR, get a specific parameter. If it does not exist, the function will return FALSE
$parameterValue = $wh->getParameter('country');
`````


## Sending Data
Sending data is quite easy, simply use any of the below API calls.

### Building Google-Device-Specific Actions
The build_* functions create specific rich markups that can be used to send more complicated information to devices running the google assistant.

#### Sending a simple message
Here's an example of how to set up a simple service for only google devices.

`````php

// Instantiate
include('Webhook.php');
$wh = new Webhook('test-project-id');


// Build the response
$textToSpeech = "Say this text out loud";
$displayText = "Display this text on screen";
$wh->build_simpleResponse($textToSpeech, $displayText);


// Send the response
$wh->respond();

`````

#### Sending a SSML response
If you want to add more nuance to your speech, consider using SSML.

`````php

// Instantiate
include('Webhook.php');
$wh = new Webhook('test-project-id');


// Build the response
$ssml = '<speak> Say this out loud, wait three seconds <break time="3s"/> then continue speaking. </speak> ';
$displayText = "Display this text on screen";
$wh->build_ssmlResponse($ssml, $displayText);


// Send the response
$wh->respond();

`````

#### Sending an audio message
Using SSML you can send only audio back to a user. Simply provide the url, or urls (as an array), to the audio files like so.

`````php

// Instantiate
include('Webhook.php');
$wh = new Webhook('test-project-id');


// Build the response
$displayText = "Display this text on screen";
$wh->build_audioResponse( 'https://www.example.com/examples/mp3/example1.mp3', $displayText);


// Send the response
$wh->respond();

`````

## Other

### Ending a conversation
By calling the endConversation() method, you can indicate to dialogFlow that you are not expecting a response. By default, a response from the user will be expected.
