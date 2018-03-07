<?php
	
	$page_name = "login";
	
	$page_title = "Login to Numu Tracker";
	
	
	// Process login details
	
	require_once 'header.php'; 
?>	

<div class="sub_title">
	Authorizations
</div>
<form name="login">
<input type="text" class='input' id="email" name="login_email" placeholder="email"/>
<input class='input' placeholder="password" id="password" name="login_password" type="password"/>
</form>
<div class="button" id="login_submit">
	Log On
</div>

<div class="sub_title">
	Registrations
</div>
<form name="register">
<input type="text" class='input' name="register_email" id="reg_email" placeholder="email"/>
<input class='input' placeholder="password" name="register_password" id="reg_password" type="password"/>
</form>
<div class="button" id="reg_submit">
	Request
</div>


<?php require_once 'footer.php'; ?>