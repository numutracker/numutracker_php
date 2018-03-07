<?php
	
	$page_name = "releases";
	$sub_page_name = "releases_yours";
	
	$page_title = "Numu - Your Releases";

	
	// Process login details
	
	require_once 'header.php';

	if ($current_user_id > 0) {
		
		// Creates data object to grab releases...
		$data = new Data();
		
		$releases = $data->returnReleasesString($current_user_id,"user",$page);
		
		// Shows releases
		echo $releases['results'];
		
	} else {
?>

<div class="sub_title">
	Not Authorized
</div>
<P>Please register or sign into your account.</P>

<?php } ?>


<?php require_once 'footer.php'; ?>