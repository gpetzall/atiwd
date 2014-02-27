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
 * course component, dealing with part "3.0.0", visualisation, and "5.0.0", documentation.
 * 
 * Pages used as help to make this code:
 * 
 * Newline types (\r\n)
 * http://www.go4expert.com/articles/difference-n-rn-t8021/
 * [Accessed 2014-02-26]
 * 
 * jQuery repository, using 1.9.0
 * https://developers.google.com/speed/libraries/devguide?hl=en#jquery
 * [Accessed 2014-02-26]
 * 
 * jQuery "foreach"
 * https://api.jquery.com/jQuery.each/
 * [Accessed 2014-02-26]
 * 
 * Ajax to pull data
 * https://api.jquery.com/jQuery.ajax/
 * [Accessed 2014-02-26]
 * 
 * Pushing data
 * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/push
 * [Accessed 2014-02-26]
 * 
 * Chart.js - Chart library for the graphs
 * http://www.chartjs.org/
 * [Accessed 2014-02-26]
 * 
 * Making a coloured legend (Ben Argo)
 * https://github.com/benargo/atwd/blob/master/media/js/client.js
 * [Accessed 2014-02-26]
 * 
 * Special credit to Ben Argo for the test script
 * http://www.cems.uwe.ac.uk/~b2-argo/atwd/test/g2-petzall
 * [Accessed 2014-02-27]
 * 
*/

// Run configuration.
require_once (__DIR__.'../../includes/config.php');

// Validate GET information.
if (isset($_GET['vis'])) { // XML or JSON.
	$vis = $_GET['vis'];
} else {
	$vis = NULL;
}


// Create a simple xml object for easy reading.
$xml = simplexml_load_file(__DIR__.'/crimes.xml');

// Checking if the provided visualisation request is valid.
$region_element = $xml->xpath("/crimes/region[@id='$vis']"); // A little bit of XPATH grabbing all elements with GET's id value.
$region_element = array_shift($region_element); // Returns the simple xml element.

if ($region_element instanceof SimpleXMLElement) // If a simple xml element was returned (checks if the region is valid).
{
	$current_region = ucwords(str_replace('_', ' ', $vis )); // Can use $vis, as it's confirmed it's a region
}
else
{
	$vis = NULL;
}

?>
<!DOCTYPE html>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title><?php if ($vis != NULL)
{
	echo $current_region; ?> visualised - ATWD 2014 (Gunnar Petz&auml;ll)<?php
}
else
{
	?>Documentation - ATWD 2014 (Gunnar Petz&auml;ll)<?php
}
?></title>

<!-- <link rel="shortcut icon" href="favicon.ico"> -->


<link rel='stylesheet' media='screen' href='css/main.css' />
<link rel="shortcut icon" href="http://police.uk/static/img/favicon.ico"/>

<script src="js/Chart.js"></script>
<meta name = "viewport" content = "initial-scale = 1, user-scalable = no">


<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>

<script type="text/javascript">
<!--

// Loading this after all the HTML has loaded.
$(function() {
	// Some premade colours matching the site.
	var chartColors = ['#1b4080', '#3d5c93', '#1c3763', '#899cbd', '#102850', '#6b7990', '#d2dae6'];
	// Setting the Chart.js variables
	var pieData = [];
	var barChartData = {labels: [''], datasets: []}; // Also setting only one label, or else it only shows one color...
	// Pulling data using AJAX from the normal getRegion.php file (or "[GET] Specific Region").
	$.ajax({
		url: "http://www.cems.uwe.ac.uk/~g2-petzall/atwd/crimes/6-2013/<?php echo $vis; ?>/json",
		async: false
	})
	.done(function(data) {
		// jQuery for each area, the function handles key/value.
		$.each(data.response.crimes.region.area, function(index, value) {
			// Just pushing value and color, simple.
			pieData.push({value: value.total, color: chartColors[index]});
			// Slightly more of a headache to figure out (see the above label).
			barChartData.datasets.push({
				fillColor: chartColors[index],
				strokeColor: chartColors[index],
				data: [value.total]
			});
			// (Look in the references above) Section that has the classes of "charts" AND "legend"; pulls data in to make colours
			$('section.charts.legend').append('<p class="legend item'+ index +'">'+ value.id +' ('+ value.total +')</p>');
		});
		// Add the totals. ONCE.
		$('section.charts.legend').append('<p class="legend total">'+ data.response.crimes.region.id +' total: '+ data.response.crimes.region.total);
	});
	// Run the changes on the loaded charts.
	var myLine = new Chart(document.getElementById("canvas2").getContext("2d")).Bar(barChartData);	
	var myPie = new Chart(document.getElementById("canvas1").getContext("2d")).Pie(pieData);
});
-->
</script>

