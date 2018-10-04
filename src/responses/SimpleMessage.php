<?php

require('ResponseBase.php');

/**
 * SimpleMessage Class
 * Generates a simple message object
 *
 * @since 2.0
 */
class SimpleMessage extends ResponseBase implements ResponseTemplate {
    
    public $speech;
    public $displayText;

    private $requiredProperties = [
        'speech', 'displayText',
    ];

    public function __construct($args) {
        $this->add_args_to_class($args);
    }

    public function render() {
        $this->check_if_required_fields_set($this->requiredProperties);
        return [
            'simpleResponse' => $this->render_all_class_props()
        ];
    }

}
