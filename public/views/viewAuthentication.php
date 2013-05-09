<?php
	// Start Session
	session_start();
	
	// Check authentication
	if(isset($_SESSION['userId']))
	{
		header('Location: ./viewUserListSms.php');
	}
	else
	{
		header('Location: ./viewUserSignin.php');
	}

?>