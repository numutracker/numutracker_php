<?php
function auth_needed() {
	if (!isset($_SERVER['PHP_AUTH_USER'])) {
		header('WWW-Authenticate: Basic realm="Numu Tracker"');
		header('HTTP/1.0 401 Unauthorized');
		echo 'Authorization failure.';
		exit;
    } else {  
	    $email = trim(strtolower($_SERVER['PHP_AUTH_USER']));
		$password = $_SERVER['PHP_AUTH_PW'];
		
		// Check password...
		
		$db = Database::getInstance();
	    $mysqli = $db->getConnection(); 
		$sql_query = "SELECT user_id,password FROM v2_users WHERE email = '" . $mysqli->real_escape_string($email) . "'";
	    $result = $mysqli->query($sql_query);
	    $num_rows = $result->num_rows;
	    
	    if ($num_rows == 1) { 
			$row = mysqli_fetch_array($result);
			$selected_user_id = $row['user_id'];
		    $db_password = $row['password'];
		} else {
			$selected_user_id = 0;
		    $db_password = 0;
		}
		
		if (password_verify($password, $db_password)) {
			$password_check = true;
			$success = 1;
		} else {
			$password_check = false;
			$success = 0;
		}
		
	    if (!$password_check) {
		    header('HTTP/1.0 401 Unauthorized');
		    echo "Your password is wrong or user does not exist.";
		    exit;
	    } else {
		    return $selected_user_id;
	    }
    }
}