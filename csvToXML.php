<?php
/*
 * Author: Gunnar Petzall (10005826)
 * 
 * Based from an example by Michael Parkin
 * http://stackoverflow.com/questions/4852796/php-script-to-convert-csv-files-to-xml
 * [Accessed on 2013-12-16]
*/

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', true);
ini_set('auto_detect_line_endings', true);

$inputFilename	= 'input2.csv'; //edited for simplicity
$outputFilename	= 'output.xml';

// Open csv to read
$inputFile = fopen($inputFilename, 'rt');

// Get the headers of the file
$headers = fgetcsv($inputFile);
//print_r($headers);
//exit;

// Create a new dom document with pretty formatting
$doc = new DomDocument();
$doc->formatOutput = true;

// Add a root node to the document
$root = $doc->createElement('rows');
$root = $doc->appendChild($root);

// Loop through each row creating a <row> node with the correct data
while (($row = fgetcsv($inputFile,1024,",")) !== FALSE)
{
	$container = $doc->createElement('row');
	
	foreach ($headers as $i => $header)
	{
		$child = $doc->createElement(str_replace(' ', '_', $header));
		$child = $container->appendChild($child);
		$value = $doc->createTextNode($row[$i]);
		$value = $child->appendChild($value);
	}

	$root->appendChild($container);
}

echo $doc->saveXML();

?>