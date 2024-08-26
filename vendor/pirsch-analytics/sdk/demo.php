<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('default_socket_timeout', 2);
error_reporting(E_ALL);
session_start();
session_destroy();

require_once 'src/Client.php';
require_once 'src/Filter.php';

$clientID = '';
$clientSecret = '';
$client = new Pirsch\Client($clientID, $clientSecret, 5.0, 'https://localhost.com:9999');

try {
	$client->hit();
	print '<p>Hit sent!</p>';
} catch(Exception $e) {
	print '<p>An error occurred while sending the hit: </p>'.$e->getMessage();
}

try {
	$client->event('PHP', 42, ['hello' => 'world']);
	print '<p>Event sent!</p>';
} catch(Exception $e) {
	print '<p>An error occurred while sending the event: </p>'.$e->getMessage();
}

try {
    $domain = $client->domain();
    var_dump($domain);
    echo '<br /><br />';

    $filter = new Pirsch\Filter();
    $filter->id = $domain->id;
    $filter->from = date('Y-m-d', strtotime('-7 days'));
    $filter->to = date('Y-m-d');
    var_dump($filter);
    echo '<br /><br />';

    $sessionDuration = $client->sessionDuration($filter);
    var_dump($sessionDuration);
    echo '<br /><br />';

    $timeOnPage = $client->timeOnPage($filter);
    var_dump($timeOnPage);
    echo '<br /><br />';

    $utmSource = $client->utmSource($filter);
    var_dump($utmSource);
    echo '<br /><br />';

    $utmMedium = $client->utmMedium($filter);
    var_dump($utmMedium);
    echo '<br /><br />';

    $utmCampaign = $client->utmCampaign($filter);
    var_dump($utmCampaign);
    echo '<br /><br />';

    $utmContent = $client->utmContent($filter);
    var_dump($utmContent);
    echo '<br /><br />';

    $utmTerm = $client->utmTerm($filter);
    var_dump($utmTerm);
    echo '<br /><br />';

    $visitors = $client->visitors($filter);
    var_dump($visitors);
    echo '<br /><br />';

    $pages = $client->pages($filter);
    var_dump($pages);
    echo '<br /><br />';

    $entryPages = $client->entryPages($filter);
    var_dump($entryPages);
    echo '<br /><br />';

    $exitPages = $client->exitPages($filter);
    var_dump($exitPages);
    echo '<br /><br />';

    $conversionGoals = $client->conversionGoals($filter);
    var_dump($conversionGoals);
    echo '<br /><br />';

    echo '<h2>Events</h2>';
    $filter->event = 'PHP';
    $events = $client->events($filter);
    var_dump($events);
    echo '<br /><br />';
    
    echo '<h2>Event Metadata</h2>';
    $filter->event_meta_key = 'hello';
    $metadata = $client->eventMetadata($filter);
    var_dump($metadata);
    echo '<br /><br />';
    $filter->event = '';
    $filter->event_meta_key = '';

    $growth = $client->growth($filter);
    var_dump($growth);
    echo '<br /><br />';

    $activeVisitors = $client->activeVisitors($filter);
    var_dump($activeVisitors);
    echo '<br /><br />';

    $timeOfDay = $client->timeOfDay($filter);
    var_dump($timeOfDay);
    echo '<br /><br />';

    $languages = $client->languages($filter);
    var_dump($languages);
    echo '<br /><br />';

    $referrer = $client->referrer($filter);
    var_dump($referrer);
    echo '<br /><br />';

    $os = $client->os($filter);
    var_dump($os);
    echo '<br /><br />';

    $browser = $client->browser($filter);
    var_dump($browser);
    echo '<br /><br />';

    $country = $client->country($filter);
    var_dump($country);
    echo '<br /><br />';

    $city = $client->city($filter);
    var_dump($city);
    echo '<br /><br />';

    $platform = $client->platform($filter);
    var_dump($platform);
    echo '<br /><br />';

    $screen = $client->screen($filter);
    var_dump($screen);
    echo '<br /><br />';

    $keywords = $client->keywords($filter);
    var_dump($keywords);
    echo '<br /><br />';
} catch(Exception $e) {
    print '<p>An error occurred while reading the statistics: </p>'.$e->getMessage();
}
