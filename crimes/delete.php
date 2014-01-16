<?php
/*
 * Author: Gunnar Petzall (UWE no: 10005826) (gpetzall@gmail.com)
 * Created: 2014-01-15
 * Modified: 2014-01-16
 * 
 * Script made for the Advanced Topics in Web Development (UFCEWT-20-3) at the
 * University of the West of England in the years 2013-2014. This is part B1 course
 * component, dealing with part "2.2.3".
 * 
 * The script deletes an area (most commonly the prescribed "wessex" one) specified
 * by HTTP $_GET in the "crimes.xml" file located at
 * http://www.cems.uwe.ac.uk/~g2-petzall/atwd/crimes/doc/ and display what has been
 * deleted as well as the new england and england_wales calculated totals.
 * 
 * In addition to the assignment specification, this script can delete any area and
 * can also be specified to one particular region (like if there are duplicates).
 *
 * If there is more than one specified area, and the region is not specified, the
 * first area that matches the specification is deleted.
 * 
 * To restore any changes, reset the "crimes.xml" using the "reset.php" script in
 * this folder.
 * 
 * 
 * Pages used as help to make this code:
 * 
 * headline:
 * 
 * [Accessed 2014-01-16]
 * 
*/

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', true);
ini_set('auto_detect_line_endings', true);

// require_once (__DIR__.'/functions/xmlpp.php');


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

$full_crime_array = array
	(
	'violence_against_the_person'=>0,
	'homicide'=>0,
	'violence_with_injury'=>0,
	'violence_without_injury'=>0,
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




// Checking if there is an area with the specified name in the specified region.
$area_element = $xml->xpath("/*/region/area[@id='$area']");

if (!empty($area_element)) // If the return was not empty (checks if the area is valid).
{
	$area_element = array_shift($area_element);// Returns the simple XML element.
	
	if (($response == 'xml') || ($response == 'json')) // Start of XML/JSON.
	{
		
		// Variables for the main calculation loop.
		$total_crimes = 0;
		$other_total = 0;
		$this_region_total = 0;
		foreach($xml->children() as $region) // Main calculation loop for totals.
		{
			$region_total = 0; // Variable for the region totals.
			
			foreach($region->children() as $area_f) // Continue down the rabbit hole... (area foreach)
			{
				foreach($area_f->children() as $crime_type) // Continue down the rabbit hole...
				{
					foreach($crime_type->children() as $crime_top) // Continue down the rabbit hole...
					{
						$region_total += $crime_top->attributes()['total']; // Add up all the crime totals.
					} // End crime_top foreach (national).
				} // End crime_type foreach (national).
				
				// Catch the total for the specified area.
				$other_areas = $area_f->attributes()['id'];
				if ($other_areas = $area)
				{
					$this_area_total = $region_total;
				}
				
			} // End area foreach (national).
			
			$total_crimes += $region_total;
			$other_regions = $region->attributes()['id']; // Other regions than the total count.
			
			switch ($other_regions) // Save/calculate the current region's and non-English's totals.
			{
				case $regi:
					$this_region_total = $region_total;
					break;
				case 'wales':
				case 'british_transport_police':
				case 'action_fraud':
					$other_total += $region_total;
					break;
				default:
					break;
			} // End of non-English loop.
		} // End of main calculation loop of each region.
		
		
		if ($response == 'xml') // Start of XML block.
		{
			// header("Content-type: text/xml"); // Proper encoding.
			
			// Create a new DOM document with pretty formatting.
			$doc = new DomDocument();
			$doc->formatOutput = true;
			
			// Add a root node to the document called response.
			$node = $doc->createElement('response');
			$node->setAttribute('timestamp',time()); // Add 'nix timestamp.
			$root = $doc->appendChild($node); // Add it to the DOM.
			
			// Add crimes.
			$node = $doc->createElement('crimes');
			$node->setAttribute('year',"6-2013"); // Set year attribute.
			$crimes = $root->appendChild($node); // Add it to the response.
			
			// An if here to add region, if specified.
			
			// Add the area
			$node = $doc->createElement('area'); // Create the area.
			$node->setAttribute('id',$area); // Set name attribute. NOTE: *without* ucwords.
			$node->setAttribute('total',$this_area_total); // Set total.
			$area_x = $crimes->appendChild($node); // Add it (AREA for Xml message).
			
			// An if here to whether the area is added to crimes or region.
			
			
			foreach ($full_crime_array as $key=>$value)
			{
				$crime_f = $xml->xpath("/*/region/area[@id='$area']//crime[@id='$key']");
				$crime_f = array_shift($crime_f); // Returns the simple XML element.
				
				$full_crime_array[$key] = (int) $crime_f['total'];
			}
			
			
			
			
			header("Content-type: text/plain");
			// Display message.
			
			print_r ($full_crime_array);
			
			
			echo $doc->saveXML();
			
			
			
		} // End of XML block.
		
		
		
	} // End if XML/JSON.
	

/*
 
 - Move the crime totals into the full crime array
 - Delete the entry
 - display all crimes where the total is not 0.
 - Specify with region. (a small if in the start of how to determine area?)
 - Make sure it's IN the db.
 
 - Add all crimes to the message
 - calculate england and england_wales totals
 - display message
 
*/





} // End of if the area exists.
else // If the area doesn't exist.
{
	echo 'No such area in the database.';
	
}









































?>