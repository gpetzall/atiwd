<?php
/* 
 * File: "includes/config.php"
 * 
 * Author: Gunnar Petzall (UWE no: 10005826) (gpetzall@gmail.com)
 * Created: 2014-01-17
 * Modified: 2014-01-17
 * 
 * Configuration file made for the Advanced Topics in Web Development (UFCEWT-20-3) at
 * the University of the West of England in the years 2013-2014. This is part B1 course
 * component, dealing with all parts in some way.
 * 
 * Config file does the following:
 * - Settings in one location.
 * - File locations
 * 
 * 
 * Pages used as help to make this code:
 * 
 * headline:
 * 
 * [Accessed 2014-01-17]
 * 
*/

// Determine if the script is working on a local server or the live one:
if (stristr($_SERVER['HTTP_HOST'], 'local') || (substr($_SERVER['HTTP_HOST'], 0, 7) == '192.168'))
{
	$local = TRUE;
}
else
{
	$local = FALSE;
}

// Determine location of files etc:
if ($local)
{
	// Always debug locally.
	$debug = TRUE;
	
	// Define constants.
	define ('BASE_URI', 'atwd/crimes/');
	define ('BASE_URL', 'http://localhost/atwd/crimes/');
}
else
{
	// Run debug during development.
	$debug = TRUE;
	
	// Define constants.
	define ('BASE_URI', '/nas/students/g/g2-petzall/unix/public_html/atwd/crimes/');
	define ('BASE_URL', 'http://www.cems.uwe.ac.uk/~g2-petzall/atwd/crimes/');
}

// Run debug if necessary.
if ($debug == TRUE)
{
	error_reporting(E_ALL | E_STRICT);
	ini_set('display_errors', true);
	ini_set('auto_detect_line_endings', true);
}



// Determine file locations.
$inputFilename = 'doc/crimes.xml'; // File that is used for the script (one exception).
$outputFilename = 'doc/crimes.xml'; // To create the file and also for any database edits.
$backupFilename = 'doc/backup.xml'; // To create a fresh, unedited, backup.





?>