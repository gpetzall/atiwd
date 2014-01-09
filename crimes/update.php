<?php
/*
 * Author: Gunnar Petzall (UWE no: 10005826) (gpetzall@gmail.com)
 * Created: 2014-01-09
 * Modified: 2014-01-09
 * 
 * Script made for the Advanced Topics in Web Development (UFCEWT-20-3) at the
 * University of the West of England in the years 2013-2014. This is part B1 course
 * component, dealing with part "2.2.1".
 * 
 * The script updates the total crime value of one region of a particular id in the
 * "crimes.xml" file at http://www.cems.uwe.ac.uk/~~g2-petzall/atwd/crimes/doc/
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
*/

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', true);
ini_set('auto_detect_line_endings', true);


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

// Configure filename data.
$inputFilename = 'doc/crimes.xml';
$outputFilename	= 'doc/crimes.xml';

// Create a simple xml object.
$xml = simplexml_load_file($inputFilename);


$region_element = $xml->xpath("/crimes/region[@id='$regi']"); // A little bit of XPATH grabbing all elements with GET's id value.
$region_element = array_shift($region_element); // Returns the simple XML element.
// NOTE: simple XML remembers where it's from later in the code when it is used.

if ($region_element instanceof SimpleXMLElement) // If a simle xml element was returned.
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
				$node->setAttribute('id',$regi); // Set name attribute. NOTE: DIFFERENT FROM OTHER CODE (doesn't format it) SO THAT IT CORRESPONDS WITH THE SPEC!
				$region = $crimes->appendChild($node); // Add it to the response.
				
				// Does the region have a total?
				$total_check = $region_element->xpath("/crimes/region[@total]");
				if (empty($total_check)) // No: Calculate the total as "previous" and add the update value as "total".
				{
					//echo "there is not total \n \n";
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
				else // Yes: 
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
				
				echo $doc->saveXML();
				
				
				
				
				
				
				
				
				
				break;
				
			case ('json'): // Start of JSON block.
				echo " *Valid region - Valid response request (JSON)* ";
				
				
				
				// update a particular 
				
				
				
				
				
				
				
				
				
				break;
				
				
			default: // No XML or JSON in URL.
				?><p>This page only works with the right URLs. Try making an <a href="update.php?response=xml&regi=north_west&update=1000">North West XML Total:1000</a> or
				<a href="update.php?response=json&regi=north_west&update=1000">North West JSON Total:1000</a> request instead.</p><?php
			break;	
			
	
		} // End of XML/JSON switch.
	} // End of update value if.
	else
	{
		
		echo " * Region is valid, but there was no update request * "; 
		
	}// Proper end of update value if.
			
			
			
			
			
			
			
} // End of simple XML element if.
else
{
	?><p>This page only works with the right URLs. Try making an <a href="update.php?response=xml&regi=north_west&update=1000">North West XML Total:1000</a> or
	<a href="update.php?response=json&regi=north_west&update=1000">North West JSON Total:1000</a> request instead.</p><?php

} // Proper end of simple XML element if.
?>