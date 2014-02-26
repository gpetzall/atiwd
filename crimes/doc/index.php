<?php
/* 
 * File: "~g2-petzall/atwd/crimes/doc/index.php"
 * 
 * Author: Gunnar Petzall (UWE no: 10005826) (gpetzall@gmail.com)
 * Created: 2014-02-26
 * Modified: 2014-02-27
 * 
 * Main page for the assignment of Advanced Topics in Web Development (UFCEWT-20-3)
 * at the University of the West of England in the years 2013-2014. This is part B1
 * course component, dealing with part "3", visualisation, and "5", documentation.
 * 
 * Pages used as help to make this code:
 * - None so far.
 * 
*/

// Run configuration.
require_once (__DIR__ .'../../includes/config.php');

?>
<!DOCTYPE html>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>Advanced Topics in Web Development Assignment 2014 - Gunnar Petz&auml;ll</title>

<!-- <link rel="shortcut icon" href="favicon.ico"> -->


<link rel='stylesheet' media='screen' href='css/main.css' />

</head>
<body>

<header id="top">
	<div class="inner">
		<p>Crime Statistics in England and Wales from June 2013</p>
		<h1>ATWD</h1>
	</div>
</header>

<header id="main_header">
	<div class="inner">



		<ul id="breadcrumbs">
			<li><a href="#">Home</a> &gt; </li>
			<li><a href="#">East Midlands</a> &gt; </li>
			<li>Visual data</li>
		</ul>
		
		<h1>East Midlands</h1>
		
		<p class="intro">Crime statistics visualised for the East Midlands.</p>

	</div>

</header>

<div id="main_content">
	<section id="navigation">
		<nav id="get">
			<h2>Select a region</h2>
			<ul>
				<li><a href="#">North East</a></li>
				<li>North West</li>
				<li><a href="#">Yorkshire and Humber</a></li>
				<li><a href="#">East Midlands</a></li>
				<li>West Midlands</li>
				<li>East of England</li>
				<li>London</li>
				<li>South East</li>
				<li>South West</li>
				<li>Wales</li>
				<li>British Transport</li>
				<li>Action Fraud</li>
			</ul>
			
		</nav>
	</section>
	
	
	
</div>


<style>



</style>

<footer>
	<div class="inner">
		<div id="images">
			<a href="#"><img id="homeoffice" src="images/home-office.png" /></a>
			<a href="#"><img id="uwe" src="images/uwe.75px.png" /></a>
		</div>
		<p>Copyright © 2013-14<br />
		University of the West of England,<br /> Bristol.<br />
		Assignment by Gunnar Petz&auml;ll (1005826).</p>
	</div>
	
</footer>



</body>
</html>