<?php
include 'BioData\BioData.php';
function with($object){ return $object; }

$var = new BioData\Sleep(null, null, new DateTime("Yesterday 8pm"), new DateTime("Today 8am"));

echo "<pre>";
print_r($var);
echo "</pre>";