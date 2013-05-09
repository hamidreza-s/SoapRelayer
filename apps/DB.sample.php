<?php	
	// Connect to DataBase
	try
	{
		$host = '';
		$dbname = '';
		$user = '';
		$pass = '';
		$db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));  
	}
	catch(PDOException $e) 
	{  
		echo $e->getMessage();  
	}	
?>