<?php
	// Include Bootstrap
	require_once './Bootstrap.php';
	
	// Retrieve user form Database
	$usersArray = retrieveUsers($db);
	
	// Show users
	header('Conten-Type: application/json');
	echo json_encode($usersArray);

	// Retrieve user form Database
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