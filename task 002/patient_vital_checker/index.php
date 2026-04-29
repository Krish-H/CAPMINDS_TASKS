<?php

include "vitals.php";
include "validate.php";
include "scanner.php";
include "rules.php";

foreach($patient_vitals as $vital ){
    if($vital['vital_type'] == "Temperature"){
        $result = validateVital($vital,'checkTemperature');
    }
    else if($vital['vital_type'] == "Pulse"){
        $result = validateVital($vital,'checkPulse');
    }
    else if($vital['vital_type'] == "BP"){
        $result = validateVital($vital,'checkBloodPressure');
    }
    else{
        echo "values invalid<br>";  
    }

   echo "Patient: " . $result['patient_name'] . "<br>";
    echo "Vital: " . $result['vital_type'] . "<br>";
    echo "Value: " . $result['value'] . "<br>";
    echo "Status: " . $result['status'] . "<br>";
    echo "Message: " . $result['message'] . "<br>";
    echo "----------------------<br>";
    
}

echo "<br><strong>Project Files:</strong><br>";

scanFolder(__DIR__);//current folder


?>

