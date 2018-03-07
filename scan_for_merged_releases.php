<?php

function scan_for_merged_releases() {
	// Scan releases in database to determine whether item should be deleted or not
$db = Database::getInstance();
$mysqli = $db->getConnection(); 
$sql_query = "SELECT v2_release_group.*,v2_artist.artist_mbid FROM v2_release_group LEFT JOIN v2_artist ON v2_artist.artist_id = v2_release_group.artist_id WHERE ((v2_release_group.last_updated < NOW() - INTERVAL 4 DAY)) AND v2_release_group.is_deleted = '0' ORDER BY v2_release_group.last_updated ASC LIMIT 200";
$result = $mysqli->query($sql_query);
if($mysqli->query($sql_query) === false) {
	trigger_error('Wrong SQL: ' . $sql_query . ' Error: ' . $mysqli->error, E_USER_ERROR);
}
$num_rows = $result->num_rows;
$result->data_seek(0);
$tot_releases_updated = 0;
$tot_releases_deleted = 0;
	while($row = $result->fetch_assoc()){
		
		//echo $row['title'];
		//echo " - ";
		$rg_mbid = $row['release_mbid'];
		//echo " - ";
		$artist_mbid = $row['artist_mbid'];
		//echo " - ";
		$artist_id = $row['artist_id'];
		//echo " - ";
		
		
		$url = "http://musicbrainz.org/ws/2/release-group/$rg_mbid?inc=artist-credits+releases&fmt=json";
		$JSON = file_get_contents($url);
		$headers = parseHeaders($http_response_header);
		$response_code = $headers['response_code'];
		
		$to_delete = 0;

		if ($response_code == 200) {

			$data = json_decode($JSON,true);
			
			//print_r($data);
			
			// Check whether or not status of releases under releasegroup is still "Official" or not.
			
			$releases = 0;
			$to_delete = 1;
			foreach ($data['releases'] as $release) {
				$releases++;
				if ($release['status'] == 'Official') {
					$official = 1;
					$to_delete = 0;
				}
			}
			
			if ($releases == 0) {
				// No releases in MB, delete?
			}
			
			// Check that mbid returned by musicbrainz equals mbid in database...
			
			if ($data['id'] != $rg_mbid) {
				// Mbid mismatch, delete...
				$to_delete = 1;
			}
			
			// Is primary artist id still in list of artist credits?
			/// Disabling this check for now because sometimes the artist_mbid assigned to a release in Numu is not the same artist_mbid in musicbrainz...
			/*
			$artist_match = 0;
			foreach ($data['artist-credit'] as $artist_credit) {
				if ($artist_credit['artist']['id'] == $artist_mbid) {
					// Artist assigned to release group still exists...
					$artist_match = 1;
					echo "Artist found in stack.<br/>";
				}
			}
			if ($artist_match == 0) {
				$to_delete = 1;
			}
			*/
			
			
			// Set release as updated since the 200 request came through...
			
			$mysqli->query("UPDATE v2_release_group SET last_updated = '" . date("Y-m-d") . "' WHERE release_mbid = '" . $rg_mbid . "'");
			$tot_releases_updated++;
			
				
		} else if ($response_code == 404) {
			
			// Release no longer exists in system, mark as deleted...
			
			$to_delete = 1;
			
			
		}
		
		//echo $to_delete;
		//echo "<br/>";
		// if to_delete equals 1, delete release
		
		if ($to_delete == 1) {
			$mysqli->query("DELETE FROM v2_release_group WHERE release_mbid = '" . $rg_mbid . "' AND artist_id = '" . $artist_id . "'");
			$tot_releases_deleted++;
		}


		// Sleep to make MB happy whoops.	
		sleep(1);
		
	}
	if ($tot_releases_updated > 0 || $tot_releases_deleted > 0) { 
		echo "Total releases updated: $tot_releases_updated / Releases deleted: $tot_releases_deleted<br/>";
	}
}