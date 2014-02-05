<?php
/** File BioData Common Classes
*
* This contains all the necessary classes
* to generate BioData classes.
*
* @author Harry Mustoe-Playfair 
* @version 0.1
*/
namespace BioData;

 
/**
* Define a custom exception class
*
* Error Codes:
*
*  6XX : Variable Error
*      60X : Measurement Variable Error
*          600 : Object passed not of type Measurement
*          601 : Variable passed not object
*  7XX : Unit Error
*      70X : 
*          700 : Unit type not allowed
*          701 : Unit type not specified
*/
class Exception extends \Exception
{
    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}

/**
* A generic class for recording times
*
* Every piece of biodata has an associated
* timestamp. This is the time when the piece
* of biodata was made. Generally, it will also
* have a time when the biodata was actually
* recorded, this is the $start. Sometimes,
* biodata will also have a finish time, this
* is recorded in $finish.
*
* For example, a piece of sleep biodata, Sleep,
* will generally have a start and finish time as well
* as a time the biodata was actually created:
*
* Time Biodata Created: $timestamp
* Time Went To Sleep:   $start
* Time Woke Up:         $finish
*
* @abstract
*  
*/
class Time
{
    /**
    * Just the classname
    *
    * @var string
    */
    public $type;
    
    /**
    * The timestamp of when the data was created.
    *
    * @var string
    */
    public $timestamp;
    
    /**
    * A timestamp of when the biodata was
    * actually recorded/started recording.
    *
    * @var string
    */
    public $start;

    /**
    * A timestamp of when the biodata was
    * finished recording, e.g. at the end 
    * of a sleep session, as described in
    * the class description.
    *
    * @var string
    */
    public $finish;
    
    /**
    * Constructor function, sets the timestamp
    * if none given and sets the type.
    *
    * @param string $timestamp The timestamp of when the data was created (optional)
    */
    public function __construct($timestamp = null) {
        $timestamp = ($timestamp === null)? $this->getCurrentTimestamp() : $timestamp;
        $this->type = get_class($this);
        $this->__set("timestamp", $timestamp);
    }
    
    /**
    * Magic get method.
    *
    * @param string $property The property to get
    */
    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        } else {
            throw new Exception("Undefined variable: " + $property);
        }
    }
    
    /**
    * Magic set method. Since every property
    * of this class must be a timestamp, it also
    * checks if they are valid timestamps.
    *
    * @param string $property The property to set
    * @param mixed $value The value to give $property
    */
    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->checkTimeStamp($value); // checks that it's a valid timestamp
            $this->$property = $value;
        } else {
            throw new Exception("Undefined variable: " + $property);
        }
        return $this;
    }
    
    /**
    * Magic call method. This will set a variable
    * if a function is called such as:
    *
    * $this->setVar("value")
    *
    * It will set the property $this->var to "value"
    *
    * Similarly:
    *
    * $this->getVar()
    *
    * will return $this->var. Otherwise, this function
    * throws an exception.
    *
    * @param string $name The name of the function called.
    * @param array $arguments An array of arguments given.
    */
    public function __call($name, $arguments){
        $ftl = substr($name, 0, 3) ;
        $var = strtolower(substr($name, 3, 1)) . substr($name, 4);
        if($ftl === "set"){
            $var = strtolower(substr($name, 3, 1)) . substr($name, 4);
            $this->$var = $arguments[0];
        } else if ($ftl === "get"){
            return $this->$var;
        } else {
            throw new Exception("Call to undefined method " . $this->type . "::" . $name . "()");
        }
    }
    
    /**
    * Checks if $timestamp is a valid timestamp
    * 
    * @param int|string $timestamp A timestamp (optional)
    */
    public static function checkTimeStamp($timestamp = null) {
        $timestamp = ($timestamp === null)? $this->timestamp : $timestamp;
        $timestamp = intval($timestamp);
        if(!(( (int) $timestamp === $timestamp) 
        && ($timestamp <= PHP_INT_MAX)
        && ($timestamp >= ~PHP_INT_MAX))){
            throw new Exception("Invalid Timestamp.", 621);
        } 
    }
    
    /**
    * Returns the current timestamp.
    */
    public static function getCurrentTimestamp(){
        $date = new \DateTime();
        return $date->getTimestamp();
    }
    
    /**
    * Returns the class JSON encoded
    */
    public function toJSON(){
        // return json_encode($this->toArray());
        return json_encode($this);
    }
    
    /**
    * Converts the class properties to an array
    * and returns the array.
    */
    public function toArray(){
        $tempArray;
        foreach($this as $var => $value){
            if(is_object($value)){
                $tempArray[$var] = $value->toArray();
            } else {
                if($value !== null) $tempArray[$var] = $value;
            }
        }
        return $tempArray;
    }
}

