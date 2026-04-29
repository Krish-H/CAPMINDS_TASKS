<?php
// check temperature value

function checkTemperature($data){
 $temp= $data['value'];
 if($temp >100){
 $data['status']='HIGH';
 $data['message']='Fever detected';
 }
 else{
 $data['status']='NORMAL';
 $data['message']='Temperature normal';
 }

 return $data;

}

// check pulse value

function checkPulse($data){
 $pulse= $data['value'];
 if($pulse >100){
 $data['status']='HIGH';
 $data['message']='Pulse rate high';
 }
 else{
 $data['status']='NORMAL';
 $data['message']='Pulse normal';

 }

 return $data;
}

// check blood pressure

function checkBloodPressure($data){

 $parts = explode("/", $data['value']);
 $first = $parts[0];
 $second = $parts[1];

 if($first > 130 || $second > 90){
  $data['status'] = 'HIGH';
  $data['message'] = 'BP high';
 }
 else{
  $data['status'] = 'NORMAL';
  $data['message'] = 'BP normal';
 }

 return $data;
}

?>