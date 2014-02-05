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
    
    function __construct() {
        call_user_func_array("parent::__construct", func_get_args());
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
    public $allowedUnits = array(
        "bpm", // beats per minute
        "b/m"  // beats per minute (alternative form)
    );
    
    function __construct() {
        call_user_func_array("parent::__construct", func_get_args());
    }
}

/**
*
*/
class BloodSugar extends GenericMeasurement
{
    public $allowedUnits = array(
        "mmol/L", // millimols per litre
        "mg/dL"   // milligrams per decilitre
    );
    
    function __construct() {
        call_user_func_array("parent::__construct", func_get_args());
    }
}

/**
* @TODO verify the units...not quite sure about these  
*/
class BloodInsulin extends GenericMeasurement
{
    public $allowedUnits = array(
        "µIU/mL",  
        "uIU/mL",  // millimols per litre
        "pmol/L"   // milligrams per decilitre
    );
    
    function __construct() {
        call_user_func_array("parent::__construct", func_get_args());
    }
}

/**
*
*/
class BloodDiastolic extends GenericMeasurement
{
    public $allowedUnits = array(
        "mmHg" // mm of mercury
    );
    
    function __construct() {
        call_user_func_array("parent::__construct", func_get_args());
    }
}

/**
*
*/
class BloodSystolic extends GenericMeasurement
{
    public $allowedUnits = array(
        "mmHg" // mm of mercury
    );
    
    function __construct() {
        call_user_func_array("parent::__construct", func_get_args());
    }
}

/**
*
*/
class BloodType extends GenericMeasurement
{
    
    function __construct() {
        call_user_func_array("parent::__construct", func_get_args());
    }
}

/**
*
*/
class DateOfBirth extends GenericMeasurement
{
    public function checkMeasurementUnit(Measurement $measurement){
        try{
            Time::checkTimeStamp($measurement->getValue());
        } catch(Exception $e){
            throw new Exception("Date of Birth must be a timestamp");
        }
    }    
    
    function __construct() {
        call_user_func_array("parent::__construct", func_get_args());
    }
}