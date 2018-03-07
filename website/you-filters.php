<?php
	
	$page_name = "you";
	$sub_page_name = "filters";
	
	$page_title = "Numu - Your Filters";
	
	
	
	require_once 'header.php';
	
	
?>	

<?php
	if ($current_user_id > 0) {
	
	$db = Database::getInstance();
	$mysqli = $db->getConnection(); 
	$sql_query = "SELECT * FROM v2_users WHERE user_id = $current_user_id";
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

	$filters = array("album"=>$album,"single"=>$single,"ep"=>$ep,"live"=>$live,"soundtrack"=>$soundtrack,"remix"=>$remix,"other"=>$other);

?>
<div class="sub_title">Filters</div>
<div id="filters" class="text">
	<div class="filter_container">
		<div class="filter_name">Albums</div>
		<div class="filter_space">&nbsp;</div>
		<div class="filter_button <?php if ($filters['album'] == 1) { echo "shown"; } else { echo "hidden"; } ?>" type="album"><?php if ($filters['album'] == 1) { echo "Shown"; } else { echo "Hidden"; } ?></div>
	</div>
	<div class="filter_container">
		<div class="filter_name">Singles</div>
		<div class="filter_space">&nbsp;</div>
		<div class="filter_button <?php if ($filters['single'] == 1) { echo "shown"; } else { echo "hidden"; } ?>" type="single"><?php if ($filters['single'] == 1) { echo "Shown"; } else { echo "Hidden"; } ?></div>
	</div>
	<div class="filter_container">
		<div class="filter_name">EPs</div>
		<div class="filter_space">&nbsp;</div>
		<div class="filter_button <?php if ($filters['ep'] == 1) { echo "shown"; } else { echo "hidden"; } ?>" type="ep"><?php if ($filters['ep'] == 1) { echo "Shown"; } else { echo "Hidden"; } ?></div>
	</div>
	<div class="filter_container">
		<div class="filter_name">Live Albums</div>
		<div class="filter_space">&nbsp;</div>
		<div class="filter_button <?php if ($filters['live'] == 1) { echo "shown"; } else { echo "hidden"; } ?>" type="live"><?php if ($filters['live'] == 1) { echo "Shown"; } else { echo "Hidden"; } ?></div>
	</div>
	<div class="filter_container">
		<div class="filter_name">Soundtracks</div>
		<div class="filter_space">&nbsp;</div>
		<div class="filter_button <?php if ($filters['soundtrack'] == 1) { echo "shown"; } else { echo "hidden"; } ?>" type="soundtrack"><?php if ($filters['soundtrack'] == 1) { echo "Shown"; } else { echo "Hidden"; } ?></div>
	</div>
	<div class="filter_container">
		<div class="filter_name">Remixes</div>
		<div class="filter_space">&nbsp;</div>
		<div class="filter_button <?php if ($filters['remix'] == 1) { echo "shown"; } else { echo "hidden"; } ?>" type="remix"><?php if ($filters['remix'] == 1) { echo "Shown"; } else { echo "Hidden"; } ?></div>
	</div>
	<div class="filter_container">
		<div class="filter_name">Other</div>
		<div class="filter_space">&nbsp;</div>
		<div class="filter_button <?php if ($filters['other'] == 1) { echo "shown"; } else { echo "hidden"; } ?>" type="other"><?php if ($filters['other'] == 1) { echo "Shown"; } else { echo "Hidden"; } ?></div>
	</div>
</div>

<div class="sub_title">Social Settings</div>
<div class="text">
<P>You can set a username that will appear in various social features throughout the site (like the above activity panel). This is optional. Usernames must be longer than 3 characters, and no greater than 10 characters.</P>
<input type="text" name="username" style="position:absolute; top:-50px;" />
<input type="text" id="public_name" name="public_name" placeholder="set username" autocomplete="off" value="<?php echo $current_username; ?>" /> <button id="public_name_button">Set Username</button>
</div>

<?php 
} else {
?>

<div class="sub_title">
	Not Authorized
</div>
<P>Please register or sign into your account.</P>

<?php } ?>

<?php require_once 'footer.php'; ?>