<!DOCTYPE html>
<html lang="en"><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <title>Re-Search - The Metasearch Engine - Login</title>
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

	<!-- Login Area -->
	<h1>User Survey Results</h1>
<?php
// Message
echo '</br><h4>Thanks for completing the form!</h4></br>';

// arrays
$emails = array();
$genders = array();
$ages = array();
$q1s = array();
$q2s = array();
$q3s = array();
$q4s = array();
$q5s = array();
$q6s = array();
$q7s = array();
$q8s = array();
$suggestions = array();

// Check the user hasn't already submitted data

// Read in data
$target='survey_data.txt';
$linecount = 0;
$th=fopen($target, 'r')or die("</br>!!!Couldn\'t open file, sorry!!!");
// Set Submitted Flag
$submitted = FALSE;
// Count lines
while(!feof($th)){
  $line = fgets($th);
  $linecount++;
}
fclose($th);

// Check if email has been submitted
$th=fopen($target, 'r')or die("</br>!!!Couldn\'t open file, sorry!!!");

//
for($i=0;$i<$linecount/12;$i++)
	{
		$line=fgets($th);
		array_push($emails, trim($line));
		$line=fgets($th);
		$line=fgets($th);
		$line=fgets($th);
		$line=fgets($th);
		$line=fgets($th);
		$line=fgets($th);
		$line=fgets($th);
		$line=fgets($th);
		$line=fgets($th);
		$line=fgets($th);
		$line=fgets($th);
		if(in_array(trim($_POST['email']), array_values($emails)))
		{
			//
			echo 'Note: '.$_POST['email'].', it seems like you have already submitted a survey, if you would like to resubmit one contact me and I\'ll reset it for you!</br>';
			$submitted = TRUE;
			break;
		}
		//echo '</br>'.$emails[$i];
		
	}
	//var_dump($emails);
fclose($th);
//
unset($emails);
$emails = array();

//
if($submitted == FALSE)
{
	echo '</br><strong>Survey Submitted!</strong></br></br>';
	$email = $_POST['email'];
	$gender = $_POST['gender'];
	$ageGroup = $_POST['ageGroup'];
	$q1 = $_POST['q1'];
	$q2 = $_POST['q2'];
	$q3 = $_POST['q3'];
	$q4 = $_POST['q4'];
	$q5 = $_POST['q5'];
	$q6 = $_POST['q6'];
	$q7 = $_POST['q7'];
	$q8 = $_POST['q8'];
	$suggestion = $_POST['suggestion'];
	
	// Open a file and append the information
	$th=fopen($target, 'a')or die("</br>!!!Couldn\'t open file, sorry!!!");

	fwrite($th, $email);
	fwrite($th, PHP_EOL);
	fwrite($th, $gender);
	fwrite($th, PHP_EOL);
	fwrite($th, $ageGroup);
	fwrite($th, PHP_EOL);
	fwrite($th, $q1);
	fwrite($th, PHP_EOL);
	fwrite($th, $q2);
	fwrite($th, PHP_EOL);
	fwrite($th, $q3);
	fwrite($th, PHP_EOL);
	fwrite($th, $q4);
	fwrite($th, PHP_EOL);
	fwrite($th, $q5);
	fwrite($th, PHP_EOL);
	fwrite($th, $q6);
	fwrite($th, PHP_EOL);
	fwrite($th, $q7);
	fwrite($th, PHP_EOL);
	fwrite($th, $q8);
	fwrite($th, PHP_EOL);
	fwrite($th, $suggestion);
	fwrite($th, PHP_EOL);
	fclose($th);
// End of writing data
}

// Open a file to count lines
$th=fopen($target, 'r')or die("</br>!!!Couldn\'t open file, sorry!!!");

// Count lines
$linecount = 0;
while(!feof($th)){
  $line = fgets($th);
  $linecount++;
}
fclose($th);

