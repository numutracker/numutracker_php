<?php

// auth_needed()

// authorization
if (isset($_GET['auth'])) {
	$current_user_id = auth_needed();
	$returned = array("result"=>"1");
}

// registration
if (isset($_GET['register']) && isset($_GET['password'])) {
	$hashed_password = password_hash($_GET['password'], PASSWORD_DEFAULT);
	$db = Database::getInstance();
	$mysqli = $db->getConnection(); 
	if ($mysqli->query("INSERT INTO v2_users (password,email) VALUES ('" . $mysqli->real_escape_string($hashed_password) . "','" . $mysqli->real_escape_string(strtolower ($_GET['register'])) . "')") == false) {
		// Should probably put more error handling in here ..
		$return_array = array("result"=>$mysqli->error);
	} else { 
		$new_user_id = $mysqli->insert_id;
		$returned = array("result"=>"1");
	}
}


// toggle filters...
if (isset($_GET['filter'])) {
	$current_user_id = auth_needed();
	// Toggle filter in user account...
	$db = Database::getInstance();
	$mysqli = $db->getConnection(); 
	$selected_filter = $mysqli->real_escape_string($_GET['filter']);
	$sql_query = "UPDATE v2_users SET $selected_filter = !$selected_filter WHERE user_id = '" . $current_user_id ."'";
	if($mysqli->query($sql_query) === false) {
		//trigger_error('Wrong SQL: ' . $sql_query . ' Error: ' . $mysqli->error, E_USER_ERROR);
		$returned = array("result"=>"0");
	} else {
		$returned = array("result"=>"1");
	}
	$returned = array("result"=>"1");
}


// toggle listen state
if (isset($_GET['listen'])) {
	$current_user_id = auth_needed();
	// Toggle filter in user account...
	$db = Database::getInstance();
	$mysqli = $db->getConnection(); 
	$selected_album_id = $mysqli->real_escape_string($_GET['listen']);
	
	$sql_query = "INSERT INTO v2_user_listen (user_id,release_id,read_status) VALUES ('" . $mysqli->real_escape_string($current_user_id ) . "','" . $selected_album_id . "',1) ON DUPLICATE KEY UPDATE read_status = !read_status";
	if($mysqli->query($sql_query) === false) {
		//trigger_error('Wrong SQL: ' . $sql_query . ' Error: ' . $mysqli->error, E_USER_ERROR);
		$returned = array("result"=>"0");
	} else {
		$returned = array("result"=>"1");
	}
	$returned = array("result"=>"1");
	
}

// Import artists from app...
if (isset($_GET['import'])) {
	$current_user_id = auth_needed();
	$data = json_decode(file_get_contents('php://input'), true);
	$db = Database::getInstance();
    $mysqli = $db->getConnection(); 
    $artists_added = 0;
	foreach ($data['artists'] as $artist) {		
	    if ($mysqli->query("INSERT INTO v2_imported_artists (user_id,artist_name) VALUES ('".$current_user_id."','" . $mysqli->real_escape_string($artist) ."')") == false) {
		 	// do nothing
	    } else { 
	    	$artists_added++;
	    } 
    }
    check_imported_artists_short();
	$returned = array("success"=>$artists_added);
}