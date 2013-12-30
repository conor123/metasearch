<?php
echo '<h1>Analyser V1.5</h1>';

class analyser
{
	// properties
	private $queries = array();
	
	private $goldStandardResults = array();
	private $goldStandardDocsRetrieved = 100;
	
	private $googleResults = array();
	private $googlePrecision = array();
	private $googleAveragePrecisions = array();
	private $googleDocsRetrieved = 0;
	private $googleRelevantResults = 0;
	private $googleRelevantResultsAtNum = 0;
	private $googleAP = 0;
	
	private $bingResults = array();
	private $bingPrecision = array();
	private $bingAveragePrecisions = array();
	private $bingDocsRetrieved = 0;
	private $bingRelevantResults = 0;
	private $bingRelevantResultsAtNum = 0;
	private $bingAP = 0;
	
	private $blekkoResults = array();
	private $blekkoPrecision = array();
	private $blekkoAveragePrecisions = array();
	private $blekkoDocsRetrieved = 0;
	private $blekkoRelevantResults = 0;
	private $blekkoRelevantResultsAtNum = 0;
	private $blekkoAP = 0;
	
	private $aggregatedResults = array();
	private $aggregatedPrecision = array();
	private $aggregatedAveragePrecisions = array();
	private $aggregatedDocsRetrieved = 0;
	private $aggregatedRelevantResults = 0;
	private $aggregatedRelevantResultsAtNum = 0;
	private $aggregatedAP = 0;
	
	//
	//private $relevantDocs = 100;
	//private $recalledDocs;
	
	// member functions
	
	// load Queries
	public function loadQueries($filename, $numQueries)
	{
		$sh = fopen($filename, 'r') or die("Couldn\'t open file, sorry");

		for($i=0;$i<$numQueries;$i++)
		{
			$line=fgets($sh);
			
			array_push($this->queries, $line);
		}
	}
	
	// Return Queries
	public function returnQueries($i)
	{
		return $this->queries[$i];
	}
	
	// Display Queries
	public function displayQueries()
	{
		foreach($this->queries as $item)
			echo '</br>'.$item;
	}
	
	//
	public function loadArray($filename, $engine, $numResults)
	{
		// Set names
		//$this->$engineDocsRetrieved = 0;
		$resultSet =  $engine.'Results';
		
		// Clear Set each iteration
		//foreach($this->$resultSet as $item);
		//	unset($item);
		
		// open the file
		$sh = fopen($filename, 'r') or die("Couldn\'t open file, sorry");
	
		/*
		for($i=0;$i<$start;$i++)
		{
			$line=fgets($sh);
			//echo '</br>!!!One here'.$i;
		}
		*/
		
		//
		for($i=0;$i<$numResults;$i++)
		{
			$line=fgets($sh);
			
			if($engine == "goldStandard")
			{
				// remove numbers
				for($j = 0;$j < strlen($line);$j++)
				{
					if($line[$j] == ' ') break;
				}
				$line = substr($line, $j+1);
				
				$line = $this->cleanLine($line);
			}
			//echo '</br>'.$resultSet;
			
			array_push($this->$resultSet, trim($line));
		}
		//echo '<h2>Test: </h2>'.'Count: '.$this->$engineDocsRetrieved.'</br></br>';
		//var_dump($this->$resultSet);
	}
	
		// Clean Link
		public function cleanLine($line) {
			//echo 'Cleaning: '.$link.'</br>';
			$line = strtolower(strip_tags(trim($line)));
			if (substr($line,-1,1) == '/') $line = substr($line,0,strlen($line) - 1);
			//echo 'Cleaned: '.$link.'</br>';
			return $line;
		}

	// Display Array
	public function displayArray($array)
	{
		foreach($this->$array as $item)
			echo '</br>'.$item;
	}
	
