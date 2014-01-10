<?php
/*
 * Author: Gunnar Petzall (UWE no: 10005826) (gpetzall@gmail.com)
 * Created: 2014-01-08
 * Modified: 2014-01-09
 * 
 * Script made for the Advanced Topics in Web Development (UFCEWT-20-3) at the
 * University of the West of England in the years 2013-2014. This is part B1 course
 * component, dealing with part "2.1.1".
 * 
 * The script accesses the database file "crimes.xml" located at
 * http://www.cems.uwe.ac.uk/~~g2-petzall/atwd/crimes/doc/
 * and return XML and Json crime totals of all regions.
 * 
 * 
 * Pages used as help to make this code:
 * 
 * Time and timestamps:
 * http://uk1.php.net/time
 * http://www.w3schools.com/php/func_date_strtotime.asp
 * [Accessed on 2014-01-08]
 * 
 * Reading XML:
 * http://stackoverflow.com/questions/10405725/read-xml-using-file-get-contents (didn't use, but good start)
 * http://www.php.net/manual/en/domelement.getattribute.php (didn't use)
 * http://stackoverflow.com/questions/13527422/simplexml-import-into-php-do-i-need-to-close-file
 * http://php.net/manual/en/function.simplexml-load-file.php
 * http://uk1.php.net/manual/en/simplexmlelement.attributes.php
 * http://uk1.php.net/manual/en/simplexml.examples-basic.php
 * [Accessed on 2014-01-08] 
 * 
 * For making it readable while coding (and provide good headers after):
 * http://stackoverflow.com/questions/1414325/is-headercontent-typetext-plain-necessary-at-all
 * http://www.php.net/manual/en/function.header.php
 * [Accessed on 2014-01-08]
 * 
 * Making Strings Capitalize Each Word:
 * http://www.php.net/manual/en/function.ucwords.php
 * [Accessed on 2014-01-08]
 * 
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

// Configure filename data.
$inputFilename = 'doc/crimes.xml'; 

// Create a new DOM document with pretty formatting.
$doc = new DomDocument();
$doc->formatOutput = true;

// Create a simple xml object for easy reading.
$xml = simplexml_load_file($inputFilename);


/*

STILL TO DO!!!

- Add England
- Add Wales

What to do:
- Make sure to skip Wales in the loop!
- Sum up all *regions* except Wales from the DOM that is displayed.
- Add an england child with the total added up.
- Add a wales child with the same data as in the region.

*/




