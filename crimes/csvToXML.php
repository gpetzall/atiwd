<?php
/*
 * Author: Gunnar Petzall (UWE no: 10005826) (gpetzall@gmail.com)
 * Created: 2013-12-16
 * Modified: 2014-01-10
 *
 * Script made for the Advanced Topics in Web Development (UFCEWT-20-3) at the
 * University of the West of England in the years 2013-2014. This is part B1 course
 * component, dealing with part "1.1".
 * 
 * The script converts the provided file
 * "policeforceareadatatablesyearendingjune13_tcm77-330992.xlsx" saved as a CSV file
 * named "source.csv" located at:
 * http://www.cems.uwe.ac.uk/~g2-petzall/atwd/crimes/doc/
 * into a valid XML file named "output.xml", also located at the above URL.
 * 
 * 
 * Initial ideas inspired by Michael Parkin:
 * http://stackoverflow.com/questions/4852796/php-script-to-convert-csv-files-to-xml
 * [Accessed on 2013-12-16]
 *
 * Pages used as help to make this code:
 * 
 * Clarifying fopen:
 * http://www.w3schools.com/php/php_file.asp
 * [Accessed on 2013-12-16]
 * 
 * Making DOM bits and pieces:
 * http://www.php.net/manual/en/domdocument.createattribute.php
 * [Accessed on 2014-01-06]
 *
 * Formatting strings:
 * http://www.w3schools.com/php/func_string_strtolower.asp
 * http://uk3.php.net/str_replace
 * [Accessed on 2014-01-07]
*/

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', true);
ini_set('auto_detect_line_endings', true);

// Configure filename data.
$inputFilename	= 'doc/input.csv'; 
$outputFilename	= 'doc/crimes.xml'; // To make an XML file as a database for the other parts of the assignment.
$backupFilename	= 'doc/backup.xml'; // To make a static backup that can be used to restore the XML database.

// Open CSV to read.
$inputFile = fopen($inputFilename, 'rt');

// Create a new DOM document with pretty formatting.
$doc = new DomDocument();
$doc->formatOutput = true;

// Add a root node to the document called crimes.
$node = $doc->createElement('crimes');
$root = $doc->appendChild($node);


// Predefining headers and instructions for reading the filelater on.
$headers = array("area_region_other","total1","total2","","violence_against_the_person","homicide","violence_with_injury",
	"violence_without_injury","sexual_offences","robbery","theft_offences","burglary","domestic_burglary","non_domestic_burglary",
	"vehicle_offences","theft_from_the_person","bicycle_theft","shoplifting","all_other_theft_offences","criminal_damage_and_arson",
	"","drug_offences","possession_of_weapon_offences","public_order_offences","miscellaneous_crimes_against_society","","fraud");

// Predefining variables so they work later.
$victim_based_crime;
$other_crimes_against_society;
$violence_against_the_person;
$theft_offences;
$burglary;


function createCrime($crime_name, $crime_total, $doc = NULL) 
{
	// createCrime
	// Used for the crime-creating loops.
	// Pass it the name of the crime type, its total crimes committed and set $doc to null so that it catches the global (backup).
	// Lots of help from here: http://www.php.net/manual/en/domdocument.createattribute.php
	
	if(is_null($doc)) global $doc; // Use the global $doc variable.
	
	$crime = $doc->createElement('crime'); // Crime element added (to be appended outside the function).
	$id = $crime->setAttribute('id',$crime_name); // Set an id attribute passed from the call.
	$total = $crime->setAttribute('total',str_replace(",","",str_replace("..","0",$crime_total))); // Set the total crime attribute passed from the call and remove the spare comma.
	return $crime; // Returned for appending it to the DOM.
}


$row_count = 1; // Counter to keep track of what rows the process is on (to ignore certain rows).

$areas = array(); // Temporarily storing areas made for the region block to put them in regions.

