<?php

function createLocalItemList($minval,$maxval,$tableName) {



	$user = 'goodeno4_groszew';
	$pass = 'Furious-Pete-Dingus2015';
	$search_db = 'goodeno4_item_search_db';

	//Parameters (hard coded, for now)
	$rec = $tableName;
	$min_price = $minval*100;
	$max_price = $maxval*100;

	// Create connection
	$con_search = new mysqli('localhost',$user,$pass,$search_db);

	// Check connection
	if ($con_search->connect_errno) {
	    printf("Connect failed: %s\n", $con_search->connect_error);
	    exit();
	}


	//Select Proper table and narrow down results
	//$sql_search_values = $con_search->query("SELECT * FROM `$rec` WHERE `Manufacturer`= 'LG'");
	$sql_search_values = $con_search->query("SELECT * FROM `$rec` WHERE `Price` >= '$min_price' AND `Price` <= '$max_price'");

	if (!$sql_search_values) { // add this check.
	    die('Invalid query on line 54: ' . mysql_error());
	}

	//Cache results 
	$cached_results = [];//php array to be saved as long as user inputs remain the same
	while($row = mysqli_fetch_assoc($sql_search_values)){
		$results = array();
	    foreach($row as $cname => $cvalue){
	        //print "$cname: $cvalue";
	        $results[$cname] = $cvalue;
	    }
	    $cached_results[] = $results;
	}

	//print_r($cached_results);
	//print(count($cached_results));
	return $cached_results;

}

function getRandomItem($local_table){
	$item = $local_table[rand(0,count($local_table)-1)];
	$price = $item['Price'];
	echo "<div>";
	echo "	<a href=" . $item['DetailPageURL'] . ">". $item['Title'] ."</a> <br>";
	echo "	<a href=" . $item['DetailPageURL'] . ">" . "<img src=" . $item['ImageURL'] . " height='100' width='100'></a>";
	echo "	<p>$" . $item['Price']/100 . "</p>";
	echo "</div>";
}

function processInputs($name_input,$range_input){

	$min=0;
	$max=0;
	$r_table = $name_input . "_results_table";

	switch ($range_input) {
	    case "zero":
	        $min=0;
	        $max=10;
	        break;
	    case "ten":
	        $min=10;
	        $max=25;
	        break;
	    case "twenty_five":
	        $min=25;
	        $max=50;
	        break;
	    case "fifty":
	        $min=50;
	        $max=100;
	        break;
	    case "one_hundred":
	        $min=100;
	        $max=200;
	        break;
	    case "two_hundred":
	        $min=200;
	        $max=300;
	        break;
	    case "three_hundred":
	        $min=300;
	        $max=400;
	        break;
	    case "four_hundred":
	        $min=400;
	        $max=500;
	        break;   
	    case "five_hundred":
	        $min=500;
	        $max=1000;
	        break;             

	}

	return array($min,$max,$r_table);

}




?>