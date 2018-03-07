<?php


// Pass it an artist MBID and then it'll download and update any releases it grabs. Neat!

function download_release_groups_official($artist_id,$artist_mbid,$artist_last_updated) {
	
	$url = "http://musicbrainz.org/ws/2/release?artist=$artist_mbid&limit=100&fmt=json&status=official&inc=release-groups";
	$JSON = file_get_contents($url);
	$headers = parseHeaders($http_response_header);
	$response_code = $headers['response_code'];
	$tot_releases_added = 0;
	$tot_releases_updated = 0;
	$tot_releases_deleted = 0;
	
	$db = Database::getInstance();
	$mysqli = $db->getConnection(); 

	if ($response_code == 200) {

		$data = json_decode($JSON,true);
		
		$total_releases = $data['release-count'];
		
		if ($total_releases > 0) {
						
			// Divide total releases by 100 and round up to determine number of cycles...
			
			$num_of_cycles = ceil($total_releases / 100);
			$elapsed_cycles = 0;
			$offset = 0;
			$retries = 0;
			$releases_added = 0;
			$releases_updated = 0;
			
			while ($elapsed_cycles < $num_of_cycles) {
				
				// Sleep to make musicbrainz rate limiting happy...
				
				sleep(1);
				
				$url = "http://musicbrainz.org/ws/2/release?artist=$artist_mbid&limit=100&fmt=json&status=official&inc=release-groups&offset=$offset";
				$JSON = file_get_contents($url);
				$headers = parseHeaders($http_response_header);
				$response_code = $headers['response_code'];

				if ($response_code == 200) {
					
					$retries = 0;
					$data = json_decode($JSON,true);
					
					foreach ($data['releases'] as $release_group) {
						
						// Primary data	
						
						$album_title = $release_group['release-group']['title'];
						$album_mbid = $release_group['release-group']['id'];
						$album_first_release = convert_date($release_group['release-group']['first-release-date']);
						
						// Parse for numu type
						
						$primary_type = $release_group['release-group']['primary-type'];
						$secondary_types = "";
						foreach ($release_group['release-group']['secondary-types'] as $secondary_type) { 
							$secondary_types .= $secondary_type." ";
						}
						$secondary_types = trim($secondary_types);
						
						// TODO: Need to cascade these in order of general uniqueness...
						
						if (strpos($secondary_types, 'Live') !== false) {
							$numu_type = "Live";
						} else if (strpos($secondary_types, 'Compilation') !== false) {
							$numu_type = "Compilation";
						} else if (strpos($secondary_types, 'Remix') !== false) {
							$numu_type = "Remix";
						} else if (strpos($secondary_types, 'Soundtrack') !== false) {
							$numu_type = "Soundtrack";
						} else if (strpos($secondary_types, 'Interview') !== false) {
							$numu_type = "Interview";
						} else if (strpos($secondary_types, 'Spokenword') !== false) {
							$numu_type = "Spokenword";
						} else if (strpos($secondary_types, 'Audiobook') !== false) {
							$numu_type = "Audiobook";
						} else if (strpos($secondary_types, 'Mixtape') !== false) {
							$numu_type = "Mixtape";
						} else if (strpos($secondary_types, 'Demo') !== false) {
							$numu_type = "Demo";
						} else if (strpos($secondary_types, 'DJ-mix') !== false) {
							$numu_type = "DJ-mix";
						} else {
							$numu_type = $primary_type;
						}
						
						if ($numu_type == '') {
							$numu_type = 'Unknown';
						}
						
						if ($album_first_release != '0000-00-00') {
						// Save to database 
							
							// TODO: This insert to check for duplicate key is causing my auto-increment value in the releases table to go nuts.
							
							$sql_query = "INSERT INTO v2_release_group (artist_id,release_mbid,title,type,date,last_updated) VALUES ('" . $mysqli->real_escape_string($artist_id) ."','" . $mysqli->real_escape_string($album_mbid) ."','" . $mysqli->real_escape_string($album_title) ."','" . $mysqli->real_escape_string($numu_type) ."','" . $mysqli->real_escape_string($album_first_release) ."','" . date("Y-m-d") . "')";
							
							if($mysqli->query($sql_query) === false) {

								// Release already exists... Update info?
								
								 $sql_query = "UPDATE v2_release_group SET title = '" . $mysqli->real_escape_string($album_title) ."', type = '" . $mysqli->real_escape_string($numu_type) ."', date = '" . $mysqli->real_escape_string($album_first_release) ."', last_updated = '" . date("Y-m-d") . "' WHERE release_mbid = '$album_mbid'";
								 if($mysqli->query($sql_query) === false) {
									 trigger_error('Wrong SQL: ' . $sql_query . ' Error: ' . $mysqli->error, E_USER_ERROR);
								 }
								 
								 $releases_updated++;
								 
							} else {
								
								$album_id = $mysqli->insert_id;
								$releases_added++;
															
								if ($artist_last_updated != '0000-00-00') {
									
									// New release added to database... create notification!
									
									// Get list of users who follow this artist currently
									
									$sql_query = "SELECT user_id FROM `v2_user_artist` WHERE artist_id = $artist_id";
									$result = $mysqli->query($sql_query);
									$num_rows = $result->num_rows;
									
									$user_id_array = array();
									
									while ($row = mysqli_fetch_array($result)) {
										array_push($user_id_array,$row['user_id']);
									}
									
									// Create a notification for each
									
									if (!empty($user_id_array)) {
										foreach ($user_id_array as $user_id) { 
											
											// Put notification in database...
											$sql_query = "INSERT INTO v2_fresh (user_id,release_id,type) VALUES ('" . $user_id ."','" . $album_id ."','added')";
											$result = $mysqli->query($sql_query);
											$affected_rows = $mysqli->affected_rows;
											
										}
									}
								}
							}
						}
					}
					
					$offset += 100;
					$elapsed_cycles++;
				}
				
				$retries++;
				
				if ($retries > 5) {
					echo "Too many retries.";
					return;
				}
			}

			$tot_releases_added += $releases_added;
			$tot_releases_updated += $releases_updated;
			
		}
		
		//Set artist as updated...
		
		$mysqli->query("UPDATE v2_artist SET last_updated = '" . date("Y-m-d") . "' WHERE artist_id = '" . $artist_id . "'");
		
	} else if ($response_code == 404) {
		
		// Delete artist
		
		$mysqli->query("DELETE FROM v2_artist WHERE artist_id = '" . $artist_id . "'");
		$tot_releases_deleted++;
		
	}
	
	if ($tot_releases_added > 0 || $tot_releases_updated > 0 || $tot_releases_deleted > 0) {
		echo "Total releases added: $tot_releases_added / Updated: $tot_releases_updated / Artist deleted: $tot_releases_deleted<br/>";
	}
	
}