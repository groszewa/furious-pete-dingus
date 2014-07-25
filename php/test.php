<?php
echo "Person: {$_POST['person']}<br />";
echo "Range: {$_POST['price_range']}<br />";

$name = htmlspecialchars($_POST['person']);
$range = htmlspecialchars($_POST['price_range']);
//echo $range;
$min=0;
$max=0;

switch ($range) {
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

echo "Min val: {$min} <br />";
echo "Max val: {$max}";




?>