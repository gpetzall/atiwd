<?php
/* 
 * File: "~g2-petzall/atwd/crimes/source.php"
 * 
 * Original author: Ben Argo (see below)
 * Adopted by: Gunnar Petzall (UWE no: 10005826) (gpetzall@gmail.com)
 * Created: 2014-02-27
 * Modified: 2014-02-27
 * 
 * Script than is passed a request to turn another file into a "PHPS" file for
 * source viewing.
 * 
 * 
 * Pages used as help to make this code:
 * 
 * Ben Argo's source.php (entirely)
 * http://www.cems.uwe.ac.uk/~b2-argo/resources/source.php
 * [Accessed 2014-02-27]
 * 
*/

if($_GET['file'])
{
	highlight_file(__DIR__ .'/'. $_GET['file']);
	exit;
}

?>