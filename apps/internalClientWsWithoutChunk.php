<?php
	// Include Bootstrap
	require_once './DB.php';
	require_once './Loader.php';
	
	/*
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
	*/
	// Retrieve data from database
	$queryString = "SELECT * FROM all_sms WHERE recipient_id = 0 LIMIT 0,10"; 
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

		// Create final arrays
		foreach ($selectResult as $sms)
		{
				$finalArrayIDs[]	= $sms['id'];
				$finalFrom[] 	= $sms['from'];
				$finalTo[] 		= $sms['to'];
				$finalText[] 	= $sms['text'];
		}
						
		// Send chunked envelope
		$recipientIDs[] = $client->enqueue(
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
		
		// Bind recipientID(s) and databaseRowID(s)
		foreach ($finalArrayIDs as $rowID)
		{
			$rowIDsPlusRecipientIDs[$rowID] = array_shift($recipientIDs);
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









