<?php

function scan_for_artist_art() {
	$db = Database::getInstance();
	$mysqli = $db->getConnection(); 
	$sql_query = "SELECT artist_mbid,name FROM `v2_artist` WHERE ((last_art_check < NOW() - INTERVAL 3 DAY) OR (last_art_check IS NULL)) AND (art = '0' OR art = '2') LIMIT 50";
	$result = $mysqli->query($sql_query);
	if($mysqli->query($sql_query) === false) {
		trigger_error('Wrong SQL: ' . $sql_query . ' Error: ' . $mysqli->error, E_USER_ERROR);
	}
	$num_rows = $result->num_rows;
	$result->data_seek(0);
	
	$total_artist_art = 0;
	$total_artist_art_not_found = 0;
	
	while($row = $result->fetch_assoc()){
		sleep(1);
		chdir(dirname(__FILE__));
		$artist = rawurlencode($row['name']);
		$mbid = $row['artist_mbid'];
				
		$saved_thumb = 0;
		$saved_full = 0;
		$saved_large = 0;
		
		$xml    = "http://ws.audioscrobbler.com/2.0/?method=artist.getinfo&artist={$artist}&api_key=";
		$xml    = @file_get_contents($xml); 
		
		if(!$xml) {
			goto a;
		}
		
		$xml = new SimpleXMLElement($xml);
		$xml = $xml->artist;
		$thumb = $xml->image[1];
		$normal = $xml->image[2];
		$large = $xml->image[3];
		$xlarge = $xml->image[4];
		
		if ($xml != '') {
			$image_urls = array("thumb"=>$thumb,"full"=>$normal,"large"=>$large,"xlarge"=>$xlarge);		
		} else {
			$image_urls = array();
		}
		
		// Save each image ...
		
		if ($image_urls['thumb'] != '') {
			$url = $image_urls['thumb'];
			$parts=pathinfo($url);
			$ext = $parts["extension"];
			$file_name = "artist/thumb/".$mbid . "." . $ext;
			if(file_put_contents($file_name, fopen($url, 'r'))) {
				$saved_thumb = 1;
				//echo "Saved thumb size.<br/>";
			}
		}
		
		if ($image_urls['full'] != '') {
			$url = $image_urls['full'];
			$parts=pathinfo($url);
			$ext = $parts["extension"];
			$file_name = "artist/".$mbid . "." . $ext;
			if(file_put_contents($file_name, fopen($url, 'r'))) {
				$saved_full = 1;
				//echo "Saved full size.<br/>";
			}
			
		}
		
		if ($image_urls['large'] != '') {
			$url = $image_urls['large'];
			$parts=pathinfo($url);
			$ext = $parts["extension"];
			$file_name = "artist/large/".$mbid . "." . $ext;
			if(file_put_contents($file_name, fopen($url, 'r'))) {
				$saved_large = 1;
				//echo "Saved large size.<br/>";
			}
		}
		
		a:
		
		if ($saved_thumb && $saved_full && $saved_large) {
			
			$filename = $mbid . "." . $ext;
			// mark album as art was found
			$sql_query = "UPDATE v2_artist SET
			art = '$filename',last_art_check = '" . date("Y-m-d") . "'
			WHERE artist_mbid = '" . $mbid ."'";
			if($mysqli->query($sql_query) === false) {
				trigger_error('Wrong SQL: ' . $sql_query . ' Error: ' . $mysqli->error, E_USER_ERROR);
			} 
			$total_artist_art++;
			
		} else {

			// mark album as art was not found
			$sql_query = "UPDATE v2_artist SET
			art = '2',last_art_check = '" . date("Y-m-d") . "'
			WHERE artist_mbid = '" . $mbid ."'";
			if($mysqli->query($sql_query) === false) {
				trigger_error('Wrong SQL: ' . $sql_query . ' Error: ' . $mysqli->error, E_USER_ERROR);
			}
			$total_artist_art_not_found++;
		}	
	}
	
	if ($total_artist_art > 0 || $total_artist_art_not_found > 0) {
		echo "Total Artist Art Found: $total_artist_art / Not Found: $total_artist_art_not_found <br/>";
	}
}
		
	