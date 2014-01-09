<?php
/*
 * Author: Gunnar Petzall (UWE no: 10005826) (gpetzall@gmail.com)
 * Created: 2014-01-08
 * Modified: 2014-01-08
 * 
 * Script made for the Advanced Topics in Web Development (UFCEWT-20-3) at the
 * University of the West of England in the years 2013-2014. This is part B1 course
 * component, dealing with part "2.1.2".
 * 
 * The script shows specific regions from the same source as 2.1.1 ("get.php" at this 
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
 * 
*/

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', true);
ini_set('auto_detect_line_endings', true);

// header("Content-type: text/plain"); // Easier to read.

// Validate GET information.
if (isset($_GET['response'])) {
	$response = $_GET['response'];
} else {
	$response = NULL;
}

if (isset($_GET['regi'])) {
	$regi = $_GET['regi'];
} else {
	$regi = NULL;
}

// Configure filename data.
$inputFilename = 'doc/crimes.xml'; 

// Create a simple xml object for easy reading.
$xml = simplexml_load_file($inputFilename);



$region_element = $xml->xpath("/crimes/region[@id='$regi']"); // A little bit of XPATH grabbing all elements with GET's id value.
$region_element = array_shift($region_element); // Returns the simple xml element.

if ($region_element instanceof SimpleXMLElement) // If a simle xml element was returned
{
	// echo " *Valid Region Request* ";
	
	switch ($response)
	{
		case ('xml'): // Start of XML block.
			// echo " *Valid XML Request* ";
			
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
			
			foreach ($region_element->children() as $area)
			{
				$node = $doc->createElement('area');
				$node->setAttribute('id',ucwords(str_replace('_', ' ', (string) $area->attributes()['id'])));
				// Simple XML element with id attribute to string, change characters and add.
				
				$total_counter = 0; // Variable for the region totals.
				
				foreach($area->children() as $crime_type) // Continue down the rabbit hole...
				{
					foreach($crime_type->children() as $crime_top) // Continue down the rabbit hole...
					{
						$total_counter += $crime_top->attributes()['total']; // Add up all the crime totals.
					} // End crime_top foreach.
				} // End crime_type foreach.
				
				$node->setAttribute('total',$total_counter); // Simple XML element.
					
				$region->appendChild($node); // Add it all to the DOM.
				
			} // End XML foreach.
			
			echo $doc->saveXML();
			
			break; // End of XML block.
		
		case ('json'): // Start of JSON block.
			// echo " *Valid JSON Request* ";
			
			
			
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
			?><p>This page only works with the right URLs. Try making an <a href="get.php?response=xml&north_west">North West XML</a> or
			<a href="get.php?response=json&north_west">North West JSON</a> request instead.</p><?php
		
		break;
		
	} // End switch for XML/JSON selection.
}
else // No match for Region.
{
	echo " No match for Region. ";
}

?>