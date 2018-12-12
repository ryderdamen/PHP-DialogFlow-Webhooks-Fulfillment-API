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
	private $inputStream = "php://input";
    
    // Response To Dialogflow
    public $expectUserResponse = true; // Default, expect a user's response
    public $items = array();
    public $conversationToken = "{\"state\":null,\"data\":{}}";
    public $speech = 'Sorry, that action is not available on this platform.';
    public $displayText = 'Sorry, that action is not available on this platform.';
	
	
	// 2.0
	public $simpleResponse;
	private $richResponse;
   

	/**
	 * Default constructor for the webhook class
	 * 
	 * @param array Arguments for webhook setup
	 */
	public function __construct($args) {
		if ( array_key_exists('projectId', $args) ) {
			$this->projectId = $args['projectId'];
		}
		// Define an input stream for testing
		if ( array_key_exists('inputStream', $args) ) {
			$this->inputStream = $args['inputStream'];
		}
		$request_type = $this->get_type_of_request();
		if ($request_type == 'webhook') {
			$this->process_webhook();
			return;
		}
		return;
	}
	  
	
	/**
	 * Determines the type of request this is
	 *
	 * @return [string] 'webhook' or 'other'
	 */
	private function get_type_of_request() {
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			try {
				$json = file_get_contents($this->inputStream);
				$action = json_decode($json, true);
				if ($action == '' or $action == null) {
					return 'other';
                }
				$this->decodedWebhook = $action;
				return 'webhook';
			}
			catch (Exception $e) {
				return 'other';
			}
		}
        return 'other';
	}
	

	/**
	 * Processes the webhook from google for the user to access
	 *
	 * @return void
	 */
	private function process_webhook() {
		// If there is a user ID, add it to the global scope for access
		if ( isset($this->decodedWebhook['originalRequest']['data']['user']['userId']) ) {
			$this->googleUserId = $this->decodedWebhook['originalRequest']['data']['user']['userId'];
		}
        
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
	

	/**
	 * Adds a rich component to the response (Card, Media Response, Carousel, etc)
	 *
	 * @param BasicCard|Carousel|MediaResponse|TableCard $response_item
	 * @return void
	 */
	public function add_rich_response($response_item) {
		try {
			$this->richResponse = $response_item->render();
		} catch (Exception $e) {
			throw new Exception('Not a valid rich response object');
		}
	}


	/**
	 * Adds a simple response to the conversation
	 */
	public function add_simple_response($text) {
		// TODO
	}

	/**
	 * Adds a response (be it a rich or simple response)
	 * 
	 * @param string|object -- String or proper response object
	 * @return null
	 */
	private function add_response($response_item) {
		if (is_string($response_item)) {
			return $this->add_simple_response($response_item);
		}
		$this->add_rich_response($response_item);
	}

	

	/**
	 * Builds the response integrations (google, etc.)
	 * 
	 * @return array
	 */
	private function build_response_integrations() {
		// Default return only Google right now  // TODO Add more
		return [
			'google' => array(
				'expectUserResponse' => $this->expectUserResponse,
				'richResponse' => [
					'items' => $this->items
				]
			)
		];
	}
	

	/**
	 * Asks the user for input (keeps the conversation open)
	 */
	public function ask($response) {
		$this->expectUserResponse = true;
		$this->add_response($response);
	}

	/** 
	 * Tells the user (closes the conversation)
	 */
	public function tell($response) {
		$this->expectUserResponse = false;
		$this->add_response($response);
	}

	
    /**
	 * Sends the HTTP application/json request back to DialogFlow
	 * @return null
	 */
    public function respond() {
	   // Prevent duplicate responses
	   if ($this->hasResponded) return;
	   $this->hasResponded = true;
	   $response = array(
		   'fulfillmentText' => $this->speech,		   
		   'payload' => $this->build_response_integrations(),		   
	   );
	   header("Content-type:application/json");
	   echo json_encode($response);
	}
	
	


    /**
	 * Redefine fallback / default text for speech (in case a user doesn't have a compatible device)
	 */
    public function setFallbackText($text) {
		$this->speech = $text;
    }


    /**
	 * Returns the full decoded webhook array for the user
	 * @return array
	 */
    public function getDecodedWebhook() {
        return $this->decodedWebhook;
    }


    /**
	 * Returns the raw input for the user (if it's from dialogflow: it's a json string)
	 * @return string
	 */
    public function getRawInput() {
	    return file_get_contents('php://input');
    }
	
	
    /**
	 * Gets the intent name passed with the webhook
	 * @return string - The name of the intent
	 */
    public function get_intent() {
	    return $this->decodedWebhook['result']['metadata']['intentName'];
    }
	
	
    /**
	 * Return the input language the user is using
	 * @return string - The language code
	 */
    public function get_language() {
	    return $this->decodedWebhook['lang'];
    }
	
	
  	/**
	* Return the timestamp this request was executed at
	* @return int|string
    */
    public function get_timestamp($string=false) {
		if ($string !== false) {
			return $this->decodedWebhook['timestamp'];
		}
		return strtotime($this->decodedWebhook['timestamp']);
    }
	
	
    /**
	 * Returns the user's query (what they asked)
	 * @return string
	 */
    public function get_query() {
	    return $this->decodedWebhook['result']['resolvedQuery'];
	}
	

	/**
	 * Returns the unique conversation ID for this conversation
	 * @return string
	 */
	public function get_conversation_id() {
		return $this->decodedWebhook['originalRequest']['data']['conversation']['conversationId'];
	}
	
	
    /**
	 * Returns a full array of the parameters passed with the webhook
	 * @return array
	 */
    public function get_parameters() {
	    return $this->decodedWebhook['result']['parameters'];
    }
	
	
    /**
	 * Returns a specific parameter, or false if no parameter exists
	 * @param string parameter -- The parameter to retrieve
	 * @return false|array Returns false if not found, or the parameter array
	 */
    public function get_parameter($parameter) {
	    if (isset($this->decodedWebhook['result']['parameters'][$parameter])) {
		    return $this->decodedWebhook['result']['parameters'][$parameter];
	    }
		return false;
    }
	
	
	/**
	 * Ends the conversation by not expecting a response from the user
	 * Google expects this by default
	 */
    public function end_conversation() {
	    $this->expectUserResponse = false;
	}
	

	/**
	 * Keeps the mic open for the user to respond
	 * Mutually exclusive with $this->end_conversation() 
	 */
	public function keep_mic_open() {
		$this->expectUserResponse = true;
	}
    

} // End of Class Webhook
