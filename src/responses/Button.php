<?php

require('ResponseBase.php');

/**
 * Button Class
 * Generates a Button object
 *
 * @since 2.0
 */
class Button extends ResponseBase implements ResponseTemplate {
    
    public $title;
    public $url;

    private $requiredProperties = [
        'title', 'url',
    ];

    /**
     * Constructor for the button object
     *
     * @param array $arguments for the object
     */
    public function __construct($args) {
        $this->add_args_to_class($args);
    }


    /**
     * Renders the button into an objective array
     *
     * @return array
     */
    public function render() {
        $this->check_if_required_fields_set($this->requiredProperties);
        return [
            'title' => $this->title,
            'openUrlAction' => [
                'url' => $this->url,
            ],
        ];
    }

}
