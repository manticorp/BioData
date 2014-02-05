BioData
=======

A universal data format for all Biometric Data in all common formats (JSON, XML, CSV, etc).

At the moment this repository consists of a set of PHP classes for creating BioData objects. Full documentation will be provided at version 0.1.

## BioData Types (units){allowed values} [info]

* Heart Rate (bpm, b/m)
* Blood
  * Sugar (mmol/L, mg/dL)
  * Pressure Diastolic (mmHG)
  * Pressure Systolic (mmHG)
  * Insulin Level (µIU/mL, uIU/mL, pmol/L)
  * ABO Type {O+, A+, B+, AB+, O-, A-, B-, AB-}
  * RH Type {DCe, DcE, Dce, DCE, dCe, dcE, dCE, dce}
* Date of Birth (PHP \DateTime)
* Height (m, ft, in){>0}
* Weight (kg, st, lbs){>0}
* Location
  * Current Location (DD){PHP Array(lat, lon)} [Latitude and Longitude array in decimal degrees]
  * GPS tracks (not yet implemented)
* Sleep
  * Length (2x PHP \Date)[Determined by measurement start and end time]
  * Quality (%){0 < Q < 1} [Percentage expressed between 0 and 1]
* Mood
  * Happiness (%){0 < Q < 1} [Percentage expressed between 0 and 1]
  * Tiredness (%){0 < Q < 1} [Percentage expressed between 0 and 1]
  * Arousal (%){0 < Q < 1} [Percentage expressed between 0 and 1]
* General Event (e.g surgery, broken bone, new job, baby born, etc)
* Diet (none yet implemented)
  * Nutritional Units
     * Carbohydrates
     * Protein
     * Calories
     * Vitamins
     * etc...
  * Actual food items (e.g banana, chocolate bar, bowl of cereal)
  * Ingredients (e.g caffeine, sucrose, etc)
  * Drink
    * Alcoholic 
      * Units
      * Actual Items (e.g pint of lager, small glass of wine)
    * Non-alcoholic (same as foodstuffs) 
  * Smoking
    * Based on Brand
    * With nicotene/tar amounts
  * Drugs
    * Prescription
    * Non-Prescription
    * Narcotics (maybe should include alcoholic drinks in this?) 