	//
	public function analyse($standard, $engine, $precision, $start, $end)
	{
		// Name Result Sets and Counters
		$resultSet = $engine.'Results';
		$resultSetPrecision = $engine.'RelevantResults';
		$resultSetPrecisionAtNum = $engine.'RelevantResultsAtNum';
		$resultSelection = $this->$resultSet;
		$engineDocsRetrieved = $engine.'DocsRetrieved';
		$engineAveragePrecisions = $engine.'AveragePrecisions';
		$engineAP = $engine.'AP';
		// standard Set
		unset($standardSet);
		
		//$standardSet =  array('hi.com','lo.com');
		$standardSet = array_slice($this->$standard, $start, 100);
		//echo '<h2>Vardump</h2>';
		//var_dump($standardSet);
		// Set Set Counters to Zero
		$this->$resultSetPrecision = 0;
		$this->$resultSetPrecisionAtNum = 0;
		$this->$engineDocsRetrieved = 0;
		$this->$engineAP =0;
		$qap = 0;
		$rank = 0;
		// Identify Relevant Documents
		for($i=$start;$i<$end;$i++)
		{
			$rank++;
			if(strlen($resultSelection[$i]) > 2)
				$this->$engineDocsRetrieved++;
				
			if(in_array($resultSelection[$i], $standardSet))
			{
				
				
				$this->$resultSetPrecision++;
				//$resultSet = substr($resultSet,0,strlen($resultSet) - 9);
				//echo '</br>'.$resultSet;
				$qap += $this->$resultSetPrecision/$rank;
				//echo '</br>+'.$resultSelection[$i].' > OI: '.$this->$resultSetPrecision.' >Rank: '.$rank.' OI/r = '.$this->$resultSetPrecision/$rank;
			}
			else
			{
				//echo '</br>-'.$resultSelection[$i];
			}
		}
		//
		//echo '</br>This '.$engine.'QAP: '.$qap;
		//echo '</br>resultSetPrecision '.$this->$resultSetPrecision;
		// Fill result for Average Precision
		
		$this->$engineAP = $qap/100;
		//echo '</br>This '.$engine.'AP: '.$this->$engineAP;
		array_push($this->$engineAveragePrecisions, $this->$engineAP);
		
		// Identify Top 10 Relevant Document

		//$temp = $this->$standard;
		$temp = array_slice($this->$standard, $start, $precision);
		//echo '<h2>Vardump Temp:</h2>';
		//echo '</br>Start: '.$start.' '.$precision.'</br>';
		//var_dump($temp);
		//
		for($i=$start;$i<$end;$i++)
		{
			//echo '</br>i: '.$i.'</br>';
			if ($i >= ($start + $precision)) break;
			//echo '</br>Result: '.$resultSelection[$i];
			if(in_array($resultSelection[$i], $temp))
			{
				
				//echo '</br>'.$resultSet;
				$this->$resultSetPrecisionAtNum++;
				//$resultSet = substr($resultSet,0,strlen($resultSet) - 9);
				//echo '</br>'.$resultSet;
			}
		}
		//
		//echo '</br>Precision at X: '.$this->$resultSetPrecisionAtNum;
		
	}
	
	//
	public function displayScoreTable($q, $precision)
	{
		//
		//echo '</br>Results for Query: '.$q.'</br>';
		//
		echo '
		<table border="1">
			<tr>
				<th style="width:100px">Query</th>
				<th style="width:100px">Engine</th>
				<th style="width:100px">Precision</th>
				<th style="width:100px">Avg Prec</th>
				<th style="width:100px">P@'.$precision.'</th>
				<th style="width:100px">Recall</th>
				<th style="width:100px">F-measure</th>
			</tr>
			<tr>
				<td rowspan="4" >'.$q.'</td>
				<td>Google</td>';
		//
		echo '<td>'.(round($this->googleRelevantResults/$this->googleDocsRetrieved, 4)).'</td>';
		array_push($this->googlePrecision, ($this->googleRelevantResults/$this->googleDocsRetrieved));
		// AP
		echo '<td>'.(round($this->googleAP, 4)).'</td>';
		echo '<td>'.(round($this->googleRelevantResultsAtNum/$precision, 4)).'</td>';
		echo '<td>'.(round($this->googleRelevantResults/$this->goldStandardDocsRetrieved, 4)).'</td>';
		
		echo '<td>'.(round((2 * ($this->googleRelevantResults/$this->googleDocsRetrieved) * ($this->googleRelevantResults/$this->goldStandardDocsRetrieved)) / (($this->googleRelevantResults/$this->googleDocsRetrieved) + ($this->googleRelevantResults/$this->goldStandardDocsRetrieved)), 4)).'</td>';
		//
		echo '</tr><tr><td>Bing</td>';
		echo '<td>'.(round($this->bingRelevantResults/$this->bingDocsRetrieved, 4)).'</td>';
		array_push($this->bingPrecision, ($this->bingRelevantResults/$this->bingDocsRetrieved));
		// AP
		echo '<td>'.(round($this->bingAP, 4)).'</td>';
		echo '<td>'.(round($this->bingRelevantResultsAtNum/$precision, 4)).'</td>';
		echo '<td>'.(round($this->bingRelevantResults/$this->goldStandardDocsRetrieved, 4)).'</td>';
		
		echo '<td>'.(round((2 * ($this->bingRelevantResults/$this->bingDocsRetrieved) * ($this->bingRelevantResults/$this->goldStandardDocsRetrieved)) / (($this->bingRelevantResults/$this->bingDocsRetrieved) + ($this->bingRelevantResults/$this->goldStandardDocsRetrieved)), 4)).'</td>';
		//
		echo '</tr><tr><td>Blekko</td>';
		echo '<td>'.(round($this->blekkoRelevantResults/$this->blekkoDocsRetrieved, 4)).'</td>';
		array_push($this->blekkoPrecision, ($this->blekkoRelevantResults/$this->blekkoDocsRetrieved));
		// AP
		echo '<td>'.(round($this->blekkoAP, 4)).'</td>';
		echo '<td>'.(round($this->blekkoRelevantResultsAtNum/$precision, 4)).'</td>';
		echo '<td>'.(round($this->blekkoRelevantResults/$this->goldStandardDocsRetrieved, 4)).'</td>';
		
		echo '<td>'.(round((2 * ($this->blekkoRelevantResults/$this->blekkoDocsRetrieved) * ($this->blekkoRelevantResults/$this->goldStandardDocsRetrieved)) / (($this->blekkoRelevantResults/$this->blekkoDocsRetrieved) + ($this->blekkoRelevantResults/$this->goldStandardDocsRetrieved)), 4)).'</td>';
		//
		echo '</tr><tr><td>Aggregated</td>';
		echo '<td>'.(round($this->aggregatedRelevantResults/$this->aggregatedDocsRetrieved, 4)).'</td>';
		array_push($this->aggregatedPrecision, ($this->aggregatedRelevantResults/$this->aggregatedDocsRetrieved));
		// AP
		echo '<td>'.(round($this->aggregatedAP, 4)).'</td>';
		echo '<td>'.(round($this->aggregatedRelevantResultsAtNum/$precision, 4)).'</td>';
		echo '<td>'.(round($this->aggregatedRelevantResults/$this->goldStandardDocsRetrieved, 4)).'</td>';
		
		echo '<td>'.(round((2 * ($this->aggregatedRelevantResults/$this->aggregatedDocsRetrieved) * ($this->aggregatedRelevantResults/$this->goldStandardDocsRetrieved)) / (($this->aggregatedRelevantResults/$this->aggregatedDocsRetrieved) + ($this->aggregatedRelevantResults/$this->goldStandardDocsRetrieved)), 4)).'</td>';
		//
		echo '</tr></table> ';
		echo '<h2>MAPs</h2>Google: '.(round(array_sum($this->googleAveragePrecisions)/count($this->googleAveragePrecisions), 4)).'</br>';
		echo 'Bing: '.(round(array_sum($this->bingAveragePrecisions)/count($this->bingAveragePrecisions), 4)).'</br>';
		echo 'Blekko: '.(round(array_sum($this->blekkoAveragePrecisions)/count($this->blekkoAveragePrecisions), 4)).'</br>';
		echo 'Aggregated: '.(round(array_sum($this->aggregatedAveragePrecisions)/count($this->aggregatedAveragePrecisions), 4)).'</br>';
		
		echo '<hr>';
		//echo '<h2>Vardump</h2>';
		//var_dump();
	}
	
