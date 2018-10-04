<?php

require('ResponseBase.php');

/**
 * BasicCard Class
 * Generates a basic card object
 *
 * @since 2.0
 */
class BasicCard extends ResponseBase implements ResponseTemplate {
    
    public $title;
    public $subtitle;
    public $formattedText;
    public $imageObject;
    public $buttons;
    public $imageDisplayOptions;

    private $requiredProperties = [
        'title', 'subtitle', 'formattedText',
        'imageObject', 'buttons',
    ];

    public function __construct($args) {
        $this->add_args_to_class($args);
    }

    public function render() {
        $this->check_if_required_fields_set($this->requiredProperties);
        return [
            'basicCard' => $this->render_all_class_props()
        ];
    }

}
