<?php

require('Response.php');

if ( ! class_exists('Image') ) {

    /**
     * Image Class
     * Generates an image object
     *
     * @since 2.0
     */
    class Image extends Response implements ResponseTemplate {
        
        public $url;
        public $accessibilityText;
        public $height;
        public $width;

        private $requiredProperties = [
            'url', 'accessibilityText',
        ];

        public function __construct($args) {
            $this->add_args_to_class($args);
        }

        public function render() {
            $this->check_if_required_fields_set($this->requiredProperties);
            return $this->render_all_class_props();
        }

    }

}