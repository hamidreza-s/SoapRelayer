<?php
	// Include Bootstrap
	require_once './DB.php';
	require_once './Loader.php';

	// Update User
	if(isset($_GET['id']))
	{
		// Fetch Get data
		$id = $_GET['id'];
		$username = $_GET['username'];
		$password =  $_GET['password'];
		$credit = $_GET['credit'];
		$url =  $_GET['url'];
		$numbers =  $_GET['numbers'];

		// Update user in Database
		$result = updateUserInDb($db, $id, $username, $password, $credit, $url, $numbers);

		if ($result)
		{
			$response['status'] = true;
			$response['id'] = $result;
			$response['username'] = $username;
			$response['password'] = $password;
			$response['credit'] = $credit;
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
			$response['credit'] = $credit;
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
		$credit = $_GET['credit'];
		$url =  $_GET['url'];
		$numbers =  $_GET['numbers'];

		// Save user to Database
		$result = saveUserToDb($db, $username, $password, $credit, $url, $numbers);
		
		if ($result)
		{
			$response['status'] = true;
			$response['id'] = $result;
			$response['username'] = $username;
			$response['password'] = $password;
			$response['credit'] = $credit;
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
			$response['credit'] = $credit;
			$response['url'] = $url;
			$response['numbers'] = $numbers;
			header('Conten-Type: application/json');
			echo json_encode($response);
		}	
	}
	
	// Update user in Database function
	function updateUserInDb($db, $id, $username, $password, $credit, $url, $numbers)
	{
		// Create query
		$updateQuery = 'UPDATE  `users` SET  `username` = ?, `password` = ?, `credit` = ?, `url` = ?, `numbers` = ? WHERE  `id` = ?';
		
		// Prepare and execute query
		$preparedStatement = $db->prepare($updateQuery);
		$result = $preparedStatement->execute(array($username, $password, $credit, $url, $numbers, $id));

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
	function saveUserToDb($db, $username, $password, $credit, $url, $numbers)
	{
		// Create query
		$insertQuery = 'INSERT INTO `users` (`username`, `password`, `credit`, `url`, `numbers`) VALUES (?, ?, ?, ?, ?)';
		
		// Prepare and execute query
		$preparedStatement = $db->prepare($insertQuery);
		$result = $preparedStatement->execute(array($username, $password, $credit, $url, $numbers));
		
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