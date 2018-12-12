<?php

require(__DIR__ . '/../Response.php');

if ( ! class_exists('Image') ) {

    /**
     * Image Class
     * Generates a Image response object
     *
     * @since 2.0
     */
    class Image extends Response implements ResponseTemplate {
        
        public $imageUrl;
        public $platform;

        private $requiredProperties = [
            'imageUrl', 'platform',
        ];

        public function __construct($args) {
            $this->add_args_to_class($args);
        }

        public function render() {
            $this->check_if_required_fields_set($this->requiredProperties);
            return [
                'imageUrl' => $this->imageUrl,
                'platform' => $this->platform,
                'type' => 3,
            ];
        }

    }

}