<?php
// Artist list

if (isset($_GET['artists'])) {
	
	$db = Database::getInstance();
	$mysqli = $db->getConnection(); 
	$sql_query = "SELECT * FROM v2_users WHERE email = '" . $mysqli->real_escape_string($_GET['artists']) . "'";
	$result = $mysqli->query($sql_query);
	$num_rows = $result->num_rows;
	
	if (isset($_GET['sortby'])) { $sort = $mysqli->real_escape_string($_GET['sortby']); } else { $sort = ''; }
	
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
		
		$current_query_string = $query;
		
		if ($bump == 0) { $current_query_string = "v2_release_group.type LIKE '%%'"; }
		
	} else {
		$current_query_string = "";
		$current_user_id = 0;
	}

	$sql_query = "SELECT v2_artist.name, v2_artist.artist_id, v2_artist.sort_name, v2_artist.art,
(SELECT MAX(v2_release_group.date) FROM v2_release_group WHERE v2_release_group.artist_id = v2_artist.artist_id";
	$sql_query .= " AND (".$current_query_string.")";
	$sql_query .= ") as recent_date, (SELECT count(release_mbid) FROM `v2_release_group`
	LEFT JOIN  (SELECT * from v2_user_listen WHERE user_id ='".$current_user_id."') AS x
	ON v2_release_group.release_id = x.release_id WHERE (read_status =  '0' OR read_status IS NULL) AND v2_release_group.artist_id = v2_artist.artist_id";
	$sql_query .= " AND (".$current_query_string.")";
	$sql_query .= ") as unread, (SELECT count(release_mbid) FROM `v2_release_group`
	LEFT JOIN  (SELECT * from v2_user_listen WHERE user_id ='".$current_user_id."') AS x
	ON v2_release_group.release_id = x.release_id WHERE v2_release_group.artist_id = v2_artist.artist_id";
	$sql_query .= " AND (".$current_query_string.")";
	$sql_query .= ") as total_releases,
	coalesce((SELECT IF(v2_user_artist.user_id IS NULL,'0','1')  FROM `v2_user_artist` WHERE v2_artist.artist_id = v2_user_artist.artist_id AND v2_user_artist.user_id = '".$current_user_id."'),0) as follow_status
	FROM  `v2_user_artist`
	LEFT JOIN  `v2_artist` ON v2_user_artist.artist_id = v2_artist.artist_id
	LEFT JOIN  `v2_users` ON v2_user_artist.user_id = v2_users.user_id WHERE v2_users.email = '". $mysqli->real_escape_string($_GET['artists']) ."' ";
	
	if ($sort == 'date') {
		$sql_query .= "ORDER BY recent_date DESC, sort_name";
	} else {
		$sql_query .= "ORDER BY sort_name";
	}
	
	//echo $sql_query."<br/><br/>";
	
	if($mysqli->query($sql_query) === false) {
		trigger_error('Wrong SQL: ' . $sql_query . ' Error: ' . $mysqli->error, E_USER_ERROR);
	} else {
		$result = $mysqli->query($sql_query);
	}
	$num_rows = $result->num_rows;
	$return_array = array();
	
	while ($row = mysqli_fetch_array($result)) {
		//$row['art'] = '0';
		if ($row['art'] == '0' || $row['art'] == '2') {
			$art = array(
				"thumb"=>"https://www.numutracker.com/nonly3-1024.png",
				"full"=>"https://www.numutracker.com/nonly3-1024.png",
				"large"=>"https://www.numutracker.com/nonly3-1024.png",
				"xlarge"=>"https://www.numutracker.com/nonly3-1024.png"
			);
		} else {
			$art = array(
				"thumb"=>"https://www.numutracker.com/v2/artist/thumb/".$row['art'],
				"full"=>"https://www.numutracker.com/v2/artist/".$row['art'],
				"large"=>"https://www.numutracker.com/v2/artist/large/".$row['art'],
				"xlarge"=>"https://www.numutracker.com/v2/artist/xlarge/".$row['art']
			);
		}
		$recent_date = date("F j, Y", strtotime($row['recent_date']));
		if ($recent_date == "January 1, 1970") { $recent_date = "No Releases"; }
		array_push($return_array, array("artist_id" => $row['artist_id'],"recent_date" => $recent_date,"artist_art" => $art,"name" => $row['name'], "unread"=>$row['unread'],"total_releases"=>$row['total_releases'],"follow_status"=>$row['follow_status']));
	}
	
	$returned = $return_array;
	
}

