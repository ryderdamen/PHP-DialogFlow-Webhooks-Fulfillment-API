<?php

/**
 * Webhook Class
 * Handles the workload for webhooks from DialogFlow, and provides an API for simple responses
 *
 * @since 2.0
 * @author Ryder Damen <dev@ryderdamen.com>
 */
 
class Webhook {
	
	// Global Variables -----------------------------------------------------------------------------------------------------------
	
	// Data from Dialogflow
    public $decodedWebhook = null;
	public $googleUserId = false;
	public $projectId = false;
    
    // Other
    public $hasResponded = false;
	private $platforms = array('PLATFORM_UNSPECIFIED');
	private $inputStream = "php://input";
    
    // Response To Dialogflow
    public $expectUserResponse = true; // Default, expect a user's response
    public $items = array();
    public $conversationToken = "{\"state\":null,\"data\":{}}";
    public $speech = 'Sorry, that action is not available on this platform.';
    public $displayText = 'Sorry, that action is not available on this platform.';
    
    
   
	// Constructor ----------------------------------------------------------------------------------------------------------------
	
	public function __construct($args) {
		
		// If a project ID is provided for verification
		if ( array_key_exists('projectId', $args) ) {
			$this->projectId = $args['projectId'];
		}

		// Define an input stream for testing
		if ( array_key_exists('inputStream', $args) ) {
			$this->inputStream = $args['inputStream'];
		}

		// Get the type of request this is
		$requestType = $this->getTypeOfRequest();
		
		if ($requestType == 'webhook') {
			$this->processWebHook();
			return;
		}
		return; // Otherwise, return nothing
	}
	  
	// Other Methods ---------------------------------------------------------------------------------------------------------------
	
