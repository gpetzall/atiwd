<?php
/*
 * Author: Gunnar Petzall (UWE no: 10005826) (gpetzall@gmail.com)
 * Created: 2014-01-10
 * Modified: 2014-01-11
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
 * Making array with my own key names:
 * http://stackoverflow.com/questions/15716608/create-custom-keys-in-array-with-php
 * [Accessed 2014-01-11]
 * 
*/

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', true);
ini_set('auto_detect_line_endings', true);

require_once (__DIR__.'/functions/xmlpp.php');


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
		$homicide = 0;
	}
} else {
	$homicide = 0;
}

if (isset($_GET['vwi'])) {
	$violence_with_injury = $_GET['vwi'];
	try // The GET result is a string, but can't be converted if it contains letters.
	{ // This tries as best as possible to make it an integer. If it fails completely, 
		$violence_with_injury = (int) $violence_with_injury; // it tells the code it's invalid.
	}
	catch (Exception $e)
	{
		$violence_with_injury = 0;
	}
} else {
	$violence_with_injury = 0;
}

if (isset($_GET['vwoi'])) {
	$violence_without_injury = $_GET['vwoi'];
	try // The GET result is a string, but can't be converted if it contains letters.
	{ // This tries as best as possible to make it an integer. If it fails completely, 
		$violence_without_injury = (int) $violence_without_injury; // it tells the code it's invalid.
	}
	catch (Exception $e)
	{
		$violence_without_injury = 0;
	}
} else {
	$violence_without_injury = 0;
}


$full_crime_array = array
	(
	'violence_against_the_person'=>$homicide+$violence_with_injury+$violence_without_injury,
	'homicide'=>$homicide,
	'violence_with_injury'=>$violence_with_injury,
	'violence_without_injury'=>$violence_without_injury,
	'sexual_offences'=>0,
	'robbery'=>0,
	'theft_offences'=>0,
	'burglary'=>0,
	'domestic_burglary'=>0,
	'non_domestic_burglary'=>0,
	'vehicle_offences'=>0,
	'theft_from_the_person'=>0,
	'bicycle_theft'=>0,
	'shoplifting'=>0,
	'all_other_theft_offences'=>0,
	'criminal_damage_and_arson'=>0,
	'drug_offences'=>0,
	'possession_of_weapon_offences'=>0,
	'public_order_offences'=>0,
	'miscellaneous_crimes_against_society'=>0,
	'fraud'=>0,
	);

// Configure filename data.
$inputFilename = 'doc/crimes.xml';
$outputFilename	= 'doc/crimes.xml';

// Create a simple xml object.
$xml = simplexml_load_file($inputFilename);


// Check if Wessex already exist!


$region_element = $xml->xpath("/crimes/region[@id='$regi']"); // Finding the region.
$region_element = array_shift($region_element); // Returns the simple XML element.

if ($region_element instanceof SimpleXMLElement) // If a simple xml element was returned (checks if the region is valid).
{
	if ($area != NULL) // If an area was provided.
	{
		//echo "YES - Region \nYES - Area \"" . $area . "\"\n";
		
		switch ($response) // XML/JSON response request.
		{
			case 'xml':
				
				//header("Content-type: text/xml"); 
				//header("Content-type: text/plain"); 
				
				// Add the specified area.
				$new_area = $region_element->addChild('area'); 
				$new_area['id'] = $area;
				$new_area['total'] = (int) $homicide+$violence_with_injury+$violence_without_injury;
				
				// Add crime category so it's available for the foreach loop.
				$victim_based_crime = $new_area->addChild('victim_based_crime');
				
				// Add crime category so it's available for the foreach loop.
				$other_crimes_against_society = $new_area->addChild('other_crimes_against_society');
				
				// Add crime variables so they are available for the foreach loop.
				$new_top_crime;
				$new_crime;
				
				// Populate the full area (simple XML saves it to the main object automatically).
				// The loop first creates a top level crime and any lower level crimes automatically
				// attach themselves to the last higher level crime. This loop could easily be extended
				// to be actually used to update ANY crime or to add more crimes in the code.
				foreach ($full_crime_array as $key=>$crime_total)
				{
					switch ($key)
						{
						// All top-level crimes
						case 'violence_against_the_person':
						case 'sexual_offences':
						case 'robbery':
						case 'theft_offences':
						case 'criminal_damage_and_arson':
							$new_top_crime = $victim_based_crime->addChild('crime');
							$new_top_crime['id'] = $key;
							$new_top_crime['total'] = $crime_total;
							break;
							
						// All mid-level crimes.
						case 'homicide':
						case 'violence_with_injury':
						case 'violence_without_injury':
						case 'burglary':
						case 'vehicle_offences':
						case 'theft_from_the_person':
						case 'bicycle_theft':
						case 'shoplifting':
						case 'all_other_theft_offences':
						case 'possession_of_weapon_offences':
						case 'public_order_offences':
						case 'miscellaneous_crimes_against_society':
						case 'fraud':
							$new_crime = $new_top_crime->addChild('crime');
							$new_crime['id'] = $key;
							$new_crime['total'] = $crime_total;
							break;
							
						// All bottom-level crimes
						case 'domestic_burglary':
						case 'non_domestic_burglary':
							$new_bot_crime = $new_crime->addChild('crime');
							$new_bot_crime['id'] = $key;
							$new_bot_crime['total'] = $crime_total;
							break;
						
						// First entry of "Other crimes..". Sets "new_top_crime" to use "other_crimes.." instead of "victim_bas.."
						// Otherwise identical to other top-level crimes.
						case 'drug_offences':
							$new_top_crime = $other_crimes_against_society->addChild('crime');
							$new_top_crime['id'] = $key;
							$new_top_crime['total'] = $crime_total;
							break;
							
						default:
							echo "Crime type error: No valid type entered.";
							break;
						}
				} // End of population foreach.
				
				header("Content-type: text/xml"); 
				//header("Content-type: text/plain"); 
				
				
				echo $new_area->asXML();
				
				
				
				
				
				
				
				
				
				// $xml->asXML($outputFilename);
				
				// $xml2 = simplexml_load_file($inputFilename);
				
				// echo $xml2->asXML();
				
				//SimpleXMLElement($xmlString);
				
				// $new_area2 = xmlpp($new_area->asXML());
				
				// $new_area3 = new SimpleXMLElement($new_area2);
				
				// echo $xml;
				
				//echo $new_area->asXML();
				//print_r ($new_area);
				//
				//$xml = simplexml_load_file($inputFilename);
				
				//$new_area_print = xmlpp($xml->asXML());
				//$new_area_print = $xml->asXML();
				
				//echo $new_area_print;
				
				//$new_area_print->asXML($outputFilename);
				
				//var_dump($new_area);
				//echo "<br><br><br>";
				//echo "\n \n \n";
				//var_dump($region_element);
				//echo "<br><br><br>";
				//echo "\n \n \n";
				//var_dump($xml);
				
				//echo 
				
				
				
				
				
				
				
				break; // End of XML block.
			
			
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