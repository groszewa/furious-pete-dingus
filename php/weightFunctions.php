<?php

function incrementClicks($table_name,$id) {

    // Set login parameters
    $user = 'root';
    $pass = '';
    $search_db = 'item_search_db';

    // Create connection
    $con_search = new mysqli('localhost',$user,$pass,$search_db);

    // Check connection
    if ($con_search->connect_errno) {
        printf("Connect failed: %s\n", $con_search->connect_error);
        exit();
    }

    // Find row with Id and increment Clicks value
    // NOTE: Use back tick around column names
    $con_search->query("UPDATE `$table_name` SET `Clicks` = `Clicks` + 1 WHERE `Id` = '$id'");

    mysqli_close($con_search);
    
}

function calculateWeightImpClicks($table_name, $id) {

    // Set login parameters
	$user = 'root';
	$pass = '';
	$search_db = 'item_search_db';

	// Create connection
	$con_search = new mysqli('localhost',$user,$pass,$search_db);

 
    $ci_results = $con_search->query("SELECT `Clicks`,`Impressions` FROM `$table_name` WHERE `Id` = '$id' LIMIT 1");
    // Get impressions and clicks values
    //$impressions = $con_search->query("SELECT `Impressions` FROM `$table_name` WHERE `Id` = '$id' LIMIT 1");
    //$clicks = $con_search->query("SELECT `Clicks` FROM `$table_name` WHERE `Id` = '$id' LIMIT 1");
    
    $ci_array = $ci_results->fetch_assoc();
    
    $impressions = $ci_array['Impressions'];
    $clicks = $ci_array['Clicks'];
    
    $weight = floor(100*($clicks / $impressions));
    
    // Close mysqli connection
    mysqli_close($con_search);
    
    return $weight;
}

function updateWeightImpClicks($table_name,$id,$weight) {
    
    // Set login parameters
	$user = 'root';
	$pass = '';
	$search_db = 'item_search_db';
    
	// Create connection
	$con_search = new mysqli('localhost',$user,$pass,$search_db);

	// Check connection
	if ($con_search->connect_errno) {
	    printf("Connect failed: %s\n", $con_search->connect_error);
	    exit();
	}

    $con_search->query("UPDATE `$table_name` SET `WeightImpClicks` = '$weight' WHERE `Id` = '$id'");
    
    // Close mysqli connection
    mysqli_close($con_search);
    
}

?>