if (isset($_GET['artist_search'])) {
	
	$db = Database::getInstance();
	$mysqli = $db->getConnection(); 
	$sql_query = "SELECT * FROM v2_users WHERE email = '" . $mysqli->real_escape_string($_GET['artist_search']) . "'";
	$result = $mysqli->query($sql_query);
	$num_rows = $result->num_rows;
	
	if (isset($_GET['search'])) { $search = $mysqli->real_escape_string($_GET['search']); }
	
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
		
		$current_query_string = $query;
		
		if ($bump == 0) { $current_query_string = "v2_release_group.type LIKE '%%'"; }
		
	} else {
		$current_query_string = "(v2_release_group.type = 'Album' OR v2_release_group.type = 'EP')";
		$current_user_id = 0;
	}

	$sql_query = "SELECT v2_artist.name, v2_artist.artist_id, v2_artist.sort_name, v2_artist.art,
(SELECT MAX(v2_release_group.date) FROM v2_release_group WHERE v2_release_group.artist_id = v2_artist.artist_id";
	$sql_query .= " AND (".$current_query_string.")";
	$sql_query .= ") as recent_date, (SELECT count(release_mbid) FROM `v2_release_group`
	LEFT JOIN  (SELECT * from v2_user_listen WHERE user_id ='".$current_user_id."') AS x
	ON v2_release_group.release_id = x.release_id WHERE (read_status =  '0' OR read_status IS NULL) AND v2_release_group.artist_id = v2_artist.artist_id";
	$sql_query .= " AND (".$current_query_string.")";
	$sql_query .= ") as unread, (SELECT count(release_mbid) FROM `v2_release_group`
	LEFT JOIN  (SELECT * from v2_user_listen WHERE user_id ='".$current_user_id."') AS x
	ON v2_release_group.release_id = x.release_id WHERE v2_release_group.artist_id = v2_artist.artist_id";
	$sql_query .= " AND (".$current_query_string.")";
	$sql_query .= ") as total_releases,
	coalesce((SELECT IF(v2_user_artist.user_id IS NULL,'0','1')  FROM `v2_user_artist` WHERE v2_artist.artist_id = v2_user_artist.artist_id AND v2_user_artist.user_id = '".$current_user_id."'),0) as follow_status
	FROM  `v2_artist`
	WHERE v2_artist.name LIKE '%". $search ."%' ";
	
	$sql_query .= "ORDER BY recent_date DESC, sort_name LIMIT 100";
	
	//echo $sql_query."<br/><br/>";
	
	if($mysqli->query($sql_query) === false) {
		trigger_error('Wrong SQL: ' . $sql_query . ' Error: ' . $mysqli->error, E_USER_ERROR);
	} else {
		$result = $mysqli->query($sql_query);
	}
	$num_rows = $result->num_rows;
	$return_array = array();
	
	while ($row = mysqli_fetch_array($result)) {
		//$row['art'] = '0';
		if ($row['art'] == '0' || $row['art'] == '2') {
			$art = array(
				"thumb"=>"https://www.numutracker.com/nonly3-1024.png",
				"full"=>"https://www.numutracker.com/nonly3-1024.png",
				"large"=>"https://www.numutracker.com/nonly3-1024.png",
				"xlarge"=>"https://www.numutracker.com/nonly3-1024.png"
			);
		} else {
			$art = array(
				"thumb"=>"https://www.numutracker.com/v2/artist/thumb/".$row['art'],
				"full"=>"https://www.numutracker.com/v2/artist/".$row['art'],
				"large"=>"https://www.numutracker.com/v2/artist/large/".$row['art'],
				"xlarge"=>"https://www.numutracker.com/v2/artist/xlarge/".$row['art']
			);
		}
		$recent_date = date("F j, Y", strtotime($row['recent_date']));
		if ($recent_date == "January 1, 1970") { $recent_date = "No Releases"; }
		array_push($return_array, array("artist_id" => $row['artist_id'],"recent_date" => $recent_date,"artist_art" => $art,"name" => $row['name'], "unread"=>$row['unread'],"total_releases"=>$row['total_releases'],"follow_status"=>$row['follow_status']));
	}
	
	$returned = $return_array;
	
}

