<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
error_reporting(E_ALL ^ E_WARNING); 
ini_set( 'date.timezone', 'America/Los_Angeles' );

require __DIR__ . '/vendor/autoload.php';

require_once 'database.php';
require_once 'functions.php';

require_once 'download_release_groups_official.php';
require_once 'download_lastfm_artists.php';
require_once 'musicbrainz_artist_search_and_add.php';
require_once 'check_imported_artists_long.php';
require_once 'check_imported_artists_short.php';
require_once 'scan_artists_releases.php';
require_once 'scan_for_album_art.php';
require_once 'scan_for_artist_art.php';
require_once 'scan_for_merged_releases.php';
require_once 'push_notifications.php';

//echo download_lastfm_artists(69);

check_imported_artists_short(); // load in imported artists without musicbrainz lookup
check_imported_artists_long(); 	// load in imported artists with musicbrainz lookup
scan_artists_releases(); 		// scan and update artist releases
scan_for_merged_releases(); 	// scan for out of date releases to merge...
scan_for_album_art(); 			// scan for album art
scan_for_artist_art();      	// scan for artist art
push_notifications();			// send notifications


	
$time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
echo "Process Time: {$time}";


?>