</head>
<body>

<header id="top">
	<div class="inner">
		<p>Crime Statistics in England and Wales from June 2013</p>
		<h1><a href="index.php">ATWD.UWE</a></h1>
	</div>
</header>

<header id="main_header">
	<div class="inner">
<?php
if ($vis != NULL)
{
?>		<ul id="breadcrumbs">
			<li><a href="index.php">Home</a> &gt; </li>
			<li><a href="index.php?vis=<?php echo $vis;?>"><?php echo $current_region .' visual data' ; ?></a> &gt; </li>
		</ul>
		
		<h1><?php echo $current_region; ?></h1>
		
		<p class="intro">Crime statistics visualised for the <?php echo $current_region; ?>.</p>
<?php
}
else
{
?>
		<h1 id="doc">Documentation</h1>
		<p class="intro">All the details about this assignment.</p>
<?php
}
?>
	</div> <!-- /Main header inner div -->
</header> <!-- /Main header -->

<div id="main_content">
	<section id="navigation">
		<nav id="get">
			<h2>Visualise a region</h2>
			<ul>			
<?php
foreach($xml->children() as $region)
{
	// Grabbing and formatting the region.
	
	$item_u = (string) $region->attributes()['id'];
	$item_f = ucwords(str_replace('_', ' ', $item_u )); // ucwords is Very Useful.
	
	?>				<li><a href="<?php echo "index.php?vis=$item_u"; ?>"><?php echo $item_f; ?></a></li>
<?php
} // End of region list foreach loop
?>
			</ul>
			
			<h2>Handy links</h2>
			<ul>
				<li><a href="https://github.com/gpetzall/atiwd" target="_blank">ATWD Git</a> (new tab)</li>
				<li><a href="http://www.cems.uwe.ac.uk/~b2-argo/atwd/test/g2-petzall" target="_blank">Ben Argo's API test</a> (new tab)</li>
				<li><a href="../6-2013/reset" target="_blank">Reset link</a>, if needed (new tab)</li>
				<li><a href="http://www.police.uk/" target="_blank">Police.uk</a> (new tab)</li>
			</ul>
		</nav>
	</section>
	
