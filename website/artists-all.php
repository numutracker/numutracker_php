<?php
	
	$page_name = "artists";
	$sub_page_name = "artists_all";
	
	$page_title = "Numu - Global Artists";
	
	
	// Process login details
	
	require_once 'header.php';
	
?>	

<?php
$data = new Data();
		
$artists = $data->returnArtistsString($current_user_id,"all",$page);

// Shows releases
echo $artists['results'];

?>


<?php require_once 'footer.php'; ?>