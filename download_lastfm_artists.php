<?php 

function download_lastfm_artists($user_id) {
		
	$apikey = "";

	// Get lastfm username for user_id...
	$db = Database::getInstance();
	$mysqli = $db->getConnection(); 
	$sql_query = "SELECT * FROM v2_users WHERE user_id = $user_id";
	$result = $mysqli->query($sql_query);
	$num_rows = $result->num_rows;

	if ($num_rows == 1) { 
		$row = mysqli_fetch_array($result);
		$last_fm_user = $row['lastfm'];
	}


	$last_fm_user = urlencode($last_fm_user);
	$limit = 500;
	$page = 1;
	$period = 'overall';

	$xml = "http://ws.audioscrobbler.com/2.0/?method=user.gettopartists&user={$last_fm_user}&limit={$limit}&api_key={$apikey}&period={$period}&page={$page}";
	$xml    = @file_get_contents($xml);
	if(!$xml) {
		$return_array = array();
	} else {
		$xml = new SimpleXMLElement($xml);
		$return_array = array();
		foreach ($xml->topartists->artist as $xml2) {
			$return_array[] = $xml2->name->__toString();
		}
	}
	$period = '12month';
	$xml = "http://ws.audioscrobbler.com/2.0/?method=user.gettopartists&user={$last_fm_user}&limit={$limit}&api_key={$apikey}&period={$period}&page={$page}";
	$xml    = @file_get_contents($xml);
	if(!$xml) {
		//$return_array = array();
	} else {
		$xml = new SimpleXMLElement($xml);
		foreach ($xml->topartists->artist as $xml2) {
			$return_array[] = $xml2->name->__toString();
		}
	}

	$artists_added = 0;
	foreach ($return_array as $artist) {				// Save to lastfm database for musicbrainz scanning...
		$db = Database::getInstance();
		$mysqli = $db->getConnection(); 
		 if ($mysqli->query("INSERT INTO v2_imported_artists (user_id,artist_name) VALUES ('" . $user_id ."','" . $mysqli->real_escape_string($artist) ."')") == false) {
			// do nothing
		} else { 
			$artists_added++;
		} 
	}

	return $artists_added;

}