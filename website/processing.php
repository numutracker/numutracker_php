<?php
	session_start(); 
	
	if (isset($_SESSION['user_id'])) {
		$current_user_id = $_SESSION['user_id'];
		$current_user_email = $_SESSION['user_email'];

	} else {
		$current_user_id = 0;
		$current_user_email = '';
	}

	require_once 'classes/database.php';
	
	$db = Database::getInstance();
	$mysqli = $db->getConnection(); 
	
	if (isset($_POST['login'])) {
		$success = 0;
		$error = "";
		$submit_email = $mysqli->real_escape_string(strtolower($_POST['login']['email']));
		$submit_password = $mysqli->real_escape_string($_POST['login']['password']);
		$sql_query = "SELECT user_id,password FROM v2_users WHERE email = '$submit_email'";
	    $result = $mysqli->query($sql_query);
	    $num_rows = $result->num_rows;
	    
	    if ($num_rows == 1) { 
			$row = mysqli_fetch_array($result);
			$selected_user_id = $row['user_id'];
		    $db_password = $row['password'];
		    if (password_verify($submit_password, $db_password)) {
				$password_check = true;
				$success = 1;
				} else {
				$password_check = false;
				$success = 0;
				$error = "Password failure.";
			}
		} else {
			$error = "Email not found.";
		}
		
		
		if ($success) {
			// store user_id in session
			$_SESSION['user_id'] = $selected_user_id;
			$_SESSION['user_email'] = $submit_email;
		}
		
		$return_array['success'] = $success;
		$return_array['error'] = $error;

	}
	
	if (isset($_POST['register'])) {
		$success = 0;
		$error = "";
		$submit_email = $mysqli->real_escape_string(strtolower($_POST['register']['email']));
		$submit_password = $mysqli->real_escape_string($_POST['register']['password']);
		$hashed_password = password_hash($submit_password, PASSWORD_DEFAULT);
		 
		if ($mysqli->query("INSERT INTO v2_users (password,email) VALUES ('" . $mysqli->real_escape_string($hashed_password) . "','" . $submit_email . "')") == false) {
			// Should probably put more error handling in here ..
			$success = 0;
			$error = $mysqli->error;
		} else { 
			$success = 1;
			$new_user_id = $mysqli->insert_id;
		}
		
		if ($success) {
			// store user_id in session
			$_SESSION['user_id'] = $new_user_id;
			$_SESSION['user_email'] = $submit_email;
		}
		
		$return_array['success'] = $success;
		$return_array['error'] = $error;

	}
	
	if (isset($_POST['filter_on'])) {
		$success = 0;
		$error = "";
		$filter_type = $mysqli->real_escape_string($_POST['filter_on']);
		
		$sql_query = "UPDATE v2_users SET $filter_type = 1 WHERE user_id = $current_user_id";
		if($mysqli->query($sql_query) === false) {
			//trigger_error('Wrong SQL: ' . $sql_query . ' Error: ' . $mysqli->error, E_USER_ERROR);
			$success = 0;
			$error = $mysqli->error;
		} else {
			$success = 1;
		}

		$return_array['success'] = $success;
		$return_array['error'] = $error;
	}
	
	if (isset($_POST['set_username'])) {
		$success = 0;
		$error = "";
		$new_username = $mysqli->real_escape_string($_POST['set_username']);
		
		if (strlen($new_username) > 3) {
			$sql_query = "UPDATE v2_users SET username = '$new_username' WHERE user_id = $current_user_id";
			if($mysqli->query($sql_query) === false) {
				//trigger_error('Wrong SQL: ' . $sql_query . ' Error: ' . $mysqli->error, E_USER_ERROR);
				$success = 0;
				$error = $mysqli->error;
			} else {
				$success = 1;
			}
		} else {
			$success = 0;
			$error = "Username too short. Usernames must be more than 3 characters long.";
		}

		$return_array['success'] = $success;
		$return_array['error'] = $error;
	}
	
	if (isset($_POST['filter_off'])) {
		$success = 0;
		$error = "";
		$filter_type = $mysqli->real_escape_string($_POST['filter_off']);
		
		$sql_query = "UPDATE v2_users SET $filter_type = 0 WHERE user_id = $current_user_id";
		if($mysqli->query($sql_query) === false) {
			//trigger_error('Wrong SQL: ' . $sql_query . ' Error: ' . $mysqli->error, E_USER_ERROR);
			$success = 0;
			$error = $mysqli->error;
		} else {
			$success = 1;
		}
		
		if ($current_user_id == 0) {
			$error = "Please login or register an account.";
		}

		$return_array['success'] = $success;
		$return_array['error'] = $error;
	}
	
	if (isset($_POST['listened'])) {
		$success = 0;
		$error = "";
		$release_id = $mysqli->real_escape_string($_POST['listened']);
		
		$sql_query = "INSERT INTO v2_user_listen (user_id,release_id,read_status) VALUES ($current_user_id,$release_id,1) ON DUPLICATE KEY UPDATE read_status = 1";
		if($mysqli->query($sql_query) === false) {
			//trigger_error('Wrong SQL: ' . $sql_query . ' Error: ' . $mysqli->error, E_USER_ERROR);
			$success = 0;
			$error = $mysqli->error;
		} else {
			$success = 1;
			$error = $mysqli->error;
		}
		
		if ($current_user_id == 0) {
			$error = "Please login or register an account.";
		}

		$return_array['success'] = $success;
		$return_array['error'] = $error;
	}
	
	if (isset($_POST['unlistened'])) {
		$success = 0;
		$error = "";
		$release_id = $mysqli->real_escape_string($_POST['unlistened']);
		
		$sql_query = "INSERT INTO v2_user_listen (user_id,release_id,read_status) VALUES ($current_user_id,$release_id,0) ON DUPLICATE KEY UPDATE read_status = 0";
		if($mysqli->query($sql_query) === false) {
			//trigger_error('Wrong SQL: ' . $sql_query . ' Error: ' . $mysqli->error, E_USER_ERROR);
			$success = 0;
			$error = $mysqli->error;
		} else {
			$success = 1;
			$error = $mysqli->error;
		}
		
		if ($current_user_id == 0) {
			$error = "Please login or register an account.";
		}

		$return_array['success'] = $success;
		$return_array['error'] = $error;
	}
	
	if (isset($_POST['follow'])) {
		$success = 0;
		$error = "";
		$artist_id = $mysqli->real_escape_string($_POST['follow']);
		
		$sql_query = "INSERT INTO v2_user_artist (artist_id,user_id) VALUES ($artist_id,$current_user_id)";
		$result = $mysqli->query($sql_query);
		$affected_rows = $mysqli->affected_rows;
		
		if ($affected_rows == 1) {
			$success = 1;
		} else {
			$success = 0;
			$error = "You may already be following this artist.";
		}
		
		if ($current_user_id == 0) {
			$error = "Please login or register an account.";
		}

		$return_array['success'] = $success;
		$return_array['error'] = $error;
		
		
	}
	
	if (isset($_POST['unfollow'])) {
		$success = 0;
		$error = "";
		$artist_id = $mysqli->real_escape_string($_POST['unfollow']);
		
		$sql_query = "DELETE FROM v2_user_artist WHERE artist_id = $artist_id and user_id = $current_user_id";
		$result = $mysqli->query($sql_query);
		$affected_rows = $mysqli->affected_rows;
		
		if ($affected_rows == 1) {
			$success = 1;
		} else {
			$success = 0;
			$error = "You probably weren't following this artist.";
		}
		
		if ($current_user_id == 0) {
			$error = "Please login or register an account.";
		}

		$return_array['success'] = $success;
		$return_array['error'] = $error;
	}

	
	echo json_encode($return_array);
	
?>