/**
* A generic biodata object, containing the
* properties required by all biodata objects.
*
* This class has all the required properties
* that each biodata object *must* have. The 
* required properties are:
*
*  - A type
*  - A Time object
*
* Every biodata object MUST inherit this object.
* It also sets a bunch of methods that are common
* to every biodata object.
*
* @abstract
*/
class GenericObject
{
    public $type;
    public $time;
    
    public function __construct($timestamp = null) {
        $this->setTime(new Time($timestamp));
        $this->type = get_class($this);
    }
    
    
    /**
    * Magic get method.
    *
    * @param string $property The property to get
    */
    public function __get($property) {
        if (property_exists($this, $property)) {
          return $this->$property;
        } else {
            throw new Exception("Tying to get undefined property: " . $property);
        }
    }

    /**
    * Magic set method.
    *
    * @param string $property The property to set
    * @param mixed $value The value to give $property
    */
    public function __set($property, $value) {
        if (property_exists($this, $property)) {
          $this->$property = $value;
        } else {
            throw new Exception("Tying to set undefined property: " . $property);
        }
        return $this;
    }
    
    /**
    * Magic call method. This will set a variable
    * if a function is called such as:
    *
    * $this->setVar("value");
    *
    * It will set the property $this->var to "value"
    *
    * Similarly:
    *
    * $this->getVar();
    *
    * will return $this->var. Otherwise, this function
    * throws an exception. Similarly:
    *
    * $this->addVar($value);
    *
    * will append the array $var with $value 
    *
    * @param string $name The name of the function called.
    * @param array $arguments An array of arguments given.
    */
    public function __call($name, $arguments){
        $ftl = substr($name, 0, 3) ;
        $var = substr($name, 3, 1) . substr($name, 4);
        
        /*
        * If the property is a biodata object, then it should
        * be prepended with BD
        */ 
        if(substr($var,0,2) != "BD"){
            $var = strtolower($var[0]) . substr($var, 1);
        } else {
            $var = substr($var,2);
            if(!class_exists($var)){
                throw new Exception("Call to undefined method " . $this->type . "::" . $name . "().");
            }
        }
        if($ftl === "set"){
            $this->$var = $arguments[0];
        } else if ($ftl === "get"){
            return $this->$var;
        } else if ($ftl === "add"){
            // handles simple plurals
            $var = $var . "s";
            if(!is_array($this->$var)){
                throw new Exception("$var is not an array, cannot add to it");
            }
            $this->{$var}[] = $arguments[0];
        } else {
            throw new Exception("Call to undefined method " . $this->type . "::" . $name . "()");
        }
    }
    
    /**
    * Setter for $this->time. Enforces type Time
    */
    public function setTime(Time $time) {
        $this->time = $time;
    }
    
    /**
    * @see Time::toJSON()
    */
    public function toJSON(){
        //return json_encode($this->toArray());
        return json_encode($this);
    }
    
    /**
    * @see Time::toArray()
    */
    public function toArray(){
        $tempArray;
        foreach($this as $var => $value){
            if(is_object($value)){
                $tempArray[$var] = $value->toArray();
            } else {
                if($value !== null) $tempArray[$var] = $value;
            }
        }
        return $tempArray;
    }
}

/**
* A generic measurement object for all biodata.
*
* All measurements need a value, the units can be null.
*
* @abstract
*/
class Measurement extends GenericObject
{
    public $value;
    public $unit;

