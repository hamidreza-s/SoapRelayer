<?php
	// Include Bootstrap
	require_once './Bootstrap.php';

	// Update User
	if(isset($_GET['id']))
	{
		// Fetch Get data
		$id = $_GET['id'];
		$username = $_GET['username'];
		$password =  $_GET['password'];
		$url =  $_GET['url'];
		$numbers =  $_GET['numbers'];

		// Update user in Database
		$result = updateUserInDb($db, $id, $username, $password, $url, $numbers);

		if ($result)
		{
			$response['status'] = true;
			$response['id'] = $result;
			$response['username'] = $username;
			$response['password'] = $password;
			$response['url'] = $url;
			$response['numbers'] = $numbers;
			header('Conten-Type: application/json');
			echo json_encode($response);
		}
		else
		{
			$response['status'] = false;
			$response['username'] = $username;
			$response['password'] = $password;
			$response['url'] = $url;
			$response['numbers'] = $numbers;
			header('Conten-Type: application/json');
			echo json_encode($response);
		}		
	}
	// Create User
	else
	{
		// Fetch Get data
		$username = $_GET['username'];
		$password =  $_GET['password'];
		$url =  $_GET['url'];
		$numbers =  $_GET['numbers'];

		// Save user to Database
		$result = saveUserToDb($db, $username, $password, $url, $numbers);
		
		if ($result)
		{
			$response['status'] = true;
			$response['id'] = $result;
			$response['username'] = $username;
			$response['password'] = $password;
			$response['url'] = $url;
			$response['numbers'] = $numbers;
			header('Conten-Type: application/json');
			echo json_encode($response);
		}
		else
		{
			$response['status'] = false;
			$response['username'] = $username;
			$response['password'] = $password;
			$response['url'] = $url;
			$response['numbers'] = $numbers;
			header('Conten-Type: application/json');
			echo json_encode($response);
		}	
	}
	
	// Update user in Database function
	function updateUserInDb($db, $id, $username, $password, $url, $numbers)
	{
		// Create query
		$updateQuery = 'UPDATE  `users` SET  `username` = ?, `password` = ?, `url` = ?, `numbers` = ? WHERE  `id` = ?';
		
		// Prepare and execute query
		$preparedStatement = $db->prepare($updateQuery);
		$result = $preparedStatement->execute(array($username, $password, $url, $numbers, $id));

		// Return result
		if($result)
		{
			return $id;
		}
		else
		{
			return false;
		}	

	}
	
	
	// Save user to Database function
	function saveUserToDb($db, $username, $password, $url, $numbers)
	{
		// Create query
		$insertQuery = 'INSERT INTO `users` (`username`, `password`, `url`, `numbers`) VALUES (?, ?, ?, ?)';
		
		// Prepare and execute query
		$preparedStatement = $db->prepare($insertQuery);
		$result = $preparedStatement->execute(array($username, $password, $url, $numbers));
		
		// Return result
		if($result)
		{
			return $db->lastInsertId();
		}
		else
		{
			return false;
		}		
	}
	
?>