	// Display Precision
	public function displayAveragePrecisions($engine)
	{
		echo '<h2>Average Precisions: '.$engine.'</h2>';
		$resultSet = $engine.'AveragePrecisions';
		
		foreach($this->$resultSet as $result)
			echo '</br>'.$result;
	}
	
} // End of Class

// Program

// Initialise Analyser
$analyser1 = new analyser;
$numQueries = 50;
$numResults = 5000;
//$start = 0;
//$end = 100;
$range = 100;
$precision = 10;

// Load Q
$analyser1->loadQueries('trec2012-queries.txt', 50);
//$analyser1->displayQueries();

// Load Gold Standard
$analyser1->loadArray('relevance_judgments.txt','goldStandard', $numResults);
//$analyser1->displayArray('goldStandardResults');


// Load Google Results
$analyser1->loadArray('Google','google', $numResults);

// Load Bing Results
$analyser1->loadArray('Bing','bing', $numResults);

// Load Blekko Results
$analyser1->loadArray('Blekko','blekko', $numResults);
	
// Load Aggregated Results
$analyser1->loadArray('Aggregated','aggregated', $numResults);	

// Analyse

for($i=0;$i<$numQueries;$i++)
{
	// enumerate Q
	echo '<h3>QUERY '.($i+1).'</h3>';
	
	// Analyse Google Results
	$analyser1->analyse('goldStandardResults', 'google', $precision, ($i*$range), (($i+1)*$range));
	//echo '</br>'.($i*$range).' '.(($i+1)*$range);
	
	// Analyse Bing Results
	$analyser1->analyse('goldStandardResults', 'bing', $precision, ($i*$range), (($i+1)*$range));

	
	// Analyse Blekko Results
	$analyser1->analyse('goldStandardResults', 'blekko', $precision, ($i*$range), (($i+1)*$range));


	// Analyse Aggregated Results
	$analyser1->analyse('goldStandardResults', 'aggregated', $precision, ($i*$range), (($i+1)*$range));

	// Display Scores
	$analyser1->displayScoreTable($analyser1->returnQueries($i), $precision);
}

// Display Precision - pass name of engine
//$analyser1->displayAveragePrecisions('aggregated');

?>