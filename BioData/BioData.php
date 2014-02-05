<?php
/** File BioData Classes
*
* This contains all the necessary classes
* to generate BioData.
*
* @author Harry Mustoe-Playfair 
* @version 0.1
*/
namespace BioData;
include 'common.php';

/**
* An event, 
*/
 class Event extends GenericObject
 {
    public $title;
    public $description;
    
    function __construct($timestamp = null) {
        parent::__construct($timestamp);
    }
    
    public function toJSON(){
        $tempArray;
        foreach($this as $var => $value){
            if(is_object($value)){
                $tempArray[$var] = $value->toJSON();
            } else {
                if($value !== null) $tempArray[$var] = $value;
            }
        }
        return json_encode($tempArray);
    }
 }

/**
*
*/
class HeartRate extends GenericMeasurement
{
    public $allowedUnits = array("bpm");

    function __construct($timestamp = null) {
        parent::__construct($timestamp);
    }
}

/**
*
*/
class BloodSugar extends GenericObject
{
    
    function __construct($timestamp = null) {
        parent::__construct($timestamp);
    }
}

/**
*
*/
class BloodInsulin extends GenericObject
{
    function __construct($timestamp = null) {
        parent::__construct($timestamp);
    }
}