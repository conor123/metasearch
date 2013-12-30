<?php

// ***********
// QUERY CLASS
// ***********

// Purpose: to handle all query processing

class query
{
	// **********
	// PROPERTIES
	// **********
	private $queryTokens = array();
	
	// **********
	// METHODS
	// **********
	
	// Tokenise Query
	public function tokeniseQuery($q){
	
		$this->queryTokens = explode(" ",$q);
		return $this->queryTokens;
	}
	
	// Display Tokens
	public function displayTokens(){
	
		foreach($this->queryTokens as $item)
			echo '</br>'.$item;
	}
	
	// Google Complex Queries
	public function complexQueryGoogle($q){
		
		$q=str_replace(" NOT "," -",$q);
		$q=urlencode("'$q'");
		return $q;
	}
	
	// Bing Complex Queries
	public function complexQueryBing($q){

		$q=urlencode("'$q'");
		return $q;
	}
	
	// Blekko Complex Queries
	public function complexQueryBlekko($q){

		$q=str_replace(" OR "," or ",$q);
		$q=str_replace(' NOT ', ' -', $q);
		$q=urlencode("'$q'");
		return $q;
	}
	
	// Expand Query
	public function expandQuery($q, $thesaurus){

		foreach ($this->queryTokens as $keyQ=>$valueQ)
		{
			foreach($thesaurus as $keyT=>$valueT)
			if($valueQ == $keyT)
			{
				$q .= ' AND '.$valueT;
			}
		}
		$q = str_replace(",", " OR ", $q);
		echo '<strong>> Expansion:</strong> '.$q.'</br>';
		return $q;
	}
	
	// ******************
	// Stemmer Functions
	// ******************
	function stem( $word )
    {
        if ( empty($word) ) {
            return false;
        }

        $result = '';

        $word = strtolower($word);

        // Strip punctuation, etc. Keep ' and . for URLs and contractions.
        if ( substr($word, -2) == "'s" ) {
            $word = substr($word, 0, -2);
        }
        $word = preg_replace("/[^a-z0-9'.-]/", '', $word);

        $first = '';
        if ( strpos($word, '-') !== false ) {
            //list($first, $word) = explode('-', $word);
            //$first .= '-';
            $first = substr($word, 0, strrpos($word, '-') + 1); // Grabs hyphen too
            $word = substr($word, strrpos($word, '-') + 1);
        }
        if ( strlen($word) > 2 ) {
            $word = $this->_step_1($word);
            $word = $this->_step_2($word);
            $word = $this->_step_3($word);
            $word = $this->_step_4($word);
            $word = $this->_step_5($word);
        }

        $result = $first . $word;

        return $result;
    }

    /**
     *  Takes a list of words and returns them reduced to their stems.
     *
     *  $words can be either a string or an array. If it is a string, it will
     *  be split into separate words on whitespace, commas, or semicolons. If
     *  an array, it assumes one word per element.
     *
     *  @param mixed $words String or array of word(s) to reduce
     *  @access public
     *  @return array List of word stems
     */
    function stem_list( $words )
    {
        if ( empty($words) ) {
            return false;
        }

        $results = array();

        if ( !is_array($words) ) {
            $words = preg_split("/[\s,]+/", trim($words));
        }

        foreach ( $words as $word ) {
            if ( $result = $this->stem($word) ) {
                $results[] = $result;
            }
        }

        return $results;
    }

    /**
     *  Performs the functions of steps 1a and 1b of the Porter Stemming Algorithm.
     *
     *  First, if the word is in plural form, it is reduced to singular form.
     *  Then, any -ed or -ing endings are removed as appropriate, and finally,
     *  words ending in "y" with a vowel in the stem have the "y" changed to "i".
     *
     *  @param string $word Word to reduce
     *  @access private
     *  @return string Reduced word
     */
    function _step_1( $word )
    {
		// Step 1a
		if ( substr($word, -1) == 's' ) {
            if ( substr($word, -4) == 'sses' ) {
                $word = substr($word, 0, -2);
            } elseif ( substr($word, -3) == 'ies' ) {
                $word = substr($word, 0, -2);
            } elseif ( substr($word, -2, 1) != 's' ) {
                // If second-to-last character is not "s"
                $word = substr($word, 0, -1);
            }
        }
		// Step 1b
        if ( substr($word, -3) == 'eed' ) {
			if ($this->count_vc(substr($word, 0, -3)) > 0 ) {
	            // Convert '-eed' to '-ee'
	            $word = substr($word, 0, -1);
			}
        } else {
            if ( preg_match('/([aeiou]|[^aeiou]y).*(ed|ing)$/', $word) ) { // vowel in stem
                // Strip '-ed' or '-ing'
                if ( substr($word, -2) == 'ed' ) {
                    $word = substr($word, 0, -2);
                } else {
                    $word = substr($word, 0, -3);
                }
                if ( substr($word, -2) == 'at' || substr($word, -2) == 'bl' ||
                     substr($word, -2) == 'iz' ) {
                    $word .= 'e';
                } else {
                    $last_char = substr($word, -1, 1);
                    $next_to_last = substr($word, -2, 1);
                    // Strip ending double consonants to single, unless "l", "s" or "z"
                    if ( $this->is_consonant($word, -1) &&
                         $last_char == $next_to_last &&
                         $last_char != 'l' && $last_char != 's' && $last_char != 'z' ) {
                        $word = substr($word, 0, -1);
                    } else {
                        // If VC, and cvc (but not w,x,y at end)
                        if ( $this->count_vc($word) == 1 && $this->_o($word) ) {
                            $word .= 'e';
                        }
                    }
                }
            }
        }
        // Step 1c
        // Turn y into i when another vowel in stem
        if ( preg_match('/([aeiou]|[^aeiou]y).*y$/', $word) ) { // vowel in stem
            $word = substr($word, 0, -1) . 'i';
        }
        return $word;
    }

