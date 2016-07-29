<?php
	
include('weightFunctions.php');

$id = $_POST['id'];
$table_name = $_POST['table_name'];

incrementClicks($table_name,$id);
$weight = calculateWeightImpClicks($table_name,$id);
updateWeightImpClicks($table_name,$id,$weight);

?>
