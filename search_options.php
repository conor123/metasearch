<?php session_start();
$_SESSION['result_op'] = (!isset($_SESSION['result_op']) ? $_SESSION['result_op'] : (isset($_GET['result_op']) ? $_GET['result_op'] : $_SESSION['result_op']));
$_SESSION['clust_op'] = (!isset($_SESSION['clust_op']) ? $_SESSION['clust_op'] : (isset($_GET['clust_op']) ? $_GET['clust_op'] : $_SESSION['clust_op']));
$_SESSION['results'] = (!isset($_SESSION['results']) ? $_SESSION['results'] : (isset($_GET['results']) ? $_GET['results'] : $_SESSION['results']));
$_SESSION['queryEx'] = (!isset($_SESSION['queryEx']) ? $_SESSION['queryEx'] : (isset($_GET['queryEx']) ? $_GET['queryEx'] : $_SESSION['queryEx']));
$_SESSION['thesaurus'] = (!isset($_SESSION['thesaurus']) ? $_SESSION['thesaurus'] : (isset($_GET['thesaurus']) ? $_GET['thesaurus'] : $_SESSION['thesaurus']));
$_SESSION['stemmer'] = (!isset($_SESSION['stemmer']) ? $_SESSION['stemmer'] : (isset($_GET['stemmer']) ? $_GET['stemmer'] : $_SESSION['stemmer']));

if(!isset($_SESSION['result_op'])) header("Location: index.php");
include 'classes.php';
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

	<!-- Search Area -->
	<div class="mini-unit">

	<h2 class="font-effect-shadow-multiple" style = "text-shadow: 2px 2px #999999; font-family: 'Audiowide';">Search Options</h2>
	<hr>
	<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<label><strong>Select Search Type</strong></label>
		<input name="result_op" type="radio" value="agg" <?php echo ($_SESSION['result_op']== 'agg') ?  'checked' : ''; ?> /> Aggregated</br>
		<input name="result_op" type="radio" value="nonAgg" <?php echo ($_SESSION['result_op']== 'nonAgg') ?  'checked' : ''; ?> /> Non-Aggregated</br>
		<input name="result_op" type="radio" value="clustered" <?php echo ($_SESSION['result_op']== 'clustered') ?  'checked' : ''; ?> /> Clustered</br>
		<hr>
		
		<label><strong>Select Search Depth (Aggregated & Clustered Results only)</strong></label>
		<label>Note: The larger the number the more results you get, but the longer the results will take!</label>
		<select name="results">
		<option value="10" <?php echo ($_SESSION['results']== 10) ?  'selected' : ''; ?>>10</option>
		<option value="20" <?php echo ($_SESSION['results']== 20) ?  'selected' : ''; ?>>20</option>
		<option value="30" <?php echo ($_SESSION['results']== 30) ?  'selected' : ''; ?>>30</option>
		<option value="40" <?php echo ($_SESSION['results']== 40) ?  'selected' : ''; ?>>40</option>
		<option value="50" <?php echo ($_SESSION['results']== 50) ?  'selected' : ''; ?>>50</option>
		<option value="60" <?php echo ($_SESSION['results']== 60) ?  'selected' : ''; ?>>60</option>
		<option value="70" <?php echo ($_SESSION['results']== 70) ?  'selected' : ''; ?>>70</option>
		<option value="80" <?php echo ($_SESSION['results']== 80) ?  'selected' : ''; ?>>80</option>
		<option value="90" <?php echo ($_SESSION['results']== 90) ?  'selected' : ''; ?>>90</option>
		<option value="100" <?php echo ($_SESSION['results']== 100) ?  'selected' : ''; ?>>100</option>
		</select>
		</br>
		<hr>
		
		<label><strong>Select Clustering Type</strong></label>
		<input name="clust_op" type="radio" value="tf" <?php echo ($_SESSION['clust_op']== 'tf') ?  'checked' : ''; ?> /> Term Frequency</br>
		<input name="clust_op" type="radio" value="b" <?php echo ($_SESSION['clust_op']== 'b') ?  'checked' : ''; ?> /> Binaclustering</br>
		<hr>
		
		<label><strong>Select Query Expansion On/Off</strong></label>
		<input name="queryEx" type="radio" value="on" <?php echo ($_SESSION['queryEx']== 'on') ?  'checked' : ''; ?> /> On &nbsp;
		<input name="queryEx" type="radio" value="off" <?php echo ($_SESSION['queryEx']== 'off') ?  'checked' : ''; ?> /> OFF</br>
		</br>
		
		<label><strong>Select Thesaurus</strong></label>
		<select name="thesaurus">
		<option value="thesaurus_roget.txt" <?php echo ($_SESSION['thesaurus']== "thesaurus_roget.txt") ?  'selected' : ''; ?>>Roget's Thesaurus</option>
		<option value="thesaurus_roget_short.txt" <?php echo ($_SESSION['thesaurus']== "thesaurus_roget_short.txt") ?  'selected' : ''; ?>>Shorter Roget's Thesaurus</option>
		<option value="thesaurus_trec.txt" <?php echo ($_SESSION['thesaurus']== "thesaurus_trec.txt") ?  'selected' : ''; ?>>Trecmatic Thesaurus</option>
		</select>
		</br>
		<label><strong>Expansion Stemmer On/Off</strong></label>
		<input name="stemmer" type="radio" value="on" <?php echo ($_SESSION['stemmer']== 'on') ?  'checked' : ''; ?> /> On &nbsp;
		<input name="stemmer" type="radio" value="off" <?php echo ($_SESSION['stemmer']== 'off') ?  'checked' : ''; ?> /> OFF</br>
		<hr>
		
		<input type="submit" value="Save" class="btn btn-primary btn-large" />
	</form>
	</div><!-- End of Search Area -->
	
	<!-- Search Options -->
	<div>
	<?php  ?>
	</div>
	
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