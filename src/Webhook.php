<?php

/**
 * Webhook Class
 * Handles the workload for webhooks from DialogFlow, and provides an API for simple responses
 *
 * @since 2.0
 * @author Ryder Damen <dev@ryderdamen.com>
 */
class Webhook {


	/**
	 * Data from dialogflow
	 */
    public $decoded_webhook = null;
	public $google_user_id = false;
	public $project_id = false;


	/**
	 * Response variables
	 */
	private $has_responded = false;
    private $expect_user_response = null;
    private $conversation_token = "{\"state\":null,\"data\":{}}";
	private $rich_responses = [];
	private $simple_responses = [];


    /**
	 * Other
	 */
	private $input_stream = "php://input";
	private $is_test = false;


	/**
	 * Default constructor for the webhook class
	 * 
	 * @param array Arguments for webhook setup
	 * @return Webhook|null
	 */
	public function __construct($args) {
		if ( array_key_exists('projectId', $args) ) {
			$this->project_id = $args['projectId'];
		}
		// Define an input stream for testing
		if ( array_key_exists('inputStream', $args) ) {
			$this->input_stream = $args['inputStream'];
		}
		if ( array_key_exists('isTest', $args) ) {
			$this->is_test = true;
		}
		$request_type = $this->get_type_of_request();
		if ($request_type == 'webhook') {
			$this->process_webhook();
			return;
		}
		return;
	}


	/**
	 * Destructor for the webhook class (responds if haven't already)
	 * 
	 * @return null
	 */
	public function __destruct() {
		if ($this->is_test) {
			return;
		}
		$this->respond();
	}


	/**
	 * Determines the type of request this is
	 *
	 * @return [string] 'webhook' or 'other'
	 */
	private function get_type_of_request() {
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			try {
				$json = file_get_contents($this->input_stream);
				$action = json_decode($json, true);
				if ($action == '' or $action == null) {
					return 'other';
                }
				$this->decoded_webhook = $action;
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
		if ( isset($this->decoded_webhook['originalRequest']['data']['user']['userId']) ) {
			$this->google_user_id = $this->decoded_webhook['originalRequest']['data']['user']['userId'];
		}
    }


	/**
	 * Adds a rich component to the response (Card, Media Response, Carousel, etc)
	 *
	 * @param BasicCard|Carousel|MediaResponse|TableCard $response_item
	 * @return void
	 */
	private function add_rich_response($response_item) {
		try {
			$this->rich_responses[] = $response_item->render();
		} catch (Exception $e) {
			throw new Exception('Not a valid rich response object');
		}
	}


	/**
	 * Adds a simple response to the conversation
	 * 
	 * @param string
	 */
	private function add_simple_response($text) {
		$this->simple_responses[] = $text;
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
				'expect_user_response' => $this->expect_user_response,
				'rich_response' => [
					'items' => $this->rich_responses
				]
			)
		];
	}


	/**
	 * Asks the user for input (keeps the conversation open)
	 * 
	 */
	public function ask($response) {
		if ($this->expect_user_response === false) {
			throw new Exception('You cannot call ask() if you have already called tell()');
		}
		$this->expect_user_response = true;
		$this->add_response($response);
	}


	/** 
	 * Tells the user (closes the conversation)
	 * 
	 */
	public function tell($response) {
		if ($this->expect_user_response === true) {
			throw new Exception('You cannot call tell() if you have already called ask()');
		}
		$this->expect_user_response = false;
		$this->add_response($response);
	}


	/**
	 * Renders simple_responses text array into a string for fulfillment text.
	 * 
	 * @return string
	 */
	private function render_fulfillment_text() {
		return join(' ', $this->simple_responses);
	}


    /**
	 * Sends the HTTP application/json request back to DialogFlow
	 * 
	 * @return null
	 */
    private function respond() {
		if ($this->has_responded) return;
		$this->has_responded = true;
		$response = array(
			'fulfillmentText' => $this->render_fulfillment_text(),
			'payload' => $this->build_response_integrations(),		   
		);
		if ( ! headers_sent() ) {
			header("Content-type:application/json");
		}
		echo json_encode($response);
	}


    /**
	 * Returns the full decoded webhook array for the user
	 * 
	 * @return array
	 */
    public function get_decoded_webhook() {
        return $this->decoded_webhook;
    }


    /**
	 * Returns the raw input for the user (if it's from dialogflow: it's a json string)
	 * 
	 * @return string
	 */
    public function get_raw_input() {
	    return file_get_contents($this->input_stream);
    }


    /**
	 * Gets the intent name passed with the webhook
	 * 
	 * @return string - The name of the intent
	 */
    public function get_intent() {
	    return $this->decoded_webhook['result']['metadata']['intentName'];
    }


    /**
	 * Return the input language the user is using
	 * 
	 * @return string - The language code
	 */
    public function get_language() {
	    return $this->decoded_webhook['lang'];
    }


  	/**
	* Return the timestamp this request was executed at

	* @return int|string
    */
    public function get_timestamp($string=false) {
		if ($string !== false) {
			return $this->decoded_webhook['timestamp'];
		}
		return strtotime($this->decoded_webhook['timestamp']);
    }


    /**
	 * Returns the user's query (what they asked)
	 * 
	 * @return string
	 */
    public function get_query() {
	    return $this->decoded_webhook['result']['resolvedQuery'];
	}


	/**
	 * Returns the unique conversation ID for this conversation
	 * 
	 * @return string
	 */
	public function get_conversation_id() {
		return $this->decoded_webhook['originalRequest']['data']['conversation']['conversationId'];
	}


    /**
	 * Returns a full array of the parameters passed with the webhook
	 * 
	 * @return array
	 */
    public function get_parameters() {
	    return $this->decoded_webhook['result']['parameters'];
    }


    /**
	 * Returns a specific parameter, or false if no parameter exists
	 * 
	 * @param string parameter -- The parameter to retrieve
	 * @return false|array Returns false if not found, or the parameter array
	 */
    public function get_parameter($parameter) {
	    if (isset($this->decoded_webhook['result']['parameters'][$parameter])) {
		    return $this->decoded_webhook['result']['parameters'][$parameter];
	    }
		return false;
    }


} // End of Class Webhook
