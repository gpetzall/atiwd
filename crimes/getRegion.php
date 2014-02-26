<?php
/* 
 * File: "~g2-petzall/atwd/crimes/getRegion.php"
 * 
 * Author: Gunnar Petzall (UWE no: 10005826) (gpetzall@gmail.com)
 * Created: 2014-01-08
 * Modified: 2014-01-20
 * 
 * Script made for the Advanced Topics in Web Development (UFCEWT-20-3) at the
 * University of the West of England in the years 2013-2014. This is part B1 course
 * component, dealing with part "2.1.2".
 * 
 * The script shows specified regions from the same source as 2.1.1 ("get.php" at this 
 * location), returning the data as XML and Json.
 * 
 * 
 * Pages used as help to make this code:
 * 
 * Time and timestamps:
 * http://uk1.php.net/time
 * http://www.w3schools.com/php/func_date_strtotime.asp
 * [Accessed on 2014-01-08]
 * 
 * Grabbing particular XML element matching selection (XPATH):
 * http://stackoverflow.com/questions/992450/simplexml-selecting-elements-which-have-a-certain-attribute-value
 * http://stackoverflow.com/questions/12145639/find-element-with-attribute-simplexml-xpath
 * [Accessed on 2014-01-08] 
 * 
 * Converting array to simple XML (and only returning the first!):
 * http://uk1.php.net/array_shift
 * [Accessed on 2014-01-08]
 * 
*/

// Run configuration.
require_once (__DIR__ .'/includes/config.php');


// Validate GET information.
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

// Create a simple xml object for easy reading.
$xml = simplexml_load_file($inputFilename);



$region_element = $xml->xpath("/crimes/region[@id='$regi']"); // A little bit of XPATH grabbing all elements with GET's id value.
$region_element = array_shift($region_element); // Returns the simple xml element.

if ($region_element instanceof SimpleXMLElement) // If a simple xml element was returned (checks if the region is valid).
{
	switch ($response) // Depending on the data response request.
	{
		case ('xml'): // Start of XML block.
			header("Content-type: text/xml"); // To display correctly.
			
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
			$node = $doc->createElement('region');
			$node->setAttribute('id',ucwords(str_replace('_', ' ',$regi))); // Set name attribute.
			$region = $crimes->appendChild($node); // Add it to the response.
			
			$region_counter = 0; // To add up area totals.
			
			foreach ($region_element->children() as $area)
			{
				$node = $doc->createElement('area');
				$node->setAttribute('id',ucwords(str_replace('_', ' ', (string) $area->attributes()['id'])));
				// Simple XML element with id attribute to string, change characters and add.
				
				$area_counter = 0; // Variable for the region totals.
				
				foreach($area->children() as $crime_type) // Continue down the rabbit hole...
				{
					foreach($crime_type->children() as $crime_top) // Continue down the rabbit hole...
					{
						$area_counter += $crime_top->attributes()['total']; // Add up all the crime totals.
					} // End crime_top foreach.
				} // End crime_type foreach.
				
				$node->setAttribute('total',$area_counter); // Simple XML element.
				$region->appendChild($node); // Add it all to the DOM.
				
				$region_counter += $area_counter; // Add up areas' totals for the region.
				
			} // End XML foreach.
			
			$region->setAttribute('total',$region_counter);
			
			echo $doc->saveXML();
			
			break; // End of XML block.
		
		case ('json'): // Start of JSON block.
			header("Content-type: application/json"); // So that my Firefox "JSONview" add-on will display it properly.
			
			$json = array(); // Regular array.
			$json['response']['timestamp']=time(); // Create the first array item and its child with a name of timestamp and value of time().
			$json['response']['crimes']['year']='6-2013'; // Create a second child of response with a child named year and value "6-2013".
						
			$json['response']['crimes']['region']['id']=ucwords(str_replace('_', ' ',$regi)); // Create a child of crimes named region.
			
			
			
			$area_count = 0; // To make the loop make new children instead of overwriting itself.
			//$region_total = 0; // Looping a total out of the iterations.
			
			$json['response']['crimes']['region']['total']=0; // Places the total attribute before the other JSON elements.
			
			foreach($region_element->children() as $area) // Similar to the XML loop.
			{
					$json['response']['crimes']['region']['area'][$area_count]['id']=(string)ucwords(str_replace('_', ' ',$area->attributes()['id']));
					// Create region; put multiple numbered 'area' entries in region; which each hold one "id" that is the name of the XML-attribute pulled (same way as XML code).
					
					$total_counter = 0; // Loop identical to XML loop.
					
					foreach($area->children() as $crime_type) // Continue down the rabbit hole...
					{
						foreach($crime_type->children() as $crime_top) // Continue down the rabbit hole...
						{
							$total_counter += $crime_top->attributes()['total']; // Add up all the crime totals.
						} // End crime_top (all other).
					} // End crime_type foreach (all other).
					
				$json['response']['crimes']['region']['area'][$area_count]['total']=(int) $total_counter; // Adding the total value for each area.
				$json['response']['crimes']['region']['total'] += $total_counter;
				$area_count++;
				
			} // End json foreach.
			
			
			
			
			echo json_encode($json); //Json!
			
			break; // End of JSON block;
			
		default: // No XML or JSON in URL.
			?><p>This page only works with the right URLs. Try making an <a href="getRegion.php?response=xml&regi=north_west">North West XML</a> or
			<a href="getRegion.php?response=json&regi=north_west">North West JSON</a> request instead.</p><?php
		
		break;
		
	} // End switch for XML/JSON selection.
}
else // No match for Region.
{
	?><p>This page only works with the right URLs. Try making an <a href="getRegion.php?response=xml&regi=north_west">North West XML</a> or
	<a href="getRegion.php?response=json&regi=north_west">North West JSON</a> request instead.</p><?php
}

?>