<?php
require('ResponseBase.php');

/**
 * CarouselItem Class
 * Generates a CarouselItem object for populating the Carousel
 *
 * @since 2.0
 */
class CarouselItem extends ResponseBase implements ResponseTemplate {
    
    public $title;
    public $description;
    public $imageObject;
    public $dialogKey;
    public $dialogSynonyms;

    private $requiredProperties = ['title'];

    /**
     * Constructor: Adds arguments to properties
     *
     * @param array $args
     */
    public function __construct($args) {
        $this->add_args_to_class($args);
    }


    /**
     * Retrieves the URL from the provided image object
     * or returns null if there is no image object
     *
     * @return string|null
     */
    private function get_image_url() {
        if ($this->imageObject != null) {
            return $this->imageObject->url;
        }
        return null;
    }


    /**
     * Retrieves the alt text from the provided image object
     * or returns null if there is no image object
     *
     * @return string|null
     */
    private function get_image_alt() {
        if ($this->imageObject != null) {
            return $this->imageObject->accessibilityText;
        }
        return null;
    }


    /**
     * Renders the CarouselItem into an array
     *
     * @return array
     */
    public function render() {
        $this->check_if_required_fields_set($this->requiredProperties);
        return [
		    'info' => [
			    'key' => $this->dialogKey,
			    'synonyms' => $this->dialogSynonyms,
            ],
		    'title' => $this->title,
		    'description' => $this->description,
		    'image' => [
			    'imageUri' => $this->get_image_url(),
			    'accessibilityText' => $this->get_image_alt(),
            ],
        ];
    }

}
