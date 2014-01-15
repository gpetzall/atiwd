<?php
/*
 * Author: Gunnar Petzall (UWE no: 10005826) (gpetzall@gmail.com)
 * Created: 2014-01-10
 * Modified: 2014-01-15
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
 * How to DELETE a simple XML element (took HOURS!):
 * http://stackoverflow.com/questions/262351/remove-a-child-with-a-specific-attribute-in-simplexml-for-php
 * [Accessed 2014-01-15]
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

$created_total = $homicide+$violence_with_injury+$violence_without_injury;

$full_crime_array = array
	(
	'violence_against_the_person'=>$created_total,
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


$region_element = $xml->xpath("/*/region[@id='$regi']"); // Finding the region.

if (!empty($region_element)) // If the return was not empty (checks if the region is valid).
{
	$region_element = array_shift($region_element);// Returns the simple XML element.
	
	if ($area != NULL) // If an area was provided.
	{
		if (($response == 'xml') || ($response == 'json'))
		{
			//echo "YES - Region \nYES - Area \"" . $area . "\"\n";
			
			// Checking if there already is an area with the specified name in the specified region.
			$area_element = $xml->xpath("/*/region[@id='$regi']/area[@id='$area']"); 
			$area_element_exist = FALSE;
			
			if (!empty($area_element)) // Is there already an area with specified name?
			{
				$area_element = array_shift($area_element); // Turn the area into simple xml.
				unset($area_element[0], $area_element);
			}
			
			// Add the specified area.
			$new_area = $region_element->addChild('area');
			$new_area['id'] = $area;
			$new_area['total'] = (int) $created_total;
			
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
			
			// The simple XML object retains changes made to child elements.
			// The drawback is that it doesn't format it prettily with indentations, but it works!
			$xml->asXML($outputFilename);
			
			
			// Variables for the main calculation loop.
			$total_crimes = 0;
			$other_total = 0;
			$this_region_total = 0;
			foreach($xml->children() as $region) // Main calculation loop for totals.
			{
				$region_total = 0; // Variable for the region totals.
				
				foreach($region->children() as $area_f) // Continue down the rabbit hole...
				{
					foreach($area_f->children() as $crime_type) // Continue down the rabbit hole...
					{
						foreach($crime_type->children() as $crime_top) // Continue down the rabbit hole...
						{
							$region_total += $crime_top->attributes()['total']; // Add up all the crime totals.
						} // End crime_top foreach (national).
					} // End crime_type foreach (national).
				} // End area foreach (national).
				
				$total_crimes += $region_total;
				$other_regions = $region->attributes()['id'];
				
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
			} // End of main calculation loop.
			
			
			
			if ($response == 'xml')
			{
				header("Content-type: text/xml"); // Proper encoding.
				
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
				
				// Add the region.
				$node = $doc->createElement('region'); // Create the region.
				$node->setAttribute('id',ucwords(str_replace('_', ' ',$regi))); // Set name attribute. NOTE: *with* ucwords.
				$node->setAttribute('total',$this_region_total); // Set the updated total number.
				$region_x = $crimes->appendChild($node); // Add all of it to the response.
				
				// Add the area
				$node = $doc->createElement('area'); // Create the area.
				$node->setAttribute('id',$area); // Set name attribute. NOTE: *without* ucwords.
				$node->setAttribute('total',$created_total); // Set total.
				$area_x = $region_x->appendChild($node); // Add it.
				
				// Add the new crime recordings.
				$recorded_counter = 1;
				foreach ($full_crime_array as $key=>$crime_total)
				{
					if (($recorded_counter >= 2) && ($recorded_counter <= 4)) // Add "&& ($crime_total >= 1)" here to hide non-recorded crimes. Added the 0 so it matches the spec.
					{
						$node = $doc->createElement('recorded'); // Create the recorded item.
						$node->setAttribute('id',ucwords(str_replace('_', ' ',$key))); // Set name attribute. NOTE: *with* ucwords.
						$node->setAttribute('total',$crime_total); // Set name attribute. NOTE: *with* ucwords.
						$recorded = $area_x->appendChild($node); // Add it.
					}
					$recorded_counter ++;
				}
				
				// Add England
				$node = $doc->createElement('england'); // Create england.
				$node->setAttribute('total',($total_crimes - $other_total)); // Set total. 
				$england = $crimes->appendChild($node); // Add it.
				
				// Add England and Wales
				$node = $doc->createElement('england_wales'); // Create england_wales.
				$node->setAttribute('total',$total_crimes); // Set total. 
				$england = $crimes->appendChild($node); // Add it.
				
				// Display message.
				echo $doc->saveXML();
				
				
			} // End of XML block.
			else
			{ // Start of JSON block (can only be JSON).
				
				header("Content-type: application/json"); // So that my Firefox "JSONview" add-on will display it properly.
				
				$json = array(); // Regular array.
				$json['response']['timestamp']=time(); // Create the first array item and its child with a name of timestamp and value of time().
				$json['response']['crimes']['year']='6-2013'; // Create a second child of response with a child named year and value "6-2013".
				
				$json['response']['crimes']['region']['id']=ucwords(str_replace('_', ' ',$regi)); // Create a child of crimes named region. NOTE: using ucwords.
				$json['response']['crimes']['region']['total']=$this_region_total; // Add total.
				
				$json['response']['crimes']['region']['area']['id']=$area; // Create area. NOTE: *not* using ucwords.
				$json['response']['crimes']['region']['area']['total']=$created_total; // Add total.
				
				// Add the new crime recordings.
				$recorded_counter = 1;
				foreach ($full_crime_array as $key=>$crime_total)
				{
					if (($recorded_counter >= 2) && ($recorded_counter <= 4)) // Add "&& ($crime_total >= 1)" here to hide non-recorded crimes. Added the 0 so it matches the spec.
					{
						$json['response']['crimes']['region']['area']['recorded'][$recorded_counter]['id']=ucwords(str_replace('_', ' ',$key)); // Set name attribute. NOTE: *with* ucwords.
						$json['response']['crimes']['region']['area']['recorded'][$recorded_counter]['total']=$crime_total; // Add total.
					}
					$recorded_counter ++;
				}
				
				$json['response']['crimes']['england']['total']=($total_crimes - $other_total); // Add England total.
				$json['response']['crimes']['england_wales']['total']=$total_crimes; // Add England & Wales total.
				
				echo json_encode($json); //Json!
			} // End of JSON block.
			

			
			
		} // Is there xml or json?
		else // If no xml/json was specified.
		{
			echo 'No XML or JSON request';
		}
		
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