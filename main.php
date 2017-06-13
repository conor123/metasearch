<?php
require "config.php";
// Time query
$time_pre = microtime(true);

// Get the Query from User input
$q = strtolower($_SESSION['query']);

if($_SESSION['queryEx'] == 'on')
{
    // Query Preprocess
    $thesaurus1 = new thesaurus;
    $thesaurus1->loadThesaurusFile($_SESSION['thesaurus']);
    $qX = new query;
    if($_SESSION['stemmer'] == 'off')
        $qX->tokeniseQuery($q);
    else if($_SESSION['stemmer'] == 'on')
    {
        $qX->tokeniseQuery(implode(" ",$qX->stem_list($q)));
    }

    $q = $qX->expandQuery($q, $thesaurus1->returnThesaurus());
}

// Process the Query
$q1 = new query;
$query1 = $q1->complexQueryGoogle($q);
$query2 = $q1->complexQueryBing($q);
$query3 = $q1->complexQueryBlekko($q);
	
	
// AGG
if($_SESSION['result_op']=='agg')
{
	// Instantiate a new API
	$api1 = new api;
	// Instantiate a new formatter with the 3 result sets as properties
	$formatter1 = new formatter(new resultSet(), new resultSet(), new resultSet());
	
	// Google Results
	for($i=0;$i<($_SESSION['results']/10);$i++)
	{
		// Get offset
		$offset = 1+($i*10);
		// Call Google API
		$api1->googleApi($googleApiKey, $id, $query1, $offset);
		
		// Set Google JSON Data
		$formatter1->setGoogleJson($api1->returnGoogleJsonData(), $api1->returnGoogleJsonResultFlag());
		$formatter1->formatGoogleJson($_SESSION['results'], $i*10);
	}
	
	// Bing Results
	for($i=0;$i<($_SESSION['results'] > 50 ? 2 : 1);$i++)
	{
		// Get offset
		$offset = 1+($i*50);
		// Call Bing API
		$api1->bingApi($bingApiKey, $query2, $_SESSION['results'], $offset);
		// Set BING JSON Data
		$formatter1->setBingJson($api1->returnBingJsonData(), $api1->returnBingJsonResultFlag());
		$formatter1->formatBingJson($_SESSION['results'], $i*50);
	}

	// Blekko Results
	$api1->blekkoApi($blekkoApiKey, $query3, $_SESSION['results'], 0);
	// Set BLEKKO JSON Data
	$formatter1->setBlekkoJson($api1->returnBlekkoJsonData(), $api1->returnBlekkoJsonResultFlag());
	$formatter1->formatBlekkoJson($_SESSION['results'], 0);
	
	// Instantate Aggregator
	$aggregator1 = new aggregator(new resultSet());
	// Send result sets 1,2 & 3 to Data Fusion Function
	$aggregator1->dataFusion($api1->returnGoogleJsonResultFlag(), $api1->returnBingJsonResultFlag(), $api1->returnBlekkoJsonResultFlag(), $formatter1->returnResultSet('resultSet1'), $formatter1->returnResultSet('resultSet2'), $formatter1->returnResultSet('resultSet3'));
	// Print Agg Results
	$aggregator1->printResultSetAgg();
	
	// Query Timer
	$time_post = microtime(true);
	$exec_time = $time_post - $time_pre;
	echo '</br>Query Time: '.$exec_time;
	
	// Print Results to file - for metrics
	//$aggregator1->outputResultSetToFile('Aggregated', 'resultSetAgg', $_SESSION['results']);
	//$formatter1->outputResultSetToFile('Google', 'resultSet1', $_SESSION['results']);
	//$formatter1->outputResultSetToFile('Bing', 'resultSet2', $_SESSION['results']);
	//$formatter1->outputResultSetToFile('Blekko', 'resultSet3', $_SESSION['results']);
}

else if($_SESSION['result_op']=='nonAgg')
{
	// Instantiate a new API
	$api1 = new api;
	// Instantiate a new formatter with the 3 result sets as properties
	$formatter1 = new formatter(new resultSet(), new resultSet(), new resultSet());
	
	// Call Google API
	$api1->googleApi($googleApiKey, $id, $query1, $_SESSION['offset']);
	// Set Google JSON Data
	$formatter1->setGoogleJson($api1->returnGoogleJsonData(), $api1->returnGoogleJsonResultFlag());
	$formatter1->formatGoogleJson(100, $_SESSION['offset']);

	// Call Bing API
	$api1->bingApi($bingApiKey, $query2, 10, $_SESSION['offset']);
	// Set BING JSON Data
	$formatter1->setBingJson($api1->returnBingJsonData(), $api1->returnBingJsonResultFlag());
	$formatter1->formatBingJson(100, $_SESSION['offset']);

	// Call Blekko API
	$api1->blekkoApi($blekkoApiKey, $query3, 10, ((int)$_SESSION['offset']/10));
	// Set BLEKKO JSON Data
	$formatter1->setBlekkoJson($api1->returnBlekkoJsonData(), $api1->returnBlekkoJsonResultFlag());
	$formatter1->formatBlekkoJson(100, $_SESSION['offset']);
	
	echo '<div class="row"><div class="span4"><h2>Google</h2>';
	
	// Display Google Results to Screen
	$formatter1->printResultSet('resultSet1', $_SESSION['results']);
	echo '</div><div class="span4"><h2>Bing</h2>';
	
	// Display Bing Results
	$formatter1->printResultSet('resultSet2', $_SESSION['results']);
	echo '</div><div class="span4"><h2>Blekko</h2>';
	
	// Display Blekko Results
	$formatter1->printResultSet('resultSet3', $_SESSION['results']);
	echo '</div></div> <!-- End of Class row -->';
	
	// Query Timer
	$time_post = microtime(true);
	$exec_time = $time_post - $time_pre;
	echo '</br>Query Time: '.$exec_time;

}

