<?php
include 'BioData\BioData.php';

$var = new BioData\HeartRate();
$var->addHeartRate(123, "bpm");

echo "<pre>";
print_r($var->toArray());
echo "</pre>";