    /**
     *  Performs the function of step 2 of the Porter Stemming Algorithm.
     *
     *  Step 2 maps double suffixes to single ones when the second-to-last character
     *  matches the given letters. So "-ization" (which is "-ize" plus "-ation"
     *  becomes "-ize". Mapping to a single character occurence speeds up the script
     *  by reducing the number of possible string searches.
     *
     *  Note: for this step (and steps 3 and 4), the algorithm requires that if
     *  a suffix match is found (checks longest first), then the step ends, regardless
     *  if a replacement occurred. Some (or many) implementations simply keep
     *  searching though a list of suffixes, even if one is found.
     *
     *  @param string $word Word to reduce
     *  @access private
     *  @return string Reduced word
     */
    function _step_2( $word )
    {
        switch ( substr($word, -2, 1) ) {
            case 'a':
                if ( $this->_replace($word, 'ational', 'ate', 0) ) {
                    return $word;
                }
                if ( $this->_replace($word, 'tional', 'tion', 0) ) {
                    return $word;
                }
                break;
            case 'c':
                if ( $this->_replace($word, 'enci', 'ence', 0) ) {
                    return $word;
                }
                if ( $this->_replace($word, 'anci', 'ance', 0) ) {
                    return $word;
                }
                break;
            case 'e':
                if ( $this->_replace($word, 'izer', 'ize', 0) ) {
                    return $word;
                }
                break;
            case 'l':
                // This condition is a departure from the original algorithm;
                // I adapted it from the departure in the ANSI-C version.
				if ( $this->_replace($word, 'bli', 'ble', 0) ) {
                    return $word;
                }
                if ( $this->_replace($word, 'alli', 'al', 0) ) {
                    return $word;
                }
                if ( $this->_replace($word, 'entli', 'ent', 0) ) {
                    return $word;
                }
                if ( $this->_replace($word, 'eli', 'e', 0) ) {
                    return $word;
                }
                if ( $this->_replace($word, 'ousli', 'ous', 0) ) {
                    return $word;
                }
                break;
            case 'o':
                if ( $this->_replace($word, 'ization', 'ize', 0) ) {
                    return $word;
                }
                if ( $this->_replace($word, 'isation', 'ize', 0) ) {
                    return $word;
                }
                if ( $this->_replace($word, 'ation', 'ate', 0) ) {
                    return $word;
                }
                if ( $this->_replace($word, 'ator', 'ate', 0) ) {
                    return $word;
                }
                break;
            case 's':
                if ( $this->_replace($word, 'alism', 'al', 0) ) {
                    return $word;
                }
                if ( $this->_replace($word, 'iveness', 'ive', 0) ) {
                    return $word;
                }
                if ( $this->_replace($word, 'fulness', 'ful', 0) ) {
                    return $word;
                }
                if ( $this->_replace($word, 'ousness', 'ous', 0) ) {
                    return $word;
                }
                break;
            case 't':
                if ( $this->_replace($word, 'aliti', 'al', 0) ) {
                    return $word;
                }
                if ( $this->_replace($word, 'iviti', 'ive', 0) ) {
                    return $word;
                }
                if ( $this->_replace($word, 'biliti', 'ble', 0) ) {
                    return $word;
                }
                break;
            case 'g':
                // This condition is a departure from the original algorithm;
                // I adapted it from the departure in the ANSI-C version.
                if ( $this->_replace($word, 'logi', 'log', 0) ) { //*****
                    return $word;
                }
                break;
        }
        return $word;
    }

    /**
     *  Performs the function of step 3 of the Porter Stemming Algorithm.
     *
     *  Step 3 works in a similar stragegy to step 2, though checking the
     *  last character.
     *
     *  @param string $word Word to reduce
     *  @access private
     *  @return string Reduced word
     */
    function _step_3( $word )
    {
        switch ( substr($word, -1) ) {
            case 'e':
                if ( $this->_replace($word, 'icate', 'ic', 0) ) {
                    return $word;
                }
                if ( $this->_replace($word, 'ative', '', 0) ) {
                    return $word;
                }
                if ( $this->_replace($word, 'alize', 'al', 0) ) {
                    return $word;
                }
                break;
            case 'i':
                if ( $this->_replace($word, 'iciti', 'ic', 0) ) {
                    return $word;
                }
                break;
            case 'l':
                if ( $this->_replace($word, 'ical', 'ic', 0) ) {
                    return $word;
                }
                if ( $this->_replace($word, 'ful', '', 0) ) {
                    return $word;
                }
                break;
            case 's':
                if ( $this->_replace($word, 'ness', '', 0) ) {
                    return $word;
                }
                break;
        }
        return $word;
    }

    /**
     *  Performs the function of step 4 of the Porter Stemming Algorithm.
     *
     *  Step 4 works similarly to steps 3 and 2, above, though it removes
     *  the endings in the context of VCVC (vowel-consonant-vowel-consonant
     *  combinations).
     *
     *  @param string $word Word to reduce
     *  @access private
     *  @return string Reduced word
     */
    function _step_4( $word )
    {
        switch ( substr($word, -2, 1) ) {
            case 'a':
                if ( $this->_replace($word, 'al', '', 1) ) {
                    return $word;
                }
                break;
            case 'c':
                if ( $this->_replace($word, 'ance', '', 1) ) {
                    return $word;
                }
                if ( $this->_replace($word, 'ence', '', 1) ) {
                    return $word;
                }
                break;
            case 'e':
                if ( $this->_replace($word, 'er', '', 1) ) {
                    return $word;
                }
                break;
            case 'i':
                if ( $this->_replace($word, 'ic', '', 1) ) {
                    return $word;
                }
                break;
            case 'l':
                if ( $this->_replace($word, 'able', '', 1) ) {
                    return $word;
                }
                if ( $this->_replace($word, 'ible', '', 1) ) {
                    return $word;
                }
                break;
            case 'n':
                if ( $this->_replace($word, 'ant', '', 1) ) {
                    return $word;
                }
                if ( $this->_replace($word, 'ement', '', 1) ) {
                    return $word;
                }
                if ( $this->_replace($word, 'ment', '', 1) ) {
                    return $word;
                }
                if ( $this->_replace($word, 'ent', '', 1) ) {
                    return $word;
                }
                break;
            case 'o':
                // special cases
                if ( substr($word, -4) == 'sion' || substr($word, -4) == 'tion' ) {
                    if ( $this->_replace($word, 'ion', '', 1) ) {
                        return $word;
                    }
                }
                if ( $this->_replace($word, 'ou', '', 1) ) {
                    return $word;
                }
                break;
            case 's':
                if ( $this->_replace($word, 'ism', '', 1) ) {
                    return $word;
                }
                break;
            case 't':
                if ( $this->_replace($word, 'ate', '', 1) ) {
                    return $word;
                }
                if ( $this->_replace($word, 'iti', '', 1) ) {
                    return $word;
                }
                break;
            case 'u':
                if ( $this->_replace($word, 'ous', '', 1) ) {
                    return $word;
                }
                break;
            case 'v':
                if ( $this->_replace($word, 'ive', '', 1) ) {
                    return $word;
                }
                break;
            case 'z':
                if ( $this->_replace($word, 'ize', '', 1) ) {
                    return $word;
                }
                break;
        }
        return $word;
    }

    /**
     *  Performs the function of step 5 of the Porter Stemming Algorithm.
     *
     *  Step 5 removes a final "-e" and changes "-ll" to "-l" in the context
     *  of VCVC (vowel-consonant-vowel-consonant combinations).
     *
     *  @param string $word Word to reduce
     *  @access private
     *  @return string Reduced word
     */
    function _step_5( $word )
    {
        if ( substr($word, -1) == 'e' ) {
            $short = substr($word, 0, -1);
            // Only remove in vcvc context...
            if ( $this->count_vc($short) > 1 ) {
                $word = $short;
            } elseif ( $this->count_vc($short) == 1 && !$this->_o($short) ) {
                $word = $short;
            }
        }
        if ( substr($word, -2) == 'll' ) {
            // Only remove in vcvc context...
            if ( $this->count_vc($word) > 1 ) {
                $word = substr($word, 0, -1);
            }
        }
        return $word;
    }

    /**
     *  Checks that the specified letter (position) in the word is a consonant.
     *
     *  Handy check adapted from the ANSI C program. Regular vowels always return
     *  FALSE, while "y" is a special case: if the prececing character is a vowel,
     *  "y" is a consonant, otherwise it's a vowel.
     *
     *  And, if checking "y" in the first position and the word starts with "yy",
     *  return true even though it's not a legitimate word (it crashes otherwise).
     *
     *  @param string $word Word to check
     *  @param integer $pos Position in the string to check
     *  @access public
     *  @return boolean
     */
    function is_consonant( $word, $pos )
    {
        // Sanity checking $pos
        if ( abs($pos) > strlen($word) ) {
            if ( $pos < 0 ) {
                // Points "too far back" in the string. Set it to beginning.
                $pos = 0;
            } else {
                // Points "too far forward." Set it to end.
                $pos = -1;
            }
        }
        $char = substr($word, $pos, 1);
        switch ( $char ) {
            case 'a':
            case 'e':
            case 'i':
            case 'o':
            case 'u':
                return false;
            case 'y':
                if ( $pos == 0 || strlen($word) == -$pos ) {
                    // Check second letter of word.
                    // If word starts with "yy", return true.
                    if ( substr($word, 1, 1) == 'y' ) {
                        return true;
                    }
                    return !($this->is_consonant($word, 1));
                } else {
                    return !($this->is_consonant($word, $pos - 1));
                }
            default:
                return true;
        }
    }

