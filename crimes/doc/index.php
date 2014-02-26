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
 * 
 * Newline types (\r\n)
 * http://www.go4expert.com/articles/difference-n-rn-t8021/
 * [Accessed 2014-02-26]
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

<title>Advanced Topics in Web Development Assignment 2014 - Gunnar Petz&auml;ll</title>

<!-- <link rel="shortcut icon" href="favicon.ico"> -->


<link rel='stylesheet' media='screen' href='css/main.css' />


<script>
$.ajax({
	url: "http://cems.uwe.ac.uk/",
		beforeSend: function( xhr ) {
	xhr.overrideMimeType( "text/plain; charset=x-user-defined" );
	}
})
.done(function( data ) {
	if ( console && console.log ) {
	console.log( "Sample of data:", data.slice( 0, 100 ) );
	}
});
	
</script>

</head>
<body>

<header id="top">
	<div class="inner">
		<p>Crime Statistics in England and Wales from June 2013</p>
		<h1><a href="index.php">ATWD</a></h1>
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
	
	</div>

</header>

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