<?php

if ( ! function_exists('createSlug'))
{
	function createSlug($text)
	{
		// replace non letter or digits by -
		$text = preg_replace('~[^\pL\d]+~u', '-', $text);

	 	 // transliterate
	  	$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

	  	// remove unwanted characters
	  	$text = preg_replace('~[^-\w]+~', '', $text);

	  	// trim
	  	$text = trim($text, '-');

	 	 // remove duplicate -
	  	$text = preg_replace('~-+~', '-', $text);

	  	// lowercase
	 	 $text = strtolower($text);

	  	if (empty($text)) {
	    	return 'n-a';
	 	}
	 	
	 	$text .= '-'.substr(md5(date('Y-m-d H:i:s')), 0, rand(4,9));

	  return $text;
	}
}