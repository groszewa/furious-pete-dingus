<?php
//echo "Person: {$_POST['person']}<br />";
//echo "Range: {$_POST['price_range']}<br />";


include('helperFunctions.php');
    



$input_array = processInputs(htmlspecialchars($_POST['person']),htmlspecialchars($_POST['price_range']));
$min = $input_array[0];
$max = $input_array[1];
$r_table = $input_array[2];




//echo "$result_table <br />";
$local_table = createLocalItemList($min,$max,$r_table);
//print_r( getRandomItem($local_table) );
getRandomItem($local_table);




?>