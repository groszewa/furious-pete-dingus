<?php

include('amazon_scripts.php'); 

$user = 'root';
$pass = '';
$search_db = 'item_search_db';
$results_db = 'search_results_db';

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
mysqli_close($con_search);

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

// Catch response in xml object
$response = file_get_contents($request);

// Parse xml
$parsed_xml = simplexml_load_string($response);

// Connect to MySQL results table
$con_results = new mysqli('localhost',$user,$pass,$results_db);

// Parse xml into MySQL table
$numOfItems = $parsed_xml->Items->TotalResults;
$results_array = array(
    'Actor' => "",
    'Artist' => "",
    'ASIN' => "",
    'Author' => "",
    'CorrectedQuery' => "",
    'Director' => "",
    'Keywords' => "",
    'Manufacturer' => "",
    'ProductGroup' => "",
    'Title' => "",
    'TotalPages' => "",
    'TotalResutls' => "");

print_r($results_array);

if($numOfItems > 0){
    foreach($parsed_xml->Items->Item as $current){
        foreach($results_array as $entry=>$value){
            if (isset($current->ItemAttributes->$entry)) {
                print $current->ItemAttributes->$entry . "<br>";
                print "Entry = " . $entry . "<br>";
                $results_array[$entry] = (string)$current->ItemAttributes->$entry;   
            }            
        }
    }
}

print_r($results_array);

/*
        
        if (isset($current->ItemAttributes->Actor)) {
            $results_array['Actor'] = $current->ItemAttributes->Actor;
        }
            else { $results_array['Actor'] = "";
        }
        
        if (isset($current->ItemAttributes->Actor)) {
            $results_array['Actor'] = $current->ItemAttributes->Actor;
        }
            else { $results_array['Actor'] = "";
        }
        
        
            
            
        } elseif(isset($current->ItemAttributes->Author)) {
            print("<br>Author: ".$current->ItemAttributes->Author);
        } elseif
            (isset($current->Offers->Offer->Price->FormattedPrice)){
            print("<br>Price:".$current->Offers->Offer->Price->FormattedPrice);
        }else{
            print("<center>No matches found.</center>");
        }
    }
}

• Actor(p.318)
• Artist(p.319)
• ASIN (p.319)
• Author(p.319)
• CorrectedQuery(p.321) • Creator(p.322)
• Director(p.322)
• Keywords(p.326)
• Manufacturer(p.327) • Message(p.328)
• ProductGroup(p.330) • Role(p.331)
• Title(p.333)
• TotalPages(p.333)
• TotalResults(p.333)
*/

?>