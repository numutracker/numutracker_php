<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set( 'date.timezone', 'America/Los_Angeles' );
error_reporting(E_ALL);

require_once 'database.php';
require_once 'functions.php';

require_once 'auth_needed.php';
require_once 'check_imported_artists_short.php';

require_once 'json_user.php';
require_once 'json_releases.php';
require_once 'json_artists.php';

require_once '../web/classes/data.php';



if (isset($_GET['filters'])) {

	$current_user_id = auth_needed();
	
	$db = Database::getInstance();
	$mysqli = $db->getConnection(); 
	$sql_query = "SELECT * FROM v2_users WHERE user_id = '" . $mysqli->real_escape_string($current_user_id) . "'";
	$result = $mysqli->query($sql_query);
	$num_rows = $result->num_rows;
	
	if ($num_rows == 1) { 
		$row = mysqli_fetch_array($result);
		$album = $row['album'];
		$single = $row['single'];
		$ep = $row['ep'];
		$live = $row['live'];
		$soundtrack = $row['soundtrack'];
		$remix = $row['remix'];
		$other = $row['other'];
		
	}

	$returned = array("album"=>$album,"single"=>$single,"ep"=>$ep,"live"=>$live,"soundtrack"=>$soundtrack,"remix"=>$remix,"other"=>$other);
}

if (isset($_GET['stats'])) {

	$current_user_id = auth_needed();
	
	$db = Database::getInstance();
	$mysqli = $db->getConnection(); 
	
	$data = new Data();
	
	$query_string = $data->returnFilterString($current_user_id);
	
	$sql_query = "SELECT count(release_id) as total_listens FROM v2_release_group WHERE release_id IN (SELECT release_id FROM v2_user_listen WHERE user_id = $current_user_id AND read_status = 1) AND artist_id IN (SELECT artist_id FROM v2_user_artist WHERE user_id = $current_user_id) $query_string";
	$result = $mysqli->query($sql_query);
	$num_rows = $result->num_rows;
	
	if ($num_rows == 1) { 
		$row = mysqli_fetch_array($result);
		$total_listens = $row['total_listens'];	
	}
	
	$sql_query = "SELECT count(release_id) as total_listens,count(distinct artist_id) as total_artists FROM v2_release_group WHERE release_id IN (SELECT release_id FROM v2_user_listen WHERE user_id = $current_user_id AND read_status = 1)";
	$result = $mysqli->query($sql_query);
	$num_rows = $result->num_rows;
	
	if ($num_rows == 1) { 
		$row = mysqli_fetch_array($result);
		$total_listens_unfiltered = $row['total_listens'];
		$total_artists_unfiltered = $row['total_artists'];
	}
	
	$sql_query = "SELECT count(artist_id) as total_follows FROM v2_user_artist WHERE user_id = $current_user_id";
	$result = $mysqli->query($sql_query);
	$num_rows = $result->num_rows;
	
	if ($num_rows == 1) { 
		$row = mysqli_fetch_array($result);
		$total_follows = $row['total_follows'];
		
	}
	
	$sql_query = "SELECT count(release_id) as total_unlistened, count(distinct artist_id) as total_artists FROM v2_release_group WHERE artist_id IN (SELECT artist_id FROM v2_user_artist WHERE user_id = $current_user_id) $query_string";
	$result = $mysqli->query($sql_query);
	$num_rows = $result->num_rows;
	
	if ($num_rows == 1) { 
		$row = mysqli_fetch_array($result);
		$total_releases = $row['total_unlistened'];
		
	}
	
	if ($total_releases > 0) {
		$percentage = round((($total_listens/$total_releases)*100),2);
	} else {
		$percentage = 0;
	}
	
	$returned = array("total_listens_unfilt"=>(int)$total_listens_unfiltered,"total_list_artists_unfilt"=>(int)$total_artists_unfiltered,"total_follows"=>(int)$total_follows,"total_rel_fol"=>(int)$total_releases,"percentage"=>$percentage);
}



if (isset($_GET['unfollow'])) {
	
	$current_user_id = auth_needed();
	
	$db = Database::getInstance();
	$mysqli = $db->getConnection(); 
	$sql_query = "DELETE FROM v2_user_artist WHERE artist_id = '" . $mysqli->real_escape_string($_GET['unfollow']) . "' and user_id = '" . $mysqli->real_escape_string($current_user_id) . "'";
	$result = $mysqli->query($sql_query);
	$affected_rows = $mysqli->affected_rows;
	
	if ($affected_rows == 1) {
		$returned = array("result"=>"1");
	} else {
		// Create ...
		$sql_query = "INSERT INTO v2_user_artist (artist_id,user_id) VALUES ('" . $mysqli->real_escape_string($_GET['unfollow']) . "','" . $mysqli->real_escape_string($current_user_id) . "')";
		$result = $mysqli->query($sql_query);
		$affected_rows = $mysqli->affected_rows;
		if ($affected_rows == 1) {
			$returned = array("result"=>"2");
		} else {
			$returned = array("result"=>"0");
		}
	}
	
}

