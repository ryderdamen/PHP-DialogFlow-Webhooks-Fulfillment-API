<?php

require(__DIR__ . '/../Response.php');

if ( ! class_exists('Text') ) {

    /**
     * Text Class
     * Generates a Text response object
     *
     * @since 2.0
     */
    class Text extends Response implements ResponseTemplate {
        
        public $speech;
        public $platform;

        private $requiredProperties = [
            'speech', 'platform',
        ];

        public function __construct($args) {
            $this->add_args_to_class($args);
        }

        public function render() {
            $this->check_if_required_fields_set($this->requiredProperties);
            return [
                'speech' => $this->speech,
                'platform' => $this->platform,
                'type' => 0,
            ];
        }

    }

}