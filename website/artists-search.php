<?php
	
	$page_name = "artists";
	$sub_page_name = "artists_search";
	
	$page_title = "Numu - Search Artists";
	
	
	// Process login details
	
	require_once 'header.php';
	
?>	

<form method="get" id="artist_search">
	<input type="text" name="search" placeholder="Search Artists" value = "<?php echo $_GET['search']; ?>" />
	<input type="submit" value="Search" />
</form>

<?php if (isset($_GET['search'])) {
	if (trim($_GET['search']) != '') {
		if (strlen(trim($_GET['search'])) > 2) {
			$search = trim($_GET['search']);
			
			$data = new Data();
		
			$artists = $data->returnSearchArtistsString($current_user_id,$search,$page);
			
			// Shows releases
			echo $artists['results'];

			
		}
	}
}
?>

<?php require_once 'footer.php'; ?>