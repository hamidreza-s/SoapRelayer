<?php
	// Include Bootstrap
	require_once './Bootstrap.php';
	
	// Retrieve one user
	if (isset($_GET['id']))
	{
		if (is_numeric($_GET['id']))
		{
			// Retrieve SMSes by User form Database
			$userArray = retrieveSmsByUser($db, $_GET['id']);
			
			// Show SMSes
			header('Conten-Type: application/json');
			echo json_encode($userArray);	
		}
		else
		{
			echo false;
		}
	}
	// Retrieve all SMSes
	else
	{
		// Retrieve SMSes form Database
		$usersArray = retrieveAllSms($db);
		
		// Show SMSes
		header('Conten-Type: application/json');
		echo json_encode($usersArray);	
	}
	


	// Retrieve all SMSes by user from Database
	function retrieveSmsByUser($db, $userId)
	{
		// Create query
		$selectQuery = 'SELECT * FROM `all_sms` WHERE `user_id` = ?';

		// Prepare and execute query
		$preparedStatement = $db->prepare($selectQuery);
		$preparedStatement->execute(array($userId));
		
		// Fetch and return data
		$usersArray = null;
		while ($userArray = $preparedStatement->fetch(PDO::FETCH_ASSOC))
		{
			$usersArray[] = $userArray;
		}
		
		// Return result
		if (count($usersArray) > 0)
		{
			return $usersArray;
		}
		else
		{
			return false;
		}
	}
	
	// Retrieve all SMSes form Database
	function retrieveAllSms($db)
	{
		// Create query
		$selectQuery = 'SELECT * FROM `all_sms`';

		// Prepare and execute query
		$preparedStatement = $db->prepare($selectQuery);
		$preparedStatement->execute();
		
		// Fetch data
		while ($userArray = $preparedStatement->fetch(PDO::FETCH_ASSOC))
		{
			$usersArray[] = $userArray;
		}
		
		// Return result
		if (count($usersArray) > 0)
		{
			return $usersArray;
		}
		else
		{
			return false;
		}
	}	
?>