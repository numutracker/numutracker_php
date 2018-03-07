<?php

if (isset($_GET['rel_mode'])) {
	
	$db = Database::getInstance();
	$mysqli = $db->getConnection(); 
	
	$rel_mode = $_GET['rel_mode'];
	
	if (isset($_GET['page'])) {
		$page = $_GET['page'];
		$limit = $_GET['limit'];
		$offset = $_GET['offset'];
	} else if ($rel_mode == 'artist') {
		$page = 1;
		$limit = 5000;
		$offset = 0;
	} else {
		$page = 1;
		$limit = 50;
		$offset = 0;
	}
	
	// Get user ID...
	
	if (isset($_GET['user'])) {

		$sql_query = "SELECT * FROM v2_users WHERE email = '" . $mysqli->real_escape_string($_GET['user']) . "'";
	    $result = $mysqli->query($sql_query);
	    $num_rows = $result->num_rows;
	    
	    if ($num_rows == 1) { 
			$row = mysqli_fetch_array($result);
		    $current_user_id = $row['user_id'];
			
			// Generate query string for searches...
			$query = "";
			$bump = 0;
			if ($row['album'] == 1) {
				$query .= "v2_release_group.type = 'Album'";
				$bump++;
			}
			
			if ($row['single'] == 1) {
				if ($bump > 0) { $query .= " OR ";}
				$query .= "v2_release_group.type = 'Single'";
				$bump++;
			}
			
			if ($row['ep'] == 1) {
				if ($bump > 0) { $query .= " OR ";}
				$query .= "v2_release_group.type = 'EP'";
				$bump++;
			}
			
			if ($row['live'] == 1) {
				if ($bump > 0) { $query .= " OR ";}
				$query .= "v2_release_group.type = 'Live'";
				$bump++;
			}
			
			if ($row['soundtrack'] == 1) {
				if ($bump > 0) { $query .= " OR ";}
				$query .= "v2_release_group.type = 'Soundtrack'";
				$bump++;
			}
			
			if ($row['remix'] == 1) {
				if ($bump > 0) { $query .= " OR ";}
				$query .= "v2_release_group.type = 'Remix'";
				$bump++;
			}
			
			if ($row['other'] == 1) {
				if ($bump > 0) { $query .= " OR ";}
				$query .= "(v2_release_group.type != 'Album' AND v2_release_group.type != 'Single' AND v2_release_group.type != 'EP' AND v2_release_group.type != 'Live' AND v2_release_group.type != 'Soundtrack' AND v2_release_group.type != 'Remix')";
				$bump++;
			}
			
			$current_query_string = " AND (".$query.")";
			
			if ($bump == 0) { $current_query_string = ""; }
			
		} else {
			$current_query_string = "";
			$current_user_id = 0;
		}
	} else {
		$current_query_string = "";
		$current_user_id = 0;
	}
	
	if ($current_user_id != 0) {
		
		if ($rel_mode == 'all') {
		
			$sql = "FROM `v2_release_group` LEFT JOIN `v2_artist` as ar ON v2_release_group.artist_id = ar.artist_id WHERE date < NOW()  AND is_deleted = 0";
			$sql .= $current_query_string;
			$sql .= " ORDER BY date DESC,name ASC";
			
		} else if ($rel_mode == 'allunlistened') {
			
			$sql = "FROM `v2_release_group` LEFT JOIN `v2_artist` as ar ON v2_release_group.artist_id = ar.artist_id WHERE date < NOW() AND ((SELECT read_status from v2_user_listen WHERE user_id ='".$current_user_id."' and release_id = v2_release_group.release_id) < 1 || (SELECT read_status from v2_user_listen WHERE user_id ='".$current_user_id."' and release_id = v2_release_group.release_id) IS NULL) AND is_deleted = 0 ";
			$sql .= $current_query_string;
			$sql .= " ORDER BY date DESC,name ASC";
			
		} else if ($rel_mode == 'allupcoming') {
			
			$sql = "FROM `v2_release_group` LEFT JOIN `v2_artist` as ar ON v2_release_group.artist_id = ar.artist_id WHERE date > NOW() AND is_deleted = 0 ";
			$sql .= $current_query_string;
			$sql .= " ORDER BY date asc,name ASC";
			
		} else if ($rel_mode == 'unlistened') {
			
			$sql = "FROM `v2_release_group` LEFT JOIN `v2_artist` as ar ON v2_release_group.artist_id = ar.artist_id WHERE date <= NOW() AND v2_release_group.artist_id IN (SELECT artist_id FROM `v2_user_artist` where user_id = '".$current_user_id."') AND ((SELECT read_status from v2_user_listen WHERE user_id ='".$current_user_id."' and release_id = v2_release_group.release_id) < 1 || (SELECT read_status from v2_user_listen WHERE user_id ='".$current_user_id."' and release_id = v2_release_group.release_id) IS NULL) AND is_deleted = 0 ";
			$sql .= $current_query_string;
			$sql .= " ORDER BY date DESC,name ASC";
			
		} else if ($rel_mode == 'user') {
			
			$sql = "FROM `v2_release_group` LEFT JOIN `v2_artist` as ar ON v2_release_group.artist_id = ar.artist_id WHERE date <= NOW() AND v2_release_group.artist_id IN (SELECT artist_id FROM `v2_user_artist` where user_id = '".$current_user_id."') AND is_deleted = 0 ";
			$sql .= $current_query_string;
			$sql .= " ORDER BY date DESC,name ASC";
			
		} else if ($rel_mode == 'upcoming') {
			
			$sql = "FROM `v2_release_group` LEFT JOIN `v2_artist` as ar ON v2_release_group.artist_id = ar.artist_id WHERE date > NOW() AND v2_release_group.artist_id IN (SELECT artist_id FROM `v2_user_artist` where user_id = '".$current_user_id."') AND is_deleted = 0 ";
			$sql .= $current_query_string;
			$sql .= " ORDER BY date asc,name ASC";
			
		} else if ($rel_mode == 'fresh') {
			
			$sql = "FROM `v2_fresh` LEFT JOIN `v2_release_group` ON v2_release_group.release_id = v2_fresh.release_id LEFT JOIN `v2_artist` as ar ON v2_release_group.artist_id = ar.artist_id WHERE is_deleted = 0 AND v2_fresh.user_id = '".$current_user_id."'";
			$sql .= $current_query_string;
			$sql .= " ORDER BY timestamp DESC,name ASC";
			
		} else if ($rel_mode == 'artist') {
			
			$artist_id = $_GET['artist'];
			$sql = "FROM `v2_release_group` LEFT JOIN `v2_artist` as ar ON v2_release_group.artist_id = ar.artist_id WHERE v2_release_group.artist_id = '".$mysqli->real_escape_string($artist_id)."'  AND is_deleted = 0";
			$sql .= $current_query_string;
			$sql .= " ORDER BY date DESC,title ASC";
			
		}
		
		$sql_query = "SELECT v2_release_group.artist_id,v2_release_group.release_id, (SELECT read_status from v2_user_listen WHERE user_id ='".$current_user_id."' and release_id = v2_release_group.release_id) as read_status, ar.name AS artist_name, title, v2_release_group.type, ar.art AS artist_art, v2_release_group.art AS album_art, date ".$sql." LIMIT $limit OFFSET $offset";
		
	} else {
		
		if ($rel_mode == 'allupcoming') {
			$sql = "FROM `v2_release_group` LEFT JOIN `v2_artist` as ar ON v2_release_group.artist_id = ar.artist_id WHERE date > NOW() AND is_deleted = 0 AND (type = 'Album' OR type = 'EP')";
			$sql .= " ORDER BY date ASC,name ASC";
		} else if ($rel_mode == 'artist') {
			
			$artist_id = $_GET['artist'];
			$sql = "FROM `v2_release_group` LEFT JOIN `v2_artist` as ar ON v2_release_group.artist_id = ar.artist_id WHERE v2_release_group.artist_id = '".$mysqli->real_escape_string($artist_id)."'  AND is_deleted = 0 AND (type = 'Album' OR type = 'EP')";
			$sql .= $current_query_string;
			$sql .= " ORDER BY date DESC,title ASC";
			
		} else {
			$sql = "FROM `v2_release_group` LEFT JOIN `v2_artist` as ar ON v2_release_group.artist_id = ar.artist_id WHERE date <= NOW() AND is_deleted = 0 AND (type = 'Album' OR type = 'EP')";
			$sql .= " ORDER BY date DESC,name ASC";
		}		
		$sql_query = "SELECT v2_release_group.release_id,v2_release_group.release_id, 0 as read_status, ar.name AS artist_name, ar.artist_id AS artist_id, title, v2_release_group.type, ar.art AS artist_art, v2_release_group.art AS album_art, date ".$sql." LIMIT $limit OFFSET $offset";
		
	}
		
			//echo $sql_query; 
		if($mysqli->query($sql_query) === false) {
			trigger_error('Wrong SQL: ' . $sql_query . ' Error: ' . $mysqli->error, E_USER_ERROR);
		} else {
			$result = $mysqli->query($sql_query);
		}
		
		
		$num_rows = $result->num_rows;
		$results_array = array();
		while ($row = mysqli_fetch_array($result)) {
			
			// Pick art...
			//$row['album_art'] = '0';
			if ($row['album_art'] == '0' || $row['album_art'] == '2') {
				$album_art = array(
					"thumb"=>"https://www.numutracker.com/nonly3-1024.png",
					"full"=>"https://www.numutracker.com/nonly3-1024.png",
					"large"=>"https://www.numutracker.com/nonly3-1024.png",
					"xlarge"=>"https://www.numutracker.com/nonly3-1024.png"
				);
			} else {
				$album_art = array(
					"thumb"=>"https://www.numutracker.com/v2/covers/thumb/".$row['album_art'],
					"full"=>"https://www.numutracker.com/v2/covers/".$row['album_art'],
					"large"=>"https://www.numutracker.com/v2/covers/large/".$row['album_art'],
					"xlarge"=>"https://www.numutracker.com/v2/covers/xlarge/".$row['album_art']
				);
			}
			
			// Artist art
			//$row['artist_art'] = '0';
			if ($row['artist_art'] == '0' || $row['artist_art'] == '2') {
				$artist_art = array(
					"thumb"=>"https://www.numutracker.com/nonly3-1024.png",
					"full"=>"https://www.numutracker.com/nonly3-1024.png",
					"large"=>"https://www.numutracker.com/nonly3-1024.png",
					"xlarge"=>"https://www.numutracker.com/nonly3-1024.png"
				);
			} else {
				$artist_art = array(
					"thumb"=>"https://www.numutracker.com/v2/artist/thumb/".$row['artist_art'],
					"full"=>"https://www.numutracker.com/v2/artist/".$row['artist_art'],
					"large"=>"https://www.numutracker.com/v2/artist/large/".$row['artist_art'],
					"xlarge"=>"https://www.numutracker.com/v2/artist/xlarge/".$row['artist_art']
				);
			}
			
			if ($row['read_status'] == null) { $row['read_status'] = 0; }		
			
			array_push(
			$results_array,
			array("artist" => $row['artist_name'],
			"artist_id" =>$row['artist_id'],
			"title" => $row['title'],
			"release_id" => $row['release_id'],
			"type" => $row['type'],
			"date" => date("F j, Y", strtotime($row['date'])),
			"art" =>  $album_art,
			"artist_art" =>  $artist_art,
			"status" => $row['read_status']
			));
			
		}
		
		if ($rel_mode != 'artist') {
			// Get total number of results and total pages based on limit...
			$sql_query = "SELECT count(v2_release_group.release_id) as count ".$sql;
			$result = $mysqli->query($sql_query);
			$num_rows = $result->num_rows;
			
			while ($row = mysqli_fetch_array($result)) {
				$total_rows = $row['count'];
			}
			$total_pages = ceil($total_rows/$limit);
			
			$data_array = array("page"=>"$page","total_pages"=>"$total_pages","total_results"=>"$total_rows","results"=>$results_array);
		} else {
			$data_array = $results_array;
		}
		
		$returned = $data_array;
	
}