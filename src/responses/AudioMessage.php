<?php

require('ResponseBase.php');

if ( ! class_exists('AudioMessage') ) {

    /**
     * AudioMessage Class
     * Generates an AudioMessage object
     *
     * @since 2.0
     */
    class AudioMessage extends ResponseBase implements ResponseTemplate {
        
        public $url;
        public $displayText;

        private $requiredProperties = [
            'url', 'displayText',
        ];


        /**
         * Builds SSML from the list of audio URLs
         *
         * @since 2.0.0
         * @return string
         */
        private function build_ssml() {
            $ssml = ' <speak> ';
            if ( is_array($this->url) ) {
                foreach($this->url as $u) {
                    $ssml .= "<audio src = '" . $u . "' /> ";
                }
            }
            else {
                $ssml .= "<audio src = '" . $this->url . "' /> ";
            }
            $ssml .= '</speak>';
            return $ssml;
        }


        public function __construct($args) {
            $this->add_args_to_class($args);
        }


        public function render() {
            $this->check_if_required_fields_set($this->requiredProperties);
            return [
                'simpleResponse' => [
                    'ssml' => $this->build_ssml(),
                    'displayText' => $this->displayText,
                ]
            ];
        }

    }

}