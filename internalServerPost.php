<?php
	// Include Bootstrap
	require_once './Bootstrap.php';
		
	// Fetch POST request
	$username = $_POST['username'];
	$password = $_POST['password'];
	$from = $_POST['from'];
	$to = $_POST['to'];
	$text = $_POST['text'];

	// Log it
	$logData = "Date: " . date('c', time()) . "\n";
	$logData .= "Username: $username \n";
	$logData .= "Password: $password \n";
	$logData .= "From: $from \n";
	$logData .= "To: $to \n";
	$logData .= "Text: $text \n";
	$logData .= "-------------------------------------------------\n";
	file_put_contents('/smsRelayPostLog.log', $logData, FILE_APPEND | LOCK_EX);
	
	// Save sms to Databse
	$result = saveSmsToDB($db, $username, $password, $from, $to, $text);
	echo $result;
	
	// Define function to save input sms to database
    function saveSmsToDB($db, $username, $password, $from, $to, $text) 
    {		
		// Implode "to" string to array
		$toArray = explode(',', $to);
		
		// Initialize insert query
		$insertQueryMain = 'INSERT INTO `all_sms` (`username`, `password`, `from`, `to`, `text`) VALUES ';
		$insertQueryValuesArray = array_fill(0, count($toArray), "(?, ?, ?, ?, ?)");
		$insertQueryMain .= implode(',', $insertQueryValuesArray);

		// Append to insert query
		foreach ($toArray as $toSingle)
		{
			$bindParamsArray[] = $username;
			$bindParamsArray[] = $password;
			$bindParamsArray[] = $from;
			$bindParamsArray[] = $toSingle;
			$bindParamsArray[] = $text;
		}
		
		// Prepare and execute query
		$preparedStatement = $db->prepare($insertQueryMain);
		$result = $preparedStatement->execute($bindParamsArray);

		// Return result
		return $result;
    }	


?>