    /**
     *  Counts (measures) the number of vowel-consonant occurences.
     *
     *  Based on the algorithm; this handy function counts the number of
     *  occurences of vowels (1 or more) followed by consonants (1 or more),
     *  ignoring any beginning consonants or trailing vowels. A legitimate
     *  VC combination counts as 1 (ie. VCVC = 2, VCVCVC = 3, etc.).
     *
     *  @param string $word Word to measure
     *  @access public
     *  @return integer
     */
    function count_vc( $word )
    {
        $m = 0;
        $length = strlen($word);
        $prev_c = false;
        for ( $i = 0; $i < $length; $i++ ) {
            $is_c = $this->is_consonant($word, $i);
            if ( $is_c ) {
                if ( $m > 0 && !$prev_c ) {
                    $m += 0.5;
                }
            } else {
                if ( $prev_c || $m == 0 ) {
                    $m += 0.5;
                }
            }
            $prev_c = $is_c;
        }
        $m = floor($m);
        return $m;
    }

    /**
     *  Checks for a specific consonant-vowel-consonant condition.
     *
     *  This function is named directly from the original algorithm. It
     *  looks the last three characters of the word ending as
     *  consonant-vowel-consonant, with the final consonant NOT being one
     *  of "w", "x" or "y".
     *
     *  @param string $word Word to check
     *  @access private
     *  @return boolean
     */
    function _o( $word )
    {
        if ( strlen($word) >= 3 ) {
            if ( $this->is_consonant($word, -1) && !$this->is_consonant($word, -2) &&
                 $this->is_consonant($word, -3) ) {
		        $last_char = substr($word, -1);
		        if ( $last_char == 'w' || $last_char == 'x' || $last_char == 'y' ) {
		            return false;
		        }
                return true;
            }
        }
        return false;
    }

    /**
     *  Replaces suffix, if found and word measure is a minimum count.
     *
     *  @param string $word Word to check and modify
     *  @param string $suffix Suffix to look for
     *  @param string $replace Suffix replacement
     *  @param integer $m Word measure value that the word must be greater
     *                    than to replace
     *  @access private
     *  @return boolean
     */
    function _replace( &$word, $suffix, $replace, $m = 0 )
    {
        $sl = strlen($suffix);
        if ( substr($word, -$sl) == $suffix ) {
            $short = substr_replace($word, '', -$sl);
            if ( $this->count_vc($short) > $m ) {
                $word = $short . $replace;
            }
            // Found this suffix, doesn't matter if replacement succeeded
            return true;
        }
        return false;
    }
}


// **********
// API CLASS
// **********

// Purpose: To handle all interaction with external APIs

class api
{
	// **********
	// PROPERTIES
	// **********
	
	// Gooogle JSON data and flags for checking usable data is present
	private $js1;
	private $js1ResultFlag;
	// Bing JSON var
	private $js2;
	private $js2ResultFlag;
	// Blekko JSON var
	private $js3;
	private $js3ResultFlag;
	
	// **********
	// METHODS
	// **********
	
	// Display Google JSON
	public function displayGoogleJsonData() {
		echo '</br><strong>Vardump:</strong></br></br>';
		var_dump($this->js1);
		echo '</br></br>';
    }
	
	// Return Google JSON
	public function returnGoogleJsonData() {
		return $this->js1;
    }
	
	// Return Google JSON Results Flag
	public function returnGoogleJsonResultFlag() {
		return $this->js1ResultFlag;
    }
	
	// Display Bing JSON
	public function displayBingJsonData() {
		echo '</br><strong>Vardump:</strong></br></br>';
		var_dump($this->js2);
		echo '</br></br>';
    }
	
	// Return Bing JSON
	public function returnBingJsonData() {
		return $this->js2;
    }
	
	// Return Bing JSON Results Flag
	public function returnBingJsonResultFlag() {
		return $this->js2ResultFlag;
    }
	
	// Display Blekko JSON
	public function displayBlekkoJsonData() {
		echo '</br><strong>Vardump:</strong></br></br>';
		var_dump($this->js3);
		echo '</br></br>';
    }
	
	// Return Blekko JSON
	public function returnBlekkoJsonData() {
		return $this->js3;
    }
	
	// Return Blekko JSON Results Flag
	public function returnBlekkoJsonResultFlag() {
		return $this->js3ResultFlag;
    }
	
	// **********
	// Google API
	// **********
	public function googleApi($q, $offset) {
		
		// Multiple keys available due to overcome limits of 100 queries per day per key
		
		// Google API key 1
		$googleApiKey='AIzaSyDaDcmBjDUCAjc24BldgjWyg8q9XGXNrk4';
		$id='004553691612569663192:yswazcgthyw';
		
		// Google API key 2
		//$googleApiKey='AIzaSyCzSaVpcOkrHS6FIB_0xDBynaz4vIcd6N0';
		//$id='004553691612569663192:gd_zraf1v8w';
		
		// Google API key 3
		//$googleApiKey='AIzaSyB7P8S4rrSCSsoJ1dBkU2Pdes97VbozrOU';
		//$id='004553691612569663192:ijnh5euybl8';
		
		// Google API key 4
		//$googleApiKey='AIzaSyDznVCOSGLJcNZ9RAWSdRzOppatGhDUacE';
		//$id='004553691612569663192:vhgi8oaqlaq';
		
		// Google API key 5
		//$googleApiKey='AIzaSyANDTitlWV6aFPTGmtWsjhuggeVEs4XFBc';
		//$id='004553691612569663192:g_hxag4ezoi';
		
		// Google API key 6
		//$googleApiKey='AIzaSyAAGRUJM4PIBVnlB2VDa9aBrxzhSubMCjc';
		//$id='004553691612569663192:g_nwnf3f7f8';
		
		// Construct the link
		$url='https://www.googleapis.com/customsearch/v1?'.'key='.$googleApiKey.'&cx='.$id.'&q='.$q.'&alt=json'.'&start='.$offset;
		// Clean spaces from the string
		$url=str_replace(' ','%20',$url);
		// initiate cURL
		$ch=curl_init();
		// set the URL
		curl_setopt($ch, CURLOPT_URL, $url);
		// return the transfer as a string
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		// Fix the SSL cery problem with Google custom search API
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		// get the page source into $data variable
		$data = curl_exec($ch);
		
		//echo '</br>Vardumpdata3:';
		//var_dump($data);
		
		// json decode
		$this->js1 = json_decode($data);
		//echo '</br></br>Vardumpjs1: ('.$offset.')</br></br>';
		//var_dump($this->js1);
		
		// Echo total results from Google
		//echo '</br>Google Results: '.$this->js1->{'searchInformation'}->{'totalResults'};
		
		// Set results Flag
		if (!empty($this->js1->{'searchInformation'}->{'totalResults'}) > '0')
		{
			//echo '</br>Blekko API: Results!!!';
			$this->js1ResultFlag = TRUE;
		}
		else 
		{
			//echo '</br>Blekko API: No Results!';
			$this->js1ResultFlag = FALSE;
		}
		
		// Stores data in a file if required
		//$content = serialize($this->js1);
		//file_put_contents('tmp1',$content);
		/*
		*/
		// Recovers data from a file if required
		//$this->js1 = unserialize(file_get_contents('tmp1'));
		//$this->js1ResultFlag = TRUE;
	}
	
