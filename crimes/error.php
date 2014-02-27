<?php
/* 
 * File: "~g2-petzall/atwd/crimes/error.php"
 * 
 * Author: Gunnar Petzall (UWE no: 10005826) (gpetzall@gmail.com)
 * Created: 2014-01-17
 * Modified: 2014-02-27
 * 
 * Script made for the Advanced Topics in Web Development (UFCEWT-20-3) at the
 * University of the West of England in the years 2013-2014. This is part B1 course
 * component, dealing with part "2.3".
 * 
 * The script receives an error code via .htaccess redirect and displays an error
 * and its description in XML format. It's also used directly by other php files by
 * setting the $_GET['err'] variable manually and including it to produce an error
 * message.
 * 
 * Please note that the catch-all is 501 as per the assignment specification; as
 * opposed to the "501 - Not Implemented" specified in HTTP/1.1 by R. Fielding et.
 * al. (1999) http://www.w3.org/Protocols/rfc2616/rfc2616.html [accessed 2014-01-17].
 * 
 * Please also note that this code CAN differentiate between different errors!
 * A compromise between using 600 errors as suggested in the assignment spec (but not
 * endorsed by the HTTP spec) and following standards is that they are cought but
 * converted to 404s where appropriate in this script.
 * 
 * Pages used as help to make this code:
 * - None yet
 * 
*/

// Run configuration.
require_once (__DIR__ .'/includes/config.php');


if (isset($_GET['err']) || isset($err)) // Error code grabbed, or provided by other document?
{
	$err = $_GET['err'];
	
	try // The GET result is a string, but can't be converted if it contains letters.
	{ // This tries as best as possible to make it an integer. If it fails completely, 
		$err = (int) $err; // it goes 501.
	}
	catch (Exception $e)
	{
		$err = 501;
	}
	
	switch ($err)
	{
		case 404:
			$err_msg = 'Not Found';
			break;
			
		case 500:
			$err_msg = 'Service Error';
			break;
		
		case 601:
			// $err_msg = 'URL pattern error: No response (XML/JSON) provided';
			$err_msg = 'Not Found';
			$err = 404;
			break;
		
		case 602:
			// $err_msg = 'URL pattern error: No such region';
			$err_msg = 'Not Found';
			$err = 404;
			break;
				
		case 603:
			// $err_msg = 'URL pattern error: No area specified';
			$err_msg = 'Not Found';
			$err = 404;
			break;
			
		case 604:
			// $err_msg = 'URL pattern error: No such area';
			$err_msg = 'Not Found';
			$err = 404;
			break;
			
		case 605:
			// $err_msg = 'URL pattern error: No update amount set';
			$err_msg = 'Not Found';
			$err = 404;
			break;
			
		default:
			$err = 501;
			$err_msg = 'URL pattern not recognised';
			break;	
	} // End of error switch.
	
	http_response_code($err);
	// Create a new DOM document with pretty formatting.
	$doc = new DomDocument();
	$doc->formatOutput = true;
	
	// Add a root node to the document called response.
	$node = $doc->createElement('response');
	$node->setAttribute('timestamp',time()); // Add 'nix timestamp.
	$root = $doc->appendChild($node); // Add it to the DOM.
	
	// Add crimes.
	$node = $doc->createElement('error');
	$node->setAttribute('code',$err); // Set error code.
	$node->setAttribute('desc',$err_msg); // Set error code.
	$crimes = $root->appendChild($node); // Add it to the response.
	
	// Display message. Correct formatting.
	header("Content-type: text/xml");
	echo $doc->saveXML();
	
}
else // No error code.
{
	echo 'Error: No error received.';
}


?>