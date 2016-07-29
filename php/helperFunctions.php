<?php

function createLocalItemList($minval,$maxval,$tableName) {
<<<<<<< HEAD
    
    // Set login parameters
	$user = 'root';
	$pass = '';
	$search_db = 'item_search_db';
=======



	$user = 'goodeno4_groszew';
	$pass = 'Furious-Pete-Dingus2015';
	$search_db = 'goodeno4_item_search_db';
>>>>>>> FETCH_HEAD

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

    // Close mysqli connection
    mysqli_close($con_search);
    
	//print_r($cached_results);
	//print(count($cached_results));
	return $cached_results;

}

function displayResult($table_name,$item){
	$price = $item['Price'];
    
	echo "<div>";
<<<<<<< HEAD
    // Display link and attach click counter
	echo "	<a href=" . $item['DetailPageURL'] . " id='product_link'>". $item['Title'] ."</a> <br>";
    
    $id = $item['Id'];
    
	echo "	<img src=" . $item['ImageURL'] . " id='product_image' height='100' width='100'>";
    
echo '<script type="text/javascript"> ';
    echo 'document.getElementById("product_link").onclick = function(){increment_clicks_func()};';
    echo 'document.getElementById("product_image").onclick = function(){increment_clicks_func()};';
echo '</script>';    
    
    
        echo '<script type="text/javascript">';
            echo 'function increment_clicks_func(){';
                echo '$.ajax({';
                    echo 'url: "php/incrementClicks.php",';
                    echo 'type: "POST",';
                    echo "data: { id : '$id' , table_name : '$table_name' },";
                    echo 'success: function(msg){';
                    echo '}';
                echo '});';
            echo '};';
        echo '</script>';
    	//echo '<img src="http://i.imgur.com/QZMFqy2.jpg" onclick="increment_clicks_func()" height="100" width="100">';
    
	echo "	<p>" . $item['Price']/100 . "</p>";
    echo "  <p>" . $item['Id'] . "</p>";
=======
	echo "	<a href=" . $item['DetailPageURL'] . ">". $item['Title'] ."</a> <br>";
	echo "	<a href=" . $item['DetailPageURL'] . ">" . "<img src=" . $item['ImageURL'] . " height='100' width='100'></a>";
	echo "	<p>$" . $item['Price']/100 . "</p>";
>>>>>>> FETCH_HEAD
	echo "</div>";
}

function getRandomResult($local_table){
    return $local_table[rand(0,count($local_table)-1)];
}

function getFitnessProportionateSelection($local_table){
    // Need to get:
    // Id
    // Weights
    
    // Get the max
    $max = 0;
    
    foreach ($local_table as $item) {
        $max += $item['WeightImpClicks'];
    }
    
    $pick = rand(0,$max);
    $selected_item = null;  
    $current = 0;
    
    // Build key value pairs: Id => WeightImpClicks
    foreach ($local_table as $item) {
        $id = $item['Id'];
        $weight = $item['WeightImpClicks'];
        $current += $weight;
        if ($current > $pick) {
            $selected_item = $item;
            
            break;
        }
    }
    
    return $item;
    
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

function incrementImpressions($table_name,$item) {
    
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

    $id = $item['Id'];
    //echo $id;
    
    // Find row with Id and increment Impressions value
    // NOTE: Use back tick around column names
    $con_search->query("UPDATE `$table_name` SET `Impressions` = `Impressions` + 1 WHERE `Id` = '$id'");
    
    mysqli_close($con_search);
        
}

/*
function incrementClicks($table_name,$item) {
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
    
}
*/


?>