    function __construct($value = null, $unit = null, $timestamp = null) {
        parent::__construct($timestamp);
        $this->setValue($value);
        $this->setUnit($unit);
    }
}

/**
* An array of measurements
*
* This class deals with when you have multiple
* measurements for one piece of biodata. In general,
* all biodata classes will have arrays of measurements.
* There are no exceptions, yet. If you only have one
* measurement then it will be an array of length one.
*/
class MeasurementArray extends GenericObject
{
    public $measurements = array();
    
    function __construct($timestamp = null) {
        parent::__construct($timestamp);
    }
    
    /**
    * Checks that all the measurements are the right
    * type, BioData\Measurement
    */
    public function checkMeasurementsTypes(){
        foreach($this->measurements as $measurement){
            $this->checkMeasurement($measurement);
        }
        return true;
    }
    
    private function checkMeasurement($measurement){
        if(gettype($measurement) !== "object" || get_class($measurement) !== "BioData\Measurement"){
            if(is_object($measurement)){
                throw new Exception("Unsupported object: " . get_class($measurement) . ". All measurements must be an object of type BioData\Measurement.", 600);
            } else {
                throw new Exception("Unsupported type: " . gettype($measurement) . ". All measurements must be an object of type BioData\Measurement.", 601);
            }
        }
    }
    
    /**
    * Checks the measurement, then adds it to
    * the array of measurements.
    */
    public function addMeasurement($measurement){
        $this->checkMeasurement($measurement);
        $this->measurements[] = $measurement;
    }
    
    public function toArray(){
        $this->checkMeasurementsTypes();
        return parent::toArray();
    }
}

/**
* A generic measurement object type.
*
* This abstract type should be inherited by
* all the different types of biodata measurement
* that can be created. It defines a few imporant
* methods and properties that all biodata should
* possess.
* 
* @abstract
*/
class GenericMeasurement extends GenericObject
{
    /**
    * @var BioData\MeasurementArray
    */
    public $measurementArray;
    
    /**
    * @var array An array of units that the biodata may be measured in.
    */
    public $allowedUnits = array();
    
    function __construct($measurement = null, $units = null, $timestamp = null) {
        $this->measurementArray = new MeasurementArray();
        parent::__construct($timestamp);
        if($measurement !== null){
            return $this->addMeasurement($measurement, $units, $timestamp);
        }
    }
    
    public function __call($name, $arguments){
        $ftl = substr($name, 0, 3) ;
        $var = substr($name, 3);
        if($ftl === "add" && ("BioData\\" . $var) === get_class($this)){
            if(count($arguments) === 2)
                $this->addMeasurement($arguments[0], $arguments[1]);
            else if(count($arguments) === 1)
                $this->addMeasurement($arguments[0]);
        } else {
            parent::__call($name, $arguments);
        }
    }
    
    /**
    * Checks that the $measurement is of the right type.
    *
    * @param BioData\Measurement the measurement to be checked.
    */
    public function checkMeasurementUnit(Measurement $measurement){
        $isAllowed = (false || ((count($this->allowedUnits) == 0) && $measurement->getUnit() === null)); // If allowed units is empty, it's a dimensionless unit, and is allowed to be null
        $isAllowed = (in_array($measurement->getUnit(), $this->allowedUnits) || $isAllowed); // changes to true if units are in allowedUnits
        if($isAllowed === false){
            $unitsAllowed = "Allowed units (case sensitive): "; 
            foreach($this->allowedUnits as $unit){
                $unitsAllowed .= "'" . $unit . "', ";
            }
            if($measurement->getUnit() === null){
                throw new Exception("Unit type not specified. $unitsAllowed", 701);
            }
            throw new Exception("Unit type not allowed. $unitsAllowed", 700);
        }
        return true;
    }
    
    public function addMeasurement($measurement, $units = null, $timestamp = null){
        if(!(is_object($measurement) && get_class($measurement) === "BioData\Measurement")){
            $measurement = new Measurement($measurement, $units, $timestamp);
        }
        $this->checkMeasurementUnit($measurement);
        $this->measurementArray->addMeasurement($measurement);
        return $this;
    }
}