<?php session_start();
$_SESSION['result_op'] = (isset($_SESSION['result_op']) ? $_SESSION['result_op'] : 'agg');
$_SESSION['clust_op'] = (isset($_SESSION['clust_op']) ? $_SESSION['clust_op'] : 'tf');

$_SESSION['results'] = (isset($_SESSION['results']) ? $_SESSION['results'] : 10);
$_SESSION['queryEx'] = (isset($_SESSION['queryEx']) ? $_SESSION['queryEx'] : 'off');
$_SESSION['thesaurus'] = (isset($_SESSION['thesaurus']) ? $_SESSION['thesaurus'] : 'thesaurus_roget_short.txt');
$_SESSION['stemmer'] = (isset($_SESSION['stemmer']) ? $_SESSION['stemmer'] : 'off');
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
	<link href='http://fonts.googleapis.com/css?family=Audiowide' rel='stylesheet' type='text/css'>
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
    </style>
    <link href="css/bootstrap-responsive.css" rel="stylesheet">

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
      <div class="hero-unit">
        <center>
		<h1 class="font-effect-shadow-multiple" style = "color:#150C63; text-align:center;text-shadow: 2px 2px #999999; font-family: 'Audiowide';">Re-Search</h1></br>
		<form method="get" action="./search.php">
			<input style="font-weight:bold;" type="text" name="q" size = "10" required />
			<input type="hidden" name="offset" value = "1" required /></br>
			<input type="submit" value="Search" class="btn btn-primary btn-large" style="margin-top:20px;"/>
			
			</br>
			<hr>
			<div style = "width: 160px; text-align:left;">
				<input name="result_op" type="radio" value="agg" <?php echo ($_SESSION['result_op']== 'agg') ?  'checked' : ''; ?> /> Aggregated</br>
				<input name="result_op" type="radio" value="nonAgg" <?php echo ($_SESSION['result_op']== 'nonAgg') ?  'checked' : ''; ?> /> Non-Aggregated</br>
				<input name="result_op" type="radio" value="clustered" <?php echo ($_SESSION['result_op']== 'clustered') ?  'checked' : ''; ?> /> Clustered</br>
			</div>
			</br>  
			
        </form>
		</center>
	  </div>

      <hr>

      <footer>
        <p>© Re-Search 2013</p>
      </footer>

    </div> <!-- /container -->

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

</body></html>