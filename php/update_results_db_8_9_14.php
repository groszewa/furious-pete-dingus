<?php

//////////////////////////////////////
//                                  //
//          GENERAL SETUP           //
//                                  //
//////////////////////////////////////

include('amazon_scripts.php'); 

$user = 'root';
$pass = '';
$search_db = 'item_search_db';
$search_table = 'dad_search_table';
$results_table = 'dad_results_table';

// Set timezone
date_default_timezone_set('America/Los_Angeles');

// Create connection
$con_search = new mysqli('localhost',$user,$pass,$search_db);

// Check connection
if ($con_search->connect_errno) {
    printf("Connect failed: %s\n", $con_search->connect_error);
    exit();
}

// Get the column names from our table
$column_names = get_php_column_names($con_search, $search_db, $search_table);

// Get the values from our table for the item search operation
$sql_itemsearch_params = $con_search->query("SELECT * FROM $search_table");
if (!$sql_itemsearch_params) { // add this check.
    die('Unable to get * from table ' . $search_table . ' ' . mysql_error());
}

$requests = array();
$requests_more_pages = array();

// ------------------- Main Loop ----------------------

while($search_values = $sql_itemsearch_params->fetch_array(MYSQL_NUM)){
    $params = clean_itemsearch_params($search_values, $column_names);   // Clean up our params before sending them to URL generator
    $request = amazon_get_signed_url($params);                          // Generate item search URL
    
    $total_pages = 1;
    
    $parsed_xml = simplexml_load_string(file_get_contents($request));
    
    update_results_table($con_search, $results_table, $parsed_xml);
    
    if (isset($parsed_xml->Items->TotalPages)) {
        $total_pages = $con_search->real_escape_string((string)$parsed_xml->Items->TotalPages);
        print "Total pages = " . $total_pages . "<br>";
    }
    
    if ($total_pages > 1) {
        for ($i = 2; $i < floor(0.5*$total_pages)+1; $i++) {
            $params['ItemPage'] = $i;
            $request = amazon_get_signed_url($params);
            $parsed_xml = simplexml_load_string(file_get_contents($request));
            update_results_table($con_search, $results_table, $parsed_xml);
        }
    }   
}

//////////////////////////////////////
//                                  //
//            FUNCTIONS             //
//                                  //
//////////////////////////////////////

function clean_itemsearch_params($search_values, $column_names){
    $constant_elements = array(
        'AWSAccessKeyId' => get_aws_access_key_id(),
        'AssociateTag' => get_amazon_assoc_tag(),
        'Version' => "2010-11-01",
        'Operation' => "ItemSearch",
        'Service' => "AWSECommerceService");
    
    // Combine the two arrays (elementName => elementValue) and filter out all empty key values
    $params = array_filter(array_combine($column_names, $search_values));    
    $params += $constant_elements;           // Add the constant elements to our array
    unset($params['Id']);
    unset($params['DateUpdated']);
    return $params;
}

function get_php_column_names($con, $db_name, $table_name) {
    // Get the column names from our SQL table
    $sql_column_names = $con->query("SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` 
    WHERE `TABLE_SCHEMA`='$db_name' AND `TABLE_NAME`='$table_name'");

    if (!$sql_column_names) { // add this check.
        die('Failed to get column names: ' . mysql_error());
    }

    // Put column names in PHP array
    $column_names = [];
    while($row = $sql_column_names->fetch_array())
    {
        $column_names[] = $row[0];
        echo $row[0] . "<br>";
    }

    $sql_column_names->close();
    return $column_names;
}

function update_results_table($con, $table_name, $parsed_xml) {
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
        'Price' => 0,
        'ImageURL' => "",
        'LastUpdated' => "");

    // print_r($results_array);

    if($numOfItems > 0){
        foreach($parsed_xml->Items->Item as $current){
            foreach($item_results_array as $entry=>$value){
                if (isset($current->ItemAttributes->$entry)) {
                    // print $current->ItemAttributes->$entry . "<br>";
                    // print "Entry = " . $entry . "<br>";
                    $item_results_array[$entry] = $con->real_escape_string((string)$current->ItemAttributes->$entry);   
                }            
            }
            
            if (isset($current->ASIN)) {
                $item_results_array['ASIN'] = $con->real_escape_string((string)$current->ASIN);
            }

            if (isset($current->DetailPageURL)) {
                $item_results_array['DetailPageURL'] = $con->real_escape_string((string)$current->DetailPageURL);
            }
            
            if (isset($current->OfferSummary->LowestNewPrice->Amount)) {
                $item_results_array['Price'] = $con->real_escape_string((string)$current->OfferSummary->LowestNewPrice->Amount);
            }
            
            if (isset($current->MediumImage->URL)) {
                $item_results_array['ImageURL'] = $con->real_escape_string((string)$current->MediumImage->URL);
            }

            $item_results_array['LastUpdated'] = date("Y-m-d H:i:s");
            
            // Insert results into MySQL table
            $sql = sprintf(
                'INSERT INTO dad_results_table (%s) VALUES ("%s")',
                implode(',',array_keys($item_results_array)),
                implode('","',array_values($item_results_array))
            );

            if (!$con->query($sql)) {
                printf("Errormessage: %s\n", $con->error);
            }
        }    
    }
    return;
}
?>