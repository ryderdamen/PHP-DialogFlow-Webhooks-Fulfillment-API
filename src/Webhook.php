<?php

/**
 * Webhook Class
 * Handles the workload for webhooks from DialogFlow, and provides an API for simple responses
 *
 * @since 1.0
 */
 
 
class Webhook {
	
	// Global Variables -----------------------------------------------------------------------------------------------------------
	
	// Data from Dialogflow
    public $decodedWebhook = null;
    public $googleUserId = false;
    
    // Other
    public $hasResponded = false;
    
    // Response To Dialogflow
    public $expectUserResponse = true; // Default, expect a user's response
    private $items = array();
    public $conversationToken = "{\"state\":null,\"data\":{}}";
    public $speech = 'Sorry, that action is not available on this platform.';
    public $displayText = 'Sorry, that action is not available on this platform.';
    
    
   
	// Constructor ----------------------------------------------------------------------------------------------------------------
	
	public function __construct($projectId) {
		
		// Get the type of request this is
		$requestType = $this->getTypeOfRequest($projectId);
		
		if ($requestType == 'webhook') {
			$this->processWebHook();
			return;
		}
		return; // Otherwise, return nothing
	}
	
	  
	// Other Methods -------------------------------------------------------------------------------------------------------------------
	
	// Determines the type of request this is @return STRING: webhook | other
	private function getTypeOfRequest($projectId) {
		
		// If this is a POST request, likely it's Google, but let's check to confirm
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			try {
				$json = file_get_contents('php://input');
				$action = json_decode($json, true);
				if ($action == '' or $action == null) {
					return 'other';
                }
                // Confirm that this request matches the projectID

				$this->decodedWebhook = $action; // Make the webhook JSON available to the class
				return 'webhook';
			}
			catch (Exception $e) {
				return 'other';
			}
		}
        return 'other'; // Else, just return something else
	}
	
	// Processes the webhook from google for the user to access @return void
	private function processWebHook() {
		
		// If there is a user ID, add it to the global scope for access
		if ( isset($this->decodedWebhook['originalRequest']['data']['user']['userId']) ) {
			$this->googleUserId = $this->decodedWebhook['originalRequest']['data']['user']['userId'];
		}
        
    }
	
		
    
    // Respond to DialogFlow --------------------------------------------------------------------------------------------------------
    
    // Build Methods ---------------------------------
    
    
    
    // Builds a BasicCard Object
    public function build_basicCard($simpleResponseText, $title, $subtitle, $formattedText, $imageObject, $buttonObject, $imageDisplayOptions) {
	   
	   // There must be a simple response before a card, so create one now
	   $this->build_simpleResponse($simpleResponseText, $simpleResponseText);
	   
	   // Construct the basic card JSON
	   $basicCard = array(
	   		'basicCard' => array(
			   'title' => $title,
			   'subtitle' => $subtitle,
			   'formattedText' => $formattedText,
			   'image' => $imageObject,
			   'buttons' => array($buttonObject),
			   'imageDisplayOptions' => $imageDisplayOptions,
		   )
	   );
	   $this->items[] = $basicCard;
	}
	
	
	
    
    // Builds the image attribute for a structure like the basic card
    public function build_image($url, $accessibilityText, $height = null, $width = null) {
		   $image = array(
			   'url' => $url,
			   'accessibilityText' => $accessibilityText,
			   'height' => $height,
			   'width' => $width,
		   );
		   return $image;
    }
    
    
    
    // Builds the button attribute for a structure
    public function build_button($title, $url) {
	    return array(
		    'title' => $title,
		    'openUrlAction' => array(
			    'url' => $url,
			)
	    );
    }
    
    
    
    
    // Builds a simple response item
    public function build_simpleResponse($textToSpeech, $displayText) {
	    $response = array(
		   'simpleResponse' => array(
			   'textToSpeech' => $textToSpeech,
			   'displayText' => $displayText
			)
		);
		$this->items[] = $response;
    }
    
    
    
    
    // Builds a SSML response item    
    public function build_ssmlResponse($ssml, $displayText) {
	    $response = array(
		   'simpleResponse' => array(
			   'ssml' => $ssml,
			   'displayText' => $displayText
			)
		);
		$this->items[] = $response;
    }
    
    
    
    
    // Builds an audio response item (just a SSML with audio)
    public function build_audioResponse($url, $displayText) {
	    
	    // Loop through the URLs if they are an array and build the ssml as necessary
	    $ssml = ' <speak> ';
	    if ( is_array($url) ) {
		    foreach($url as $u) {
			     $ssml .= "<audio src = '" . $u . "' /> ";
		    }
	    }
	    else {
		    $ssml .= "<audio src = '" . $url . "' /> ";
	    }
	    $ssml .= '</speak>';
	    
	    $response = array(
		   'simpleResponse' => array(
			   'ssml' => $ssml,
			   'displayText' => $displayText
			)
		);
		$this->items[] = $response;
    }
    
    
    
    
    // Responds immediately with an array of the user's choosing, printed as JSON
    public function respond_fullJson($jsonString) {
	    
	    // Prevent duplicate responses
	    if ($this->hasResponded) return;
	    $this->hasResponded = true;
	    
        header("Content-type:application/json");
        echo $json;
    }



	// Responds immediately with a simple text/string message
    public function respond_simpleMessage($textToSpeak, $stringToDisplay = '') {
	    
	    // Prevent duplicate responses
	    if ($this->hasResponded) return;
	    $this->hasResponded = true;
	    
        // If this hasn't been defined, set it to the same text as the speech (accessibility)
        if ($stringToDisplay == '') {
            $stringToDisplay = $textToSpeak;
        }
        header("Content-type:application/json");
		echo json_encode(array(
			"speech" => $textToSpeak,
			"displayText" => $stringToDisplay,
		));
    }
    
    
    // Sends the response to Dialogflow
    public function respond() {
	   
	   // Prevent duplicate responses
	   if ($this->hasResponded) return;
	   $this->hasResponded = true;
	   
	   // Set google as default for now
	   $integrations = array(
		   'google' => array(
			   'richResponse' => array(
				   'items' => $this->items
			   )
		   )
	   );
	   $response = array(
		   'speech' => $this->speech,
		   'displayText' => $this->displayText,
		   'expectUserResponse' => $this->expectUserResponse,
		   'data' => $integrations,
	   );
	   
	   header("Content-type:application/json");
	   echo json_encode($response);
    }
      
   
    // Redefine fallback / default text for speech (in case a user doesn't have a google device)
    public function setFallbackText($text) {
		$this->speech = $text;
    }

    // DialogFlow Data Retrieval --------------------------------------------------------------------------------------------------------

    // Returns the full decoded webhook array for the user
    public function getDecodedWebhook() {
        return $this->decodedWebhook;
    }

    // Returns the raw input for the user (if it's from dialogflow: it's a json string)
    public function getRawInput() {
	    return file_get_contents('php://input');
    }
    
    // Gets the intent passed with the webhook
    public function get_intent() {
	    return $this->decodedWebhook['result']['metadata']['intentName'];
    }
    
    // Returns the language
    public function get_language() {
	    return $this->decodedWebhook['lang'];
    }
    
    // Returns the timestamp
    public function get_timestamp() {
	    return $this->decodedWebhook['timestamp'];
    }
    
    // Returns the user's query
    public function get_query() {
	    return $this->decodedWebhook['result']['resolvedQuery'];
    }
    
    // Returns a full array of the parameters passed with the webhook
    public function get_parameters() {
	    return $this->decodedWebhook['result']['parameters'];
    }
    
    // Returns a specific parameter, or false if no parameter exists
    public function get_parameter($parameter) {
	    if (isset($this->decodedWebhook['result']['parameters'][$parameter])) {
		    return $this->decodedWebhook['result']['parameters'][$parameter];
	    }
		return false;
    }
    
    // Ends the conversation by not expecting a response from the user
    public function endConversation() {
	    $this->expectUserResponse = false;
    }
    

} // End of Class Webhook
