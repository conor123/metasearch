<?php
/*
//check for required fields from the form
if ((!isset($_POST['username'])) || (!isset($_POST['password']))) {
header("Location: index.php");
exit;
}
*/
// Set Cookie - protect later!!!
$_COOKIE['auth']=1;
// check cookie
if($_COOKIE['auth']==1)
{
//connect to server and select database
//$mysqli = mysqli_connect("localhost", "root", "", "re-search") or die(mysqli_error());

//echo $_COOKIE['user'];
/*
//use mysqli_real_escape_string to clean the input
$username = mysqli_real_escape_string($mysqli, $_POST['username']);
$password = mysqli_real_escape_string($mysqli, $_POST['password']);
//create and issue the query
$sql = "SELECT f_name, l_name FROM auth_users WHERE
username = '".$username."' AND
password = PASSWORD('".$password."')";
$result = mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));
//get the number of rows in the result set; should be 1 if a match
if (mysqli_num_rows($result) == 1) {
//if authorized, get the values of f_name l_name
while ($info = mysqli_fetch_array($result)) {
$f_name = stripslashes($info['f_name']);
$l_name = stripslashes($info['l_name']);
}
//set authorization cookie
setcookie("auth", "1", 0, "/", "localhost", 0);
*/
//create display string
$display_block = '



    <div class="container">

	<!-- Login Area -->
	<h1>User Survey</h1>
	<p>Thank you for taking the time to give some feedback about your experience using the Re-Search Search Engine and helping to make it better.
	</br>Take a moment to read through the <a href = "help.php">help section</a>, it will just take a few seconds and explains how all the aspects of the Search Engine work!
	</br>Please feel free to answer the survey honestly and to write any feedback at the end of the survey up to 250 characters.
	</br>You may select one option from the buttons on the left below each question.</p>

	<div>

	</div>'.'

	<hr>
	<!-- Form Details-->

	<form method = POST action = "survey_results.php">

	<fieldset>
	<legend><strong>Questions</strong></legend>
	
	<h4>Please enter your email</h4>
	<p>(This is kept private and just used for evaluation purposes)</p>
	<input type="email" name="email" size="50" maxlength="50" required/>
	<hr>
	
	<h4>Are you male or female?</h4>
	<input type="radio" name="gender" value="Male" checked/> Male</br>
	<input type="radio" name="gender" value="Female" /> Female</br>
	<hr>
	
	<h4>Please indicate your age group</h4>
	<input type="radio" name="ageGroup" value="under18" /> Under 18</br>
	<input type="radio" name="ageGroup" value="18-25" /> 18 to 25</br>
	<input type="radio" name="ageGroup" value="26-35" checked/> 26 to 35</br>
	<input type="radio" name="ageGroup" value="36-65" /> 36 to 65</br>
	<input type="radio" name="ageGroup" value="over65" /> Over 65
	<hr>
	
	<h4>What Search Engine do you normally use?</h4>
	<input type="radio" name="q1" value="Google" checked/> Google</br>
	<input type="radio" name="q1" value="Bing" /> Bing</br>
	<input type="radio" name="q1" value="Yahoo" /> Yahoo</br>
	<input type="radio" name="q1" value="Blekko" /> Blekko</br>
	<input type="radio" name="q1" value="Other" /> Other
	<hr>
	
	<h4>The User Interface is well presented intuitive and easy to use</h4>
	<input type="radio" name="q2" value="1" /> Strongly Disagree</br>
	<input type="radio" name="q2" value="2" /> Disagree</br>
	<input type="radio" name="q2" value="3" checked/> Neutral</br>
	<input type="radio" name="q2" value="4" /> Agree</br>
	<input type="radio" name="q2" value="5" /> Strongly Agree
	<hr>
	
	<h4>The Search Results were of superior quality to your normal search engine</h4>
	<input type="radio" name="q3" value="1" /> Strongly Disagree</br>
	<input type="radio" name="q3" value="2" /> Disagree</br>
	<input type="radio" name="q3" value="3" checked/> Neutral</br>
	<input type="radio" name="q3" value="4" /> Agree</br>
	<input type="radio" name="q3" value="5" /> Strongly Agree
	<hr>
	
	<h4>Of the three display formats the most useful was</h4>
	<input type="radio" name="q4" value="Aggregated" checked/> Aggregated</br>
	<input type="radio" name="q4" value="Non-aggregated" /> Non-Aggregated</br>
	<input type="radio" name="q4" value="Clustered" /> Clustered</br>
	<hr>
	
	<h4>By general comparison to getting results from your usual Search Engine</h4>
	<input type="radio" name="q5" value="worse" /> Re-Search was slower</br>
	<input type="radio" name="q5" value="equal" checked/> Re-Search was about the same speed</br>
	<input type="radio" name="q5" value="better" /> Re-Search was faster</br>
	<hr>
	
	<h4>The Clustering Option was interesting and/or useful</h4>
	<input type="radio" name="q6" value="1" /> Strongly Disagree</br>
	<input type="radio" name="q6" value="2" /> Disagree</br>
	<input type="radio" name="q6" value="3" checked/> Neutral</br>
	<input type="radio" name="q6" value="4" /> Agree</br>
	<input type="radio" name="q6" value="5" /> Strongly Agree
	<hr>
	
	<h4>The Query Rewrite Option was interesting and/or improved results</h4>
	<input type="radio" name="q7" value="1" /> Strongly Disagree</br>
	<input type="radio" name="q7" value="2" /> Disagree</br>
	<input type="radio" name="q7" value="3" checked/> Neutral</br>
	<input type="radio" name="q7" value="4" /> Agree</br>
	<input type="radio" name="q7" value="5" /> Strongly Agree
	<hr>
	
	<h4>The facility to select User Options from the menu was interesting and/or useful</h4>
	<input type="radio" name="q8" value="1" /> Strongly Disagree</br>
	<input type="radio" name="q8" value="2" /> Disagree</br>
	<input type="radio" name="q8" value="3" checked/> Neutral</br>
	<input type="radio" name="q8" value="4" /> Agree</br>
	<input type="radio" name="q8" value="5" /> Strongly Agree
	<hr>
	
	<h4>Do you have any comments or suggestions</h4>
	<input type="text" name="suggestion" size="250" maxlength="250" />
	</fieldset>
	<hr>
	<button type="submit" name="submit" value="submit" class="btn btn-primary btn-large">Submit</button>

	</form>

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

'; // End of display block
} else {
//redirect back to login form if not authorized
header("Location: index.php");
exit;
}
//close connection to MySQL
//mysqli_close($mysqli);
?>
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
	<?php echo $display_block; ?>
</body></html>