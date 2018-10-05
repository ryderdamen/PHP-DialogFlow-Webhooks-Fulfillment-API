<?php

if ( ! class_exists('ResponseBase') ) {

    /**
     * ResponseBase Class
     * A basic response object allowing shared
     * methods for other types of responses
     *
     * @since 2.0
     */
    class ResponseBase {

        
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
                    throw new Exception( get_class($this) . ' requires the property ' . $name );
                }
            }
        }


    } // End of Class ResponseBase

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