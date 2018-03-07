<?php
	
	$selected_release_id = $mysqli->real_escape_string($path_parts[1]);
	
	// Query DB for artist info...
	
	if (!empty($selected_release_id)) {
	
	$data = new Data();
	$db = Database::getInstance();
	$mysqli = $db->getConnection(); 
	$sql_query = "SELECT v2_release_group.artist_id,v2_release_group.release_id, (SELECT read_status from v2_user_listen WHERE user_id ='".$current_user_id."' and release_id = v2_release_group.release_id) as read_status, ar.name AS artist_name, title, v2_release_group.type, ar.art AS artist_art, v2_release_group.art AS album_art, date FROM `v2_release_group` LEFT JOIN `v2_artist` as ar ON v2_release_group.artist_id = ar.artist_id WHERE is_deleted = 0 AND v2_release_group.release_id = $selected_release_id";	$result = $mysqli->query($sql_query);
	$num_rows = $result->num_rows;
	
	if ($num_rows == 1) { 
		$row = mysqli_fetch_array($result);
		 // Pick art...
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
		    
		   
	   
	    $release_array = array(
	    "artist" => $row['artist_name'],
	    "artist_id" =>$row['artist_id'],
	    "title" => $row['title'],
	    "release_id" => $row['release_id'],
	    "type" => $row['type'],
	    "date" => date("F j, Y", strtotime($row['date'])),
	    "art" =>  $album_art,
	    "artist_art" =>  $artist_art,
	    "status" => $row['read_status']
	    );
		
	}
	
	$page_name = "releases";
	$sub_page_name = "single_release";
	
	$page_title = "Numu Release - ".$release_array['artist'];
	
	
	// Process login details
	
	require_once 'header.php';
	 //print_r($release_array);
?>	


<div class="single_info">
	<div class="img_cont"><img src="<?php echo $release_array['art']['large']; ?>" class="large_image"/></div>
	<div class="info_cont">
		<div class="single_title"><?php echo $release_array['title']; ?></div>
		<div class="single_meta">by <strong><?php echo $release_array['artist']; ?></strong><br/><?php echo $release_array['type']; ?> &middot; <?php echo $release_array['date']; ?></div>
		<div class="single_button">
			<?php if ($release_array['status'] == 0) { ?>
			<button id="listen_button" class="unlistened" release_id="<?php echo $release_array['release_id']; ?>">Mark Listened</button>
			<?php } else { ?>
			<button id="listen_button" class="listened" release_id="<?php echo $release_array['release_id']; ?>">✓ Listened</button>
			<?php } ?>
		</div>
	</div>
</div>


<?Php
	if ($page == 1) {
$sql='SELECT stat.*,usr.username FROM (
SELECT usr.user_id,usr.artist_id,ar.name as artist_name, "" as release_id, "" as release_name, usr.date,"Followed" as status FROM v2_user_artist as usr
LEFT JOIN v2_artist as ar ON ar.artist_id = usr.artist_id
UNION ALL
SELECT usr.user_id,rls.artist_id,ar.name as artist_name, usr.release_id, rls.title as release_name, usr.timestamp as date, "Listened" as status FROM v2_user_listen as usr
LEFT JOIN v2_release_group as rls ON rls.release_id = usr.release_id
LEFT JOIN v2_artist as ar ON rls.artist_id = ar.artist_id
WHERE read_status = 1
) as stat
LEFT JOIN v2_users as usr ON usr.user_id = stat.user_id
WHERE stat.release_id = '.$release_array['release_id'].'
ORDER BY date DESC LIMIT 10';
 
$rs=$mysqli->query($sql);
 
if($rs === false) {
  trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $mysqli->error, E_USER_ERROR);
} else {
  $rows_returned = $rs->num_rows;
}
if ($rows_returned > 0 ) { ?>

<div class="sub_title">Recent Activity</div>
<div class="recent_activity">
	<?php $rs->data_seek(0);
	while($row = $rs->fetch_assoc()){ 
	
	if ($row['status'] == 'Listened') { ?>
		<div><?php if ($row['username'] == '') { echo "Someone "; } else { echo $row['username']; } ?> listened to <a href="/release/<?php echo $row['release_id']; ?>"><?php echo $row['release_name']; ?></a></div>
	<?php } else { ?>
		<div><?php if ($row['username'] == '') { echo "Someone "; } else { echo $row['username']; } ?> followed <a href="/artist/<?php echo $row['artist_id']; ?>"><?php echo $row['artist_name']; ?></a></div>
	<?php } 
} ?>
</div>
<?php 
}
}
?>

<div class="sub_title">All Releases</div>
<?php
	
// Creates data object to grab releases...
$data = new Data();

$releases = $data->returnReleasesString($current_user_id,"artist",$page,$release_array['artist_id']);

// Shows releases
echo $releases['results'];

	} else {
		echo "404 not found";
	}

?>


<?php require_once 'footer.php'; ?>