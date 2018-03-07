<?php
	
	$page_name = "artists";
	$sub_page_name = "artists_yours";
	
	$page_title = "Numu - Your Artists";
	
	
	// Process login details
	
	require_once 'header.php';
		
?>

<?php
	if ($current_user_id > 0) {
		$data = new Data();
		
		$artists = $data->returnArtistsString($current_user_id,"user",$page);
		
		// Shows releases
		echo $artists['results'];

	
	} else {
?>
<div class="sub_title">
	Not Authorized
</div>
<P>Please register or sign into your account.</P>
<?php } ?>


<?php require_once 'footer.php'; ?>