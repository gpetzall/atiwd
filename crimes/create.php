<?php
/*
 * Author: Gunnar Petzall (UWE no: 10005826) (gpetzall@gmail.com)
 * Created: 2014-01-10
 * Modified: 2014-01-10
 * 
 * Script made for the Advanced Topics in Web Development (UFCEWT-20-3) at the
 * University of the West of England in the years 2013-2014. This is part B1 course
 * component, dealing with part "2.2.2".
 * 
 * The script creates a new *area* within a specified *region* with data for three
 * types of crimes (Homicide, Violence with injury, Violence without injury) in the
 * "crimes.xml" file at http://www.cems.uwe.ac.uk/~g2-petzall/atwd/crimes/doc/
 * and display the new crime totals for each crime, the area and the region.
 * Unspecified data is assumed to be 0.
 * 
 * 
 * Pages used as help to make this code:
 * 
 * headline:
 * 
*/

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', true);
ini_set('auto_detect_line_endings', true);


// Validate GET information. ((Can possibly make this into a loop/function that explodes the GET array and handles each.))
if (isset($_GET['response'])) { // XML or JSON.
	$response = $_GET['response'];
} else {
	$response = NULL;
}

if (isset($_GET['regi'])) { // Region name.
	$regi = $_GET['regi'];
} else {
	$regi = NULL;
}

if (isset($_GET['area'])) { // Area name.
	$area = $_GET['area'];
} else {
	$area = NULL;
}

if (isset($_GET['hom'])) {
	$homicide = $_GET['hom'];
	try // The GET result is a string, but can't be converted if it contains letters.
	{ // This tries as best as possible to make it an integer. If it fails completely, 
		$homicide = (int) $homicide; // it tells the code it's invalid.
	}
	catch (Exception $e)
	{
		$homicide = NULL;
	}
} else {
	$homicide = NULL;
}

if (isset($_GET['vwi'])) {
	$violence_with_injury = $_GET['vwi'];
	try // The GET result is a string, but can't be converted if it contains letters.
	{ // This tries as best as possible to make it an integer. If it fails completely, 
		$violence_with_injury = (int) $violence_with_injury; // it tells the code it's invalid.
	}
	catch (Exception $e)
	{
		$violence_with_injury = NULL;
	}
} else {
	$violence_with_injury = NULL;
}

if (isset($_GET['vwoi'])) {
	$violence_without_injury = $_GET['vwoi'];
	try // The GET result is a string, but can't be converted if it contains letters.
	{ // This tries as best as possible to make it an integer. If it fails completely, 
		$violence_without_injury = (int) $violence_without_injury; // it tells the code it's invalid.
	}
	catch (Exception $e)
	{
		$violence_without_injury = NULL;
	}
} else {
	$violence_without_injury = NULL;
}

$update_array = array ($homicide,$violence_with_injury,$violence_without_injury); // For use later.

// Configure filename data.
$inputFilename = 'doc/crimes.xml';
$outputFilename	= 'doc/crimes.xml';


header("Content-type: text/plain"); 


// Create a simple xml object.
$xml = simplexml_load_file($inputFilename);


$region_element = $xml->xpath("/crimes/region[@id='$regi']"); // Finding the region.
$region_element = array_shift($region_element); // Returns the simple XML element.

if ($region_element instanceof SimpleXMLElement) // If a simle xml element was returned (checks if the region is valid).
{
	if ($area != NULL) // If an area was provided.
	{
		echo "YES - Region \nYES - Area \"" . $area . "\"\n";
		
		switch ($response) // XML/JSON response request.
		{
			case 'xml':
				$region_element->addChild('area')['id'] = $area;
				
				
				print_r($region_element);
				
				
				break;
			
			
		} // End of XML/JSON switch
	
	}
	else // If no area was provided.
	{
		echo "YES - Region \nNO - Area \n";
	} // End of area if
	
}
else // If no valid region was provided.
{
	echo "NO - Region \n";
} // End of region if.




















/*

- Don't update area or region totals.

- Find specified region
- Create area in that region of specified name
- Create a crime for each crime that exists in that area

- Calculate the total value of specified crimes
- Update the total value for the crimes
- Add the combined value to violence_against_the_person

- Calculate the total crimes of each region in one variable
- Remember the Wales total separately
- Add the full total (minus wales) to DOM
- Add the wales total to DOM

- Display message.

*/















?>