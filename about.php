<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title>Re-Search - Metasearch Engine</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">

<!-- Le styles -->
<link href="css/bootstrap.css" rel="stylesheet">
<style type="text/css">
  body {
	padding-top: 60px;
	padding-bottom: 40px;
  }
</style>
<link href="css/bootstrap-responsive.css" rel="stylesheet">
<link href='http://fonts.googleapis.com/css?family=Audiowide' rel='stylesheet' type='text/css'>
<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
  <script src="../assets/js/html5shiv.js"></script>
<![endif]-->

<!-- Fav and touch icons -->
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="http://twitter.github.io/bootstrap/assets/ico/apple-touch-icon-144-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="http://twitter.github.io/bootstrap/assets/ico/apple-touch-icon-114-precomposed.png">
  <link rel="apple-touch-icon-precomposed" sizes="72x72" href="http://twitter.github.io/bootstrap/assets/ico/apple-touch-icon-72-precomposed.png">
				<link rel="apple-touch-icon-precomposed" href="http://twitter.github.io/bootstrap/assets/ico/apple-touch-icon-57-precomposed.png">
							   <link rel="shortcut icon" href="img/favicon.ico">
</head>

<body>
<?php include 'navbar.php'; ?>
<div class="container">

	<!-- Hero Area -->
	<div class="mini-unit">

	<h2 class="font-effect-shadow-multiple" style = "text-shadow: 2px 2px #999999; font-family: 'Audiowide';">About</h2>
	<p>Re-Search is a Metasearch engine that was developed as part of a software engineering project for the Master's Degree in Computer Science at University College, Dublin during the summer of 2013.</p></br>
	
	</div><!-- End of Hero Area -->
	
	<!-- Lower Area -->
	<div>
	
	<h3>Features</h3>
	<hr>
	<h4>General Features</h4>
	<p>Re-Search is a search engine that takes your query and returns a list of documents intended to satisfy your information needs.
	It is of the metasearch class, which means that it uses three underlying search engines and creates a list of the best results from all three.
	The idea is that the results returned would be better than any single search engine on its own. Research is intended to have the following general features:</p>
	<ol>
		<li>Be useful to the end user</li>
		<li>Be intuitive and easy to use</li>
		<li>Be fast and efficient</li>		
	</ol>
	<hr>
	<h4>Search Features</h4>
	<p>To use Re-Search just enter your query and hit the search button. 
	</br>For advanced users you have the option for viewing the results as:</p>
	<ol>
		<li>Aggregated</li>
		<li>Non-Aggregated</li>
		<li>Clustered</li>
	</ol>	
	<p>For a detailed description of these display features and other complex search features see the help section <a href = "help.php#resultTypes" >here.</a></p>
	<hr>

	<h4>Technical Features</h4>
	<p>Re-Search has a number of features of interest to software developers:</p>
	<ol>
		<li>Responsive Web Design for the User Interface</li>
		<li>Object-oriented code</li>
		<li>Languages: PHP, HTML5 & CSS3</li>
		<li>Clustering feature: Term frequency and a new experimental technique called Binaclustering</li>
	</ol>	
	<hr>
	</div><!-- End of Lower Area -->
	
	<hr>
	<!-- Footer -->
	<footer>
	<p>© Re-Search 2013</p>
	</footer>

</div> <!-- End of container -->
<!-- Le javascript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="js/jquery.js"></script>
<script src="js/bootstrap-transition.js"></script>
<script src="js/bootstrap-alert.js"></script>
<script src="js/bootstrap-modal.js"></script>
<script src="js/bootstrap-dropdown.js"></script>
<script src="js/bootstrap-scrollspy.js"></script>
<script src="js/bootstrap-tab.js"></script>
<script src="js/bootstrap-tooltip.js"></script>
<script src="js/bootstrap-popover.js"></script>
<script src="js/bootstrap-button.js"></script>
<script src="js/bootstrap-collapse.js"></script>
<script src="js/bootstrap-carousel.js"></script>
<script src="js/bootstrap-typeahead.js"></script>

</body>
</html>