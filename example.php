<?php
include 'BioData\BioData.php';

$var = new BioData\DateOfBirth(-1);

echo "<pre>";
print_r(json_decode(json_encode($var)));
echo "</pre>";