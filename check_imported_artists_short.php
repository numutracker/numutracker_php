<?php

function check_imported_artists_short() {
	// Scan v2_imported_artists and add artists to database as needed...
	$db = Database::getInstance();
	$mysqli = $db->getConnection(); 
	$sql_query = "SELECT * FROM v2_imported_artists WHERE (last_check < NOW() - INTERVAL 3 DAY) AND (found_artist_id = '0') ORDER BY artist_name LIMIT 2000";
	$result = $mysqli->query($sql_query);
	if($mysqli->query($sql_query) === false) {
		trigger_error('Wrong SQL: ' . $sql_query . ' Error: ' . $mysqli->error, E_USER_ERROR);
	}
	$num_rows = $result->num_rows;
	$artist_array = array();
	if ($num_rows > 0) {
		$result->data_seek(0);
		while($row = $result->fetch_assoc()){
			$artist = array($row['artist_name'],$row['user_id']);
			array_push($artist_array,$artist);
		}
	}

	//print_r($artist_array);

	$artists_added = 0;
		
	if (!empty($artist_array)) {
		
		foreach ($artist_array as $artist) {
			
			// Check if artist already exists in v2_artist database before scanning musicbrainz for MBID...
			
			$sql_query = "SELECT artist_id FROM v2_artist WHERE name = '" . $mysqli->real_escape_string($artist[0]) . "'";
			//echo $sql_query."<br/>";
			$result = $mysqli->query($sql_query);
			$num_rows = $result->num_rows;
			$artist_id = 0;
			if ($num_rows == 1) {
				// Artist exists in database
				$row = mysqli_fetch_array($result);
				$artist_id = $row['artist_id'];
			} else { 
				// Artist does not exist in database and shouldn't be found by musicbrainz.
				//$artist_mbid = musicbrainz_artist_search_and_add($artist[1]);			
				$artist_id = 0;
			}
			
			if ($artist_id == 0) {
			// Check for AKAs
			
				$sql_query = "SELECT artist_id FROM v2_artist_aka WHERE name = '" . $mysqli->real_escape_string($artist[0]) . "'";
				//echo $sql_query."<br/>";
				$result = $mysqli->query($sql_query);
				$num_rows = $result->num_rows;
				$artist_id = 0;
				if ($num_rows == 1) {
					// Artist exists in database
					$row = mysqli_fetch_array($result);
					$artist_id = $row['artist_id'];
				} else { 
					// Artist does not exist in database and shouldn't be found by musicbrainz.
					//$artist_mbid = musicbrainz_artist_search_and_add($artist[1]);			
					$artist_id = 0;
				}
			
			}
			
			
			if ($artist_id > 0) {
				
				// Save user artist relationship...
				$date = date("Y-m-d H:i:s");
				if($mysqli->query("INSERT INTO v2_user_artist (user_id,artist_id,date) VALUES ('" . $artist[1] ."','" . $artist_id ."','" . $date . "')") === false) {
					//trigger_error('Wrong SQL: ' . $sql_query . ' Error: ' . $mysqli->error, E_USER_ERROR);
				}
				// Mark item as found in imported_artists database...
				if($mysqli->query("UPDATE v2_imported_artists SET found_artist_id = '". $artist_id."' WHERE user_id = '" . $artist[1] . "' AND artist_name = '" . $mysqli->real_escape_string($artist[0]) . "'") === false) {
					trigger_error('Wrong SQL: ' . $sql_query . ' Error: ' . $mysqli->error, E_USER_ERROR);
				}


			} else {
				// Don't mark item as not found so search later will pick it up.
				//$mysqli->query("UPDATE v2_imported_artists SET artist_mbid = '2' WHERE id = '" . $artist[0] . "'");
			}
			
			
		}
		
	}

}