// Read in data for analysis
$th=fopen($target, 'r')or die("</br>!!!Couldn\'t open file, sorry!!!");
//
//echo '</br>BEFORE!!';
//var_dump($emails);
//echo '</br>Line!!';
//echo $line;
for($i=1;$i<$linecount/12;$i++)
	{
		$line=fgets($th);
		array_push($emails, $line);
		$line=fgets($th);
		array_push($genders, $line);
		$line=fgets($th);
		array_push($ages, $line);
		$line=fgets($th);
		array_push($q1s, $line);
		$line=fgets($th);
		array_push($q2s, $line);
		$line=fgets($th);
		array_push($q3s, $line);
		$line=fgets($th);
		array_push($q4s, $line);
		$line=fgets($th);
		array_push($q5s, $line);
		$line=fgets($th);
		array_push($q6s, $line);
		$line=fgets($th);
		array_push($q7s, $line);
		$line=fgets($th);
		array_push($q8s, $line);
		$line=fgets($th);
		array_push($suggestions, $line);
	}
fclose($th);
//
//Calculations
//print_r(array_count_values($genders));

// Display Data
echo '</br>
	<table border ="1">
		<tr>
			<th style="width:260px">Category</th>
			<th style="width:100px">Result</th>
		</tr>
		<tr><td><strong>1. Total Surveys Completed:</strong></td><td>'. (count($emails)).'</td></tr>';
		echo '<tr><td colspan = "2"><strong>2. Gender</strong></td></tr>';
		$genderCounts = array_count_values($genders);
		foreach($genderCounts as $key=>$value)
			echo '<tr><td>'.$key.' respondents: </td><td>'.$value.' ('.round(($value/count($genders))*100, 2).'%)</tr>';
		echo '<tr><td colspan = "2"><strong>3. Ages</strong></td></tr>';
		$ageCounts = array_count_values($ages);
		foreach($ageCounts as $key=>$value)
			echo '<tr><td>'.$key.' age group respondents: </td><td>'.$value.' ('.round(($value/count($ages))*100, 2).'%)</tr>';
		echo '<tr><td colspan = "2"><strong>4. Search Engines Normally Used</strong></td></tr>';
		$q1Counts = array_count_values($q1s);
		foreach($q1Counts as $key=>$value)
			echo '<tr><td>'.$key.' most used engine: </td><td>'.$value.' ('.round(($value/count($q1s))*100, 2).'%)</tr>';
		
		echo '<tr><td colspan = "2"><strong>5. Application Scoring</strong></td></tr>';
		echo '<tr><td>Usability:</td><td>'.(round((array_sum($q2s)/count($q2s))*20)).'%</td></tr>';
		
		echo '<tr><td>Result Quality:</td><td>'.(round((array_sum($q3s)/count($q3s)))*20).'%</td></tr>';

		echo '<tr><td>Clustering Appraisal:</td><td>'.(round((array_sum($q6s)/count($q6s)))*20).'%</td></tr>';
		
		echo '<tr><td>Query Expansion Appraisal:</td><td>'.(round((array_sum($q7s)/count($q7s)))*20).'%</td></tr>';
		
		echo '<tr><td>User Options Appraisal:</td><td>'.(round((array_sum($q8s)/count($q8s)))*20).'%</td></tr>';
		
		echo '<tr><td colspan = "2"><strong>6. Speed compared to Usual SE</strong></td></tr>';
		$q5Counts = array_count_values($q5s);
		foreach($q5Counts as $key=>$value)
			echo '<tr><td>Re-Search performed: </td><td>'.$key.': '.$value.' ('.round(($value/count($q4s))*100, 2).'%)</tr>';
		
		echo '<tr><td colspan = "2"><strong>7. Preferred Results Display Format</strong></td></tr>';			
		$q4Counts = array_count_values($q4s);
		foreach($q4Counts as $key=>$value)
			echo '<tr><td>'.$key.' was preferred format: </td><td>'.$value.' ('.round(($value/count($q4s))*100, 2).'%)</tr>';
		
		echo '
	</table>';
		
		// Comments
		echo '<hr><table border ="1">
		<tr>
			<th style="width:360px"><strong>Comments</strong></th>
		</tr>';

		foreach($suggestions as $key=>$value)
			echo '<tr><td>'.$value.'</td></tr>';
		
		echo '
	</table>';
	
	// Return to SE
echo '</br></br><a href="index.php">Return to Search Engine</a></br>';
?>
</div>

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