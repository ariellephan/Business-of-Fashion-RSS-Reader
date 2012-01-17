<?php
$siteName = empty($_GET['siteName']) ? 'Bof-TheBusinessOfFashionInsightAnalysis' : $_GET['siteName'];

$siteList = array(
   'Bof-TheBusinessOfFashionIntelligence',
	'Bof-TheBusinessOfFashionInsightAnalysis'

);

if ( !in_array($siteName, $siteList) ) {
   $siteName = 'Bof-TheBusinessOfFashionInsightAnalysis';
}

$cache = dirname(__FILE__) . "/cache/$siteName";
// Re-cache every three hours
if(filemtime($cache) < (time() - 10800))
{
   // Get from server
   if ( !file_exists(dirname(__FILE__) . '/cache') ) {
      mkdir(dirname(__FILE__) . '/cache', 0777);
   }
   // YQL query 
   $path = "http://query.yahooapis.com/v1/public/yql?q=";
   $path .= urlencode("SELECT * FROM feed WHERE url='http://feeds.feedburner.com/$siteName'");
   $path .= "&format=json";

   // Call YQL, and if the query didn't fail, cache the returned data
   $feed = file_get_contents($path, true);

   // If something was returned, cache
   if ( is_object($feed) && $feed->query->count ) {
      $cachefile = fopen($cache, 'wb');
      fwrite($cachefile, $feed);
      fclose($cachefile);
   }
}
else
{
   // We already have local cache. Use that instead.
   $feed = file_get_contents($cache);
}

// Decode feeds
$feed = json_decode($feed);

// Include the view
include('views/site.tmpl.php');

