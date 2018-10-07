<?php

if ( ! class_exists('Response') ) {

    /**
     * Response Class
     * A basic response object allowing shared
     * methods for other types of responses
     *
     * @since 2.0
     */
    class Response {

        
        /**
         * Adds all arguments to the class properties
         * if the property exists
         *
         * @since 2.0.0
         * @param [KV array] $args
         * @return void
         */
        protected function add_args_to_class($args) {
            foreach($args as $key => $value) {
                if ( property_exists($this, $key) ) {
                    $this->$key = $value;
                }
            }
        }


        /**
         * Provides a default render function for implementing render()
         * 
         * @since 2.0.0
         * @return array
         */
        protected function render_all_class_props() {
            $renderDictionary = [];
            foreach( get_object_vars($this) as $name => $property ) {
                if  (is_object($property) ) {
                    try {
                        $renderDictionary[$name] = $property->render();
                    } catch ( Exception $e ) {
                        // Do nothing, this object shouldn't be here
                    }
                } else { // Object is of another type
                    $renderDictionary[$name] = $property;
                }
            }
            return $renderDictionary;
        }


        protected function check_if_required_fields_set($required) {
            foreach( get_object_vars($this) as $name => $property ) {
                if ($property === null && in_array($name, $required)) {
                    throw new PropertyIsRequiredException( get_class($this) . ' requires the property ' . $name );
                }
            }
        }


        /**
         * Calls the render function on one or multiple objects
         *
         * @param array $objects
         * @return array - rendered array of objects
         */
        protected function render_multiple_objects($objects) {
            if (is_array($objects)) {
                $rendered = [];
                foreach ($objects as $obj) {
                    $rendered[] = $obj-render();
                }
            } else {
                return [ $objects->render() ];
            }
        }


    } // End of Class Response

}

if ( ! interface_exists('ResponseTemplate') ) {

    /**
     * ResponseTemplate interface
     * Implemtna
     */
    interface ResponseTemplate {

        /** Abstract Render Function
         *  Renders all properties of the object into dictionary
         */
        public function render();

    }

}

if ( ! class_exists('PropertyIsRequiredException') ) {

    /**
     * Custom exception for displaying when a property is
     * required
     */
    class PropertyIsRequiredException extends Exception {

        public function __toString() {
            return __CLASS__ . ": [{$this->code}]: {$this->message} - Please add it to the args array.\n";
        }
        
    }

}