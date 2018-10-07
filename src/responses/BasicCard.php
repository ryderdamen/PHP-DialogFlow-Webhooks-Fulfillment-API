<?php

require('ResponseBase.php');

if ( ! class_exists('BasicCard') ) {

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
        public $buttons = [];
        public $imageDisplayOptions = "DEFAULT";

        private $requiredProperties = [
            'title', 'subtitle', 'formattedText',
            'imageObject', 'buttons',
        ];

        public function __construct($args) {
            $this->add_args_to_class($args);

            // Append Buttons
            if (array_key_exists('buttons', $args)) {
                // Check to make sure it's an array, or just one object
                if (is_array($args['buttons'])) {
                    foreach($args['buttons'] as $button) {
                        $this->buttons[] = $button;
                    }
                } else { // Single button object
                    if (is_object($args['buttons'])) {
                        $this->buttons[] = $args['buttons'];
                    }
                }
            }
        }

        /**
         * For each of the $this->buttons, calls the render function
         * on each object and returns the dictionary
         *
         * @return array
         */
        private function render_buttons() {
            $buttons = [];
            if ( ! empty($this->buttons) ) {
                foreach ($this->buttons as $button) {
                    $buttons[] = $button->render();
                }
            }
            return [$buttons];
        }


        public function render() {
            $this->check_if_required_fields_set($this->requiredProperties);
            return [
                'basicCard' => [
                    'title' => $this->title,
                    'subtitle' => $this->subtitle,
                    'formattedText' => $this->formattedText,
                    'image' => $this->imageObject ? $this->imageObject->render() : [],
                    'buttons' => $this->render_buttons(),
                    'imageDisplayOptions' => $this->imageDisplayOptions,
                ]
            ];
        }

    }

}