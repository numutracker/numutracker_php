<?php
function musicbrainz_artist_search_and_add($artist_name) {
	
	$blacklisted_artists = array(
	'89ad4ac3-39f7-470e-963a-56509c546377', # Various Artists
	'fe5b7087-438f-4e7e-afaf-6d93c8c888b2',
	'0677ef60-6be5-4e36-9d1e-8bb2bf85b981',
	'b7c7dfd9-d735-4733-9b10-f060ac75bd6a',
	'b05cc773-4e8e-40bc-ae12-dc88dfc2c9ec',
	'4b2228f5-e18b-4acc-ace7-b8db13a9306f',
	'046c889d-5b1c-4f54-9c7b-319a8f67e729',
	'1bf34db2-8447-4ecd-9b25-57945b28ef28',
	'023671ff-b1ad-4133-a4f3-aadaaadfd2e0',
	'f731ccc4-e22a-43af-a747-64213329e088', # [anonymous]
	'33cf029c-63b0-41a0-9855-be2a3665fb3b', # [data]
	'314e1c25-dde7-4e4d-b2f4-0a7b9f7c56dc', # [dialogue]
	'eec63d3c-3b81-4ad4-b1e4-7c147d4d2b61', # [no artist]
	'9be7f096-97ec-4615-8957-8d40b5dcbc41', # [traditional]
	'125ec42a-7229-4250-afc5-e057484327fe', # [unknown]
	'203b6058-2401-4bf0-89e3-8dc3d37c3f12',
	'5e760f5a-ea55-4b53-a18f-021c0d9779a6',
	'1d8bc797-ec8a-40d2-8d80-b1346b56a65f',
	'7734d67f-44d9-4ba2-91e3-9b067263210e',
	'f49cc9f4-dc00-48ab-9aab-6387c02738cf',
	'0035056d-72ac-41fa-8ea6-0e27e55f42f7',
	'd6bd72bc-b1e2-4525-92aa-0f853cbb41bf', # [soundtrack]
	'702245c5-dd3e-4ecd-bf7f-6cae5341cd29'  # [archive]
	);
	
	$db = Database::getInstance();
	$mysqli = $db->getConnection(); 
	$artist_name = rawurlencode(str_replace('/', ' ', "\"".$artist_name."\""));
	$url = "http://musicbrainz.org/ws/2/artist/?query=artist:$artist_name&fmt=json";
	sleep(1);
	$JSON = file_get_contents($url);
	$headers = parseHeaders($http_response_header);
	$response_code = $headers['response_code'];

	
	if ($response_code == 200) {
		$data = json_decode($JSON,true);

		// How many results are there?
		$num_of_results = $data['count'];
		
		if ($num_of_results > 0) {
			// Add top result...
			$artist_to_add = $data['artists'][0];
			$artist_name = $artist_to_add['name'];
			$artist_sort_name = $artist_to_add['sort-name'];
			$artist_mbid = $artist_to_add['id'];
			if (!empty($artist_to_add['disambiguation'])) {
				$artist_disambiguation = $artist_to_add['disambiguation'];
			} else {
				$artist_disambiguation = '';
			}
			
			if (!in_array($artist_mbid,$blacklisted_artists)) {
			
				if ($mysqli->query("INSERT INTO v2_artist (name,artist_mbid,sort_name,disambiguation,last_updated) VALUES ('" . $mysqli->real_escape_string($artist_name) . "','" . $mysqli->real_escape_string($artist_mbid ) . "','" . $mysqli->real_escape_string($artist_sort_name) . "','" . $mysqli->real_escape_string($artist_disambiguation) . "','0000-00-00')") == false) {
					
					//trigger_error('Wrong SQL: ' . $sql_query . ' Error: ' . $mysqli->error, E_USER_ERROR);
					
					return '0';
					
				} else { 
					// Artist added to DB
					echo "Added $artist_name to database. <br/>";
					return $mysqli->insert_id;
				}
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	} else {
		return 0;
	}
}