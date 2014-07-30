<?php

include('amazon_scripts.php'); 

$user = 'root';
$pass = '';
$search_db = 'item_search_db';
$results_db = 'search_results_db';

// Set timezone
date_default_timezone_set('America/Los_Angeles');

// Create connection
$con_search = new mysqli('localhost',$user,$pass,$search_db);

// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

// Get the column names from our SQL table
$sql_column_names = mysqli_query($con_search,"SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` 
WHERE `TABLE_SCHEMA`='item_search_db' AND `TABLE_NAME`='dad_search_table'");

// Put column names in PHP array
$column_names = [];
while($row = mysqli_fetch_array($sql_column_names))
{
    $column_names[] = $row[0];
}

// Get the values from our table for the item search operation
$sql_search_values = mysqli_query($con_search,"SELECT * FROM dad_search_table WHERE `Id`=1");
$search_values = mysqli_fetch_array($sql_search_values,MYSQLI_NUM);

// Close connection to DB
$con_search->close();

// Combine the two arrays (elementName => elementValue)
$item_search = array_combine($column_names, $search_values);

//print_r($item_search);

// Filter out all empty element values
$item_search_filtered = array_filter($item_search);

// Generate the necessary constant element names and values
$constant_elements = array(
    'AWSAccessKeyId' => get_aws_access_key_id(),
    'AssociateTag' => get_amazon_assoc_tag(),
    'Version' => "2010-11-01",
    'Operation' => "ItemSearch",
    'Service' => "AWSECommerceService");

// Add the constant elements to our array
$params = $constant_elements + $item_search_filtered;

// Generate item search URL
$request = amazon_get_signed_url($params);

echo $request . "<br>";

// Catch response in xml object
$response = file_get_contents($request);

// Parse xml
$parsed_xml = simplexml_load_string($response);

// Connect to MySQL results table
$con_results = new mysqli('localhost',$user,$pass,$results_db);

// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

// Parse xml into PHP array
$numOfItems = $parsed_xml->Items->TotalResults;
$item_results_array = array(
    'Id' => 0,
    'Actor' => "",
    'Artist' => "",
    'Author' => "",
    'CorrectedQuery' => "",
    'Director' => "",
    'Keywords' => "",
    'Manufacturer' => "",
    'ProductGroup' => "",
    'Title' => "",
    'LastUpdated' => "");

// print_r($results_array);

if($numOfItems > 0){
    foreach($parsed_xml->Items->Item as $current){
        foreach($item_results_array as $entry=>$value){
            if (isset($current->ItemAttributes->$entry)) {
                // print $current->ItemAttributes->$entry . "<br>";
                // print "Entry = " . $entry . "<br>";
                $item_results_array[$entry] = (string)$current->ItemAttributes->$entry;   
            }            
        }
    }
    
    if (isset($parsed_xml->Items->Item->ASIN)) {
        $item_results_array['ASIN'] = (string)$parsed_xml->Items->Item->ASIN;
    }
    
    if (isset($parsed_xml->Items->Item->DetailPageURL)) {
        $item_results_array['DetailPageURL'] = (string)$parsed_xml->Items->Item->DetailPageURL;
    }
    
    $item_results_array['LastUpdated'] = date("Y-m-d H:i:s");   
}

$sql = sprintf(
    'INSERT INTO dad_results_table (%s) VALUES ("%s")',
    implode(',',array_keys($item_results_array)),
    implode('","',array_values($item_results_array))
);

echo "<br>" . $sql;

if (!$con_results->query($sql)) {
    printf("Errormessage: %s\n", $con_results->error);
}


$con_results->close();
// Dump PHP array into MySQL row

?>