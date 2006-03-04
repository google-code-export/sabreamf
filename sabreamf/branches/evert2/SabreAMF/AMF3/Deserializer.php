<?php

    /**
     * SabreAMF_AMF3_Deserializer 
     * 
     * @package SabreAMF
     * @subpackage AMF3
     * @version $Id$
     * @copyright 2006 Rooftop Solutions
     * @author Evert Pot <evert@collab.nl> 
     * @licence http://www.freebsd.org/copyright/license.html  BSD License (4 Clause) 
     */
    class SabreAMF_AMF3_Deserializer {

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
         * storedStrings 
         * 
         * @var array 
         */
        private $storedStrings = array();

        /**
         * storedObjects 
         * 
         * @var array 
         */
        private $storedObjects = array();


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

                case SabreAMF_Const::AT_AMF3_NULL       : return null;
                case SabreAMF_Const::AT_AMF3_BOOL_FALSE : return false;
                case SabreAMF_Const::AT_AMF3_BOOL_TRUE  : return true;
                case SabreAMF_Const::AT_AMF3_INTEGER    : return $this->readInt();
                case SabreAMF_Const::AT_AMF3_NUMBER     : return $this->stream->readDouble();
                case SabreAMF_Const::AT_AMF3_STRING     : return $this->readString();
                case SabreAMF_Const::AT_AMF3_ARRAY      : return $this->readArray();
                case SabreAMF_Const::AT_AMF3_OBJECT     : return $this->readObject();
                default                   :  throw new Exception('Unsupported type: 0x' . strtoupper(str_pad(dechex($settype),2,0,STR_PAD_LEFT))); return false;


           }

        }


        /**
         * readObject 
         * 
         * @return object 
         */
        public function readObject() {

            $objref = $this->readInt();

            // Check if object is stored
            
            if (($objref & 0x01) == 0) {
                 $objref = $objref >> 1;
                 if ($objref>=count($this->storedObjects)) {
                    throw new Exception('Undefined object reference: ' . $objref);
                    return false;
                }
                return $this->storedObjects[$objref]; 
            } else {
                $classref = $objref >> 1;
        
                // Check if class is stored
                
                if (($classref & 0x01) == 0) {
                    die('Stored class');
                } else {
                    $classname = $this->readString();
                }

                $objType = ($classref>>1) & 0x03;

               if (($objType & 2)==2) {
                    $obj = array();
                    do {
                        $propertyName = $this->readString();
                        if ($propertyName!=='') {
                            $obj[$propertyName] = $this->readAMFData();
                        }
                    } while($propertyName !=='');
                } else {
                     $propertyCount = $classref >> 3;
                
                     $obj = array();
                     $propertyNames = array();
                     if (($objType & 1)==1) {
                         $propertyNames[] = 'source';
                     } else {
                        for($i=0;$i<$propertyCount;$i++) {
                            $propertyName = $this->readString();
                             $propertyNames[] = $propertyName;
                        }
                     }
                     foreach($propertyNames as $pn) {
                         $obj[$pn] = $this->readAMFData();
                     }
                }
                $this->storedObjects[] = $obj;
                return (object)$obj;
                
            }

        }

        private function readArray() {

            $arrRef = $this->readInt();
            if (($arrRef & 0x01)==0) {
                 $arrRef = $arrRef >> 1;
                 if ($arrRef>=count($this->storedObjects)) {
                    throw new Exception('Undefined array reference: ' . $arrRef);
                    return false;
                }
                return $this->storedObjects[$arrRef]; 
            }
            $arrLen = $arrRef >> 1;
            
            $data = array();

            for($i=0;$i<$arrLen;$i++) {
                $data[] = $this->readAMFData();
            }

            $this->storedObjects[] = $data;
            return $data;

        }
        

        /**
         * readString 
         * 
         * @return string 
         */
        private function readString() {

            $strref = $this->readInt();

            if (($strref & 0x01) == 0) {
                $strref = $strref >> 1;
                if ($strref>=count($this->storedStrings)) {
                    throw new Exception('Undefined string reference: ' . $strref);
                    return false;
                }
                return $this->storedStrings[$strref >> 1];
            } else {
                $strlen = $strref >> 1; 
                $str = $this->stream->readBuffer($strlen);
                $this->storedStrings[] = $str;
                return $str;
            }

        }


        private function readInt() {

            $count = 1;
            $int = 0;

            $byte = $this->stream->readByte();

            while($byte >> 7 == 1 && $count < 4) {
                $int = $int | (($byte & 0x7F) << ($count*7));
                $byte = $this->stream->readByte();
                $count++;
            }
            $int = $int | $byte;

            if (($int >> 27)==1) {
                $int = $int | 0xF0000000;
            }

            return $int;
         
        }


    }

?>
