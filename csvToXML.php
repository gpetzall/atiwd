<?php
/*
 * Author: Gunnar Petzall (10005826)
 * 
 * 
 * Initial ideas inspired by Michael Parkin
 * http://stackoverflow.com/questions/4852796/php-script-to-convert-csv-files-to-xml
 * [Accessed on 2013-12-16]
*/

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', true);
ini_set('auto_detect_line_endings', true);

$inputFilename	= 'input.csv'; //edited for simplicity
$outputFilename	= 'output.xml';

// Open csv to read
$inputFile = fopen($inputFilename, 'rt');

// Get the headers of the file (seems pointless)
// $headers = fgetcsv($inputFile);
// print_r($headers);



// NEW TRY FROM HERE!!!

// Create a new DOM document with pretty formatting
$doc = new DomDocument();
$doc->formatOutput = true;

// Add a root node to the document called crimes.
$node = $doc->createElement('crimes');
$root = $doc->appendChild($node);





// Predefining headers and instructions for reading the file.
$headers = array("area_region_other","total","total","","violence_against_the_person","homicide","violence_with_injury",
	"violence_without_injury","sexual_offences","robbery","theft_offences","burglary","domestic_burglary","non_domestic_burglary",
	"vehicle_offences","theft_from_the_person","bicycle_theft","shoplifting","all_other_theft_offences","criminal_damage_and_arson",
	"","drug_offences","possession_of_weapon_offences","public_order_offences","miscellaneous_crimes_against_society","","fraud");

// Predefining variables so they work later.
$victim_based_crime;
$other_crimes_against_society;
$violence_against_the_person;
$homicide; 
$violence_with_injury;
$violence_without_injury;
$sexual_offences;
$robbery;
$theft_offences;
$burglary;
$domestic_burglary;
$non_domestic_burglary;
$vehicle_offences;
$theft_from_the_person;
$bicycle_theft;
$shoplifting;
$all_other_theft_offences;
$criminal_damag_and_arson;
$drug_offences;
$possession_of_weapon_offences;
$public_order_offences;
$miscellaneous_crimes_against_society;
$fraud;




// Make counter to keep track of what rows the process is on (to ignore certain rows).
$row_count = 1;

