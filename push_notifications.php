<?php
	
function push_notifications() {
	
$current_hour = date('G');

//$current_hour = 8;

if ($current_hour == 8) {
	
// Production
$pusher = new Pusher('','','');

$db = Database::getInstance();
$mysqli = $db->getConnection();

// Development
//$pusher = new Pusher('','','');

// Get list of users with pending notifications...



$db = Database::getInstance();
$mysqli = $db->getConnection(); 
$sql_query = "select fr.user_id from v2_fresh as fr LEFT JOIN v2_users as usr ON fr.user_id = usr.user_id WHERE sent_state = 0 AND read_state = 0 GROUP BY user_id";
$result = $mysqli->query($sql_query);
$num_rows = $result->num_rows;
$users_array = array();
if ($num_rows > 0) {
	$result->data_seek(0);
	while($row = $result->fetch_assoc()){
		$users_array[] = $row['user_id'];
	}
}


// Iterate through users array to send notifications

foreach ($users_array as $user) {
	
	// See if we can detect this user's time zone from their most recent authorization request...
	
	
	
	// Process upcoming release notifications ...
	
	// Get username from id...
	$sql_query = "select * from v2_users where user_id = $user";
	$result = $mysqli->query($sql_query);
	$num_rows = $result->num_rows;
	if ($num_rows == 1) {
		$result->data_seek(0);
		while($row = $result->fetch_assoc()){
			$user_name = $row['email'];
			// Generate query string for searches...
			$query = "";
			$bump = 0;
			if ($row['album'] == 1) {
				$query .= "rg.type = 'Album'";
				$bump++;
			}
			
			if ($row['single'] == 1) {
				if ($bump > 0) { $query .= " OR ";}
				$query .= "rg.type = 'Single'";
				$bump++;
			}
			
			if ($row['ep'] == 1) {
				if ($bump > 0) { $query .= " OR ";}
				$query .= "rg.type = 'EP'";
				$bump++;
			}
			
			if ($row['live'] == 1) {
				if ($bump > 0) { $query .= " OR ";}
				$query .= "rg.type = 'Live'";
				$bump++;
			}
			
			if ($row['soundtrack'] == 1) {
				if ($bump > 0) { $query .= " OR ";}
				$query .= "rg.type = 'Soundtrack'";
				$bump++;
			}
			
			if ($row['remix'] == 1) {
				if ($bump > 0) { $query .= " OR ";}
				$query .= "rg.type = 'Remix'";
				$bump++;
			}
			
			if ($row['other'] == 1) {
				if ($bump > 0) { $query .= " OR ";}
				$query .= "(rg.type != 'Album' AND rg.type != 'Single' AND rg.type != 'EP' AND rg.type != 'Live' AND rg.type != 'Soundtrack' AND rg.type != 'Remix')";
				$bump++;
			}		
		}	
	
		$sql_query = "select ar.name as artist_name, rg.title as release_title, rg.date as release_date from v2_fresh as n LEFT JOIN v2_release_group as rg ON n.release_id = rg.release_id LEFT JOIN v2_artist as ar ON rg.artist_id = ar.artist_id WHERE n.sent_state = 0 AND n.read_state = 0 and n.user_id = $user AND rg.date > NOW( ) AND ($query)";
	
	$result = $mysqli->query($sql_query);
	$num_rows = $result->num_rows;
	if ($num_rows > 0) {
		$result->data_seek(0);
		
		if ($num_rows == 1) {
			// Single release added...
			while($row = $result->fetch_assoc()){
				$notification_title = "Upcoming Release";
				$notification_message = "An upcoming release by ".$row['artist_name']." has been added, release date ".$row['release_date'].".";
			}
		}
		
		if ($num_rows > 1) {
			// Multiples
			
			// Get a couple artist names
			$artist_names = array();
			while($row = $result->fetch_assoc()){
				$artist_names[] = $row['artist_name'];
			}
			$artist_names = array_unique($artist_names);
			$array_rows = count($artist_names);
			$short_array = array_slice($artist_names, 0, 3);
			//print_r($short_array);
			$artist_string = "";
			$i = 1;
			foreach ($short_array as $name) {
				if ($i == $array_rows && count($artist_names) > 1) {
					$artist_string .= "and ";
				}
				$artist_string .= "$name";
				if ($i >= 1 && $i < 3 && $array_rows > $i) {
					$artist_string .= ", ";
				}
				$i++;
			}
			$notification_title = "$num_rows Upcoming Releases Added";
			if ($array_rows > 3) {
				$notification_message = "Upcoming releases by $artist_string and more just added.";	
				} else {
				$notification_message = "Upcoming releases by $artist_string just added.";		
			}
		}
		
		echo $notification_title . " - " . $notification_message . " <br/>";
		
		// Send notifications...
		
		$pusher->notify(array("newAnnouncements_$user_name"),array('apns' => array('aps' => array('alert' => array('title' => "$notification_title",'body' => "$notification_message"))))); // end send
		
		// Mark notifications as sent...
		
		$sql_query = "UPDATE v2_fresh as n LEFT JOIN v2_release_group as rg ON n.release_id = rg.release_id LEFT JOIN v2_artist as ar ON rg.artist_id = ar.artist_id SET sent_state = 1 WHERE n.user_id = $user AND rg.date > NOW( )";
		if($mysqli->query($sql_query) === false) {
			//trigger_error('Wrong SQL: ' . $sql_query . ' Error: ' . $mysqli->error, E_USER_ERROR);
			$returned = array("result"=>"0");
		} else {
			$returned = array("result"=>"1");
		}
	
			
		} else {
			// Something's wrong...
		}
		
		
		
	$sql_query = "select ar.name as artist_name, rg.title as release_title, rg.date as release_date from v2_fresh as n LEFT JOIN v2_release_group as rg ON n.release_id = rg.release_id LEFT JOIN v2_artist as ar ON rg.artist_id = ar.artist_id WHERE n.sent_state = 0 AND n.read_state = 0 and n.user_id = $user AND rg.date < NOW( ) AND ($query)";
	
	$result = $mysqli->query($sql_query);
	$num_rows = $result->num_rows;
	if ($num_rows > 0) {
		$result->data_seek(0);
		
		if ($num_rows == 1) {
			// Single release added...
			while($row = $result->fetch_assoc()){
				$notification_title = "A Past Release Added";
				$notification_message = "A past release by ".$row['artist_name']." has been added, titled ".$row['release_title'].".";
			}
		}
		
		if ($num_rows > 1) {
			// Multiples
			
			// Get a couple artist names
			$artist_names = array();
			while($row = $result->fetch_assoc()){
				$artist_names[] = $row['artist_name'];
			}
			$artist_names = array_unique($artist_names);
			$array_rows = count($artist_names);
			$short_array = array_slice($artist_names, 0, 3);
			//print_r($short_array);
			$artist_string = "";
			$i = 1;
			foreach ($short_array as $name) {
				if ($i == $array_rows && count($artist_names) > 1) {
					$artist_string .= "and ";
				}
				$artist_string .= "$name";
				if ($i >= 1 && $i < 3 && $array_rows > $i) {
					$artist_string .= ", ";
				}
				$i++;
			}
			$notification_title = "$num_rows Past Releases Added";
			if ($array_rows > 3) {
				$notification_message = "Past releases by $artist_string and more just added.";	
				} else {
				$notification_message = "Past releases by $artist_string just added.";		
			}
		}
		
		echo $notification_title . " - " . $notification_message . " <br/>";
		
		// Send notifications...
		
		$pusher->notify(array("moreReleases_$user_name"),array('apns' => array('aps' => array('alert' => array('title' => "$notification_title",'body' => "$notification_message"))))); // end send
		
		// Mark notifications as sent...
		
		$sql_query = "UPDATE v2_fresh as n LEFT JOIN v2_release_group as rg ON n.release_id = rg.release_id LEFT JOIN v2_artist as ar ON rg.artist_id = ar.artist_id SET sent_state = 1 WHERE n.user_id = $user AND rg.date < NOW( )";
		if($mysqli->query($sql_query) === false) {
			//trigger_error('Wrong SQL: ' . $sql_query . ' Error: ' . $mysqli->error, E_USER_ERROR);
			$returned = array("result"=>"0");
		} else {
			$returned = array("result"=>"1");
		}
	
			
		} else {
			// Something's wrong...
		}
			
		
	}

	
}

// Get list of users who have releases for today

$db = Database::getInstance();
$mysqli = $db->getConnection(); 
$sql_query = "SELECT v2_user_artist.user_id FROM `v2_release_group`
RIGHT JOIN `v2_user_artist` ON v2_release_group.artist_id = v2_user_artist.artist_id
WHERE v2_release_group.date = CURDATE()
GROUP BY user_id";
$result = $mysqli->query($sql_query);
$num_rows = $result->num_rows;
$users_array = array();
if ($num_rows > 0) {
	$result->data_seek(0);
	while($row = $result->fetch_assoc()){
		$users_array[] = $row['user_id'];
	}
}
foreach ($users_array as $user) {
	
	// Ensure that current date is not in v2_notifications_release_days
	
	$sql_query = "select * from v2_notifications_release_days where user_id = $user AND `date` = CURDATE()";
	$result = $mysqli->query($sql_query);
	$num_rows = $result->num_rows;
	if ($num_rows == 0) {
	

	// Process upcoming release notifications ...
	
	// Get username from id...
	$sql_query = "select * from v2_users where user_id = $user";
	$result = $mysqli->query($sql_query);
	$num_rows = $result->num_rows;
	if ($num_rows == 1) {
		$result->data_seek(0);
		while($row = $result->fetch_assoc()){
			$user_name = $row['email'];
			// Generate query string for searches...
			$query = "";
			$bump = 0;
			if ($row['album'] == 1) {
				$query .= "rg.type = 'Album'";
				$bump++;
			}
			
			if ($row['single'] == 1) {
				if ($bump > 0) { $query .= " OR ";}
				$query .= "rg.type = 'Single'";
				$bump++;
			}
			
			if ($row['ep'] == 1) {
				if ($bump > 0) { $query .= " OR ";}
				$query .= "rg.type = 'EP'";
				$bump++;
			}
			
			if ($row['live'] == 1) {
				if ($bump > 0) { $query .= " OR ";}
				$query .= "rg.type = 'Live'";
				$bump++;
			}
			
			if ($row['soundtrack'] == 1) {
				if ($bump > 0) { $query .= " OR ";}
				$query .= "rg.type = 'Soundtrack'";
				$bump++;
			}
			
			if ($row['remix'] == 1) {
				if ($bump > 0) { $query .= " OR ";}
				$query .= "rg.type = 'Remix'";
				$bump++;
			}
			
			if ($row['other'] == 1) {
				if ($bump > 0) { $query .= " OR ";}
				$query .= "(rg.type != 'Album' AND rg.type != 'Single' AND rg.type != 'EP' AND rg.type != 'Live' AND rg.type != 'Soundtrack' AND rg.type != 'Remix')";
				$bump++;
			}		
		}	


	$sql_query = "SELECT rg.title as release_title, ar.name artist_name FROM `v2_release_group` as rg LEFT JOIN `v2_artist` as ar ON rg.artist_id = ar.artist_id WHERE date = CURDATE() AND ar.artist_id IN (SELECT artist_id FROM `v2_user_artist` where user_id = $user) AND ($query)";
	
	$result = $mysqli->query($sql_query);
	$num_rows = $result->num_rows;
	if ($num_rows > 0) {
		$result->data_seek(0);
		
		if ($num_rows == 1) {
			// Single release added...
			while($row = $result->fetch_assoc()){
				$notification_title = "New Music Today";
				$notification_message = "A new release by ".$row['artist_name']." came out today, titled ".$row['release_title'].".";
			}
		}
		
		if ($num_rows > 1) {
			// Multiples
			
			// Get a couple artist names
			$artist_names = array();
			while($row = $result->fetch_assoc()){
				$artist_names[] = $row['artist_name'];
			}
			$artist_names = array_unique($artist_names);
			$array_rows = count($artist_names);
			$short_array = array_slice($artist_names, 0, 3);
			//print_r($short_array);
			$artist_string = "";
			$i = 1;
			foreach ($short_array as $name) {
				if ($i == $array_rows && count($artist_names) > 1) {
					$artist_string .= "and ";
				}
				$artist_string .= "$name";
				if ($i >= 1 && $i < 3 && $array_rows > $i) {
					$artist_string .= ", ";
				}
				$i++;
			}
			$notification_title = "$num_rows New Releases Today";
			if ($array_rows > 3) {
				$notification_message = "New releases by $artist_string and others came out today.";	
				} else {
				$notification_message = "New releases by $artist_string came out today.";		
			}
		}
		
		echo $notification_title . " - " . $notification_message . " <br/>";
		
		// Send notifications...
		
		$pusher->notify(array("newReleased_$user_name"),array('apns' => array('aps' => array('alert' => array('title' => "$notification_title",'body' => "$notification_message"))))); // end send
		
		// Mark notifications as sent...
		
		$sql_query = "INSERT INTO v2_notifications_release_days (user_id,date) VALUES ($user,CURDATE())";
		if($mysqli->query($sql_query) === false) {
			trigger_error('Wrong SQL: ' . $sql_query . ' Error: ' . $mysqli->error, E_USER_ERROR);
			$returned = array("result"=>"0");
		} else {
			$returned = array("result"=>"1");
		}
		
	
			
		} else {
			// Something's wrong...
		}

}
}
}
	
}

}