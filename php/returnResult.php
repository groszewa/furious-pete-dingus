<?php
//echo "Person: {$_POST['person']}<br />";
//echo "Range: {$_POST['price_range']}<br />";

include('weightFunctions.php');
include('helperFunctions.php');

$input_array = processInputs(htmlspecialchars($_POST['person']),htmlspecialchars($_POST['price_range']));
$min = $input_array[0];
$max = $input_array[1];
$table_name = $input_array[2];

//echo "$result_table <br />";
$local_table = createLocalItemList($min,$max,$table_name);
//print_r( getRandomItem($local_table) );

// Retrieve random result
//$item = getRandomResult($local_table);

$item = getFitnessProportionateSelection($local_table);

// Show result (image, link, etc.)
displayResult($table_name,$item);

// Increment impression in sql table
incrementImpressions($table_name,$item);

$id = $item['Id'];

// Update weight imp clicks after incrementing impressions
$weight = calculateWeightImpClicks($table_name,$id);

updateWeightImpClicks($table_name,$id,$weight);

?>