	// **********
	// Bing API
	// **********
	public function bingApi($q, $results, $offset) {
		
		
		// Keys
		$acctKey = 'FpJuEBJPBTE9xY5X/k+xXXLJY8y1RoXC+wFxNb5s9jc= ';
		$rootUri = 'https://api.datamarket.azure.com/Bing/Search';
		
		// Get the selected service operation (Web or Image).
		$serviceOp = 'Web';
		$numResults = '$top='.$results;
		$skip = '$skip='.$offset;
		
		// Construct the full URI for the query.
		$requestUri = "$rootUri/$serviceOp?\$format=json&Query=$q&$numResults&$skip";
		//
		$ch = curl_init($requestUri);
		//
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, $acctKey . ":" . $acctKey );
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec($ch);
		
		// json decode
		$this->js2 = json_decode($data);
		//echo '</br></br>Vardumpjs2: ('.$offset.')</br></br>';
		//var_dump($this->js2);
		
		// Set results Flag
		if (!empty($this->js2->{'d'}->{'results'}) > '0')
		{
			//echo '</br>Blekko API: Results!!!';
			$this->js2ResultFlag = TRUE;
		}
		else 
		{
			//echo '</br>Blekko API: No Results!';
			$this->js2ResultFlag = FALSE;
		}
		
		// Stores data in a file if required
		//$content = serialize($this->js2);
		//file_put_contents('tmp2',$content);
		/*
		*/
		// Recovers data from a file if required
		//$this->js2 = unserialize(file_get_contents('tmp2'));
		//$this->js2ResultFlag = TRUE;
		
	} // End of Bing API Class
	
	// **********
	// blekko api
	// **********
	public function blekkoApi($q, $results, $offset) {
		
		
		// Set the Blekko API key
		$blekkoApiKey='f4c8acf3';
		// Construct the link
		$url="http://"."blekko.com"."/ws/?q=".$q."+/json"."+/ps=".$results."&auth=".$blekkoApiKey."&p=".$offset;
		// Clean spaves from the string
		$url=str_replace(" ","+",$url);
		// initiate cURL
		$ch=curl_init();
		// set the URL
		curl_setopt($ch, CURLOPT_URL, $url);
		// return the transfer as a string
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// get the page source into $data variable
		$data = curl_exec($ch);
		// json decode
		$this->js3 = json_decode($data);
		//echo '</br></br>Vardumpjs3: ('.$offset.')</br></br>';
		//var_dump($this->js3);

		// Set results Flag

		if (!empty($this->js3->{'RESULT'}) > '0')
		{
			//echo '</br>Blekko API: Results!!!';
			$this->js3ResultFlag = TRUE;
		}
		else 
		{
			//echo '</br>Blekko API: No Results!';
			$this->js3ResultFlag = FALSE;
		}

		// Saves data to a file if required
		//$content = serialize($this->js3);
		//file_put_contents('tmp3',$content);
		/*
		*/
		// Recovers data from a file if required
		//$this->js3 = unserialize(file_get_contents('tmp3'));
		//$this->js3ResultFlag = TRUE;
		
		} // End of Blekko API
} // End of API Class


// **********
// RESULT SET CLASS
// **********

// Purpose: To handle all the result sets from each input search engine in a single object

class resultSet
{
    // **********
	// PROPERTIES
	// **********
	
    private $numItems = 0;
	private $urls = array();
	private $titles = array();
	private $snippets = array();
	private $scores = array();
	
	// **********
	// METHODS
	// **********
	
	// Add Url to resultSet
	public function addUrl($url){
		array_push($this->urls, $url);
		$this->addNumItems();
	}
	
	// Unset Url from Agg liist - for displaying clusters
	public function unsetUrl($url){
		unset($this->urls[$url]);
		$this->removeNumItems();
	}
    
	// Add Title
	public function addTitle($title){
		array_push($this->titles, $title);
	}
	
	// Unset Title
	public function unsetTitle($title){
		unset($this->titles[$title]);
	}

	// Add Snippet
	public function addSnippet($snippet){
		array_push($this->snippets, $snippet);
	}
	
	// Unset Snippet
	public function unsetSnippet($snippet){
		unset($this->snippets[$snippet]);
	}

	// Add Score
	public function addScore($score){
		array_push($this->scores, $score);
	}
	
	// Unset Score
	public function unsetScore($score){
		unset($this->scores[$score]);
	}

	// Increment num items
	public function addNumItems(){
		$this->numItems++;
	}
	
	// Decrement num items
	public function removeNumItems(){
		$this->numItems--;
	}
	
	// Print Urls
	public function printUrls(){
		foreach($this->urls as $item)
			echo $item.'</br>';
	}
	
	// Print Titles
	public function printTitles(){
		foreach($this->titles as $item)
			echo $item.'</br>';
	}
	
	// Print Snippets
	public function printSnippets(){
		foreach($this->snippets as $item)
			echo $item.'</br>';
	}
	
	// Print Scores
	public function printScores(){
		foreach($this->scores as $item)
			echo $item.'</br>';
	}
	
	// RETURN URLS
	public function returnUrls(){
		return $this->urls;
	}
	
	// RETURN URLS V2
	public function returnUrlsV2($i){
		return $this->urls[$i];
	}
	
	// RETURN TITLES
	public function returnTitles(){
		return $this->titles;
	}
	
	// RETURN TITLES V2
	public function returnTitlesV2($i){
		return $this->titles[$i];
	}
	
	// RETURN SNIPPETS
	public function returnSnippets(){
		return $this->snippets;
	}
	
	// RETURN SNIPPETS V2
	public function returnSnippetsV2($i){
		return $this->snippets[$i];
	}
	
	// RETURN SCORES
	public function returnScores(){
		return $this->scores;
	}
	
	// RETURN SCORES V2
	public function returnScoresV2($i){
		return $this->scores[$i];
	}
	
	// RETURN Num Items
	public function returnNumItems(){
		return $this->numItems;
	}
	
	// SUM Fused Scores
	public function sumFusedScores($fusedScore, $index, $weight){
		$this->scores[$index]+=($fusedScore*$weight);
	}
	
} // End of resultSet Class


// **********
// FORMATTER CLASS
// **********

// Purpose: To format the JSON data returned from the input search engines and make available to other objects in the program 

class formatter
{
	// **********
	// PROPERTIES
	// **********
	
	// Google Formatted ResultSet Properties
	private $resultSet1 = NULL;
	private $js1;
	private $js1ResultFlag;
	
	// Bing Properties
	private $resultSet2 = NULL;
	private $js2;
	private $js2ResultFlag;
	
	// Blekko Properties
	private $resultSet3 = NULL;
	private $js3;
	private $js3ResultFlag;
	
	// **********
	// METHODS
    // **********
	
	// Object Property Constructor
	public function __construct(resultSet $resultSet1, resultSet $resultSet2, resultSet $resultSet3) {
		$this->resultSet1 = $resultSet1;
		$this->resultSet2 = $resultSet2;
		$this->resultSet3 = $resultSet3;
    }
	
