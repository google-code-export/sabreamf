<?php

    /**
     * SabreAMF_AMF3_Wrapper 
     * 
     * @package SabreAMF
     * @subpackage AMF3
     * @version $Id$
     * @copyright 2006 Rooftop Solutions
     * @author Evert Pot <evert@collab.nl> 
     * @licence http://www.freebsd.org/copyright/license.html  BSD License (4 Clause) 
     */
    class SabreAMF_AMF3_Wrapper {

        /**
         * data 
         * 
         * @var mixed
         */
        private $data;

        /**
         * getData 
         * 
         * @return mixed 
         */
        public function getData() {

            return $data;

        }

        /**
         * setData 
         * 
         * @param mixed $data 
         * @return void
         */
        public function setData($data) {

            $this->data = $data;

        }
            

    }

?>    
