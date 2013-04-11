<?php
	// Include Bootstrap
	require_once './Bootstrap.php';
		
	// Fetch POST request
	$username = $_POST['username'];
	$password = $_POST['password'];
	$from = $_POST['from'];
	$to = $_POST['to'];
	$text = $_POST['text'];
	$textMetaData = calculateStringLengh($text);
	$length = $textMetaData['byteLength'];
	$textEncoding = $textMetaData['textEncoding'];
	$date = time();

	// Authenticate user
	// Get its data
	$userData = authenticateUser($db, $username, $password, $from);

	// ** Case 1: Correct user, password and number
	if ($userData['authentication'] == 'correct_all')
	{	
		// Save sms to Databse
		$result = saveSmsToDB($db, $userData['user_id'], $from, $to, $text, $length, $date);
		if ($result)
		{
			echo $status = "Success: The SMS was saved in database with ID#" . $result . "\n";
		}
		else
		{
			echo $status = "Error: There was an error in saving data to database!\n";
		}
	}
	// ** Case 2: Wrong user password
	elseif ($userData['authentication'] == 'wrong_pass')
	{
		echo $status = "Error: Wrong Password!\n";
	}
	// ** Case 3: Wrong username
	elseif ($userData['authentication'] == 'wrong_user')
	{
		echo $status = "Error: Wrong Username!\n";
	}
	// ** Case 4: Wrong number
	elseif ($userData['authentication'] == 'wrong_from')
	{
		echo $status = "Error: Wrong From Number!\n";
	}

	// Log Incoming POST data
	logToFile($username, $password, $from, $to, $text, $status, $textEncoding, $length, $date);
	
	// Log data
	function logToFile($username, $password, $from, $to, $text, $status, $textEncoding, $length, $date)
	{
		// Log Post request
		$logData = "Date: " . date('c', $date) . "\n";
		$logData .= "Username: $username \n";
		$logData .= "Password: $password \n";
		$logData .= "From: $from \n";
		$logData .= "To: $to \n";
		$logData .= "Text: $text \n";
		$logData .= "Text Encoding: $textEncoding \n";
		$logData .= "Length: $length\n";
		$logData .= "Status: $status";
		$logData .= "-------------------------------------------------\n";
		return file_put_contents('/smsRelayPostLog.log', $logData, FILE_APPEND | LOCK_EX);	
	}
	
	// Authenticate user
	function authenticateUser($db, $username, $password, $from)
	{
		// Prepare and execute query
		$preparedStatement = $db->prepare("SELECT `id`, `username`, `password`, `numbers` FROM `users` WHERE `username` = :username");
		$preparedStatement->execute(array(":username" => $username));
		$dbUserData = $preparedStatement->fetch();
		
		// Check "From Numbers" correctness
		$dbFromNumbersArray = explode(',', $dbUserData['numbers']);
		$fromNumberCorrectness = false;
		foreach ($dbFromNumbersArray as $dbFromNumber)
		{
			if ($dbFromNumber == $from) { $fromNumberCorrectness = true;	break;}
		}
		
		
		if ($dbUserData)
		{
			// Password is correct
			if ($dbUserData['password'] == $password)
			{
				// From number is Correct
				if ($fromNumberCorrectness)
				{
					return array('authentication' => 'correct_all', 'user_id' => $dbUserData['id']);
				}
				// From number is Wrong
				else
				{
					return array('authentication' => 'wrong_from');
				}
			}
			// Password is wrong
			else
			{
				return array('authentication' => 'wrong_pass');
			}
		}
		// Username is wrong
		else
		{
			return array('authentication' => 'wrong_user');
		}
	}
	
	// Define function to save input sms to database
    function saveSmsToDB($db, $user_id, $from, $to, $text, $length, $date) 
    {		
		// Implode "to" string to array
		$toArray = explode(',', $to);
		
		// Initialize insert query
		$insertQueryMain = 'INSERT INTO `all_sms` (`user_id`, `from`, `to`, `text`, `length`, `date`) VALUES ';
		$insertQueryValuesArray = array_fill(0, count($toArray), "(?, ?, ?, ?, ?, ?)");
		$insertQueryMain .= implode(',', $insertQueryValuesArray);

		// Append to insert query
		foreach ($toArray as $toSingle)
		{
			$bindParamsArray[] = $user_id;
			$bindParamsArray[] = $from;
			$bindParamsArray[] = $toSingle;
			$bindParamsArray[] = $text;
			$bindParamsArray[] = $length;
			$bindParamsArray[] = $date;
		}
		
		// Prepare and execute query
		$preparedStatement = $db->prepare($insertQueryMain);
		$result = $preparedStatement->execute($bindParamsArray);

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

	function calculateStringLengh($text)
	{
		$textEncoding = mb_detect_encoding($text);
		$charLength = mb_strlen($text, $textEncoding);
		
		if ($textEncoding == 'UTF-8') 
		{
			$byteLength = $charLength * 2;
		}
		elseif ($textEncoding == 'ASCII') 
		{
			$byteLength = $charLength;
		}
		else
		{
			$byteLength = 'undefiend';
		}
		
		return array('textEncoding' => $textEncoding, 'charLength' => $charLength, 'byteLength' => $byteLength);
	}

?>