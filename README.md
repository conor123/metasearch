# Description
Metasearch engines take input from a user and send out queries to third party search engines for results. 
Sufficient data is gathered, formatted by their ranks and presented to the users.
## Screenshots
 <img src="https://i.imgur.com/2nmnWRF.png" width="600" height="400"/> <img src="https://imgur.com/GkUPKHE.png" width="600" height="400" />
## Features
<ul>
<li> Web & Image Search.</li>
<li> Ask.com HTML Parsing.</li>
<li> Query Tokenization.</li>
<li> Query Stemming.</li>
<li> Query Suggestion.</li>
<li> Document Term Clustering.</li>
<li> Document Bina Clustering.</li>
</ul>

## Technologies
<ul>
<li> HTML5 & CSS3 </li>
<li> PHP v5.6 </li>
<li> Twitter Bootstrap v2 </li>
<li> Google CSE API </li>
<li> Bing Search API </li>
<li> MySQL Database </li>
</ul>

## How it works
<img src="https://i.imgur.com/yBUDS1H.png" alt="Zsearch - how it works"/>

### Query Tokenization
Tokenizing is the process of forming words from the sequence of characters in a document. All uppercase letters were also
converted to lowercase.

### Query Preparation
- In Google: NOT -> Hyphen (-). All non-alphanumeric characters except (-_.) have been replaced with a percent(%) sign followed by two hex digits and spaces encoded as plus (+) signs.
- It is encoded the same way that the
posted data from a WWW form is
encoded.

### Query Stemming
Stemming is the process of reducing inflected (or sometimes derived) words to their word stem, base or root form—generally a written word form.
<ul> Stemming Algorithms used :- 
  <li> Porter Stemming </li>
  <li> Porter2 Stemming </li>
 </ul> 

### Ask.com Parsing
- Query Ask.com website.
- Fetch results page.
- Look for each HTML element with class PartialSearchResults.
- Separate each result.

### Format Results
- Extract link, title, and snippet from each JSON object.
- Extract data from Ask.com results Html page.
- Render Google, Bing data from JSON format to resultsets.

### Clustering 
( Clustering methods can be used to automatically group the retrieved documents into a list of meaningful categories.)
- Tokenize retrieved snippets.
- Remove any query words.
- Remove any stopping terms.
- Search for the most frequent terms.
- Sort frequent terms in decreasing order.
### Aggregate Results
- Combine fetched results from each search engine.
- Clean links from Http/Http/www.
- Increase score of results with repeated link.
- Sort results in decreasing order.

### Query Suggestions
( Suggestions are displayed to give user more insights.)
- Query tokenization.
- Load choosed Thesaurus files.
- Look for synonym terms for each token.
- Display possible alternative queries.

### Display Results
- Results are formatted in convenient way to be displayed for the user.
