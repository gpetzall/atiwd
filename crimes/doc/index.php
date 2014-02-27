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
require_once (__DIR__ .'../../includes/config.php');

// Validate GET information.
if (isset($_GET['vis'])) { // XML or JSON.
	$vis = $_GET['vis'];
} else {
	$vis = NULL;
}


// Create a simple xml object for easy reading.
$xml = simplexml_load_file('../'.$inputFilename);

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
				<li><a href="http://www.cems.uwe.ac.uk/~b2-argo/atwd/test/g2-petzall" target="_blank">Ben Argo's API test</a> (new tab)</li>
				<li><a href="../6-2013/reset" target="_blank">Reset link</a>, if needed (new tab)</li>
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
	
	<h2>Challenges</h2>
	
	
	
	
	
	
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