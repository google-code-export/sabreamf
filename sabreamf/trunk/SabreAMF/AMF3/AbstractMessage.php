<?php

    /**
     * SabreAMF_AMF3_AbstractMessage 
     * 
     * @package 
     * @version $Id$
     * @copyright 2006 Rooftop Solutions
     * @author Evert Pot <evert@rooftopsolutions.nl> 
     * @licence http://www.freebsd.org/copyright/license.html  BSD License (4 Clause) 
     */
    abstract class SabreAMF_AMF3_AbstractMessage {

        /**
         * The body of the message 
         * 
         * @var mixed
         */
        public $body;
        
        /**
         * Unique client ID 
         * 
         * @var string 
         */
        public $clientId;
       
        /**
         * destination 
         * 
         * @var string 
         */
        public $destination;
      
        /**
         * Message headers 
         * 
         * @var array 
         */
        public $headers;
      
        /**
         * Unique message ID 
         * 
         * @var string 
         */
        public $messageId;
        
        /**
         * timeToLive 
         * 
         * @var int 
         */
        public $timeToLive;

        /**
         * timestamp 
         * 
         * @var int 
         */
        public $timestamp;

    }

?>
