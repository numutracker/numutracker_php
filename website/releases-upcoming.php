<?php
	
	$page_name = "releases";
	$sub_page_name = "releases_upcoming";
	
	$page_title = "Numu - Your Upcoming Releases";
	
	
	// Process login details
	
	require_once 'header.php';
		
?>	
<?php
	$data = new Data();
	
	$releases = $data->returnReleasesString($current_user_id,"upcoming",$page);
	
	// Shows releases
	echo $releases['results'];

?>

<?php require_once 'footer.php'; ?>