<?php
	
	$page_name = "you";
	$sub_page_name = "stats";
	
	$page_title = "Numu - Your Stats";
	
	
	// Process login details
	
	require_once 'header.php';
	
	if ($current_user_id > 0) {
	
	$db = Database::getInstance();
	$mysqli = $db->getConnection(); 
	
	$data = new Data();
	
	$query_string = $data->returnFilterString($current_user_id);
		
	// Get some stats for this user...
	
	// Total listens
	// SELECT count(release_id) FROM v2_user_listen WHERE user_id = 4 AND read_status = 1;
	
	$sql_query = "SELECT count(release_id) as total_listens FROM v2_release_group WHERE release_id IN (SELECT release_id FROM v2_user_listen WHERE user_id = $current_user_id AND read_status = 1) AND artist_id IN (SELECT artist_id FROM v2_user_artist WHERE user_id = $current_user_id) $query_string";
	$result = $mysqli->query($sql_query);
	$num_rows = $result->num_rows;
	
	if ($num_rows == 1) { 
		$row = mysqli_fetch_array($result);
		$total_listens = $row['total_listens'];
		
	}
	
	// Total artists follow
	
	$sql_query = "SELECT count(artist_id) as total_follows FROM v2_user_artist WHERE user_id = $current_user_id";
	$result = $mysqli->query($sql_query);
	$num_rows = $result->num_rows;
	
	if ($num_rows == 1) { 
		$row = mysqli_fetch_array($result);
		$total_follows = $row['total_follows'];
		
	}
	
	// Total listens unfiltered
	
	$sql_query = "SELECT count(release_id) as total_listens,count(distinct artist_id) as total_artists FROM v2_release_group WHERE release_id IN (SELECT release_id FROM v2_user_listen WHERE user_id = $current_user_id AND read_status = 1)";
	$result = $mysqli->query($sql_query);
	$num_rows = $result->num_rows;
	
	if ($num_rows == 1) { 
		$row = mysqli_fetch_array($result);
		$total_listens_unfiltered = $row['total_listens'];
		$total_artists_unfiltered = $row['total_artists'];
		
	}
	
	// Total unlistened
	// SELECT count(release_id) FROM v2_release_group WHERE release_id NOT IN (SELECT release_id FROM v2_user_listen WHERE user_id = 4);
	$sql_query = "SELECT count(release_id) as total_unlistened, count(distinct artist_id) as total_artists FROM v2_release_group WHERE artist_id IN (SELECT artist_id FROM v2_user_artist WHERE user_id = $current_user_id) $query_string";
	$result = $mysqli->query($sql_query);
	$num_rows = $result->num_rows;
	
	if ($num_rows == 1) { 
		$row = mysqli_fetch_array($result);
		$total_unlistened = $row['total_unlistened'];
		
	}
	
	// Register Date
	// SELECT register_date FROM v2_users WHERE user_id = 4;
	
	$sql_query = "SELECT register_date,username FROM v2_users WHERE user_id = $current_user_id";
	$result = $mysqli->query($sql_query);
	$num_rows = $result->num_rows;
	
	if ($num_rows == 1) { 
		$row = mysqli_fetch_array($result);
		$register_date = $row['register_date'];
		$current_username = $row['username'];
	}
	
	
	
?>	

<?Php
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
WHERE stat.user_id = '.$current_user_id.'
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
		<div><?php if ($row['username'] == '') { echo "Someone "; } else { echo $row['username']; } ?> listened to <a href="/release/<?php echo $row['release_id']; ?>"><?php echo $row['release_name']; ?></a> by <a href="/artist/<?php echo $row['artist_id']; ?>"><?php echo $row['artist_name']; ?></a></div>
	<?php } else { ?>
		<div><?php if ($row['username'] == '') { echo "Someone "; } else { echo $row['username']; } ?> followed <a href="/artist/<?php echo $row['artist_id']; ?>"><?php echo $row['artist_name']; ?></a></div>
	<?php } 
} ?>
</div>
<?php 
}
?>

<div class="sub_title">Stats</div>

<div class="text"><P>You joined on <?php echo date("M jS Y",strtotime($register_date)); ?>, that's about <?php echo time_elapsed_string($register_date); ?>.</P><P>You've listened to <strong><?php echo $total_listens; ?></strong> out of <strong><?php echo $total_unlistened; ?></strong> releases by the <strong><?php echo $total_follows; ?></strong> artists you follow. <?php if ($total_unlistened > 0 ) { ?>That's a <strong><?php echo round((($total_listens/$total_unlistened)*100),2); ?>%</strong> completion rate based on your filters.<?php } ?></P>
<P>Overall you've listened to <strong><?php echo $total_listens_unfiltered; ?></strong> releases, across <strong><?php echo $total_artists_unfiltered; ?></strong> different artists.</div>

<?php
	} else {
?>

<div class="sub_title">
	Not Authorized
</div>
<P>Please register or sign into your account.</P>

<?php } ?>

<?php require_once 'footer.php'; ?>