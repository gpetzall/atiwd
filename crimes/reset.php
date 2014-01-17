<?php
/*
 * Author: Gunnar Petzall (UWE no: 10005826) (gpetzall@gmail.com)
 * Created: 2014-01-09
 * Modified: 2014-01-17
 * 
 * Script made for the Advanced Topics in Web Development (UFCEWT-20-3) at the
 * University of the West of England in the years 2013-2014. This is part B1 course
 * component, supporting part "2.1.X" and "2.2.X".
 * 
 * The script opens the "crimes.xml" file in http://www.cems.uwe.ac.uk/~g2-petzall/atwd/crimes/doc/
 * and replaces its content with "backup.xml" at the same location so that any edits
 * are reset when using "2.1.X" and "2.2.X" components of the assignment.
 * 
 * 
 * Pages used as help to make this code:
 * 
 * Convert Simple XML to DOM:
 * http://www.php.net/manual/en/function.dom-import-simplexml.php (not used in the end)
 * [Accessed: 2014-01-09]
 * 
 * Save Simple XML to a document:
 * http://stackoverflow.com/questions/3418376/how-to-save-changed-simplexml-object-back-to-file
 * [Accessed: 2014-01-09]
*/

// Run configuration.
require_once ('/includes/config.php');

// Other input.
$inputFilename = 'doc/backup.xml';

// Create a simple XML object with the backup file.
$xml = simplexml_load_file($inputFilename);

// Save the simple XML object back as file.
$xml->asXml($outputFilename);
?>

<p>The file <strong><?php echo $outputFilename ?></strong> has been reset.</p>