// Loop through each row creating a <row> node with the correct data
while (($row = fgetcsv($inputFile,1024,",")) !== FALSE)
{
	
	
	switch ($row_count)
	{
		case ($row_count < 7): // Skip first 6 lines.
		case 64: // Skip ENGLAND.
		case ($row_count > 8): // Skip last lines (75 through to 78).
			break;

		default: // If not told to skip, process data!
			
			if (empty($row[0])) // Skip empty lines.
			{
				break;
			}
			
			
			$area = $doc->createElement('area'); // Creates the area.
			
			
			
			foreach ($headers as $i => $header) // Each column from $headers as $i in this loop makes one $header
			{ 
				
				
				
					// $valid_elem = $doc->createElement('field', 'correct attribute');#
					
					// $valid_attr = $doc->createAttribute('name');

					// $valid_attr->value = 'foo&amp;bar';
					// $valid_elem->appendChild($valid_attr);
					// $doc->appendChild($valid_elem);
				
				
				switch ($header) // Lots of help from here: http://www.php.net/manual/en/domdocument.createattribute.php
				{	
					case "area_region_other": // If Area/Region or Other, do this.
						$area_name = $doc->createAttribute('name'); // Create a name attribute.
						$area_name->value = $row[$i]; // Give it the row value.
						$area->appendChild($area_name); // Give the area its name.
						$doc->appendChild($area); // Add name attribute to document.
						break;
					
					case "total": // If it's a column with totals or an empty column; break.
					case "":
						break;
					
					
					case "violence_against_the_person": // Is a parent crime type, so uses variable declared outside.
						
						// make function called that does the loop, but add the append bit at the bottom.
						
						
						$victim_based_crime = $doc->createElement('victim_based_crime'); // Create victim-based crime (VBC) category.
						$area->appendChild($victim_based_crime); // Add it.
						
						
						$violence_against_the_person = $doc->createElement('crime'); // Crime element added.
						
						$crime_name = $doc->createAttribute('name'); // Create a name attribute.
						$crime_name->value = $header; // Name the crime.
						$violence_against_the_person->appendChild($crime_name); // Give the crime its name.
						
						$crime_total = $doc->createAttribute('total'); // Create a total attribute.
						$crime_total->value = str_replace(",","",$row[$i]); // Specify the total and remove the spare comma.
						$violence_against_the_person->appendChild($crime_total); // Give the crime its name.
						
						$victim_based_crime->appendChild($violence_against_the_person); // VBC takes parenthood of crime.
						
						break;
						
					case "homicide":
					case "violence_with_injury":
					case "violence_without_injury":
						
						$crime = $doc->createElement('crime'); // Crime element added.
						
						$crime_name = $doc->createAttribute('name'); // Create a name attribute.
						$crime_name->value = $header; // Name the crime.
						$crime->appendChild($crime_name); // Give the crime its name.
						
						$crime_total = $doc->createAttribute('total'); // Create a total attribute.
						$crime_total->value = str_replace(",","",$row[$i]); // Specify the total and remove the spare comma.
						$crime->appendChild($crime_total); // Give the crime its name.
						
						$violence_against_the_person->appendChild($crime); // The category of crime takes parenthood of crime.
						
						break;
						
					case "sexual_offences":
					case "robbery":
					case "criminal_damage_and_arson":
					
						$crime = $doc->createElement('crime'); // Crime element added.
						
						$crime_name = $doc->createAttribute('name'); // Create a name attribute.
						$crime_name->value = $header; // Name the crime.
						$crime->appendChild($crime_name); // Give the crime its name.
						
						$crime_total = $doc->createAttribute('total'); // Create a total attribute.
						$crime_total->value = str_replace(",","",$row[$i]); // Specify the total and remove the spare comma.
						$crime->appendChild($crime_total); // Give the crime its name.
						
						$victim_based_crime->appendChild($crime); // VBC takes parenthood of crime.
						
						break;
					
					
					case "theft_offences": // Is a parent crime type, so uses variable declared outside.
					
						$theft_offences = $doc->createElement('crime'); // Crime element added.
						
						$crime_name = $doc->createAttribute('name'); // Create a name attribute.
						$crime_name->value = $header; // Name the crime.
						$theft_offences->appendChild($crime_name); // Give the crime its name.
						
						$crime_total = $doc->createAttribute('total'); // Create a total attribute.
						$crime_total->value = str_replace(",","",$row[$i]); // Specify the total and remove the spare comma.
						$theft_offences->appendChild($crime_total); // Give the crime its name.
						
						$victim_based_crime->appendChild($theft_offences); // VBC takes parenthood of crime.
						
						break;
					
					
					case "burglary": // Is a parent crime type, so uses variable declared outside. Also a child, so last line is slightly different.
					
						$burglary = $doc->createElement('crime'); // Crime element added.
						
						$crime_name = $doc->createAttribute('name'); // Create a name attribute.
						$crime_name->value = $header; // Name the crime.
						$burglary->appendChild($crime_name); // Give the crime its name.
						
						$crime_total = $doc->createAttribute('total'); // Create a total attribute.
						$crime_total->value = str_replace(",","",$row[$i]); // Specify the total and remove the spare comma.
						$burglary->appendChild($crime_total); // Give the crime its name.
						
						$theft_offences->appendChild($burglary); // Theft offences takes parenthood of crime.
						
						break;
					
					
					case "domestic_burglary":
					case "non_domestic_burglary":
					
						$crime = $doc->createElement('crime'); // Crime element added.
						
						$crime_name = $doc->createAttribute('name'); // Create a name attribute.
						$crime_name->value = $header; // Name the crime.
						$crime->appendChild($crime_name); // Give the crime its name.
						
						$crime_total = $doc->createAttribute('total'); // Create a total attribute.
						$crime_total->value = str_replace(",","",$row[$i]); // Specify the total and remove the spare comma.
						$crime->appendChild($crime_total); // Give the crime its name.
						
						$burglary->appendChild($crime); // Burglary takes parenthood of crime.
						
						break;
					
					
					case "vehicle_offences":
					case "theft_from_the_person":
					case "bicycle_theft":
					case "shoplifting":
					case "all_other_theft_offences":
					
						$crime = $doc->createElement('crime'); // Crime element added.
						
						$crime_name = $doc->createAttribute('name'); // Create a name attribute.
						$crime_name->value = $header; // Name the crime.
						$crime->appendChild($crime_name); // Give the crime its name.
						
						$crime_total = $doc->createAttribute('total'); // Create a total attribute.
						$crime_total->value = str_replace(",","",$row[$i]); // Specify the total and remove the spare comma.
						$crime->appendChild($crime_total); // Give the crime its name.
						
						$theft_offences->appendChild($crime); // Theft offences takes parenthood of crime.
						
						break;
					
					
					// $other_crimes_against_society;
					
					case "drug_offences":
						
						
						$other_crimes_against_society = $doc->createElement('other_crimes_against_society'); // Create other_crimes_against_society (OCAS) category.
						$area->appendChild($victim_based_crime); // Add it.
						
						
						$crime = $doc->createElement('crime'); // Crime element added.
						
						$crime_name = $doc->createAttribute('name'); // Create a name attribute.
						$crime_name->value = $header; // Name the crime.
						$crime->appendChild($crime_name); // Give the crime its name.
						
						$crime_total = $doc->createAttribute('total'); // Create a total attribute.
						$crime_total->value = str_replace(",","",$row[$i]); // Specify the total and remove the spare comma.
						$crime->appendChild($crime_total); // Give the crime its name.
						
						$other_crimes_against_society->appendChild($crime); // OCAS takes parenthood of crime.
						
						break;
						
					
					case "possession_of_weapon_offences":
					case "public_order_offences":
					case "miscellaneous_crimes_against_society":
					case "fraud":
					
						$crime = $doc->createElement('crime'); // Crime element added.
						
						$crime_name = $doc->createAttribute('name'); // Create a name attribute.
						$crime_name->value = $header; // Name the crime.
						$crime->appendChild($crime_name); // Give the crime its name.
						
						$crime_total = $doc->createAttribute('total'); // Create a total attribute.
						$crime_total->value = str_replace(",","",$row[$i]); // Specify the total and remove the spare comma.
						$crime->appendChild($crime_total); // Give the crime its name.
						
						$other_crimes_against_society->appendChild($crime); // OCAS takes parenthood of crime.
						
						break;
						
					
					
					default:
						
						echo "default";
						
						if (empty($header)) 
						{
							$child = $doc->createElement('empty');
							$child = $area->appendChild($child);
							
							$value = $doc->createTextNode($row[$i]); // Header's value
							$value = $child->appendChild($value); // Adds it to the DOM
						}
						else
						{
						
						$child = $doc->createElement(str_replace(' ', '_', $header)); // Add a header value from $header
						$child = $area->appendChild($child); // Adds it to the DOM
						
						$value = $doc->createTextNode($row[$i]); // Header's value
						$value = $child->appendChild($value); // Adds it to the DOM
						}
						
						break;
						
				} // end switch for header
				
				
				
			} // end foreach for header
			
			$root->appendChild($area); // Adds the row to the DOM
			
			
			break; // break row count default
		
		
	} // end of row count switch
	
	$row_count++;
	
} // end of while
	
	





echo $doc->saveXML();



// NEW TRY ENDS HERE!!

exit; // IF NOT COMMENTED, THE SCRIPT ENDS HERE!

// Create a new dom document with pretty formatting
$doc = new DomDocument();
$doc->formatOutput = true;

// Add a root node to the document
$node = $doc->createElement('root');
$root = $doc->appendChild($node);

// Loop through each row creating a <row> node with the correct data
while (($row = fgetcsv($inputFile,1024,",")) !== FALSE)
{
	$container = $doc->createElement('row');
	
	foreach ($headers as $i => $header) // Each column from $headers as $i in this loop makes one $header
	{
		$node = $doc->createElement(str_replace(' ', '_', $header)); // Add a header value from $header
		$child = $container->appendChild($node); // Adds it to the DOM
		
		$value = $doc->createTextNode($row[$i]); // Header's value
		$value = $child->appendChild($value); // Adds it to the DOM
	}

	$root->appendChild($container); // Adds it to the DOM
}


echo $doc->saveXML();


?>