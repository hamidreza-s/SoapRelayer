<?php
	// Include Bootstrap
	require_once './Loader.php';
	require_once '../apps/DB.php';
		
	// Fetch POST request
	$username = $_POST['username'];
	$password = $_POST['password'];
	$from = $_POST['from'];
	$to = $_POST['to'];
	$text = $_POST['text'];
	$textMetaData = calculateStringLengh($text);
	$textEncoding = $textMetaData['textEncoding'];
	$byteLength = $textMetaData['byteLength'];
	$charLength = $textMetaData['charLength'];
	$date = time();

	// Authenticate user
	// Get its data
	$userData = authenticateUser($db, $username, $password, $from);

	// ** Case 1: Correct user, password and number
	if ($userData['authentication'] == 'correct_all')
	{
		// Save sms to Databse
		$result = saveSmsToDB($db, $userData['user_id'], $from, $to, $text, $textEncoding, $byteLength, $charLength, $date);
		if ($result)
		{
			$status = "Success: The SMS was saved in database with ID#" . $result;
		}
		else
		{
			$status = "Error: There was an error in saving data to database!";
		}
	}
	// ** Case 2: Wrong user password
	elseif ($userData['authentication'] == 'wrong_pass')
	{
		$status = "Error: Wrong Password!";
	}
	// ** Case 3: Wrong username
	elseif ($userData['authentication'] == 'wrong_user')
	{
		$status = "Error: Wrong Username!";
	}
	// ** Case 4: Wrong number
	elseif ($userData['authentication'] == 'wrong_from')
	{
		$status = "Error: Wrong From Number!";
	}

	// Log Incoming POST data
	logToFile($username, $password, $from, $to, $text, $status, $textEncoding, $byteLength, $charLength, $date);
	
	// Show Status to browser JSON-Formated
	showToBrowser($username, $password, $from, $to, $text, $status, $textEncoding, $byteLength, $charLength, $date);

	// Show data
	function showToBrowser($username, $password, $from, $to, $text, $status, $textEncoding, $byteLength, $charLength, $date)
	{
		// Set HTTP response header to JSON
		header('Content-Type: application/json');
			
		// Create Post request array
		$arrayData['Date'] = date('c', $date);
		$arrayData['Username'] = $username ;
		$arrayData['Password'] = $password;
		$arrayData['From'] = $from;
		$arrayData['To'] = $to;
		$arrayData['Text'] = $text;
		$arrayData['Text Encoding'] = (int) $textEncoding;
		$arrayData['Byte Length'] = (int) $byteLength;
		$arrayData['Char Length'] = (int) $charLength;
		$arrayData['Status'] = $status;
		echo json_encode($arrayData);
	}
	
	// Log data
	function logToFile($username, $password, $from, $to, $text, $status, $textEncoding, $byteLength, $charLength, $date)
	{
		// Log Post request
		$logData = "Date: " . date('c', $date) . "\n";
		$logData .= "Username: $username \n";
		$logData .= "Password: $password \n";
		$logData .= "From: $from \n";
		$logData .= "To: $to \n";
		$logData .= "Text: $text \n";
		$logData .= "Text Encoding: $textEncoding \n";
		$logData .= "Byte Length: $byteLength\n";
		$logData .= "Char Length: $charLength\n";
		$logData .= "Status: $status\n";
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
    function saveSmsToDB($db, $user_id, $from, $to, $text, $textEncoding, $byteLength, $charLength, $date) 
    {		
		// Implode "to" string to array
		$toArray = explode(',', $to);
		
		// Initialize insert query
		$insertQueryMain = 'INSERT INTO `all_sms` (`user_id`, `from`, `to`, `text`, `encoding`, `byte_length`, `char_length`, `date`) VALUES ';
		$insertQueryValuesArray = array_fill(0, count($toArray), "(?, ?, ?, ?, ?, ?, ?, ?)");
		$insertQueryMain .= implode(',', $insertQueryValuesArray);

		// Append to insert query
		foreach ($toArray as $toSingle)
		{
			$bindParamsArray[] = $user_id;
			$bindParamsArray[] = $from;
			$bindParamsArray[] = $toSingle;
			$bindParamsArray[] = $text;
			$bindParamsArray[] = $textEncoding;
			$bindParamsArray[] = $byteLength;
			$bindParamsArray[] = $charLength;
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