<?php
if ($vis != NULL) // If requesting visualisation: Show it!
{
?>	<canvas id="canvas2" height="200" width="400"></canvas>
	
	<section class="charts legend"></section>
	
	<canvas id="canvas1" height="450" width="450"></canvas>
	
<?php
}
else // If NOT requesting visualisation: Show documentation.
{
?>
	
	<style>

	</style>
	
	<h2>Initial challenges</h2>
	
	<p>Not being the most skilled of programmers, there was plenty of challenges in this assignment. The first was the overly "dirty" XLSX crime
		statistics from the source and getting used to coding again. It took a lotof time just to formulate an initial plan of action for the coding and
		for the XML structure on paper (as a visual learner
		
		(notes: <a href="images/xml1.jpg">1</a>, <a href="images/xml2.jpg">2</a>, <a href="images/csvtoxml1.jpg">3</a>, <a href="images/csvtoxml1.jpg">4</a>))
		
		it wasn’t so much hard as it was time-consuming and also required a lot of reading online.
		The spec seemed to allow editing the document, but I decided against it as it probably would have taken a long time anyway (sub-category crimes etc.) and no further code insight.</p>
	
	<h2>The DOM</h2>
	<p>The second, and perhaps greatest, challenge was with XML manipulation. Initial attempts with PHP's DOMdocument failed because it was hard to visualise it. Reading the instructions and writing down how it works made it possible to make DOM-coding a lot easier, "seeing" how it fits like a puzzle to my inner eye.</p>
	
	<h2>XML issues</h2>
	<p>SimpleXML and XPath usage was easier, but later stages of the project required deleting nodes (for "[POST]" and "[DELETE]"), which halted the development for many hours. It took a long time to understand just how "sticky" SimpleXML is; having to unset the first array item and then unsetting the variable. This is not really that hard, but it is also not very semantic.</p>
	<p>JSON looked like a challenge at first, but really is not. It has been a pleasure to work with and great to be introduced to.</p>
	
	<h2>.htaccess</h2>
	<p>I have had previous problems with .htaccess and I struggle with regular expressions (RegEx). I do not feel I learned much other than getting used to .htaccess again, but it took a lot of time and research to get especially the "[POST]" requirement working, with its odd URL. However, getting used to it again is very useful.</p>
	
	<h2>Learning outcomes</h2>
	<p>Each of the challenges I faced have brought me confidence in my craft skill of programming. Managing to (more or less) learn all of the functionality for the assignment in the Christmas break was the main reason for this confidence, especially since we had not touched on much of these skills in lectures. My most treasured learning experience from this assignment is knowing I actually can code if I put my mind to it.</p>
	
	<h2>Highlights</h2>
	<p>The experiences that meant most was the code not required in the spec. Making the reset button was the first and making the "[POST]" function able to add areas in any region, replacing the old item was the biggest and most satisfying.</p>
	
	<h2>Other reflections</h2>
	<p>Please compare the HTML design with <a href="http://www.police.uk/">Police.uk</a>.</p>
	<p>This assignment may have had its flaws and caused some frustration, especially in terms of its sheer size, source material, specification ambiguity, but it honestly has been enormously helpful to improve my coding skills. Both for my Digital Media project but also looking for jobs after university.</p>
	<p>This frustration may be my everyday life after university, but let us hope this is not the case. Regardless: I am now more prepared.</p>
	
	
	
	<h2>Assignment task examples and source files</h2>
	<dl>
		<dt>Important links</dt>
			<dd>Github repository - <a href="https://github.com/gpetzall/atiwd">https://github.com/gpetzall/atiwd</a></dd>
			<dd>Argo's API test - <a href="http://www.cems.uwe.ac.uk/~b2-argo/atwd/test/g2-petzall">http://www.cems.uwe.ac.uk/~b2-argo/atwd/test/g2-petzall</a></dd>
			
		<dt>Part 1.1 - Data conversion</dt>
			<dd>Source: <a href="http://www.cems.uwe.ac.uk/~p-chatterjee/modules/atwd/assignment/policeforceareadatatablesyearendingjune13_tcm77-330992.xlsx">http://www.cems.uwe.ac.uk/~p-chatterjee/...tablesyearendingjune13_tcm77-330992.xlsx</a></dd>
			<dd>CSV: <a href="input.csv">http://www.cems.uwe.ac.uk/~g2-petzall/atwd/crimes/doc/input.csv</a></dd>
			<dd><em><strong>Converter: <a href="../csvToXML.php">http://www.cems.uwe.ac.uk/~g2-petzall/atwd/crimes/csvToXML.php</a></strong></em></dd>
			<dd>Resulting XML: <a href="backup.xml">http://www.cems.uwe.ac.uk/~g2-petzall/atwd/crimes/doc/backup.xml</a></dd>
			<dd class="source">Sources: <a href="../csvToXML.phps">csvToXML.phps</a> (<a href="https://github.com/gpetzall/atiwd/blob/master/crimes/csvToXML.php">Git</a>)</dd>
		
		<dt>Part 1.2 - Validation</dt>
			<dd><em><strong>Schema validation XSD: <a href="schema.xsd">http://www.cems.uwe.ac.uk/~g2-petzall/atwd/crimes/doc/schema.xsd</a></strong></em></dd>
			<dd>Secondary schema for further tasks: <a href="schema2.xsd">http://www.cems.uwe.ac.uk/~g2-petzall/atwd/crimes/doc/schema2.xsd</a></dd>
		
		<dt>Part 2.1.1 - Recorded crimes by region</dt>
			<dd>XML response: <a href="../6-2013/xml">http://www.cems.uwe.ac.uk/~g2-petzall/atwd/crimes/6-2013/xml</a></dd>
			<dd>JSON response: <a href="../6-2013/json">http://www.cems.uwe.ac.uk/~g2-petzall/atwd/crimes/6-2013/json</a></dd>
			<dd class="source">Sources: <a href="../get.phps">get.phps</a> (<a href="https://github.com/gpetzall/atiwd/blob/master/crimes/get.php">Git</a>)</dd>
			
		<dt>Part 2.1.2 - Recorded crimes for specific region</dt>
			<dd>XML response: <a href="../6-2013/south_west/xml">http://www.cems.uwe.ac.uk/~g2-petzall/atwd/crimes/6-2013/south_west/xml</a></dd>
			<dd>JSON response: <a href="../6-2013/south_west/json">http://www.cems.uwe.ac.uk/~g2-petzall/atwd/crimes/6-2013/south_west/json</a></dd>
			<dd class="source">Sources: <a href="../getRegion.phps">getRegion.phps</a> (<a href="https://github.com/gpetzall/atiwd/blob/master/crimes/getRegion.php">Git</a>)</dd>
			
		<dt>Part 2.2.1 - Update total for BTP or region</dt>
			<dd>XML response: <a href="../6-2013/put/british_transport_police:51970/xml">http://www.cems.uwe.ac.uk/~g2-petzall/atwd/crimes/6-2013/put/british_transport_police:51970/xml</a></dd>
			<dd>JSON response: <a href="../6-2013/put/british_transport_police:51970/json">http://www.cems.uwe.ac.uk/~g2-petzall/atwd/crimes/6-2013/put/british_transport_police:51970/json</a></dd>
			<dd class="source">Sources: <a href="../update.phps">update.phps</a> (<a href="https://github.com/gpetzall/atiwd/blob/master/crimes/update.php">Git</a>)</dd>
			
		<dt>Part 2.2.2 - Create are and sample data</dt>
			<dd>XML response: <a href="../6-2013/post/south_west/wessex/hom:4-vwi:15-vwoi:25/xml">http://www.cems.uwe.ac.uk/~g2-petzall/.../post/south_west/wessex/hom:4-vwi:15-vwoi:25/xml</a></dd>
			<dd>JSON response: <a href="../6-2013/post/south_west/wessex/hom:4-vwi:15-vwoi:25/json">http://www.cems.uwe.ac.uk/~g2-petzall/.../post/south_west/wessex/hom:4-vwi:15-vwoi:25/json</a></dd>
			<dd><em><strong>Note: It is possible to add areas in other regions than the South West, but not multiples of the same area in one region.</strong></em></dd>
			<dd class="source">Sources: <a href="../create.phps">create.phps</a> (<a href="https://github.com/gpetzall/atiwd/blob/master/crimes/create.php">Git</a>)</dd>
			
		<dt>Part 2.2.3 - Delete an area</dt>
			<dd><em><strong>Note: Error is thrown if the specified area does not exist.</strong></em></dd>
			<dd>XML response: <a href="../6-2013/delete/wessex/xml">http://www.cems.uwe.ac.uk/~g2-petzall/atwd/crimes/6-2013/delete/wessex/xml</a></dd>
			<dd>JSON response: <a href="../6-2013/delete/wessex/json">http://www.cems.uwe.ac.uk/~g2-petzall/atwd/crimes/6-2013/delete/wessex/json</a></dd>
			<dd><em><strong>Note: It is possible to remove ANY area. Also possible to specify which region to delete from.</strong></em></dd>
			<dd>XML response: <a href="../6-2013/delete/wessex/xml">http://www.cems.uwe.ac.uk/~g2-petzall/atwd/crimes/6-2013/delete/south_west/wessex/xml</a></dd>
			<dd>JSON response: <a href="../6-2013/delete/wessex/json">http://www.cems.uwe.ac.uk/~g2-petzall/atwd/crimes/6-2013/delete/south_west/wessex/json</a></dd>
			<dd class="source">Sources: <a href="../delete.phps">delete.phps</a> (<a href="https://github.com/gpetzall/atiwd/blob/master/crimes/delete.php">Git</a>)</dd>
		
		<dt>Part 2.3 - Error codes, a few examples</dt>
			<dd>Also see the comments in the error.php file. These are caught differently; balancing HTTP spec and assignment spec.</dd>
			<dd>501 - Pattern not recognised: <a href="../6-2013/">http://www.cems.uwe.ac.uk/~g2-petzall/atwd/crimes/6-2013/</a></dd>
			<dd>404 - Non-existing region: <a href="../6-2013/narnia/xml">http://www.cems.uwe.ac.uk/~g2-petzall/atwd/crimes/6-2013/narnia/xml</a></dd>
			<dd>404 - Non-existing area: <a href="../6-2013/delete/veryoddplace/xml">http://www.cems.uwe.ac.uk/~g2-petzall/atwd/crimes/6-2013/delete/veryoddplace/xml</a></dd>
			<dd>404 - Non-existing update value: <a href="../6-2013/put/british_transport_police:/xml">http://www.cems.uwe.ac.uk/~g2-petzall/.../6-2013/put/british_transport_police:/xml</a></dd>
			<dd class="source">Sources: <a href="../error.phps">error.phps</a> (<a href="https://github.com/gpetzall/atiwd/blob/master/crimes/error.php">Git</a>)</dd>
		
		<dt>Part 3 - Visualisation</dt>
			<dd>Please select any region in the right hand side menu.</dd>
			<dd class="source">Sources: <a href="../doc/index.phps">index.phps</a> (<a href="https://github.com/gpetzall/atiwd/blob/master/crimes/doc/index.php">Git</a>)</dd>
			
		<dt>Part 4 - Caching</dt>
			<dd><strong>Locally:</strong> Using the browser's JavaScript engine. Can be used in any API. (See the config file.)</dd>
			<dd><strong>Server-side:</strong> The scripts create a cache xml file that loads if it's roughly as newly updated as the main database. If the main database is more updated, it loads it but also updates the cache. Not overly compatible with local cache (but nobody expected them to be).</dd>
			<dd>Use the visualisation and see the config file (<a href="../includes/config.phps">PHPS</a>/<a href="https://github.com/gpetzall/atiwd/blob/master/crimes/includes/config.php">Git</a>) for the main code. Each individual script also have functions relating to the server cache, such as checking for it and updating if the main database is more recently updated.</dd>
			<!-- <dd class="source">Sources: <a href="../cache.phps">cache.phps</a> (<a href="">Git</a>)</dd> -->
			
		<dt>Other files</dt>
			<dd>.htaccess in all its glory - <a href="https://github.com/gpetzall/atiwd/blob/master/crimes/.htaccess">Github only</a></dd>
			<dd>Reset file to remove custom data - <a href="../reset.phps">reset.phps</a> (<a href="https://github.com/gpetzall/atiwd/blob/master/crimes/reset.php">Git</a>)</dd>
			<dd>Source file to create PHPS files - <a href="../source.phps">source.phps</a> (<a href="https://github.com/gpetzall/atiwd/blob/master/crimes/source.php">Git</a>)</dd>
	</dl> <!-- <dd></dd> -->
	
<?php
} // End of visualisation/documentation IF.
?>
</div>

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