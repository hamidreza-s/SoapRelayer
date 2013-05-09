<?php
	// Include Bootstrap
	require_once './DB.php';
	require_once './Loader.php';
	
	// Determine chunk limit
	$chunkLimit = 5;
	
	// Set Web Service WSDL
	$_WSDL = '__SWDL__ADDRESS__';
	
	// Set credentials
	$_username = '__username__';
	$_password = '__password__';
	$_domain = '__domain__';

	
	// Connect to WebService
	try
	{
		// Initialize Client
		$client = new Zend_Soap_Client(null,
			array(
				'location'	=>	$_WSDL,
				'uri'			=>	$_WSDL
			)
		);
		
		// Set WSDL to client
		$client->setWsdl($_WSDL);
	}
	catch (SoapFault $s) { Zend_Debug::dump($s); }
	catch (Exception $e) { Zend_Debug::dump($e); }
	
	// Retrieve data from database
	$queryString = "SELECT * FROM all_sms WHERE recipient_id = 0"; 
	$createQuery = $db->query($queryString);
	
	// If recipiend_id = 0 exists
	if ($fetchQuery = $createQuery->fetchAll())
	{
		// Loop throug result
		foreach ($fetchQuery as $row)
		{
			// Make array
			$selectResult[] = $row;
		}
		
		// Chunked array
		$chunkedResult = array_chunk($selectResult, $chunkLimit);
		
		// Create final arrays
		$counter = 1;
		foreach ($chunkedResult as $chunk)
		{
			foreach ($chunk as $sms)
			{
				$finalArrayIDs[]	= $sms['id'];
				$finalFrom[] 	= $sms['from'];
				$finalTo[] 		= $sms['to'];
				$finalText[] 	= $sms['text'];
			}
			
				
			// Send chunked envelope
			$chunkedRecipientIDs[] = $client->enqueue(
				$_username,	# Webservice Username
				$_password,	# Webservice Passowrd
				$_domain,		# Webservice Domain
				0,						# Sending Type (0: One-to-One, 1: One-to-Many)
				$finalText,		#	Message Text (Datatype: Array)
				$finalTo,			# Message Recipient (Datatype: Array)
				$finalFrom,		# Message Originator (Datatype: Array)
				null,					# UDHS (Optional)
				null					# Message Class (Optional)
			);
			
			
			/*
			// Test output
			echo "<h3>Chunk#" . $counter++ . "</h3>";
			echo "From:";
			Zend_Debug::dump($finalFrom);
			echo "To:";
			Zend_Debug::dump($finalTo);
			echo "Text:";
			Zend_Debug::dump($finalText);
			echo "<hr/>";
			*/
			
			// Clean arrays
			unset($finalFrom);
			unset($finalTo);
			unset($finalText);
		}
		
		/*
		// For Test
		$chunkedRecipientIDs[0][] = '54965286';
		$chunkedRecipientIDs[0][] = '55445544';
		$chunkedRecipientIDs[0][] = '88887744';
		$chunkedRecipientIDs[1][] = '88445588';
		$chunkedRecipientIDs[1][] = '88554212';
		$chunkedRecipientIDs[1][] = '88554422';
		$chunkedRecipientIDs[2][] = '44558855';
		$chunkedRecipientIDs[2][] = '85458974';
		$chunkedRecipientIDs[2][] = '22418543';
		*/
		
		// Flatten $chunkedRecipientIDs Array
		foreach ($chunkedRecipientIDs as $recipientIDs)
		{
			foreach ($recipientIDs as $recipientID)
			{
				$flettenRecipientIDs[] = $recipientID;
			}
		}
		
		// Bind recipientID(s) and databaseRowID(s)
		foreach ($finalArrayIDs as $rowID)
		{
			$rowIDsPlusRecipientIDs[$rowID] = array_shift($flettenRecipientIDs);
		}

		// Fire query!
		foreach ($rowIDsPlusRecipientIDs as $rowIDsPlusRecipientIDKey => $rowIDsPlusRecipientIDValue)
		{
			// Initialize insert query to store recipientID(s) to database
			$updateQuery = 'UPDATE `all_sms` SET `recipient_id` = ? WHERE `id` = ?';
			$preparedStatement = $db->prepare($updateQuery);
			$updateResult[] = $preparedStatement->execute(array($rowIDsPlusRecipientIDValue, $rowIDsPlusRecipientIDKey));
		}
		
		Zend_Debug::dump($updateResult);
	}
	else
	{
		echo 'Right now, there is no SMS chunk in Database to send!';
	}
?>