if (isset($_GET['single_artist'])) {
	
	$db = Database::getInstance();
	$mysqli = $db->getConnection(); 
	$sql_query = "SELECT * FROM v2_users WHERE email = '" . $mysqli->real_escape_string($_GET['single_artist']) . "'";
	$result = $mysqli->query($sql_query);
	$num_rows = $result->num_rows;
	
	if (isset($_GET['search'])) { $search = $mysqli->real_escape_string($_GET['search']); }
	
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
		
		$current_query_string = $query;
		
		if ($bump == 0) { $current_query_string = "v2_release_group.type LIKE '%%'"; }
		
	} else {
		$current_query_string = "";
		$current_user_id = 0;
	}

	$sql_query = "SELECT v2_artist.name, v2_artist.artist_id, v2_artist.sort_name, v2_artist.art,
(SELECT MAX(v2_release_group.date) FROM v2_release_group WHERE v2_release_group.artist_id = v2_artist.artist_id";
	$sql_query .= " AND (".$current_query_string.")";
	$sql_query .= ") as recent_date, (SELECT count(release_mbid) FROM `v2_release_group`
	LEFT JOIN  (SELECT * from v2_user_listen WHERE user_id ='".$current_user_id."') AS x
	ON v2_release_group.release_id = x.release_id WHERE (read_status =  '0' OR read_status IS NULL) AND v2_release_group.artist_id = v2_artist.artist_id";
	$sql_query .= " AND (".$current_query_string.")";
	$sql_query .= ") as unread, (SELECT count(release_mbid) FROM `v2_release_group`
	LEFT JOIN  (SELECT * from v2_user_listen WHERE user_id ='".$current_user_id."') AS x
	ON v2_release_group.release_id = x.release_id WHERE v2_release_group.artist_id = v2_artist.artist_id";
	$sql_query .= " AND (".$current_query_string.")";
	$sql_query .= ") as total_releases,
	coalesce((SELECT IF(v2_user_artist.user_id IS NULL,'0','1')  FROM `v2_user_artist` WHERE v2_artist.artist_id = v2_user_artist.artist_id AND v2_user_artist.user_id = '".$current_user_id."'),0) as follow_status
	FROM  `v2_artist`
	WHERE v2_artist.artist_id = '". $search ."' ";
	
	$sql_query .= "ORDER BY recent_date DESC, sort_name LIMIT 100";
	
	//echo $sql_query."<br/><br/>";
	
	if($mysqli->query($sql_query) === false) {
		trigger_error('Wrong SQL: ' . $sql_query . ' Error: ' . $mysqli->error, E_USER_ERROR);
	} else {
		$result = $mysqli->query($sql_query);
	}
	$num_rows = $result->num_rows;
	$return_array = array();
	
	while ($row = mysqli_fetch_array($result)) {
		if ($row['art'] == '0' || $row['art'] == '2') {
			$art = array(
				"thumb"=>"https://www.numutracker.com/nonly3-1024.png",
				"full"=>"https://www.numutracker.com/nonly3-1024.png",
				"large"=>"https://www.numutracker.com/nonly3-1024.png",
				"xlarge"=>"https://www.numutracker.com/nonly3-1024.png"
			);
		} else {
			$art = array(
				"thumb"=>"https://www.numutracker.com/v2/artist/thumb/".$row['art'],
				"full"=>"https://www.numutracker.com/v2/artist/".$row['art'],
				"large"=>"https://www.numutracker.com/v2/artist/large/".$row['art'],
				"xlarge"=>"https://www.numutracker.com/v2/artist/xlarge/".$row['art']
			);
		}
		$recent_date = date("F j, Y", strtotime($row['recent_date']));
		if ($recent_date == "January 1, 1970") { $recent_date = "No Releases"; }
		array_push($return_array, array("artist_id" => $row['artist_id'],"recent_date" => $recent_date,"artist_art" => $art,"name" => $row['name'], "unread"=>$row['unread'],"total_releases"=>$row['total_releases'],"follow_status"=>$row['follow_status']));
	}
	
	$returned = $return_array;
	
}