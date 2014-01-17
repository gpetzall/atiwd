<?php
/*
 * Author: Gunnar Petzall (UWE no: 10005826) (gpetzall@gmail.com)
 * Created: 2014-01-15
 * Modified: 2014-01-17
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
 * If there is more than one specified area and the region is not specified the
 * script deletes the first area that matches the specification is deleted.
 * 
 * To restore any changes, reset the "crimes.xml" using the "reset.php" script in
 * this folder.
 * 
 * 
 * Pages used as help to make this code:
 * 
 * Another way to delete: (Wasn't used in the end)
 * http://www.kavoir.com/2008/12/how-to-delete-remove-nodes-in-simplexml.html
 * [Accessed 2014-01-17]
 * 
*/

// Run configuration.
require_once ('/includes/config.php');

// Create a simple xml object.
$xml = simplexml_load_file($inputFilename);


// Validate GET information. ((Can possibly make this into a loop/function that explodes the GET array and handles each.))
if (isset($_GET['response'])) { // XML or JSON.
	$response = $_GET['response'];
} else {
	$response = NULL;
}

$regi_correct = TRUE;
if (isset($_GET['regi'])) { // Region name.
	$regi = $_GET['regi'];
	$region_element = $xml->xpath("//region[@id='$regi']");
	if (empty($region_element)) // If it doesn't exist.
	{
		$regi = NULL; // Not a valid region.
		$regi_correct = FALSE;
		echo 'Region does not exist.';
	}
} else {
	$regi = NULL;
}
if (isset($_GET['area'])) { // Area name.
	$area = $_GET['area'];
} else {
	$area = NULL;
}

// Setting the crimes.
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

// Different $area_element depending on if region was provided.
if ($regi != NULL)
{
	$area_element = $xml->xpath("/*/region[@id='$regi']/area[@id='$area']");// Checking if there is an area with the specified name in the specified region.
}
else
{
	$area_element = $xml->xpath("/*/region/area[@id='$area']");// Checking if there is an area with the specified name (regardless of region).
}