	// Set Google JSON data
	public function setGoogleJson($js_import, $js1ResultFlag) {
		$this->js1 = $js_import;
		$this->js1ResultFlag = $js1ResultFlag;
    }
	
	// Render GOOGLE data from JSON object to result set property
	public function formatGoogleJson($results, $offset) {	
		
		if($this->js1ResultFlag == TRUE)
		{
			// Rank starting from 1
			// Score starting from $_SESSION['results'] and down - Borda Count
			$j = $results-$offset + 1;
			foreach($this->js1->{'items'} as $item)
			{
				if(!in_array($this->cleanLink($item->{'link'}), $this->resultSet1->returnUrls(), TRUE))
				{
					$this->resultSet1->addUrl($this->cleanLink($item->{'link'}));
					$this->resultSet1->addTitle($this->cleanText($item->{'title'}));
					$this->resultSet1->addSnippet($this->cleanText($item->{'snippet'}));
					$this->resultSet1->addScore($j);
					$j--;
				}
			}
		}	
		else{	
			; //echo '</br>Google: No Results!!!';
		}
	}
	
	// Set BING JSON data
	public function setBingJson($js_import, $js2ResultFlag) {
		$this->js2 = $js_import;
		$this->js2ResultFlag = $js2ResultFlag;
    }
	
	// Render BING data from JSON object to result set property
	public function formatBingJson($results, $offset) {	
		
		if($this->js2ResultFlag == TRUE){

			$j = $results - $offset + 1;
			foreach($this->js2->{'d'}->{'results'} as $item)
			{
				if(!in_array($this->cleanLink($item->{'Url'}), $this->resultSet2->returnUrls(), TRUE))
				{
					$this->resultSet2->addUrl($this->cleanLink($item->{'Url'}));
					$this->resultSet2->addTitle($this->cleanText($item->{'Title'}));
					$this->resultSet2->addSnippet($this->cleanText($item->{'Description'}));
					$this->resultSet2->addScore($j);
					$j--;
				}
			}
		}	
		else{	
			; // echo '</br>Bing: No Results!!!';
		}
	}
	
	// Set Blekko JSON data
	public function setBlekkoJson($js_import, $js3ResultFlag) {
		$this->js3 = $js_import;
		$this->js3ResultFlag = $js3ResultFlag;
    }
	
	// Render Blekko data from JSON object to result set property
	public function formatBlekkoJson($results, $offset) {	
		
		if($this->js3ResultFlag == TRUE){

			$j = $results - $offset + 1;
			foreach($this->js3->{'RESULT'} as $item)
			{
				if(!in_array($this->cleanLink($item->{'url'}), $this->resultSet3->returnUrls(), TRUE))
				{
					$this->resultSet3->addUrl($this->cleanLink($item->{'url'}));
					$this->resultSet3->addTitle($this->cleanText($item->{'url_title'}));
					$this->resultSet3->addSnippet($this->cleanText(!empty($item->{'snippet'})) ? $item->{'snippet'} : '');
					$this->resultSet3->addScore($j);
					$j--;
				}
			}
		}
		else{	
			; // echo '</br>Blekko: No Results!!!';
		}
	}
	
	// Print Urls
	public function printUrls($resultSet) {
		$this->$resultSet->printUrls();
    }
	
	// Print Titles
	public function printTitles($resultSet) {
		$this->$resultSet->printTitles();
    }
	
	// Print Snippets
	public function printSnippets($resultSet) {
		$this->$resultSet->printSnippets();
    }

	// Print Scores
	public function printScores($resultSet) {
		$this->$resultSet->printScores();
    }
	
	// Return Resultset
	public function returnResultSet($resultSet) {
		return $this->$resultSet;
    }
	
	// Clean Link
	public function cleanLink($link) {
		
		$link = strip_tags($link);
		if (substr($link,-1,1) == '/') $link = substr($link,0,strlen($link) - 1);
		return $link;
    }
	
	// Clean Text
	public function cleanText($text) {

		$text = strip_tags($text);
		return $text;
    }
	
	// Print Result Set
	// Iterate the sorted key array
	public function printResultSet($resultSet, $int) {
		
		$i=0;
		if($this->$resultSet->returnUrls() != NULL){
			foreach($this->$resultSet->returnUrls() as $key=>$value)
			{
				// Print the details according to the ordered array
				{
					echo '</br><strong>#'.($i+$_SESSION['offset']).': '.$this->$resultSet->returnTitlesV2($i).'</strong>';
					echo '</br>'.'<a href="'.$this->$resultSet->returnUrlsV2($i).'">'.$this->$resultSet->returnUrlsV2($i).'</a>';
					echo '</br>'.$this->$resultSet->returnSnippetsV2($i);
					//echo '</br>Score: '.$this->$resultSet->returnScoresV2($i);
					$i++;
				}
			}
		}
		else {
			echo '</br>Sorry, no results available!';
		}
	}
	
	// Output Result Set to File
	// Iterate the sorted key array
	public function outputResultSetToFile($filename, $resultSet, $int) {
		
		$target = $filename;
		$th=fopen($target, 'a')or die("Couldn't open file, sorry");
		if($this->$resultSet->returnUrls() == NULL)
			echo '</br>No results!!!';
		else {
			for($i=0; $i<$int;$i++)
			{
				// Print the details according to the ordered array
				{
					$line = ''.$this->$resultSet->returnUrlsV2($i).PHP_EOL;
					fwrite($th, $line); 
				}
			}
		}
		fclose($th);
	}
	
} // End of Formatter Class


// **********
// AGGREGATOR CLASS
// **********

// Purpose: To aggregate the search results and display them as required

class aggregator
{
	// **********
	// PROPERTIES
	// **********
	
	private $resultSetAgg = NULL;
	private $resultSetAggCluster = NULL;
	
	// **********
	// METHODS
	// **********
	
	// Object Property Constructor
	public function __construct(resultSet $resultSetAgg) {
		$this->resultSetAgg = $resultSetAgg;
    }
	
	// ***********************
	// Aggregation Algorithm
	// ***********************		
	
