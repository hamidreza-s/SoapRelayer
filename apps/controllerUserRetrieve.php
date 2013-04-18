<?php
	// Include Bootstrap
	require_once './Bootstrap.php';
	
	// Retrieve one user
	if (isset($_GET['id']))
	{
		// Retrieve user form Database
		$userArray = retrieveUserById($db, $_GET['id']);
		
		// Show users
		header('Conten-Type: application/json');
		echo json_encode($userArray);		
	}
	// Retrieve all users
	else
	{
		// Retrieve user form Database
		$usersArray = retrieveUsers($db);
		
		// Show users
		header('Conten-Type: application/json');
		echo json_encode($usersArray);	
	}
	


	// Retrieve one user by ID from Database
	function retrieveUserById($db, $userId)
	{
		// Create query
		$selectQuery = 'SELECT * FROM `users` WHERE `id` = ?';

		// Prepare and execute query
		$preparedStatement = $db->prepare($selectQuery);
		$preparedStatement->execute(array($userId));
		
		// Fetch and return data
		if ($userArray = $preparedStatement->fetch(PDO::FETCH_ASSOC))
		{
			return $userArray;
		}
		else
		{
			return false;
		}	
	}
	
	// Retrieve all users form Database
	function retrieveUsers($db)
	{
		// Create query
		$selectQuery = 'SELECT * FROM `users`';

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