switch ($response)
{
	case 'xml': // XML block starting.
	
		header("Content-type: text/xml"); // To display correctly.
		
		
		// Add a root node to the document called response.
		$node = $doc->createElement('response');
		$node->setAttribute('timestamp',time()); // Add 'nix timestamp.
		$root = $doc->appendChild($node); // Add it to the DOM.

		// Add crimes.
		$node = $doc->createElement('crimes');
		$node->setAttribute('year',"6-2013"); // Set year attribute.
		$crimes = $root->appendChild($node); // Add it to the response.
		
	
		foreach($xml->children() as $region) // Simple XML for the reading, DOM for writing.
		{
			if ($region->attributes()['id'] == 'british_transport_police' || $region->attributes()['id'] == 'action_fraud') // Filtering national.
			{
				$node = $doc->createElement('national');
				$node->setAttribute('id',ucwords(str_replace('_', ' ', (string) $region->attributes()['id']))); // ucwords is Very Useful.
				// Simple XML element with id attribute to string, change characters and add.
				
				$region_total = 0; // Variable for the region totals.
				
				foreach($region->children() as $area) // Continue down the rabbit hole...
				{
					foreach($area->children() as $crime_type) // Continue down the rabbit hole...
					{
						foreach($crime_type->children() as $crime_top) // Continue down the rabbit hole...
						{
							$region_total += $crime_top->attributes()['total']; // Add up all the crime totals.
						} // End crime_top foreach (national).
					} // End crime_type foreach (national).
				} // End area foreach (national).
				
				$node->setAttribute('total',$region_total); // Add the total number.
				
				$crimes->appendChild($node); // Add it all to the DOM.
			} // End if (national).
			else // (all other)
			{
				$node = $doc->createElement('region');
				$node->setAttribute('id',ucwords(str_replace('_', ' ', (string) $region->attributes()['id']))); // ucwords is Very Useful.
				// Simple XML element with id attribute to string, change characters and add.
				
				$region_total = 0; // Variable for the region totals.
				
				foreach($region->children() as $area) // Continue down the rabbit hole...
				{
					foreach($area->children() as $crime_type) // Continue down the rabbit hole...
					{
						foreach($crime_type->children() as $crime_top) // Continue down the rabbit hole...
						{
							$region_total += $crime_top->attributes()['total']; // Add up all the crime totals.
						} // End crime_top foreach (all other).
					} // End crime_type foreach (all other).
				} // End area foreach (all other).
				
				$node->setAttribute('total',$region_total); // Add the total number.
				
				$crimes->appendChild($node); // Add it all to the DOM.
		
			} // End else (all other).
			
		} // End XML foreach.
		
		echo $doc->saveXML();
		break; // XML block end.
	
	
	
	case 'json': // JSON block starting.
		
		header("Content-type: application/json"); // So that my Firefox "JSONview" add-on will display it properly.
		
		$json = array(); // Regular array.
		$json['response']['timestamp']=time(); // Create the first array item and its child with a name of timestamp and value of time().
		$json['response']['crimes']['year']='6-2013'; // Create a second child of response with a child named year and value "6-2013".
		
		$region_count = 0; // To make the loop make new children instead of overwriting itself.
		
		foreach($xml->children() as $region) // Similar to the XML loop.
		{
			if ($region->attributes()['id'] == 'british_transport_police' || $region->attributes()['id'] == 'action_fraud') // Filtering national.
			{
				$json['response']['crimes']['national'][$region_count]['id']=(string)ucwords(str_replace('_', ' ',$region->attributes()['id'])); // ucwords is Very Useful.
				// Create region; put multiple numbered entries in region; which each hold one "id" that is the name of the XML-attribute pulled (same way as XML code).
				
				$region_total = 0; // Loop identical to XML loop.
				
				foreach($region->children() as $area) // Continue down the rabbit hole...
				{
					foreach($area->children() as $crime_type) // Continue down the rabbit hole...
					{
						foreach($crime_type->children() as $crime_top) // Continue down the rabbit hole...
						{
							$region_total += $crime_top->attributes()['total']; // Add up all the crime totals.
						} // End crime_top (national).
					} // End crime_type foreach (national).
				} // End area foreach (national).
				
				$json['response']['crimes']['national'][$region_count]['total']=(int) $region_total; // Correct data type.
			} // End if (national)
			else // All other.
			{
				$json['response']['crimes']['region'][$region_count]['id']=(string)ucwords(str_replace('_', ' ',$region->attributes()['id'])); // ucwords is Very Useful.
				// Create region; put multiple numbered entries in region; which each hold one "id" that is the name of the XML-attribute pulled (same way as XML code).
				
				$region_total = 0; // Loop identical to XML loop.
				
				foreach($region->children() as $area) // Continue down the rabbit hole...
				{
					foreach($area->children() as $crime_type) // Continue down the rabbit hole...
					{
						foreach($crime_type->children() as $crime_top) // Continue down the rabbit hole...
						{
							$region_total += $crime_top->attributes()['total']; // Add up all the crime totals.
						} // End crime_top (all other).
					} // End crime_type foreach (all other).
				} // End area foreach (all other).
				
				$json['response']['crimes']['region'][$region_count]['total']=(int) $region_total; // Correct data type.
			} // End else (all other).
			
			$region_count++; // Keeping track of location in array.
			
		} // End JSON foreach.
		
		echo json_encode($json); //Json!
		
		break; // End of JSON block.
		
	default:
		?><p>This page only works with the right URLs. Try making an <a href="get.php?response=xml">XML</a> or <a href="get.php?response=json">JSON</a> request instead.</p><?php
		
		break;
}
?>