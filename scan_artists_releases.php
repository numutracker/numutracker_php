<?php

function scan_artists_releases() {
	// Scan artists that haven't been updated for 3 days...
	$db = Database::getInstance();
	$mysqli = $db->getConnection(); 
	$sql_query = "SELECT artist_id,artist_mbid,last_updated FROM v2_artist WHERE ((last_updated < NOW() - INTERVAL 3 DAY) OR (last_updated IS NULL)) ORDER BY last_updated ASC LIMIT 100";
	$result = $mysqli->query($sql_query);
	if($mysqli->query($sql_query) === false) {
		trigger_error('Wrong SQL: ' . $sql_query . ' Error: ' . $mysqli->error, E_USER_ERROR);
	}
	$num_rows = $result->num_rows;
	$result->data_seek(0);
	while($row = $result->fetch_assoc()){
		download_release_groups_official($row['artist_id'],$row['artist_mbid'],$row['last_updated']);
	}
}