else if($_SESSION['result_op']=='clustered')
{
	// Limit Results
	$limiter = 100;
	if(($_SESSION['results'] > $limiter) && ($_SESSION['clust_op']=='tf'))
	{
		$_SESSION['results'] = $limiter;
	}
	
	// ***********************
	// ** TERM FREQ CLUSTERING
	// ***********************
	if($_SESSION['clust_op']=='tf') 
	{
		// Instantiate a new API
		$api1 = new api;
		// Instantiate a new formatter with the 3 result sets as properties
		$formatter1 = new formatter(new resultSet(), new resultSet(), new resultSet());
		
		// Google Results
		for($i=0;$i<($_SESSION['results']/10);$i++)
		{
			// Get offset
			$offset = 1+($i*10);
			// Call Google API
			$api1->googleApi($googleApiKey, $id, $query1, $offset);
			
			// Set Google JSON Data
			$formatter1->setGoogleJson($api1->returnGoogleJsonData(), $api1->returnGoogleJsonResultFlag());
			$formatter1->formatGoogleJson($_SESSION['results'], $i*10);
		}
		
		// Bing Results
		for($i=0;$i<($_SESSION['results'] > 50 ? 2 : 1);$i++)
		{
			// Get offset
			$offset = 1+($i*50);
			// Call Bing API
			$api1->bingApi($bingApiKey, $query2, $_SESSION['results'], $offset);
			// Set BING JSON Data
			$formatter1->setBingJson($api1->returnBingJsonData(), $api1->returnBingJsonResultFlag());
			$formatter1->formatBingJson($_SESSION['results'], $i*50);
		}

		// Blekko Results
		$api1->blekkoApi($blekkoApiKey, $query3, $_SESSION['results'], 0);
		// Set BLEKKO JSON Data
		$formatter1->setBlekkoJson($api1->returnBlekkoJsonData(), $api1->returnBlekkoJsonResultFlag());
		$formatter1->formatBlekkoJson($_SESSION['results'], 0);
		
		// Instantate Aggregator
		$aggregator1 = new aggregator(new resultSet());
		// Send result sets 1,2 & 3 to Data Fusion Function
		$aggregator1->dataFusion($api1->returnGoogleJsonResultFlag(), $api1->returnBingJsonResultFlag(), $api1->returnBlekkoJsonResultFlag(),$formatter1->returnResultSet('resultSet1'), $formatter1->returnResultSet('resultSet2'), $formatter1->returnResultSet('resultSet3'));
		
		// Instantiate Cluster Object
		$cluster1 = new cluster;
		
		// Instantiate Stopword Dictionary
		$stopwordDictionary = new dictionary('stop-words-english1.txt');
		//$stopwordDictionary->loadStopwordFile();
		// Add Query to Stopwords
		$stopwordDictionary->addQueryToStopwords($cluster1->tokeniseString($q));
		$stopwords = $stopwordDictionary->returnStopwords();
		
		// Can use Title or Snippets for Clusters - Using Snippets
		
		//$aggTitles = $aggregator1->returnResultSetAggTitles();
		// Get array of snippets in list
		//$aggSnippets = $aggregator1->returnResultSetAggSnippets();
		
		// Find the cluster terms of interest
		// But don't include stopwords
		//$cluster1->findTerms($aggTitles, $stopwords);
		$cluster1->findTerms($aggregator1->returnResultSetAggSnippets(), $stopwords);
		// Count Term Freq
		//$cluster1->countTermFrequency($aggTitles);
		$cluster1->countTermFrequency($aggregator1->returnResultSetAggSnippets());
		// Set most frequet terms
		$cluster1->setMostFrequentTerms(10);
		//$cluster1->stopwordRemoval($stopwords);
		
		echo '<div class="row"><div class="span3"><h2>F-Clusters</h2>';
		// Print Clustered Terms
		$cluster1->displayMostFrequentTerms($q);
		// $cluster1->displayMostFrequentBinTerms($q);
		echo '</div><div class="span5"><h2>Results</h2>';
		// Print Cluser Term Results
		$aggregator1->printResultSetAggCluster(isset($_GET['term'])?$_GET['term']:$_GET['term']='');
		// end of DIV
		echo '</div></div> <!-- End of Class row -->';
		
		// Query Timer
		$time_post = microtime(true);
		$exec_time = $time_post - $time_pre;
		echo '</br>Query Time: '.$exec_time;
	}
	// ******************
	// ** BINA CLUSTERING
	// ******************
	else if($_SESSION['clust_op']=='b') // ** BINA CLUSTERING
	{
			// Instantiate a new API
		$api1 = new api;
		// Instantiate a new formatter with the 3 result sets as properties
		$formatter1 = new formatter(new resultSet(), new resultSet(), new resultSet());
		
		// Google Results
		for($i=0;$i<($_SESSION['results']/10);$i++)
		{
			// Get offset
			$offset = 1+($i*10);
			// Call Google API
			$api1->googleApi($googleApiKey, $id, $query1, $offset);
			
			// Set Google JSON Data
			$formatter1->setGoogleJson($api1->returnGoogleJsonData(), $api1->returnGoogleJsonResultFlag());
			$formatter1->formatGoogleJson($_SESSION['results'], $i*10);
		}
		
		// Bing Results
		for($i=0;$i<($_SESSION['results'] > 50 ? 2 : 1);$i++)
		{
			// Get offset
			$offset = 1+($i*50);
			// Call Bing API
			$api1->bingApi($bingApiKey, $query2, $_SESSION['results'], $offset);
			// Set BING JSON Data
			$formatter1->setBingJson($api1->returnBingJsonData(), $api1->returnBingJsonResultFlag());
			$formatter1->formatBingJson($_SESSION['results'], $i*50);
		}

		// Blekko Results
		$api1->blekkoApi($blekkoApiKey, $query3, $_SESSION['results'], 0);
		// Set BLEKKO JSON Data
		$formatter1->setBlekkoJson($api1->returnBlekkoJsonData(), $api1->returnBlekkoJsonResultFlag());
		$formatter1->formatBlekkoJson($_SESSION['results'], 0);
		
		// Instantate Aggregator
		$aggregator1 = new aggregator(new resultSet());
		// Send result sets 1,2 & 3 to Data Fusion Function
		$aggregator1->dataFusion($api1->returnGoogleJsonResultFlag(), $api1->returnBingJsonResultFlag(), $api1->returnBlekkoJsonResultFlag(),$formatter1->returnResultSet('resultSet1'), $formatter1->returnResultSet('resultSet2'), $formatter1->returnResultSet('resultSet3'));
		
		// Instantiate Cluster Object
		$cluster1 = new cluster;
		
		// Instantiate Stopword Dictionary
		$stopwordDictionary = new dictionary('stop-words-english2-short.txt');
		//$stopwordDictionary->loadStopwordFile();
		// Add Query to Stopwords
		$stopwordDictionary->addQueryToStopwords($cluster1->tokeniseString($q));
		$stopwords = $stopwordDictionary->returnStopwords();
		
		// Find the cluster terms of interest
		$cluster1->findBinaTerms($aggregator1->returnResultSetAggSnippets());
		// Count Term Freq
		//$cluster1->countTermFrequency($aggTitles);
		//$cluster1->countTermFrequency($aggregator1->returnResultSetAggSnippets());
		// Set most frequet terms
		//$cluster1->setMostFrequentTerms(10);
		//$cluster1->stopwordRemoval($stopwords);
		//echo $cluster1->countClusteredTerms();

		//$cluster1->displayClusteredTerms();
		$cluster1->setDocumentBinatures($aggregator1->returnResultSetAggSnippets());
		//$cluster1->printBinatures();
		//$cluster1->printBinatureSums();
		//echo $aggSnippets[0];
		$ticks = 3;
		$cluster1->setBindroids($ticks);
		$cluster1->binBinatures($ticks);
		
		$cluster1->setBinTerms(($ticks+1));
		
		echo '<div class="row"><div class="span3"><h2>Bina-clusters</h2>';
		// Print Clustered Terms
		//$cluster1->displayMostFrequentTerms($q);
		// $cluster1->displayMostFrequentBinTerms($q);
		$cluster1->displayBinTerms($q);
		
		echo '</div><div class="span5"><h2>Results</h2>';
		// Print Cluser Term Results
		//$aggregator1->printResultSetAggCluster(isset($_GET['term'])?$_GET['term']:$_GET['term']='');
		//
		$aggregator1->printResultSetAggBinCluster((isset($_GET['binTerm'])?$_GET['binTerm']:$_GET['binTerm']=''), $cluster1->returnBins(), $cluster1->returnBinatureSums());

		// end of DIV
		
		// Helper
		//echo '</div><div class="span3"><h2>Helper</h2>';

		// end of DIV
		echo '</div></div> <!-- End of Class row -->';
		
		// Query Timer
		$time_post = microtime(true);
		$exec_time = $time_post - $time_pre;
		echo '</br>Query Time: '.$exec_time;
	}
}
?>