# PHP Dialogflow Webhook Filfillment
A simple class for handling webhook requests from Dialogflow in PHP.

## Getting Started

Grab the Webhook.php file out of the src folder, and put it in your project where necessary (composer coming soon).

Include the file with the following code, keeping in mind to adjust it to your directory as neccessary:

`````php

include('Webhook.php');

`````

Next, initialize a new instance of the class to receive webhooks from Dialogflow, with your project id

`````php

$wh = new Webhook('test-project-id');

`````

Finally, send a simple text / speech response back to dialogflow


`````php

$wh->respond_simpleMessage('Say this out loud', 'Display this text on screen');

`````


## Functions

### Retreiving Data

#### getDecodedWebhook()
returns the decoded JSON from google as an array

#### getRawInput()
gets the raw php://input (should be a JSON string if from google)

### Sending Data

#### respond_simpleMessage($textToSpeak, $stringToDisplay)
Sends a simple message (to say and to display)
second parameter will default to first if left empty
	
#### respond_fullJson($jsonString)
Send your own JSON string to DialogFlow