<?php

//////////////////////////////////////
//                                  //
//        ITEM SEARCH SECTION       //
//                                  //
//////////////////////////////////////

include('amazon_scripts.php'); 

$user = 'root';
$pass = '';
$search_db = 'item_search_db';

// Set timezone
date_default_timezone_set('America/Los_Angeles');

// Create connection
$con_search = new mysqli('localhost',$user,$pass,$search_db);

// Check connection
if ($con_search->connect_errno) {
    printf("Connect failed: %s\n", $con_search->connect_error);
    exit();
}

// Generate the necessary constant element names and values
$constant_elements = array(
    'AWSAccessKeyId' => get_aws_access_key_id(),
    'AssociateTag' => get_amazon_assoc_tag(),
    'Version' => "2010-11-01",
    'Operation' => "ItemSearch",
    'Service' => "AWSECommerceService");

// Get the column names from our SQL table
$sql_column_names = $con_search->query("SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` 
WHERE `TABLE_SCHEMA`='item_search_db' AND `TABLE_NAME`='dad_search_table'");

if (!$sql_column_names) { // add this check.
    die('Invalid query on line 39: ' . mysql_error());
}

// Put column names in PHP array
$column_names = [];
while($row = $sql_column_names->fetch_array())
{
    $column_names[] = $row[0];
    echo $row[0] . "<br>";
}

$sql_column_names->close();

echo "Hello world <br>";

// Get the values from our table for the item search operation
$sql_search_values = $con_search->query("SELECT * FROM dad_search_table");
if (!$sql_search_values) { // add this check.
    die('Invalid query on line 54: ' . mysql_error());
}

$requests = array();

// Iterate over each row
while($search_values = $sql_search_values->fetch_array(MYSQL_NUM)){
    $item_search = array_combine($column_names, $search_values);    // Combine the two arrays (elementName => elementValue)
    $item_search_filtered = array_filter($item_search);             // Filter out all empty element values
    $params = $constant_elements + $item_search_filtered;           // Add the constant elements to our array
    unset($params['Id']);
    unset($params['DateUpdated']);
    
    $requests[] = amazon_get_signed_url($params);                   // Generate item search URL
}

print_r($requests);

//////////////////////////////////////
//                                  //
//      RESULTS PARSING SECTION     //
//                                  //
//////////////////////////////////////

// Delete contents of MySQL table
$con_search->query("TRUNCATE TABLE dad_results_table") or die("Could not delete table");

foreach ($requests as $request) {
    // Catch response in xml object
    $response = file_get_contents($request);

    // Parse xml
    $parsed_xml = simplexml_load_string($response);

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
            
            if (isset($current->ASIN)) {
                $item_results_array['ASIN'] = (string)$current->ASIN;
            }

            if (isset($current->DetailPageURL)) {
                $item_results_array['DetailPageURL'] = (string)$current->DetailPageURL;
            }

            $item_results_array['LastUpdated'] = date("Y-m-d H:i:s");
            
            // Insert results into MySQL table
            $sql = sprintf(
                'INSERT INTO dad_results_table (%s) VALUES ("%s")',
                implode(',',array_keys($item_results_array)),
                implode('","',array_values($item_results_array))
            );

            if (!$con_search->query($sql)) {
                printf("Errormessage: %s\n", $con_search->error);
            }
        }    
    }
}

?>