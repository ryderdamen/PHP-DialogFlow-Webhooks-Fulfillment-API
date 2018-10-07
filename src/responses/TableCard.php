<?php
require('ResponseBase.php');


if ( ! class_exists('Table') ) {

    /**
     * Table Class
     * Generates a Table object (tablecard)
     *
     * @since 2.0
     */
    class Table extends ResponseBase implements ResponseTemplate {
        
        public $title;
        public $subtitle;
        private $headers;
        private $tableRows;
        public $button;
        public $horizontalAlignment = 'CENTER';
        
        private $requiredProperties = ['tableRows'];


        /**
         * Constructor: Adds arguments to properties
         *
         * @param array $args
         */
        public function __construct($args) {
            $this->add_args_to_class($args);
        }


        /**
         * Throws an exception if requirements are not met
         *
         * @return void
         */
        private function confirm_requirements_met() {
            
            // Ensure the button is of proper class type
            if ($this->button != null) {
                if( get_class($this->button) !== 'Button' ) {
                    throw new Exception('Non-button object provided.');
                }
            }

            // Check tableRows
            if (is_array($this->tableRows)) {
                if( get_class($this->tableRows[0]) !== 'TableRow' ) {
                    throw new Exception('Non table row object provided.');
                }
            } else {
                if( get_class($this->tableRows) !== 'TableRow' ) {
                    throw new Exception('Non table row object provided.');
                }
            }

            // Check headers match columns
            if (count($this->headers) != count($this->tableRows[0]['cells'])) {
                throw new Exception('Table header count does not match row count.');
            }
        }


        /**
         * Builds the column properties array
         *
         * @return void
         */
        private function build_column_properties() {
            $rendered = [];
            $i = 0;


            // Number of cells in first column is the iterator
            foreach($this->tableRows[0]['cells'] as $cell) {
                
                $partial = [];
                $alignment = '';

                // Map Horizontal Alignments
                if ( ! is_array($this->horizontalAlignment) ) {
                    // Account for not set, or global value
                    if ($this->horizontalAlignment === null) {
                        $partial['horizontalAlignment'] = 'CENTER';
                    } else {
                        $partial['horizontalAlignment'] = $this->horizontalAlignment;
                    }
                } else if ( is_array($this->horizontalAlignment) ) {
                    // Attempt to map horizontal alignment
                    $partial['horizontalAlignment'] = $this->horizontalAlignment[$i];
                }

                // Map Headers
                if ($this->headers !== null) {
                    $partial['header'] = $this->headers[$i];
                }

                $rendered[] = $partial;
                $i++;
            } 
        }


        /**
         * Appends a new row to the table
         *
         * @param array $cellArray - Array of values representing each cell
         * @param boolean $dividerAfter - default true
         * @return void
         */
        public function add_row($cellArray, $dividerAfter = true) {
            $rendered = [];
            if ( is_array($cellArray) ) {
                foreach($cellArray as $text) {
                    $rendered[] = [
                        'text' => $text,
                    ];
                }
            } else {
                $rendered[] = [
                    'text' => $cellArray,
                ];
            }
            $this->tableRows[] = [
                'cells' => $rendered,
                'dividerAfter' => $dividerAfter,
            ];
        }


        /**
         * Sets the header name of each column
         *
         * @param [type] $headerArray
         * @return void
         */
        public function set_headers($headerArray) {
            $this->headers[] = $headerArray;
        }

        


        /**
         * Sets horizontal alignment for columns, each item in array represents
         * alignment for a specific column
         *
         * @param array|string $alignmentArray ex: ['CENTER', 'LEADING', 'TRAILING']
         * @return void
         */
        public function set_horizontal_alignment($alignmentArray) {
            if ( is_array($alignmentArray) ) {
                // Array, try to match to headers
                // TODO build out
                $this->horizontalAlignment = $alignmentArray;
            } else {
                $this->horizontalAlignment = $alignmentArray;
            }
        }


        /**
         * Renders the CarouselItem into an array
         *
         * @return array
         */
        public function render() {
            $this->check_if_required_fields_set($this->requiredProperties);
            $this->confirm_requirements_met();
            return [
                'tableCard' => [
                    'title' => $this->title,
                    'subtitle' => $this->subtitle,
                    'image' => $this->image->render(),
                    'rows' => $this->render_multiple_objects(
                        $this->tableRows
                    ),
                    'columnProperties' => $this->build_column_properties(),
                    'button' => $this->render_multiple_objects(
                        $this->button
                    ),
                ]
            ];
        }

    }

}