// Loop through each row creating an XML element with the correct data.
while (($row = fgetcsv($inputFile,1024,",")) !== FALSE)
{
	
	switch ($row_count) // Using switch to control what happens on each line.
	{
		case ($row_count < 7): // Skip first 6 lines.
		case 64: // Skip ENGLAND.
		case ($row_count > 74): // Skip last lines (75 through to 78).
			break;

		default: // If not told to skip, process data!
			
			if (empty($row[0])) // Skip empty lines.
			{
				break;
			}
			
			if (preg_match("/(Region|^WALES)$/", $row[0])) // Region block start. Makes sure any areas added gets put into a region with correct name.
			{
				$region = $doc->createElement('region'); // Create a region for the department.
				if($row[0] == "WALES")
				{
					$region_name = strtolower($row[0]); // Wales only need set to lowercase. // http://www.w3schools.com/php/func_string_strtolower.asp
				}
				elseif(preg_match('/ Region$/', $row[0])) // If first column of row ends with " Region"...
				{
					$region_name = strtolower(str_replace(' ', '_', preg_replace('/ Region$/', '', $row[0])));
					// Make lowercase, replace with "_", remove Region in the first column of row.
				}
				$region->setAttribute('id', $region_name); // Give it the name as id.
				$region = $root->appendChild($region); // Region is taken over by root (crime) and put in it.
				foreach($areas as $area) // Group all the areas created without a region into this region.
				{
					$region->appendChild($area);
				}
				unset($areas); // Clear for next set of areas.
			}
			else // Area block start.
			{
				$area = $doc->createElement('area'); // Creates the area.
				
				foreach ($headers as $i => $header) // Each column from $headers as $i in this loop makes one $header
				{ 
					switch ($header) // Lots of help from here: http://www.php.net/manual/en/domdocument.createattribute.php
					{	
						case "area_region_other": // If Area/Region or Other, do this.
							
							$area->setAttribute('id',strtolower(str_replace(' ', '_',(str_replace('1', '',($row[$i])))))); // Set the id attribute. Remove "1" from Action Fraud.

							break;
						
						case "total1": // If it's a column with totals or an empty column; break.
						case "total2":
						case "":
							break;
						
						case "violence_against_the_person": // Is a parent crime type, so uses variable declared outside.
							
							// Used only once
							$victim_based_crime = $doc->createElement('victim_based_crime'); // Crime *category* element added.
							$area->appendChild($victim_based_crime); // Appending it.
							// Used only once
							
							$violence_against_the_person = $doc->createElement('crime'); // Crime element added.
							$id = $violence_against_the_person->setAttribute('id',$header); // Set an id attribute passed.
							$total = $violence_against_the_person->setAttribute('total',str_replace(",","",str_replace("..","0",$row[$i])));
							// Set the total crime attribute and remove the spare comma as well as the Action Fraud "..".
							$victim_based_crime->appendChild($violence_against_the_person); // VBC takes parenthood of crime.
							break;
							
						case "homicide":
						case "violence_with_injury":
						case "violence_without_injury":
							$crime = createCrime($header,$row[$i],$doc); // Function above, passed crime name ($header) and total crime data.
							$violence_against_the_person->appendChild($crime); // The category of crime takes parenthood of crime.
							break;
							
						case "sexual_offences":
						case "robbery":
						case "criminal_damage_and_arson":
							$crime = createCrime($header,$row[$i],$doc); // Function above, passed crime name ($header) and total crime data.
							$victim_based_crime->appendChild($crime); // VBC takes parenthood of crime.
							break;
						
						case "theft_offences": // Is a parent crime type, so uses variable declared outside.
							$theft_offences = $doc->createElement('crime'); // Crime element added.
							$id = $theft_offences->setAttribute('id',$header); // Set an id attribute.
							$total = $theft_offences->setAttribute('total',str_replace(",","",str_replace("..","0",$row[$i]))); // Total crime, remove comma and "..".
							$victim_based_crime->appendChild($theft_offences); // VBC takes parenthood of crime.
							break;
						
						case "burglary": // Is a parent crime type, so uses variable declared outside. Also a child, so last line is slightly different.
							$burglary = $doc->createElement('crime'); // Crime element added.
							$id = $burglary->setAttribute('id',$header); // Set an id attribute.
							$total = $burglary->setAttribute('total',str_replace(",","",str_replace("..","0",$row[$i]))); // Total crime, remove comma and "..".
							$theft_offences->appendChild($burglary); // Theft offences takes parenthood of crime.
							break;
						
						case "domestic_burglary":
						case "non_domestic_burglary":
							$crime = createCrime($header,$row[$i],$doc); // Function above, passed crime name ($header) and total crime data.
							$burglary->appendChild($crime); // Burglary takes parenthood of crime.
							break;
						
						case "vehicle_offences":
						case "theft_from_the_person":
						case "bicycle_theft":
						case "shoplifting":
						case "all_other_theft_offences":
							$crime = createCrime($header,$row[$i],$doc); // Function above, passed crime name ($header) and total crime data.
							$theft_offences->appendChild($crime); // Theft offences takes parenthood of crime.
							break;
						
						case "drug_offences":
							
							// Used only once
							$other_crimes_against_society = $doc->createElement('other_crimes_against_society'); // Create other_crimes_against_society (OCAS) category.
							$area->appendChild($other_crimes_against_society); // Add it.
							// Used only once
							
							$crime = createCrime($header,$row[$i],$doc); // Function above, passed crime name ($header) and total crime data.
							$other_crimes_against_society->appendChild($crime); // OCAS takes parenthood of crime.
							break;
							
						case "possession_of_weapon_offences":
						case "public_order_offences":
						case "miscellaneous_crimes_against_society":
						case "fraud":
							$crime = createCrime($header,$row[$i],$doc); // Function above, passed crime name ($header) and total crime data.
							$other_crimes_against_society->appendChild($crime); // OCAS takes parenthood of crime.
							break;
							
						default:
							echo "Some fail: Default was used.";
							break;
							
					} // End switch for header.
					
				} // End foreach for header.
				
				$areas[$row_count] = $area; // Put it in to a unique place in the array.
			
			} // Area Block end.

			if (preg_match("/(British Transport Police|Action Fraud1)$/", $row[0])) // OTHER block for the non-area "regions". Set as "region" because of assignment wording.
			{
				$region = $doc->createElement('region'); // Create a region for the department.
				if($row[0] == "British Transport Police")
				{
					$region_name = strtolower(str_replace(' ', '_',$row[0])); // Make lowercase, replace space with "_" in the first column of row.
				}
				elseif($row[0] == "Action Fraud1")
				{
					$region_name = strtolower(str_replace(' ', '_', str_replace('1', '', $row[0]))); // Make lowercase, replace space with "_" in the first column of row, remove "1".
				}
				$region->setAttribute('id', $region_name); // Give it the edited name.
				$region = $root->appendChild($region); // Region is taken over by root (crime) and put in it.
				foreach($areas as $area) // In case there will ever be more than one for each "region".
				{
					$region->appendChild($area);
				}
				unset ($areas); // Clear for next set of areas.
				
			} // End of OTHER block.
			
			break; // Break row_count default.
		
	} // End of row count switch.
	
	$row_count++;
	
} // End of main while loop.

echo $doc->saveXML();


$save_xml = $doc->saveXML();

// Save the database output.
$file = fopen($outputFilename, "w");
fwrite($file, $save_xml);
fclose($file);

// Save the database backup copy.
$file = fopen($backupFilename, "w");
fwrite($file, $save_xml);
fclose($file);

?>
<p>The CSV file <strong>"<?php echo $inputFilename; ?>"</strong> was parsed into XML.</p>
<p>Download the output file <a href="doc/backup.xml">here</a>.</p>
<p>The file was also saved for edits in other parts of the assignment <a href="doc/crimes.xml">here</a>.</p>