	// Weighted Borda-Fuse
	public function dataFusion($resultSetFlag1, $resultSetFlag2, $resultSetFlag3, $resultSet1, $resultSet2, $resultSet3) {
				
		// input search engine weights
		$weight1=1.34;
		$weight2=1.27;
		$weight3=1.0;
		
		// Conditional Initialising of the the aggregated array - includes checks for error of null result set
		if ($resultSetFlag2 == TRUE)
		{
			for($i=0, $count = $resultSet2->returnNumItems(); $i<$count;$i++ )
			{
				$this->resultSetAgg->addUrl($resultSet2->returnUrlsV2($i));
				$this->resultSetAgg->addTitle($resultSet2->returnTitlesV2($i));
				$this->resultSetAgg->addSnippet($resultSet2->returnSnippetsV2($i));
				$this->resultSetAgg->addScore($resultSet2->returnScoresV2($i)*$weight2);
			}
		}
		else if($resultSetFlag1 == TRUE)
		{
			for($i=0, $count = $resultSet1->returnNumItems(); $i<$count;$i++ )
			{
				$this->resultSetAgg->addUrl($resultSet1->returnUrlsV2($i));
				$this->resultSetAgg->addTitle($resultSet1->returnTitlesV2($i));
				$this->resultSetAgg->addSnippet($resultSet1->returnSnippetsV2($i));
				$this->resultSetAgg->addScore($resultSet1->returnScoresV2($i)*$weight1);
			}
		}
		else if ($resultSetFlag3 == TRUE)
		{
			for($i=0, $count = $resultSet3->returnNumItems(); $i<$count;$i++ )
			{
				$this->resultSetAgg->addUrl($resultSet3->returnUrlsV2($i));
				$this->resultSetAgg->addTitle($resultSet3->returnTitlesV2($i));
				$this->resultSetAgg->addSnippet($resultSet3->returnSnippetsV2($i));
				$this->resultSetAgg->addScore($resultSet3->returnScoresV2($i)*$weight3);
			}
		}
		else
		{
			echo '</br>Warning! No results from any Search Engines. Try Again Later.';
		}
		
		// ***********
		// Condition 1
		//************
		if($resultSetFlag2 == TRUE)
		{
			//
			// Fusion Stage 1
			//
			
			$countAL = $this->resultSetAgg->returnNumItems();

			for($i=0, $count = $resultSet1->returnNumItems(); $i<$count;$i++ )
			{
				for($j=0; $j<$countAL;$j++ )
				{
					if(in_array($resultSet1->returnUrlsV2($i), array($this->resultSetAgg->returnUrlsV2($j)), TRUE))
					{
						$this->resultSetAgg->sumFusedScores($resultSet1->returnScoresV2($i), $j, $weight1); //
					}
					else if(!in_array($resultSet1->returnUrlsV2($i), $this->resultSetAgg->returnUrls(), TRUE))
					{
						$this->resultSetAgg->addUrl($resultSet1->returnUrlsV2($i));
						$this->resultSetAgg->addTitle($resultSet1->returnTitlesV2($i));
						$this->resultSetAgg->addSnippet($resultSet1->returnSnippetsV2($i));
						$this->resultSetAgg->addScore($resultSet1->returnScoresV2($i)*$weight1);
					}
				}
			}
			
			//
			// Fusion Stage 2
			//
			
			$countAL = $this->resultSetAgg->returnNumItems();

			for($i=0, $count = $resultSet3->returnNumItems(); $i<$count;$i++ )
			{
				for($j=0; $j<$countAL;$j++ )
				{
					if(in_array($resultSet3->returnUrlsV2($i), array($this->resultSetAgg->returnUrlsV2($j)), TRUE))
					{
						$this->resultSetAgg->sumFusedScores($resultSet3->returnScoresV2($i), $j, $weight3); //
					}
					else if(!in_array($resultSet3->returnUrlsV2($i), $this->resultSetAgg->returnUrls(), TRUE))
					{
						$this->resultSetAgg->addUrl($resultSet3->returnUrlsV2($i));
						$this->resultSetAgg->addTitle($resultSet3->returnTitlesV2($i));
						$this->resultSetAgg->addSnippet($resultSet3->returnSnippetsV2($i));
						$this->resultSetAgg->addScore($resultSet3->returnScoresV2($i)*$weight3);
					}
				}
			}
		} // End Cond 1
		
		// ***********
		// Condition 2
		//************
		if($resultSetFlag1 == TRUE && $resultSetFlag2 == FALSE)
		{
			//
			// Fusion Stage 1
			//
			
			$countAL = $this->resultSetAgg->returnNumItems();

			for($i=0, $count = $resultSet3->returnNumItems(); $i<$count;$i++ )
			{
				for($j=0; $j<$countAL;$j++ )
				{
					if(in_array($resultSet3->returnUrlsV2($i), array($this->resultSetAgg->returnUrlsV2($j)), TRUE))
					{
						$this->resultSetAgg->sumFusedScores($resultSet3->returnScoresV2($i), $j, $weight3); //
					}
					else if(!in_array($resultSet3->returnUrlsV2($i), $this->resultSetAgg->returnUrls(), TRUE))
					{
						$this->resultSetAgg->addUrl($resultSet3->returnUrlsV2($i));
						$this->resultSetAgg->addTitle($resultSet3->returnTitlesV2($i));
						$this->resultSetAgg->addSnippet($resultSet3->returnSnippetsV2($i));
						$this->resultSetAgg->addScore($resultSet3->returnScoresV2($i)*$weight3);
					}
				}
			}
		} // End Cond 2
		
    } // End of Data Fusion Function
	
	
	// Sort and Display Agg List
	public function printResultSetAgg() {
		
		// Sorting technique
		$sortedKeys = $this->resultSetAgg->returnScores();
		arsort($sortedKeys);
		$sortedKeys = array_keys($sortedKeys);

		// Iterate the sorted key array
		$i=0;
		foreach($this->resultSetAgg->returnUrls() as $item)
		// Print the details according to the ordered array
		{
			echo '</br><strong>#'.($i+1).': '.$this->resultSetAgg->returnTitlesV2($sortedKeys[$i]).'</strong>';
			echo '</br>'.'<a href="'.$this->resultSetAgg->returnUrlsV2($sortedKeys[$i]).'">'.$this->resultSetAgg->returnUrlsV2($sortedKeys[$i]).'</a>';
			echo '</br>'.$this->resultSetAgg->returnSnippetsV2($sortedKeys[$i]);
			echo '</br>Score: '.$this->resultSetAgg->returnScoresV2($sortedKeys[$i]).'</br>';
			$i++;
		}

    }// End of printResultSetAgg()
	
	// Return Aggregated List Urls
	public function returnResultSetAggUrls() {
		return $this->resultSetAgg->returnUrls();
	}
	
	// Return Aggregated List Titles
	public function returnResultSetAggTitles() {
		return $this->resultSetAgg->returnTitles();
	}
	
	// Return Aggregated List Snippets
	public function returnResultSetAggSnippets() {
		return $this->resultSetAgg->returnSnippets();
	}
	// *****************************************************
	// Sort and Display Agg List according to Clustered Term
	// *****************************************************
	public function printResultSetAggCluster($clusterTerm) {
		
		//echo trim($clusterTerm).'</br>';
		$this->resultSetAggCluster = $this->resultSetAgg;
		
		// Filter By Clustered Term
		foreach($this->resultSetAgg->returnSnippets() as $key=>$value)
		{
			$value = preg_replace('/[^\w]/', ' ', $value);
			$value = explode(' ', strtolower($value));
			
			if(!in_array($clusterTerm, $value))
			//if(strpos(strtolower($value), $clusterTerm ) == NULL)
			//if(strpos(strtolower(strip_tags($value)), (!empty($clusterTerm) ? $clusterTerm : " ") ) == NULL)
			{
				$this->resultSetAgg->unsetUrl($key);
				$this->resultSetAgg->unsetTitle($key);
				$this->resultSetAgg->unsetSnippet($key);
				$this->resultSetAgg->unsetScore($key);
			}
		}
		
		// Sorting technique required to sort unrelated arrays in parallel
		$sortedKeys = $this->resultSetAgg->returnScores();
		arsort($sortedKeys);
		$sortedKeys = array_keys($sortedKeys);

		// Iterate the sorted key array
		for($i=0, $count = count($sortedKeys); $i<$count;$i++)
		{
			{
				echo '</br><strong>#'.($i+1).': '.$this->resultSetAgg->returnTitlesV2($sortedKeys[$i]).'</strong>';
				echo '</br>'.'<a href="'.$this->resultSetAgg->returnUrlsV2($sortedKeys[$i]).'">'.$this->resultSetAgg->returnUrlsV2($sortedKeys[$i]).'</a>';
				echo '</br>'.$this->resultSetAgg->returnSnippetsV2($sortedKeys[$i]).'</br>';
				//echo '</br>Score: '.$this->resultSetAgg->returnScoresV2($sortedKeys[$i]).'</br>';
			}
		}
    }// End of printResultSetAggCluster()
	