if (isset($_GET['follow'])) {
	
	$current_user_id = auth_needed();
	
	$db = Database::getInstance();
	$mysqli = $db->getConnection(); 
	$sql_query = "INSERT INTO v2_user_artist (artist_id,user_id) VALUES ('" . $mysqli->real_escape_string($_GET['follow']) . "','" . $mysqli->real_escape_string($current_user_id) . "')";
	$result = $mysqli->query($sql_query);
	$affected_rows = $mysqli->affected_rows;
	
	if ($affected_rows == 1) {
		$returned = array("result"=>"1");
	} else {
		$returned = array("result"=>"0");
	}
	
}

if (isset($_GET['sub_status'])) {
	
	$current_user_id = auth_needed();
	
	$db = Database::getInstance();
	$mysqli = $db->getConnection();
	
	$sql_query = "SELECT subscription_end FROM v2_users WHERE user_id = $current_user_id";
	$result = $mysqli->query($sql_query);
	$num_rows = $result->num_rows;
	
	if ($num_rows == 1) { 
		$row = mysqli_fetch_array($result);
		$subscription_end = $row['subscription_end'];	
	}
	
	if ($subscription_end == '0000-00-00 00:00:00' || strtotime($subscription_end) < (strtotime(time()))) {
		$sub_status = 0;
	} else {
		$sub_status = 1;
	}
	$date_now = date("Y-m-d H:i:s",time());
	$returned = array("result"=>"$sub_status","subscription_end"=>"$subscription_end","date_now"=>"$date_now");
	
}

if (isset($_GET['purchased'])) {
	
	$current_user_id = auth_needed();
	
	$db = Database::getInstance();
	$mysqli = $db->getConnection();
	
	// Get current subscription information for user...
	
	$sql_query = "SELECT subscription_end FROM v2_users WHERE user_id = $current_user_id";
	$result = $mysqli->query($sql_query);
	$num_rows = $result->num_rows;
	
	if ($num_rows == 1) { 
		$row = mysqli_fetch_array($result);
		$subscription_end = $row['subscription_end'];	
	}
	
	// Check subscription end time...
	// If it is 0000-00-00 00:00:00 or less than current time, we need to set the subscription to increment from date().
	// Otherwise, add time from current date.
	
	if ($subscription_end == '0000-00-00 00:00:00' || strtotime($subscription_end) < (strtotime(time()))) {
		$new_sub_start_date = date("Y-m-d H:i:s",time());
	} else {
		$new_sub_start_date = $subscription_end;
	}
	
	switch ($_GET['purchased']) {
		case "com.numutracker.oneMonthNotifications":
			// Add 30 days...
			$new_sub_end_date = date("Y-m-d H:i:s",strtotime($new_sub_start_date.' +30 days'));
			break;
		case "com.numutracker.oneYearNotifications":
			// Add 365 days...
			$new_sub_end_date = date("Y-m-d H:i:s",strtotime($new_sub_start_date.' +365 days'));
			break;
		default:
			$new_sub_end_date = '0000-00-00 00:00:00';
			break;
	}
	
	// Record purchase in purchase database...
	$sql_query = "INSERT INTO v2_users_purchase_history (user_id,product_id,old_expiration_date,new_expiration_date) VALUES ('" . $mysqli->real_escape_string($current_user_id) . "','" . $mysqli->real_escape_string($_GET['purchased']) . "','" . $mysqli->real_escape_string($subscription_end) . "','" . $mysqli->real_escape_string($new_sub_end_date) . "')";
	$result = $mysqli->query($sql_query);
	
	
	// Record new expiration date in database.
	
	$sql_query = "UPDATE v2_users SET subscription_end = '$new_sub_end_date' WHERE user_id = $current_user_id";
	$result = $mysqli->query($sql_query);
	$affected_rows = $mysqli->affected_rows;
	
	if ($affected_rows == 1) {
		$returned = array("result"=>"1");
	} else {
		$returned = array("result"=>"0");
	}
	
}

if (isset($_GET['arts'])) {
	
	$db = Database::getInstance();
	$mysqli = $db->getConnection(); 

	$sql_query = "SELECT art as release_art FROM v2_release_group
	WHERE art != '2' AND art != '0' AND date < NOW() AND type = 'Album'
	GROUP BY title
	ORDER BY date DESC
	LIMIT 32";
	
	if($mysqli->query($sql_query) === false) {
		trigger_error('Wrong SQL: ' . $sql_query . ' Error: ' . $mysqli->error, E_USER_ERROR);
	} else {
		$result = $mysqli->query($sql_query);
	}
	
	
	$num_rows = $result->num_rows;
	$results_array = array();
	while ($row = mysqli_fetch_array($result)) {
		$results_array[] = "https://www.numutracker.com/v2/covers/large/".$row['release_art'];
		//$results_array[] = "https://www.numutracker.com/nonly3-1024.png";
	}

	$returned = $results_array;
	
}





if (!empty($returned)) {
	echo json_encode($returned,JSON_PRETTY_PRINT);
} else {
	echo "Something went wrong or you shouldn't be here.";
}

?>