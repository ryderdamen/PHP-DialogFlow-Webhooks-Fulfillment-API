<?php
require('ResponseBase.php');

/**
 * Carousel Class
 * Generates a Carousel object with Carousel Items
 *
 * @since 2.0
 */
class CarouselItem extends ResponseBase implements ResponseTemplate {
    
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
    }


    /**
     * Ensures the carousel meets requirements as outlined
     * by Google
     *
     * @return void
     */
    private function check_carousel_meets_requirements() {
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