	// **********************
	// Display Binaclustering
	// **********************
	public function printResultSetAggBinCluster($binTerm, $bins, $binatureSums) {
		
		// Filter By Bins
		$tempBins = array_keys($bins);
		
		//for($i=0;$i<count($binatureSums);$i++)
		//{
			//echo '</br>!!!i: '.$i;
			//foreach($bins as $binKey=>$binValue)
			for($j=0;$j<count($bins);$j++)
			{
				//echo '</br>j: '.$j;
				//foreach($binValue as $Key=>$Value)
				for($k=0;$k<count($bins[$j]);$k++)
				{
					//echo '</br>k: '.$k;
					//echo '</br>V: '.$binKey.' T: '.$binTerm;
					//if($binTerm != $binKey)
					//echo '</br>Bin Contents: '.$bins[$j][$k];
					//echo '</br>BinTerm: '.$binTerm.' Bin Key j: '.array_keys($bins)[$j];
					if($binTerm != $tempBins[$j] && $binTerm != "")
					{
						//echo '</br>!!!HIT Bin Array Key j: '.array_keys($bins)[$j];
						//echo '</br>>>>Remove index: '.$bins[$j][$k];
						//echo '</br>Bin Contents: '.$bins[$j][$k];
						$this->resultSetAgg->unsetUrl($bins[$j][$k]);
						$this->resultSetAgg->unsetTitle($bins[$j][$k]);
						$this->resultSetAgg->unsetSnippet($bins[$j][$k]);
						$this->resultSetAgg->unsetScore($bins[$j][$k]);
					}
				}
			}
		//}
		
		// Sorting technique required to sort unrelated arrays in parallel
		$sortedKeys = $this->resultSetAgg->returnScores();
		arsort($sortedKeys);
		$sortedKeys = array_keys($sortedKeys);

		// Iterate the sorted key array
		for($i=0, $count = count($sortedKeys); $i<$count;$i++)
		{
			{
				echo '</br><strong>#'.($i+1).': '.$this->resultSetAgg->returnTitlesV2($sortedKeys[$i]).'</strong>';
				echo '</br>'.'<a href="'.$this->resultSetAgg->returnUrlsV2($sortedKeys[$i]).'">'.$this->resultSetAgg->returnUrlsV2($sortedKeys[$i]).'</a>';
				echo '</br>'.$this->resultSetAgg->returnSnippetsV2($sortedKeys[$i]).'</br>';
				//echo '</br>Score: '.$this->resultSetAgg->returnScoresV2($sortedKeys[$i]).'</br>';
			}
		}
    }// End of printResultSetAggBinCluster()
	
	// Output Result Set to File
	// Iterate the sorted key array
	public function outputResultSetToFile($filename, $resultSet, $int) {
		
		// Sorting technique
		$sortedKeys = $this->resultSetAgg->returnScores();
		arsort($sortedKeys);
		$sortedKeys = array_keys($sortedKeys);

		$target = $filename;
		$th=fopen($target, 'a')or die("Couldn't open file, sorry");
		
		if($this->$resultSet->returnUrls() == NULL)
			echo '</br>No results!!!';
		else {
			for($i=0; $i<$int;$i++)
			{
				{
					$line = ''.$this->$resultSet->returnUrlsV2($sortedKeys[$i]).PHP_EOL;
					fwrite($th, $line); 
				}
			}
		}
		fclose($th);
	}
} // End of Aggregator Class


// **************
// CLUSTER CLASS
// **************

// Purpose: To handle clustering of the aggregated list results

class cluster
{
	// **********
	// PROPERTIES
	// **********
	
	private $stringTokens = array();
	private $clusteredTerms = array();
	private $mostFrequentTerms = array();
	//
	private $masterBinature = "";
	private $binatures = array();
	private $binatureSums = array();
	private $bindroids = array();
	private $bins = array(array(),array(),array(),array());
	private $binTerms = array();
	private $clusteredBinaTerms = array();
	
	// **********
	// METHODS
	// **********
	
	// Tokenise
	public function tokeniseString($string){
	
		$stringTokens = NULL;
		//strip punctuation
		//$string = preg_replace('/(\'|&#0*39;)/', '', $string); // possibly related to a 's error "mccartney's" ie keeps possessive s
		$string = preg_replace('/[^a-z]+/i', ' ', $string);
		$this->stringTokens = explode(" ", strtolower($string));
		return $this->stringTokens;
	}
	
	// Display String Tokens
	public function displayStringTokens(){
	
		foreach($this->stringTokens as $item)
			echo '</br>'.$item;
	}

	// findTerms() is passed an array of snippets
	public function findTerms($array, $stopwords)
	{
		// foreach snippet
		foreach($array as $item)
		{
			$this->tokeniseString(strip_tags($item));

			foreach($this->stringTokens as $key=>$value)
			{
				if(!in_array($value, $stopwords, true))
				{
					$this->clusteredTerms[$value]=0;
					break;
				}
			}
				
			
		}
	}// End of find Terms
	
	// find Bina Terms() is passed an array of snippets
	public function findBinaTerms($array)
	{
		// foreach snippet
		foreach($array as $item)
		{
			$this->tokeniseString(strip_tags($item));

			foreach($this->stringTokens as $key=>$value)
			{
				// !!! Here
				$this->clusteredBinaTerms[$value]=1;
			}
				
			// Cluster Dictionary Word Fusion
			foreach($this->stringTokens as $key=>$value)
			{
				foreach($this->clusteredTerms as $key1=>$value1)
				{
					if(!in_array($value, $this->clusteredBinaTerms, true))
					{
						$this->clusteredBinaTerms[$value] = 1;
					}
				}
			}
		}
		sort($this->clusteredBinaTerms);
		//echo '</br>VARDUMP!!!';
		//var_dump(sort($this->clusteredBinaTerms));
	}// End of find BinaTerms
	
	// Print All Clustered Terms
	public function displayClusteredTerms(){

		echo '<h4>!!!Clustered Terms!!!</h4>';
		foreach($this->clusteredTerms as $key=>$value){
			echo '</br>'.$key.' ('.$value.')';
		}	
	}
	
	// Count Cluster Frequency
	public function countTermFrequency($array){
		
		foreach($this->clusteredTerms as $termKey=>$termValue)
		{
			foreach($array as $stringKey=>$stringValue)
			{
				$this->tokeniseString($stringValue);
				foreach($this->stringTokens as $tokenKey=>$tokenValue)
				{
					if($termKey==$tokenValue)
					{
						$this->clusteredTerms[$termKey] = ++$termValue;
					}
				}
			}	
		}
	}
	
	// Top X Clustered Terms where X is a passed int - could be user selectable
	public function setMostFrequentTerms($int){
		
		$topTerms = arsort($this->clusteredTerms);
		$i=1;
		foreach($this->clusteredTerms as $key=>$value)
		{
			array_push($this->mostFrequentTerms, $key = preg_replace('/[^a-z]+/i', ' ', $key));
			if($i++>=$int)
				break;
		}
	}
	
