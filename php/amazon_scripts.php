<?php
function get_aws_access_key_id() {
    $xml = simplexml_load_file(".crit.xml");
    return $xml->aws_access_key_id;
}

function get_aws_secret_access_key() {
    $xml = simplexml_load_file(".crit.xml");
    return $xml->aws_secret_access_key;
}

function get_amazon_assoc_tag() {
    $xml = simplexml_load_file(".crit.xml");
    return $xml->amazon_assoc_tag;
}
?>