	/**
	 * Determines the type of request this is
	 *
	 * @return [string] 'webhook' or 'other'
	 */
	private function getTypeOfRequest() {
		
		// If this is a POST request, likely it's Google, but let's check to confirm
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			try {
				$json = file_get_contents($this->inputStream);
				$action = json_decode($json, true);
				if ($action == '' or $action == null) {
					return 'other';
                }
                // Confirm that this request matches the projectID // TODO
				$this->decodedWebhook = $action; // Make the webhook JSON available to the class
				return 'webhook';
			}
			catch (Exception $e) {
				return 'other';
			}
		}
        return 'other'; // Else, just return something else
	}
	

	/**
	 * Processes the webhook from google for the user to access
	 *
	 * @return void
	 */
	private function processWebHook() {
		
		// If there is a user ID, add it to the global scope for access
		if ( isset($this->decodedWebhook['originalRequest']['data']['user']['userId']) ) {
			$this->googleUserId = $this->decodedWebhook['originalRequest']['data']['user']['userId'];
		}
        
    }
	        
    
	/**
	 * Builds an item for the carousel
	 *
	 * @deprecated  2.0.0
	 * @param string $title
	 * @param string $description
	 * @param string $imageUrl
	 * @param string $imageAlt
	 * @param string $dialogKey
	 * @param string $dialogSynonyms
	 * @return void
	 */
    public function build_carouselItem($title, $description, $imageUrl, $imageAlt, $dialogKey = '', $dialogSynonyms = '' ) {
	    return array(
		    'info' => array(
			    'key' => $dialogKey,
			    'synonyms' => $dialogSynonyms,
		    ),
		    'title' => $title,
		    'description' => $description,
		    'image' => array(
			    'imageUri' => $imageUrl,
			    'accessibilityText' => $imageAlt,
		    ),
	    );
    }
	
	
	/**
	 * Builds the carousel from carouselItems
	 *
	 * @deprecated 2.0.0
	 * @param string $simpleResponseText
	 * @param string $items
	 * @return void
	 */
    public function build_carousel($simpleResponseText, $items) {
	    
	    // There must be a simple response before a carousel, so create one now
	   $this->build_simpleResponse($simpleResponseText, $simpleResponseText);
	   $carousel = array(
	   		'carouselSelect' => array(
			   'items' => $items
		   )
	   );
	   $this->items[] = $carousel;
    }


    /**
	 * Builds a BasicCard Object
	 *
	 * @param [type] $simpleResponseText
	 * @param [type] $title
	 * @param [type] $subtitle
	 * @param [type] $formattedText
	 * @param [type] $imageObject
	 * @param [type] $buttonObject
	 * @param string $imageDisplayOptions
	 * @return void
	 */
    public function build_basicCard(
    	$simpleResponseText,
    	$title,
    	$subtitle,
    	$formattedText,
    	$imageObject,
    	$buttonObject,
    	$imageDisplayOptions = 'DEFAULT'
    	) {
	   
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


    /**
	 * Builds the image attribute for a structure like the basic card
	 *
	 * @deprecated 2.0.0
	 * @param [type] $url
	 * @param [type] $accessibilityText
	 * @param [type] $height
	 * @param [type] $width
	 * @return void
	 */
    public function build_image($url, $accessibilityText, $height = null, $width = null) {
		   $image = array(
			   'url' => $url,
			   'accessibilityText' => $accessibilityText,
			   'height' => $height,
			   'width' => $width,
		   );
		   return $image;
    }    


    /**
	 * Builds the button attribute for a structure
	 *
	 * @deprecated 2.0.0
	 * @param string $title
	 * @param string $url
	 * @return void
	 */
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
    

	/**
	 * Builds an audio response item (just a SSML with audio)
	 *
	 * @deprecated 2.0.0
	 * @param string $url
	 * @param string $displayText
	 * @return void
	 */
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
        echo $jsonString;
    }


	/**
	 * Responds immediately with a simple text/string message
	 * @deprecated version 2.0.0
	 * @param string $textToSpeak
	 * @param string $stringToDisplay
	 * @return void
	 */
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
	

	public function addResponse($responseItem) {
		try {
			$this->items[] = $responseItem->render();
		} catch (Exception $e) {
			throw new Exception('Not a valid response object');
		}
	}
	
	
    // Sends the response to Dialogflow
    public function respond() {
	   
	   // Prevent duplicate responses
	   if ($this->hasResponded) return;
	   $this->hasResponded = true;
	   
	   // Set google as default for now
	   $integrations = array(
		   'google' => array(
			   'expectUserResponse' => $this->expectUserResponse,
			   'richResponse' => array(
				   'items' => $this->items
			   )
		   )
	   );
	   
	   $fulfillmentMessages = array();
	   
	   $response = array(
		   'fulfillmentText' => $this->speech,		   
		   'payload' => $integrations,		   
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
	    return $this->decodedWebhook['queryResult']['intent']['displayName'];
    }
    
    // Returns the language
    public function get_language() {
	    return $this->decodedWebhook['queryResult']['languageCode'];
    }
    
    // Returns the timestamp
    public function get_timestamp() {
	    return $this->decodedWebhook['timestamp'];
    }
    
    // Returns the user's query
    public function get_query() {
	    return $this->decodedWebhook['queryResult']['queryText'];
    }
    
    // Returns a full array of the parameters passed with the webhook
    public function get_parameters() {
	    return $this->decodedWebhook['queryResult']['parameters'];
    }
    
    // Returns a specific parameter, or false if no parameter exists
    public function get_parameter($parameter) {
	    if (isset($this->decodedWebhook['queryResult']['parameters'][$parameter])) {
		    return $this->decodedWebhook['queryResult']['parameters'][$parameter];
	    }
		return false;
    }
    
    // Ends the conversation by not expecting a response from the user
    public function endConversation() {
	    $this->expectUserResponse = false;
    }
 
    // Adds FACEBOOK, SLACK, TELEGRAM, KIK, SKYPE, LINE, VIBER, ACTIONS_ON_GOOGLE, for rich messages
    public function addPlatform($platformEnum) {
	    $this->platform[] = $platform;
    }
    

} // End of Class Webhook
