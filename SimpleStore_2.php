<?php
define('AWS_ACCESS_KEY_ID', 'AKIAJGQHTY3M674DX2NQ');
define('AWS_SECRET_ACCESS_KEY', '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');
define('AMAZON_ASSOC_TAG', 'gooenogif-20');

function amazon_get_signed_url($searchTerm) {
	$base_url = "http://ecs.amazonaws.com/onca/xml";
	$params = array(
		'AWSAccessKeyId' => AWS_ACCESS_KEY_ID,
		'AssociateTag' => AMAZON_ASSOC_TAG,
		'Version' => "2010-11-01",
		'Operation' => "ItemSearch",
		'Service' => "AWSECommerceService",
		'Availability' => "Available",
		'Condition' => "All",
		'Operation' => "ItemSearch",
		'SearchIndex' => 'Music', //Change search index if required, you can also accept it as a parameter for the current method like $searchTerm
		'Keywords' => $searchTerm);

		//'ItemPage'=>"1",
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
	$signature = hash_hmac("sha256", $string_to_sign, AWS_SECRET_ACCESS_KEY, TRUE);
	
	// Base64 encode the signature and make it URL safe
	$signature = urlencode(base64_encode($signature));
	 
	$url = $base_url . '?' . $url_string . "&Signature=" . $signature;
	
	return ($url);
}


$searchTerm = $_REQUEST['searchTerm'];
$url = amazon_get_signed_url($searchTerm);

//Below is just sample request dispatch and response parsing for example purposes.

$response = file_get_contents($url);
$parsed_xml = simplexml_load_string($response);

$result = array();

foreach($parsed_xml->Items->Item as $current){
	if($current->ItemAttributes->ProductGroup == 'Music') {
		$item = array(
			'track_id' => implode(((array)$current->ASIN), ', '),
			'source' => 1,
			'track_name' => implode(((array)$current->ItemAttributes->Title), ', '),
			'track_url' => implode(((array)$current->DetailPageURL), ', '),
			'artist_name' => implode(((array)$current->ItemAttributes->Artist), ', '),
			'artist_url' => '',
			'collection_name' => '',
			'collection_url' => '',
			'genre' => '',
			);
		
		$result[] = $item;
	}
}
?>
