<?php
/*
 * Author: Gunnar Petzall (UWE no: 10005826) (gpetzall@gmail.com)
 * Created: 2014-01-17
 * Modified: 2014-01-17
 * 
 * Script made for the Advanced Topics in Web Development (UFCEWT-20-3) at the
 * University of the West of England in the years 2013-2014. This is part B1 course
 * component, dealing with part "2.3".
 * 
 * The script receives an error code via .htaccess redirect and displays an error
 * and its description in XML format.
 * 
 * Please note that the catch-all is 501 as per the assignment specification; as
 * opposed to the "501 - Not Implemented" specified in HTTP/1.1 by R. Fielding et
 * al (1999) http://www.w3.org/Protocols/rfc2616/rfc2616.html [accessed 2014-01-17].
 * 
 * 
 * Pages used as help to make this code:
 * 
 * Another way to delete: (Wasn't used in the end)
 * http://www.kavoir.com/2008/12/how-to-delete-remove-nodes-in-simplexml.html
 * [Accessed 2014-01-17]
 * 
*/

// Run configuration.
require_once ('/includes/config.php');

if (isset($_GET['err'])) // Error code grabbed.
{
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
		
		default:
			$err = 501;
			$err_msg = 'URL pattern not recognised';
			break;
	} // End of error switch.
}
else // No error code.
{
	echo 'Error: No error received.';
}




// Build XML here!!!












?>