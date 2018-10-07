<?php
require('Response.php');

if ( ! class_exists('Carousel') ) {

    /**
     * Carousel Class
     * Generates a Carousel object with Carousel Items
     *
     * @since 2.0
     */
    class Carousel extends Response implements ResponseTemplate {
        
        const MAXIMUM_ITEMS = 10;
        const MINIMUM_ITEMS = 2;

        public $carouselItems;

        private $requiredProperties = ['carouselItems'];


        /**
         * Constructor: Adds arguments to properties
         *
         * @param array $args
         */
        public function __construct($args) {
            $this->add_args_to_class($args);

            // Ensure each carouselItem is of object CarouselItem
            foreach($args['carouselItems'] as $item) {
                if ( get_class($item) !== 'CarouselItem' ) {
                    $err = "Object in Carousel is not of type CarouselItem";
                    throw new Exception($err);
                }
            }
        }


        /**
         * Ensures the carousel meets requirements as outlined
         * by Google
         *
         * @return void
         */
        private function check_carousel_meets_requirements() {
            
            // Ensure minimums and maximums are met
            if ( count($this->carouselItems) < self::MAXIMUM_ITEMS) {
                $error_msg = 'You have ' . count($this->carouselItems) . ' items and  the ';
                $error_msg .= 'maximum is ' . self::MAXIMUM_ITEMS . '.';
                throw new Exception(error_msg);
            }
            if ( count($this->carouselItems) > self::MINIMUM_ITEMS) {
                $error_msg = 'You have ' . count($this->carouselItems) . ' items and  the ';
                $error_msg .= 'minimum is ' . self::MAXIMUM_ITEMS . '.';
                throw new Exception(error_msg);
            }

            // Check for duplicates in the title
            $titles = [];
            foreach($this->carouselItems as $item) {
                $titles[] = $item->title;
            }
            if (count($titles) !== count(array_unique($titles))) {
                throw new Exception('Titles in the carousel must be unique.');
            }
        }


        /**
         * For each CarouselItem passed in, renders the item
         *
         * @return array
         */
        private function render_carousel_items() {
            $rendered = [];
            foreach ($this->carouselItems as $item) {
                $rendered[] = $item->render();
            }
            return $rendered;
        }


        /**
         * Renders the Carousel into an array
         *
         * @return array
         */
        public function render() {
            $this->check_if_required_fields_set($this->requiredProperties);
            $this->check_carousel_meets_requirements();
            return [
                'carouselSelect' => [
                    'items' => $this->render_carousel_items()
                ],
            ];
        }

    }

}