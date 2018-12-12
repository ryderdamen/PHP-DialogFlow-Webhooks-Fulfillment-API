<?php

require(__DIR__ . '/../Response.php');

if ( ! class_exists('Card') ) {

    /**
     * Card Class
     * Generates a Card response object
     *
     * @since 2.0
     */
    class Card extends Response implements ResponseTemplate {
        
        public $imageUrl;
        public $buttons;
        public $title;
        public $subtitle;
        public $platform;

        private $requiredProperties = [
            'imageUrl', 'platform',
        ];

        public function __construct($args) {
            $this->add_args_to_class($args);
        }

        /**
         * @param string $text - Button text
         * @param string $postback - URL or postback text
         */
        public function add_button($text, $postback) {
            $this->buttons[] = [
                'text' => $text,
                'postback' => $postback,
            ];
        }

        public function render() {
            $this->check_if_required_fields_set($this->requiredProperties);
            return [
                'title' => $this->title,
                'subtitle' => $this->subtitle,
                'buttons' => $this->buttons,
                'imageUrl' => $this->imageUrl,
                'platform' => $this->platform,
                'type' => 1,
            ];
        }

    }

}