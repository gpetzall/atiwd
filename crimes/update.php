<?php
/* 
 * File: "~g2-petzall/atwd/crimes/update.php"
 * 
 * Author: Gunnar Petzall (UWE no: 10005826) (gpetzall@gmail.com)
 * Created: 2014-01-09
 * Modified: 2014-02-27
 * 
 * Script made for the Advanced Topics in Web Development (UFCEWT-20-3) at the
 * University of the West of England in the years 2013-2014. This is part B1 course
 * component, dealing with part "2.2.1".
 * 
 * The script updates the total crime value of one region of a specified id in the
 * "crimes.xml" file at http://www.cems.uwe.ac.uk/~g2-petzall/atwd/crimes/doc/
 * with the amount specified in the URL's GET and displays the new data.
 * 
 * 
 * Pages used as help to make this code:
 * 
 * Getting string numbers into integers without throwing errors if it contains letters.
 * http://phptrycatch.blogspot.co.uk/
 * [Accessed on 2014-01-09]
 * 
 * Simple XML writing etc:
 * http://stackoverflow.com/questions/2370631/php-simplexml-how-to-set-attributes
 * [Accessed on 2014-01-09]
 * 
 * XPath issue solution:
 * http://stackoverflow.com/questions/1006283/xpath-select-first-element-with-a-specific-attribute
 * [Accessed on 2014-01-10]
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

if (isset($_GET['update'])) {
	$update = $_GET['update'];
	try // The GET result is a string, but can't be converted if it contains letters.
	{ // This tries as best as possible to make it an integer. If it fails completely, 
		$update = (int) $update; // it tells the code it's invalid.
	}
	catch (Exception $e)
	{
		$update = NULL;
	}
} else {
	$update = NULL;
}

// Create a simple xml object.
$xml = simplexml_load_file($inputFilename);


$region_element = $xml->xpath("/crimes/region[@id='$regi']"); // A little bit of XPATH grabbing all elements with GET's id value.
$region_element = array_shift($region_element); // Returns the simple XML element.
// NOTE: simple XML remembers where it's from later in the code when it is used.

if ($region_element instanceof SimpleXMLElement) // If a simple xml element was returned (checks if the region is valid).
{
	if ($update != NULL)
	{
		switch ($response) // XML/JSON response request.
		{
			case ('xml'): // Start of XML block.
				header("Content-type: text/xml"); // To display correctly. xml
				
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
				$node->setAttribute('id',$regi); // Set name attribute. NOTE: THE CODE DOES NOT CONVERT IT WITH ucwords.
				$region = $crimes->appendChild($node); // Add it to the response.
				
				// Does the region have a total? (This was originally a big problem, see in references above.)
				$total_check = $region_element->xpath("/crimes/region[@id='$regi'][@total]");
				if (empty($total_check)) // No: Calculate the total as "previous" and add the update value as "total".
				{
					$region_total = 0; // Variable for the region totals.
					
					foreach($region_element->children() as $area) // Continue down the rabbit hole...
					{
						foreach($area->children() as $crime_type) // Continue down the rabbit hole...
						{
							foreach($crime_type->children() as $crime_top) // Continue down the rabbit hole...
							{
								$region_total += $crime_top->attributes()['total']; // Add up all the crime totals.
							} // End crime_top foreach (national).
						} // End crime_type foreach (national).
					} // End area foreach (national).
					
					// Display Message.
					$node->setAttribute('previous',$region_total); // Add the total number.
					$node->setAttribute('total',$update); // Add the updated total number.
					$crimes->appendChild($node); // Add it all to the response DOM.
					
					// Actual update in the database.
					$region_element['previous'] = $region_total;
					$region_element['total'] = $update;
				}
				else // Yes: Move the old total and display the new.
				{
					// Display Message.
					$node->setAttribute('previous',(int)$region_element->attributes()['total']); // Get the current total and add it as previous.
					$node->setAttribute('total',$update); // Add the updated total number.
					$crimes->appendChild($node); // Add it all to the DOM.
					
					// Actual update in the database.
					$region_element['previous'] = $region_element['total'];
					$region_element['total'] = $update;
				}
				
				// NOTE: Updating the region element also updates the simple XML document ($xml).
				$xml->asXML($outputFilename);
				
				// Display message.
				echo $doc->saveXML();
				
				break; // End of XML block.
				
			case ('json'):  // Start of JSON block.
				
				header("Content-type: application/json"); // So that my Firefox "JSONview" add-on will display it properly.
				
				$json = array(); // Regular array.
				$json['response']['timestamp']=time(); // Create the first array item and its child with a name of timestamp and value of time().
				$json['response']['crimes']['year']='6-2013'; // Create a second child of response with a child named year and value "6-2013".
				
				$json['response']['crimes']['region']['id']=$regi; // Create a child of crimes named region. NOTE: THE CODE DOES NOT CONVERT IT WITH ucwords.
				
				// Does the region have a total?
				$total_check = $region_element->xpath("/crimes/region[@id='$regi'][@total]");
				if (empty($total_check)) // No: Calculate the total as "previous" and add the update value as "total".
				{
					$region_total = 0; // Variable for the region totals.
					
					foreach($region_element->children() as $area) // Continue down the rabbit hole...
					{
						foreach($area->children() as $crime_type) // Continue down the rabbit hole...
						{
							foreach($crime_type->children() as $crime_top) // Continue down the rabbit hole...
							{
								$region_total += $crime_top->attributes()['total']; // Add up all the crime totals.
							} // End crime_top foreach (national).
						} // End crime_type foreach (national).
					} // End area foreach (national).
					
					
					// Display Message.
					$json['response']['crimes']['region']['previous']=$region_total;
					$json['response']['crimes']['region']['total']=$update;
					echo json_encode($json); //Json!
					
					// Actual update in the database.
					$region_element['previous'] = $region_total;
					$region_element['total'] = $update;
				}
				else // Yes: 
				{
					// Display Message.
					$json['response']['crimes']['region']['previous']=(int)$region_element->attributes()['total'];
					$json['response']['crimes']['region']['total']=$update;
					
					echo json_encode($json); //Json!
					
					// Actual update in the database.
					$region_element['previous'] = $region_element['total'];
					$region_element['total'] = $update;
				}
				
				// NOTE: Updating the region element also updates the simple XML document ($xml).
				$xml->asXML($outputFilename);
				
				break; // End of JSON block;
				
			default: // No XML or JSON in URL.
				$_GET['err'] = 601; // 'URL pattern error: No response (XML/JSON) provided'
				require_once('error.php');
				exit;
		} // End of XML/JSON switch.
	} // End of update value if.
	else
	{ // No valid update request.
		$_GET['err'] = 605; // 'URL pattern error: No update amount set'
		require_once('error.php');
		exit;
		
	}// Final end of update value if.
	
} // End of simple XML element if.
else
{
	$_GET['err'] = 601; // 'URL pattern error: No response (XML/JSON) provided'
	require_once('error.php');
	exit;

} // Proper end of simple XML element if.
?>