// If the return was not empty (checks if the area is valid). Starts main if.
if (!empty($area_element) && $regi_correct == TRUE)
{
	$area_element = array_shift($area_element); // Returns the *first* simple XML element.
	
	if (($response == 'xml') || ($response == 'json')) // Start of XML/JSON.
	{
		// Adding values to each crime of the specified area.
		foreach ($full_crime_array as $key=>$value)
		{
			$crime_f;
			
			if ($regi != NULL) // If (existing) region was provided.
			{
				$crime_f = $xml->xpath("//region[@id='$regi']/area[@id='$area']//crime[@id='$key']");
			}
			else // If region was not provided.
			{
				$crime_f = $xml->xpath("//region/area[@id='$area']//crime[@id='$key']");
			}
			
			$crime_f = array_shift($crime_f); // Returns the *first* simple XML element.
			$full_crime_array[$key] = (int) $crime_f['total'];
		}
		
		// Variables for the main calculation loop.
		$total_crimes = 0;
		$other_total = 0;
		$this_region_total = 0;
		foreach($xml->children() as $region) // Main calculation loop for totals.
		{
			$region_total = 0; // Variable for the region totals.
			
			foreach($region->children() as $area_f) // Continue down the rabbit hole... (area foreach)
			{
				$area_total = 0; // Variable for the area totals.
				
				foreach($area_f->children() as $crime_type) // Continue down the rabbit hole...
				{
					foreach($crime_type->children() as $crime_top) // Continue down the rabbit hole...
					{
						$region_total += $crime_top->attributes()['total']; // Add up all the crime totals.
						$area_total += $crime_top->attributes()['total']; // Add up all the crime totals.
					} // End crime_top foreach (national).
				} // End crime_type foreach (national).
				
				// Catch the total for the specified area.
				$other_areas = $area_f->attributes()['id'];
				if ($other_areas == $area)
				{
					$this_area_total = $area_total;
				}
				
			} // End area foreach (national).
			
			$total_crimes += $region_total;
			$other_regions = $region->attributes()['id']; // Other regions than the total count.
			
			switch ($other_regions) // Save/calculate the current region's and non-English's totals.
			{
				case $regi:
					$this_region_total = $region_total; // Adds it in case it's needed.
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
			
			if ($regi != NULL)
			{
				// Add the region
				$node = $doc->createElement('region'); // Create the region.
				$node->setAttribute('id',$regi); // Set name attribute. NOTE: *without* ucwords.
				// $node->setAttribute('deleted',$this_region_total); // Set total for region; if ever needed.
				$region_x = $crimes->appendChild($node); // Add it (REGION for Xml message).
				
				// Add the area
				$node = $doc->createElement('area'); // Create the area.
				$node->setAttribute('id',$area); // Set name attribute. NOTE: *without* ucwords.
				$node->setAttribute('deleted',$this_area_total); // Set total deleted.
				$area_x = $region_x->appendChild($node); // Add it (AREA for Xml message).
			}
			else
			{
				// Add the area
				$node = $doc->createElement('area'); // Create the area.
				$node->setAttribute('id',$area); // Set name attribute. NOTE: *without* ucwords.
				$node->setAttribute('deleted',$this_area_total); // Set total deleted.
				$area_x = $crimes->appendChild($node); // Add it (AREA for Xml message).
			}
			
			// Add any crimes as deleted if they have more than one crime total.
			foreach ($full_crime_array as $key=>$value)
			{
				if ( // 1 or more AND not one of these:
					$value >=1 &&
					$key != 'violence_against_the_person' &&
					$key != 'theft_offences' &&
					$key != 'burglary'
					)
				{
					$node = $doc->createElement('deleted'); // Create the deleted crime.
					$node->setAttribute('id',ucfirst(str_replace('_', ' ',$key))); // Set name attribute. (ucfirst is better for crimes)
					$node->setAttribute('total',$value); // Set total deleted.
					$deleted = $area_x->appendChild($node); // Add it (AREA for Xml message).
				}
			}
			
			// Add England
			$node = $doc->createElement('england'); // Create england.
			$node->setAttribute('total',($total_crimes - $other_total)); // Set total. 
			$england = $crimes->appendChild($node); // Add it.
			
			// Add England and Wales
			$node = $doc->createElement('england_wales'); // Create england_wales.
			$node->setAttribute('total',$total_crimes); // Set total. 
			$england = $crimes->appendChild($node); // Add it.
			
			// Display message. Correct formatting.
			header("Content-type: text/xml");
			echo $doc->saveXML();
			
		} // End of XML block.
		else
		{ // Start of JSON block.
			
			$json = array(); // Regular array.
			$json['response']['timestamp']=time(); // Create the first array item and its child with a name of timestamp and value of time().
			$json['response']['crimes']['year']='6-2013'; // Create a second child of response with a child named year and value "6-2013".
			
			if ($regi != NULL)
			{
				// Add the region
				$json['response']['crimes']['region']['id']=str_replace('_', ' ',$regi); // Create a child of crimes named region. NOTE: *without* ucwords.
				// $json['response']['crimes']['region']['total']=$this_region_total; // Add total if ever needed.
			
				// Add the area
				$json['response']['crimes']['region']['area']['region']['id']=$area; // Create area. NOTE: *not* using ucwords.
				$json['response']['crimes']['region']['area']['region']['total']=$this_area_total; // Add total.
			}
			else
			{
				// Add the area
				$json['response']['crimes']['region']['area']['id']=$area; // Create area. NOTE: *not* using ucwords.
				$json['response']['crimes']['region']['area']['total']=$this_area_total; // Add total.
				
			}
			
			// Add any crimes as deleted if they have more than one crime total.
			$recorded_counter = 1;
			foreach ($full_crime_array as $key=>$value)
			{
				if ( // 1 or more AND not one of these:
					$value >=1 &&
					$key != 'violence_against_the_person' &&
					$key != 'theft_offences' &&
					$key != 'burglary'
					)
				{
					$json['response']['crimes']['region']['area']['deleted'][$recorded_counter]['id']=ucfirst(str_replace('_', ' ',$key)); // Set name attribute.
					$json['response']['crimes']['region']['area']['deleted'][$recorded_counter]['total']=ucfirst(str_replace('_', ' ',$value)); // Set total deleted attribute.
				}
				$recorded_counter ++;
			}
			
			$json['response']['crimes']['england']['total']=($total_crimes - $other_total); // Add England total.
			$json['response']['crimes']['england_wales']['total']=$total_crimes; // Add England & Wales total.
			
			// Display message.
			header("Content-type: application/json"); // So that my Firefox "JSONview" add-on will display it properly.
			echo json_encode($json);
			
		} // End of JSON block.
		
		// Delete the specified area.
		unset($area_element[0],$area_element);
		$xml->asXML($outputFilename);
	} // End if XML/JSON.

} // End of if the area exists.
else // If the area doesn't exist.
{
	echo 'No such area in the database (or missing in the specified region).';
}
?>