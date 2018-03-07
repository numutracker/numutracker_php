<?php
$page_name = "index";
$sub_page_name = 'recent';
$page_title = "Numu - New Music Tracker for iPhone - Discovery and Reminders";

require_once 'header.php'; 


?>	

<div class="text"><P><strong>Numu Tracker</strong> can keep you up to date on releases by the artists you love. Create an account to follow artists and get personalized lists of albums to listen to.</P></div>

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


<?php
$data = new Data();
		
$releases = $data->returnReleasesString(0,"recent",$page);
?>
<div class="sub_title">Recent Releases</div>
<?php
// Shows releases
echo $releases['results'];
?>

<?php
require_once 'footer.php'; 
?>