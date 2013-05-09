<?php
	// Include Bootstrap
	require_once './DB.php';
	require_once './Loader.php';
		
	// Fetch POST request
	$from = $_POST['from'];
	$to = $_POST['to'];
	$text = $_POST['text'];
	$textMetaData = calculateStringLengh($text);
	$byteLength = $textMetaData['byteLength'];
	$charLength = $textMetaData['charLength'];
	$textEncoding = $textMetaData['textEncoding'];
	$date = time();

	// Store incoming SMS to DB
	$dbSavedID = saveSmsToDB($db, $from, $to, $text, $textEncoding, $byteLength, $charLength, $date);
	if($dbSavedID)
	{
		// Get user data
		$userData = getUserDataByFromNumber($db, $from);
		sendReplyWithCurl($from, $to, $text, $userData['url']);
	}
	
	// Log Incoming POST data
	logToFile($from, $to, $text, $dbSavedID, $textEncoding, $byteLength, $charLength, $date);
	
	// Show Status to browser JSON-Formated
	showToBrowser($from, $to, $text, $dbSavedID, $textEncoding, $byteLength, $charLength, $date);

	// Show data
	function showToBrowser($from, $to, $text, $dbSavedID, $textEncoding, $byteLength, $charLength, $date)
	{
		// Set HTTP response header to JSON
		header('Content-Type: application/json');
		
		// Create Post request array
		$arrayData['Date'] = date('c', $date);
		$arrayData['From'] = $from;
		$arrayData['To'] = $to;
		$arrayData['Text'] = $text;
		$arrayData['Text Encoding'] = $textEncoding;
		$arrayData['Byte Length'] = (int) $byteLength;
		$arrayData['Char Length'] = (int) $charLength;
		$arrayData['DB Saved ID'] = (int) $dbSavedID;
		echo json_encode($arrayData);
	}
	
	// Log data
	function logToFile($from, $to, $text, $dbSavedID, $textEncoding, $byteLength, $charLength, $date)
	{
		// Log Post request
		$logData = "Date: " . date('c', $date) . "\n";
		$logData .= "From: $from \n";
		$logData .= "To: $to \n";
		$logData .= "Text: $text \n";
		$logData .= "Text Encoding: $textEncoding \n";
		$logData .= "Byte Length: $byteLength\n";
		$logData .= "Char Length: $charLength\n";
		$logData .= "DB Saved ID: $dbSavedID \n";
		$logData .= "-------------------------------------------------\n";
		return file_put_contents('/smsBrokerPostLog.log', $logData, FILE_APPEND | LOCK_EX);	
	}
	
	// Get URL by "From Number"
	function getUserDataByFromNumber($db, $from)
	{
		// Prepare and execute query
		$preparedStatement = $db->prepare("SELECT `id`, `username`, `password`, `url` FROM `users` WHERE `numbers` LIKE :from");
		$preparedStatement->execute(array(":from" => "%" . $from . "%"));
		$dbUserData = $preparedStatement->fetch();
		return $dbUserData;
	}
	
	// Define function to save input sms to database
    function saveSmsToDB($db, $from, $to, $text, $textEncoding, $byteLength, $charLength, $date)
    {		
		// Implode "to" string to array
		$toArray = explode(',', $to);
		
		// Initialize insert query
		$insertQueryMain = 'INSERT INTO `replys` (`from`, `to`, `text`, `encoding`, `byte_length`, `char_length`, `date`) VALUES ';
		$insertQueryValuesArray = array_fill(0, count($toArray), "(?, ?, ?, ?, ?, ?, ?)");
		$insertQueryMain .= implode(',', $insertQueryValuesArray);

		// Append to insert query
		foreach ($toArray as $toSingle)
		{
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

	function sendReplyWithCurl($from, $to, $text, $url)
	{
		// Set POST variables
		$fields = array(
								'from' => urlencode($from),
								'to' => urlencode($to),
								'text' => urlencode($text),
						);

		// Url-ify the data for the POST
		$fieldsString = '';
		foreach($fields as $key=>$value) { $fieldsString .= $key . '=' . $value . '&';  }
		rtrim($fieldsString, '&');

		// Open connection
		$ch = curl_init();

		// Set the url, number of POST vars, POST data
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, count($fields));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);

		// Execute post
		$result = curl_exec($ch);

		// Close connection
		curl_close($ch);
	}
?>