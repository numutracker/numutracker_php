<?php

function scan_for_album_art() {
	$db = Database::getInstance();
	$mysqli = $db->getConnection(); 
	$sql_query = "SELECT v2_release_group.release_mbid, v2_artist.name as artist, v2_release_group.title as title FROM `v2_release_group` LEFT JOIN `v2_artist` ON v2_release_group.artist_id = v2_artist.artist_id WHERE ((v2_release_group.last_art_check < NOW() - INTERVAL 30 DAY) OR (v2_release_group.last_art_check IS NULL)) AND (v2_release_group.art = '0' OR v2_release_group.art = '2') ORDER BY v2_release_group.last_art_check ASC, v2_release_group.date DESC LIMIT 80";
	$result = $mysqli->query($sql_query);
	if($mysqli->query($sql_query) === false) {
		trigger_error('Wrong SQL: ' . $sql_query . ' Error: ' . $mysqli->error, E_USER_ERROR);
	}
	$num_rows = $result->num_rows;
	$result->data_seek(0);
	
	$total_art = 0;
	$total_art_not_found = 0;
	
	while($row = $result->fetch_assoc()){
		sleep(1);
		chdir(dirname(__FILE__));
		$artist = rawurlencode($row['artist']);
		$album = rawurlencode($row['title']);
		$mbid = $row['release_mbid'];
				
		$saved_thumb = 0;
		$saved_full = 0;
		$saved_large = 0;
		
		$xml    = "http://ws.audioscrobbler.com/2.0/?method=album.getinfo&artist={$artist}&album={$album}&api_key=";
		$xml    = file_get_contents($xml);
		
		if(!$xml) {
			goto a;
		}
		
		$xml = new SimpleXMLElement($xml);
		$xml = $xml->album;
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
			$file_name = "covers/thumb/".$mbid . "." . $ext;
			if(file_put_contents($file_name, fopen($url, 'r'))) {
				$saved_thumb = 1;
				//echo "Saved thumb size.<br/>";
			}
		}
		
		if ($image_urls['full'] != '') {
			$url = $image_urls['full'];
			$parts=pathinfo($url);
			$ext = $parts["extension"];
			$file_name = "covers/".$mbid . "." . $ext;
			if(file_put_contents($file_name, fopen($url, 'r'))) {
				$saved_full = 1;
				//echo "Saved full size.<br/>";
			}
			
		}
		
		if ($image_urls['large'] != '') {
			$url = $image_urls['large'];
			$parts=pathinfo($url);
			$ext = $parts["extension"];
			$file_name = "covers/large/".$mbid . "." . $ext;
			if(file_put_contents($file_name, fopen($url, 'r'))) {
				$saved_large = 1;
				//echo "Saved large size.<br/>";
			}
		}
		
		a:
		
		if ($saved_thumb && $saved_full && $saved_large) {
			
			$filename = $mbid . "." . $ext;
			// mark album as art was found
			$sql_query = "UPDATE v2_release_group SET
			art = '$filename',last_art_check = '" . date("Y-m-d") . "'
			WHERE release_mbid = '" . $mbid ."'";
			if($mysqli->query($sql_query) === false) {
				trigger_error('Wrong SQL: ' . $sql_query . ' Error: ' . $mysqli->error, E_USER_ERROR);
			} 
			$total_art++;
			
		} else {

			// mark album as art was not found
			$sql_query = "UPDATE v2_release_group SET
			art = '2',last_art_check = '" . date("Y-m-d") . "'
			WHERE release_mbid = '" . $mbid ."'";
			if($mysqli->query($sql_query) === false) {
				trigger_error('Wrong SQL: ' . $sql_query . ' Error: ' . $mysqli->error, E_USER_ERROR);
			}
			$total_art_not_found++;
		}	
	}
	
	if ($total_art > 0 || $total_art_not_found > 0 ) {
		echo "Total Album Art Found: $total_art / Not Found: $total_art_not_found <br/>";
	}
}