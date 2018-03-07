<?php
	
	if (isset($_SESSION)) {
		session_destroy();
	}
	
	header("Location: https://www.numutracker.com");
	$page_name = "login";
	
	$page_title = "Login to Numu Tracker";
	
	// Process login details
	
	require_once 'header.php'; 
	
?>	


<?php require_once 'footer.php'; ?>