<?php

function humanFileSize($size)
{
    if (!$size) {
        return "";
    } elseif (($size >= 1 << 30)) {
        return number_format($size / (1 << 30), 2) . "GB";
    } elseif (($size >= 1 << 20)) {
        return number_format($size / (1 << 20), 2) . "MB";
    } elseif (($size >= 1 << 10)) {
        return number_format($size / (1 << 10),2) . "kB";
    } else {
    return number_format($size) . "B";
    }
};

function listAndLinkFiles($thepath,$ap, $tokenurl, $thumbsz)
{
	print("Listing $thepath\n <br><ul>");
	// list items in the root
	$copysite = 'https://copy.com/';
	$copythumbsite = $copysite . 'thumbs_public/';
	
	$items = $ap->listPath($thepath);
	// the url will begin with $copysite . $tokenurl . $child->{"path"}
	foreach ($items as $child) {
		echo '<li>';
		printf("%5.5s of %10.10s : ", $child->{"type"}, humanFileSize($child->{"size"}));		
		// a directory would have : revision == 0 
		$isFile = count($child->{"revisions"}) > 0 ;

		$extf = substr($child->{"path"}, -3, 3);
		$isPicture = strcasecmp( $extf, 'jpg') == 0;
		$isPicture = $isPicture or ( strcasecmp( $extf, 'png') == 0 ); 
		$isPicture = $isPicture or ( strcasecmp( $extf, 'gif') == 0 );
		
		if ($isPicture)
			echo '<img src="' . $copythumbsite . $tokenurl . $child->{"path"} . '?revision=0&size=' . $thumbsz . '" /> ';
		// https://copy.com/thumbs_public/umak4qRHcvKm1D30/public/DSCN5695.JPG?revision=0&size=128
		
		if ($isFile)
			echo '<a href="' . $copysite . $tokenurl . ($child->{"path"}) .'">'; 
		echo basename($child->{"path"}) . PHP_EOL;
		if ($isFile) 
			echo '</a>';
		
		if (count($child->{"revisions"}) > 0) {
			echo ' : ' ;
			$rev = count($child->{"revisions"});
			printf("%d" , $rev );
			echo (($rev > 1) ? ' revisions' : ' revision' );
			echo PHP_EOL;
		} else {
			echo '<BR/> this is a directory ' ;			
		};	
		echo '</li>' . PHP_EOL;
	};
	echo '</ul>';
};

function listFiles($thepath,$ap)
{
	print("Listing $thepath\n <br><ul>");
	// list items in the root
	$items = $ap->listPath($thepath);

	foreach ($items as $child) {
		echo '<li>';
		printf("%5.5s | %10.10s | ", $child->{"type"}, humanFileSize($child->{"size"}));
		echo basename($child->{"path"}) . PHP_EOL;
		if (count($child->{"revisions"}) > 0) {
			echo '<BR/> ' ;
			$rev = count($child->{"revisions"});
			printf("%d" , $rev );
			echo (($rev > 1) ? ' revisions' : ' revision' ) . PHP_EOL;
		} else {
			echo '<BR/> directory ' ;			
		};	
		echo '</li>' . PHP_EOL;
	};
	echo '</ul>';
};

require 'copycredits.php';
$cloudpath = '/public';
// THIS COMES from the web interface of copy : share the public url !
$sharedTokenUrl = 'umak4qRHcvKm1D30';

require 'vendor/autoload.php';
// Create a cloud api connection to copy
$copy = new \Barracuda\Copy\API($consumerKey, $consumerSecret, $accessToken, $tokenSecret);

listAndLinkFiles($cloudpath, $copy, $sharedTokenUrl, 128);
// listFiles($cloudpath, $copy);
?>