	// Stopword Removal
	public function stopwordRemoval($stopwordArray){
		
		// check to see if term is in stopword array, if it is, remove it by array pop
		foreach($this->mostFrequentTerms as $termKey=>$termValue)
		{
			foreach($stopwordArray as $stopKey=>$stopValue)
			{
				if($termValue == $stopValue)
				{
					unset($this->mostFrequentTerms[$termKey]);
				}
			}	
		}
	}
	
	// Display Most Frequent Terms
	public function displayMostFrequentTerms($q){
		echo '</br>'.'<img style="padding-right:10px;"src="img/folder.gif" width="18px"alt="folder icon"/><a href ="search.php?q='.$_SESSION['query'].'&result_op=clustered&term=">All Results</a>';
		foreach($this->mostFrequentTerms as $key=>$value){
			echo '</br>'.'<img style="padding-left:5px;padding-right:10px;"src="img/folder.gif" width="18px"alt="folder icon"/><a href ="search.php?q='.$_SESSION['query'].'&result_op=clustered&term='.$value.'">'.$value.'</a>';
		}	
	}
	
	// Return Most Frequent Terms
	public function returnMostFrequentTerms(){
		return $this->mostFrequentTerms;
	}
	
	// *****************
	// Binaclusteing
	// *****************
	
	// 1. Set master List - use Clustered Terms above
	// 2. Set Bindroids
	

	// get count clusteredTerms
	public function countClusteredTerms(){
		return count($this->clusteredTerms);
	}
	
	// Return Most Frequent Terms
	public function returnClusteredTerms(){
		return $this->clusteredTerms;
	}
	
	// getMinBinatureSums
	public function getMinBinatureSums(){
		return min($this->binatureSums);
	}
	
	// getMaxBinatureSums
	public function getMaxBinatureSums(){
		return max($this->binatureSums);
	}
	
	// setDocumentBinatures
	public function setDocumentBinatures($snippets){
		//echo '</br>Setting Binatures...</br>';
		foreach($snippets as $snippet)
		{	
			$i=0;
			$tempsum=0;
			$tempBinature = "";
			foreach($this->clusteredBinaTerms as $key=>$value)
			{
				if(strpos($snippet, $key) != NULL)
				{
					//echo '</br>found';
					$tempBinature[$i] = 1;
					$tempsum++;
				}
				else
				{
					$tempBinature[$i] = 0;
				}
				$i++;
			}
			array_push($this->binatureSums, $tempsum);
			$tempBinature = implode($tempBinature);
			//echo '</br>'.$tempBinature;
			array_push($this->binatures, $tempBinature);
		}
	}
	
	// Print binatures
	public function printBinatures(){
		foreach ($this->binatures as $binature)
		{
			echo '</br>'.$binature;
		}
	}
	
	// Print binatureSums
	public function printBinatureSums(){
		foreach ($this->binatureSums as $sum)
		{
			echo '</br>'.$sum;
		}
	}
	
	// Print binatureSums
	public function returnBinatureSums(){
		return $this->binatureSums;
	}
	
	// set Bindroids
	public function setBindroids($ticks){
		$range = ($this->getMaxBinatureSums() - $this->getMinBinatureSums());
		//echo '</br>Min: '.$this->getMinBinatureSums();
		//echo '</br>Max: '.$this->getMaxBinatureSums();
		//echo '</br>Range: '.$range;
		//echo '</br>Ticks: '.$ticks;
		for($i=0;$i<$ticks;$i++)
		{
			$this->bindroids[$i] = ($this->getMinBinatureSums() + (($range/($ticks+1)) * ($i+1)));
		}
		$this->bindroids[$i] = $this->getMaxBinatureSums();
		//echo '</br>Bindroids: ';var_dump($this->bindroids);
	}
	
	// Bin Binatures
	public function binBinatures($int){
		foreach ($this->binatureSums as $binSumKey=>$binSumValue)
		{
			//echo '</br>$binSumValue: '.$binSumValue;
			//foreach($this->bindroids as $bindroid)
			for($i=0;$i<=$int;$i++)
			{
				//echo '</br>$bindroid: '.$this->bindroids[$i];
				if($binSumValue <= $this->bindroids[$i])
				{
					//echo '</br>!!!Hit: '.$i;
					array_push($this->bins[$i], $binSumKey);
					break;
				}
			}
		}
		//echo '</br>Bins: ';var_dump($this->bins);
	}
	
	// Bin Terms
	public function returnBins(){
		
		return $this->bins;
	}
	
	// Bin Terms
	public function setBinTerms($int){
		
		$this->binTerms = array("0", "1", "2", "3");
	}
	
	// Display Most Frequent Bin Terms
	public function displayBinTerms($q){
		echo '</br>'.'<img style="padding-right:10px;"src="img/folder.gif" width="18px"alt="folder icon"/><a href ="search.php?q='.$_SESSION['query'].'&result_op=clustered&binTerm=">All Results</a>';
		foreach($this->binTerms as $key=>$value){
			echo '</br>'.'<img style="padding-left:5px;padding-right:10px;"src="img/folder.gif" width="18px"alt="folder icon"/><a href ="search.php?q='.$_SESSION['query'].'&result_op=clustered&binTerm='.$value.'">Cluster: '.$value.'</a>';
		}	
	}

} // End of Cluster Class


// *****************
// DICTIONARY CLASS
// *****************

// Purpose: To handle all matters to do with dictionaries such as stop word lists

class dictionary
{
	// **********
	// PROPERTIES
	// **********
	private $stopwordFilename;
	private $stopwords;

	
	// **********
	// CONSTRUCTOR
	// **********
	public function __construct($stopwordFilename) {
		$this->stopwordFilename = $stopwordFilename;
		$this->loadStopwordFile();
    }
	
	// **********
	// METHODS
	// **********
	
	// load Stopword File
	// Maybe pass in name of file se we can reuse the function to add custom lists
	public function loadStopwordFile(){
		
		$fp = fopen($this->stopwordFilename, 'r') or die("Couldn\'t open file, sorry");
		while (!feof($fp))
		{
			$line=fgets($fp);
			$this->stopwords[] = trim($line);
		}
		fclose($fp);
	}
	
	// Add Query to Stopwords
	public function addQueryToStopwords($queryTokens){
		
		foreach($queryTokens as $key=>$value)
		{
			//add to array
			$this->stopwords[] = trim($value);
		}
	}
	
	// List stopwords
	public function displayStopwordFile(){
		foreach($this->stopwords as $word)
		{
			echo '</br>'.$word;
		}
		return $this->stopwords;
	}
	
	// Return stopwords
	public function returnStopwords(){
		return $this->stopwords;
	}
	
} // End of Dictionary Class


// **********
// THESAURUS CLASS
// **********

// Purpose: To handle all matters to do with THESAURUS

class thesaurus
{
	
	// **********
	// METHODS
	// **********
	
	// load Thesaurus File
	public function loadThesaurusFile($filename){
		
		$fp = fopen($filename, 'r') or die("Couldn\'t open file, sorry");
		$line=fgets($fp);
		while (!feof($fp))
		{
			list($part1, $part2) = explode(',', $line, 2);
			// Next line shortens synonyms to just one synonym
			//list($part2, $part3) = explode(',', $part2, 2);
			//add to array
			$this->thesaurus[$part1] = trim($part2);
			$line=fgets($fp);
		}
		fclose($fp);
	}
	
	// Return Thesaurus
	public function returnThesaurus(){
		return $this->thesaurus;
	}
}

?>