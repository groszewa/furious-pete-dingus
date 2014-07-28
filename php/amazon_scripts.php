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

function amazon_get_signed_url($params) {
	$base_url = "http://ecs.amazonaws.com/onca/xml";
		//'ResponseGroup'=>"Images,ItemAttributes,EditorialReview",
	
	if(empty($params['AssociateTag'])) {
		unset($params['AssociateTag']);
	}
		
	// Add the Timestamp
	$params['Timestamp'] = gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time());
	 
	// Sort the URL parameters
	$url_parts = array();
	foreach(array_keys($params) as $key)
		$url_parts[] = $key . "=" . str_replace('%7E', '~', rawurlencode($params[$key]));
	sort($url_parts);
	 
	// Construct the string to sign
	$url_string = implode("&", $url_parts);
	$string_to_sign = "GET\necs.amazonaws.com\n/onca/xml\n" . $url_string;

	// Sign the request
	$signature = hash_hmac("sha256", $string_to_sign, get_aws_secret_access_key(), TRUE);
	
	// Base64 encode the signature and make it URL safe
	$signature = urlencode(base64_encode($signature));
	 
	$url = $base_url . '?' . $url_string . "&Signature=" . $signature;
	
	return ($url);
}
?>