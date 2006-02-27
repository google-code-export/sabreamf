<?php

//    require_once(dirname(__FILE__) . '/../Deserializer.php');

    /**
     * SabreAMF_Deserializer 
     * 
     * @package SabreAMF 
     * @version $Id$
     * @copyright 2006 Rooftop Solutions
     * @author Evert Pot <evert@collab.nl> 
     * @licence http://www.freebsd.org/copyright/license.html  BSD License (4 Clause) 
     */
    class SabreAMF_AMF0_Deserializer {

        /**
         * stream 
         * 
         * @var SabreAMF_InputStream
         */
        private $stream;
        /**
         * objectcount 
         * 
         * @var int
         */
        private $objectcount;
        /**
         * refList 
         * 
         * @var array 
         */
        private $refList;

        /**
         * __construct 
         * 
         * @param SabreAMF_InputStream $stream 
         * @return void
         */
        public function __construct(SabreAMF_InputStream $stream) {

            $this->stream = $stream;

        }

        /**
         * readAMFData 
         * 
         * @param mixed $settype 
         * @return mixed 
         */
        public function readAMFData($settype = null) {

           if (is_null($settype)) {
                $settype = $this->stream->readByte();
           }

           switch ($settype) {

                case SabreAMF_Const::AT_AMF0_NUMBER      : return $this->readDouble();
                case SabreAMF_Const::AT_AMF0_BOOL        : return $this->stream->readByte()==true;
                case SabreAMF_Const::AT_AMF0_STRING      : return $this->readString();
                case SabreAMF_Const::AT_AMF0_OBJECT      : return $this->readObject();
                case SabreAMF_Const::AT_AMF0_NULL        : return null; 
                case SabreAMF_Const::AT_AMF0_UNDEFINED   : return null;
                //case self::AT_REFERENCE   : return $this->readReference();
                case SabreAMF_Const::AT_AMF0_MIXEDARRAY  : return $this->readMixedArray();
                case SabreAMF_Const::AT_AMF0_ARRAY       : return $this->readArray();
                case SabreAMF_Const::AT_AMF0_DATE        : return $this->readDate();
                case SabreAMF_Const::AT_AMF0_LONGSTRING  : return $this->stream->readLongString();
                case SabreAMF_Const::AT_AMF0_UNSUPPORTED : return null;
                case SabreAMF_Const::AT_AMF0_XML         : return $this->stream->readLongString();
                case SabreAMF_Const::AT_AMF0_TYPEDOBJECT : return $this->readTypedObject();
                default                   :  throw new Exception('Unsupported type: 0x' . strtoupper(str_pad(dechex($settype),2,0,STR_PAD_LEFT))); return false;
 
           }

        }

        /**
         * readObject 
         * 
         * @return object 
         */
        public function readObject() {

            $object = array();
            while (true) {
                $key = $this->readString();
                $vartype = $this->stream->readByte();
                if ($vartype==SabreAMF_Const::AT_AMF0_OBJECTTERM) break;
                $object[$key] = $this->readAmfData($vartype);
            }
            return $object;    

        }

        /**
         * readArray 
         * 
         * @return array 
         */
        public function readArray() {

            $length = $this->stream->readLong();
            $arr = array();
            while($length--) $arr[] = $this->readAMFData();
            return $arr;

        }

        /**
         * readMixedArray 
         * 
         * @return array 
         */
        public function readMixedArray() {

            $highestIndex = $this->stream->readLong();
            return $this->readObject();

        }

        /**
         * readDate 
         * 
         * @return int 
         */
        public function readDate() {

            $timestamp = floor($this->readDouble() / 1000);
            $timezoneOffset = $this->readInt();
            if ($timezoneOffset > 720) $timezoneOffset = ((65536 - $timezoneOffset));
            $timezoneOffset=($timezoneOffset * 60) - date('Z');
            return $timestamp + ($timezoneOffset);


        }

        /**
         * readTypedObject 
         * 
         * @return object
         */
        public function readTypedObject() {

            $classname = $this->readString();
            return $this->readObject();

        }

         /**
         * readDouble 
         * 
         * @return float 
         */
        public function readDouble() {

            $double = $this->stream->readBuffer(8);

            $testEndian = unpack("C*",pack("S*",256));
            $bigEndian = !$testEndian[1]==1;
                        
            if ($bigEndian) $double = strrev($double);
            $double = unpack("d",$double);
            return $double[1];
        }
        
         /**
         * readInt 
         * 
         * @return int 
         */
        public function readInt() {

            $block = $this->stream->readBuffer(2);
            $int = unpack("n",$block);
            return $int[1];

        }

         /**
         * readString 
         * 
         * @return string 
         */
        public function readString() {

            $strLen = $this->readInt();
            return $this->stream->readBuffer($strLen);

        }

        /**
         * readLongString 
         * 
         * @return string 
         */
        public function readLongString() {

            $strLen = $this->readLong();
            return $this->stream->readBuffer($strLen);

        }


        /**
         * readLong 
         * 
         * @return int 
         */
        public function readLong() {

            $block = $this->stream->readBuffer(4);
            $long = unpack("N",$block);
            return $long[1];
        }


   }

?>
