<?php
	// Include Zend Library
	set_include_path(implode(PATH_SEPARATOR, array(
		realpath('./library'),
		get_include_path(),
	)));
	
	// Include Zend Autoloader
	require "Zend/Loader/Autoloader.php";
	$autoloader = Zend_Loader_Autoloader::getInstance();
	
	// Connect to DataBase
	try
	{
		$host = '__DBHOST__';
		$dbname = '__DBNAME__';
		$user = '__DBUSERNAME__';
		$pass = '__DBPASSWORD__';
		$db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);  
	}
	catch(PDOException $e) 
	{  
		echo $e->getMessage();  
	}  	
?>