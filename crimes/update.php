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

if (isset($_GET['meth'])) {
	$meth = $_GET['meth'];
} else {
	$meth = NULL;
}

// Configure filename data.
$inputFilename = 'doc/crimes.xml';
$outputFilename	= 'doc/crimes.xml';


































?>