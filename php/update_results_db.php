<?php

include('amazon_scripts.php'); 

$user = 'root';
$pass = '';
$db = 'item_search_db';

// Create connection
$con_search = new mysqli('localhost',$user,$pass,$db);

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

echo $request;

mysqli_close($con_search);

?>