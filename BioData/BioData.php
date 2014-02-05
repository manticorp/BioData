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
}

/**
*
*/
class BloodDiastolic extends GenericMeasurement
{
    public $allowedUnits = array(
        "mmHg" // mm of mercury
    );
}

/**
*
*/
class BloodSystolic extends GenericMeasurement
{
    public $allowedUnits = array(
        "mmHg" // mm of mercury
    );
}

/**
*
*/
class ABOBloodType extends GenericMeasurement
{
    public $allowedValues = array(
        "O+",
        "A+",
        "B+",
        "AB+",
        "O-",
        "A-",
        "B-",
        "AB-"
    );
}

/**
* @TODO Not sure at all if this is correct
*/
class RHBloodType extends GenericMeasurement
{
    public $allowedValues = array(
        "DCe",
        "DcE",
        "Dce",
        "DCE",
        "dCe",
        "dcE",
        "dCE",
        "dce"
    );
}

/**
*
*/
class DateOfBirth extends GenericMeasurement
{
    public function valueTest(Measurement $measurement){
        if(!($measurement->getValue() instanceof \DateTime)){
            throw new Exception("Date of birth must be of type DateTime. Note, only the date will be taken.");
        }
    }    
}

/**
*
*/
class Height extends GenericMeasurement
{
    public $allowedUnits = array(
        "m", // metres
        "ft", // feet
        "in" // inches
    ); 
    
    public function valueTest(Measurement $measurement){
        if($measurement->getValue() < 0){
            throw new Exception("Value for " . get_class($this) . " cannot be negative.");
        }
    }
}

/**
*
*/
class Weight extends GenericMeasurement
{
    public $allowedUnits = array(
        "kg", // kilograms
        "st", // stone
        "lbs" // pounds
    ); 
    
    public function valueTest(Measurement $measurement){
        if($measurement->getValue() < 0){
            throw new Exception("Value for " . get_class($this) . " cannot be negative.");
        }
    }
}

/**
*
*/
class Mood extends GenericMeasurement
{
    public $allowedUnits = array(
        "%",       // percent, from 0 to 1
        "percent"  // percent alt
    ); 
    
    public function valueTest(Measurement $measurement){
        if($measurement->getValue() > 1 || $measurement->getValue() < 0){
            throw new Exception("Value for " . get_class($this) . " can only be between 0 and 1.");
        }
    }
}

/**
*
*/
class Happiness extends Mood
{    
}

/**
*
*/
class Arousal extends Mood
{
}

/**
*
*/
class Tiredness extends Mood
{
}

/**
*
*/
class Sleep extends GenericMeasurement
{
    public $valueCanBeNull = true;

    public $allowedUnits = array(
        "%",       // percent, from 0 to 1
        "percent"  // percent alt
    );
    
    public function valueTest(Measurement $measurement){
        if($measurement->getValue() > 1 || $measurement->getValue() < 0){
            throw new Exception("Value for " . get_class($this) . " can only be between 0 and 1.");
        }
    }
}

/**
*
*/
class Location extends GenericMeasurement
{
    
}