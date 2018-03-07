<?php
	
	$selected_artist_id = $mysqli->real_escape_string($path_parts[1]);
	
	// Query DB for artist info...
	
	$data = new Data();
	$db = Database::getInstance();
	$mysqli = $db->getConnection(); 
	$sql_query = "SELECT v2_artist.name, v2_artist.artist_id, v2_artist.sort_name, v2_artist.art,
					(SELECT MAX(v2_release_group.date) FROM v2_release_group WHERE v2_release_group.artist_id = v2_artist.artist_id";
					$sql_query .= $data->returnFilterStringArtists($current_user_id);
					$sql_query .= ") as recent_date, (SELECT count(v2_release_group.release_id) FROM `v2_release_group`
					LEFT JOIN  (SELECT * from v2_user_listen WHERE user_id ='".$current_user_id."') AS x
					ON v2_release_group.release_id = x.release_id WHERE (read_status =  '0' OR read_status IS NULL) AND v2_release_group.artist_id = v2_artist.artist_id";
					$sql_query .= $data->returnFilterStringArtists($current_user_id);
					$sql_query .= ") as unread, (SELECT count(v2_release_group.release_id) FROM `v2_release_group`
					LEFT JOIN  (SELECT * from v2_user_listen WHERE user_id ='".$current_user_id."') AS x
					ON v2_release_group.release_id = x.release_id WHERE v2_release_group.artist_id = v2_artist.artist_id";
					$sql_query .= $data->returnFilterStringArtists($current_user_id);
					$sql_query .= ") as total_releases,
					coalesce((SELECT IF(v2_user_artist.user_id IS NULL,'0','1')  FROM `v2_user_artist` WHERE v2_artist.artist_id = v2_user_artist.artist_id AND v2_user_artist.user_id = '".$current_user_id."'),0) as follow_status
					FROM  `v2_artist` WHERE artist_id = $selected_artist_id";
	$result = $mysqli->query($sql_query);
	$num_rows = $result->num_rows;
	
	if ($num_rows == 1) { 
		$row = mysqli_fetch_array($result);
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
	   
	    $artist_array = array(
	    "artist_id" => $row['artist_id'],
	    "recent_date" => $recent_date,
	    "artist_art" => $art,
	    "status" => $row['follow_status'],
	    "artist" => $row['name'],
		"unread"=>$row['unread'],
		"total_releases"=>$row['total_releases'],
		"follow_status"=>$row['follow_status']);
		
	}
	
	$page_name = "artists";
	$sub_page_name = "single_artist";
	
	$page_title = "Numu Artist - ".$artist_array['artist'];
	
	
	// Process login details
	
	require_once 'header.php';
	
?>	

<div class="single_info">
	<div class="img_cont"><img src="<?php echo $artist_array['artist_art']['large']; ?>" class="large_image"/></div>
	<div class="info_cont">
		<div class="single_title"><?php echo $artist_array['artist']; ?></div>
		<div class="single_meta"><?php echo $artist_array['total_releases']; ?> Releases<br/>
		<?php 
		if ($artist_array['total_releases'] > 0) {
			echo round(100-(($artist_array['unread']/$artist_array['total_releases'])*100),0)."%"; ?> Completion</div>
		<?php } ?>
		<div class="single_button">
			<?php if ($artist_array['status'] == 1) { ?>
			<button id="follow_button" class="following" artist_id="<?php echo $artist_array['artist_id']; ?>">✓ Following</button>
			<?php } else { ?>
			<button id="follow_button" class="follow" artist_id="<?php echo $artist_array['artist_id']; ?>">Follow Artist</button>
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
WHERE stat.artist_id = '.$artist_array['artist_id'].'
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

<div class="sub_title">Releases</div>

<?php
	
// Creates data object to grab releases...
$data = new Data();

$releases = $data->returnReleasesString($current_user_id,"artist",$page,$selected_artist_id);

// Shows releases
echo $releases['results'];

?>


<?php require_once 'footer.php'; ?>