<?php
/**
 *
 *
 *
 */

 
/**
 * Define a custom exception class
 *
 * Error Codes:
 *
 *  6XX : Variable Error
 *      60X : Measurement Variable Error
 *          600 : Object passed not of type BDMeasurement
 *          601 : Variable passed not object
 *  7XX : Unit Error
 *      70X : 
 *          700 : Unit type not allowed
 *          701 : Unit type not specified
 */
class BDException extends Exception
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
 * 
 */
class BDTime
{
    public $type;
    public $timestamp;
    public $start;
    public $finish;
    
    public function __construct($timestamp = null) {
        $timestamp = ($timestamp === null)? $this->getCurrentTimestamp() : $timestamp;
        $this->type = get_class($this);
        $this->__set("timestamp", $timestamp);
    }
    
    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->checkTimeStamp($value);
            $this->$property = $value;
        }
        return $this;
    }
    
    public function __call($name, $arguments){
        if(substr($name, 0, 3) === "set"){
            $var = substr($name, 4);
            $this->$var = $arguments[0];
        } else {
            throw new BDException("Call to undefined method " . $this->type . "::" . $name . "()");
        }
    }
    
    private function checkTimeStamp($timestamp = null) {
        $timestamp = ($timestamp === null)? $this->timestamp : $timestamp;
        $timestamp = intval($timestamp);
        if(!(( (int) $timestamp === $timestamp) 
        && ($timestamp <= PHP_INT_MAX)
        && ($timestamp >= ~PHP_INT_MAX))){
            throw new BDException("Invalid Timestamp.", 621);
        } 
    }
    
    public static function getCurrentTimestamp(){
        $date = new DateTime();
        return $date->getTimestamp();
    }
    
    public function toJSON(){
        return json_encode($this->toArray());
    }
    
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
 *
 */
class BDGenericObject
{
    public $type;
    public $BDTime;
    
    public function __construct($timestamp = null) {
        $this->BDTime = new BDTime($timestamp);
        $this->type = get_class($this);
    }
    
    public function __get($property) {
        if (property_exists($this, $property)) {
          return $this->$property;
        } else {
            throw new BDException("Tying to get undefined property: " . $property);
        }
    }

    public function __set($property, $value) {
        if (property_exists($this, $property)) {
          $this->$property = $value;
        } else {
            throw new BDException("Tying to set undefined property: " . $property);
        }
        return $this;
    }
    
    public function __call($name, $arguments){
        $ftl = substr($name, 0, 3) ;
        $var = strtolower(substr($name, 3, 1)) . substr($name, 4);
        if($ftl === "set"){
            $var = strtolower(substr($name, 3, 1)) . substr($name, 4);
            $this->$var = $arguments[0];
        } else if ($ftl === "get"){
            return $this->$var;
        } else {
            throw new BDException("Call to undefined method " . $this->type . "::" . $name . "()");
        }
    }
    
    public function setTime(BDGenericTime $time) {
        $this->time = $time;
    }
    
    public function toJSON(){
        return json_encode($this->toArray());
    }
    
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
 *
 */
class BDMeasurement extends BDGenericObject
{
    public $value;
    public $unit;

    function __construct($value = null, $unit = null, $timestamp = null) {
        parent::__construct($timestamp);
        $this->setValue($value);
        $this->setUnit($unit);
    }
    
    public function toJSON(){
        $this->time->setFinish(null);
        parent::toJSON();
    }
}

/**
 *
 */
class BDMeasurementArray extends BDGenericObject
{
    public $measurements = array();
    
    function __construct($timestamp = null) {
        parent::__construct($timestamp);
    }
    
    public function checkMeasurementsTypes(){
        foreach($this->measurements as $measurement){
            $this->checkMeasurement($measurement);
        }
        return true;
    }
    
    private function checkMeasurement($measurement){
        if(gettype($measurement) !== "object" || get_class($measurement) !== "BDMeasurement"){
            if(is_object($measurement)){
                throw new BDException("Unsupported object: " . get_class($measurement) . ". All measurements must be an object of type BDMeasurement.", 600);
            } else {
                throw new BDException("Unsupported type: " . gettype($measurement) . ". All measurements must be an object of type BDMeasurement.", 601);
            }
        }
    }
    
    public function addMeasurement($measurement){
        $this->checkMeasurement($measurement);
        $this->measurements[] = $measurement;
    }
    
    public function toArray(){
        $this->checkMeasurementsTypes();
        parent::toArray();
    }
}

/**
 *
 */
class BDGenericMeasurement extends BDGenericObject
{
    public $measurements;
    public $allowedUnits = array();
    
    function __construct($timestamp = null) {
        $this->measurements = new BDMeasurementArray();
        parent::__construct($timestamp);
    }
    
    public function checkMeasurementUnit(BDMeasurement $measurement){
        $isAllowed = (false || ((count($this->allowedUnits) == 0) && $measurement->getUnit() === null)); // If allowed units is empty, it's a dimensionless unit, and is allowed to be null
        $isAllowed = (in_array($measurement->getUnit(), $this->allowedUnits) || $isAllowed); // changes to true if units are in allowedUnits
        if($isAllowed === false){
            if($measurement->getUnit() === null){
                throw new BDException("Unit type not specified", 701);
            }
            throw new BDException("Unit type not allowed", 700);
        }
        return true;
    }
    
    public function __call($name, $arguments){
        $ftl = substr($name, 0, 3) ;
        $var = substr($name, 3);
        if($ftl === "add" && $var === substr(get_class($this),2)){
            if(count($arguments) === 2)
                $this->addMeasurement($arguments[0], $arguments[1]);
            else if(count($arguments) === 1)
                $this->addMeasurement($arguments[0]);
        } else {
            parent::__call($name, $arguments);
        }
    }
    
    public function addMeasurement($measurement, $units = null){
        if(!(is_object($measurement) && get_class($measurement) === "BDMeasurement")){
            $measurement = new BDMeasurement($measurement, $units);
        }
        $this->checkMeasurementUnit($measurement);
        $this->measurements->addMeasurement($measurement);
    }
}

/**
 *
 */
 class BDEvent extends BDGenericObject
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
class BDHeartRate extends BDGenericMeasurement
{
    public $allowedUnits = array("bpm");

    function __construct($timestamp = null) {
        parent::__construct($timestamp);
    }
}

/**
 *
 */
class BDBloodSugar extends BDGenericObject
{
    
    function __construct($timestamp = null) {
        parent::__construct($timestamp);
    }
}

/**
 *
 */
class BDBloodInsulin extends BDGenericObject
{
    function __construct($timestamp = null) {
        parent::__construct($timestamp);
    }
}



$var = new BDHeartRate();
$var->addHeartRate(123, "bpm");
echo $var->toJson();