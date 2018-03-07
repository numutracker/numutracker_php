<?php
	
	$page_name = "releases";
	$sub_page_name = "releases_all";
	
	$page_title = "Numu - All Releases";	
	
	
	// Process login details
	
	require_once 'header.php';
		
?>	

<?php
	$data = new Data();
		
		$releases = $data->returnReleasesString($current_user_id,"all",$page);
		
		// Shows releases
		echo $releases['results'];
?>


<?php require_once 'footer.php'; ?>