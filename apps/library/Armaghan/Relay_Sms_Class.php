<?php

class Relay_Sms_Class
{
	// Create database property
	private $db;
	
	// Define SMS properties
	private $firstUTF8SmsLength = 70;
	private $nextUTF8SmsLength = 67;
	private $firstASCIISmsLength = 160;
	private $nextASCIISmsLength = 153;	

	// Class constructor
	public function __construct()
	{
		// Connect to DataBase
		try
		{
			$host = 'localhost';
			$dbname = 'sms_relay';
			$user = 'root';
			$pass = 'i181MYSQL';
			$this->db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);  
		}
		catch(PDOException $e) 
		{  
			echo $e->getMessage();  
		} 			
	}
	
	// Calculate string length and encoding
	private function calculateStringLengh($text)
	{
		// Calculate Text Encoding
		$textEncoding = mb_detect_encoding($text);
		
		// Calculate Text Character Length
		$charLength = mb_strlen($text, $textEncoding);
		
		// Calculate Text Byte Length and SMS Unit
		if ($textEncoding == 'UTF-8') 
		{
			// Calculate Text Byte
			$byteLength = $charLength * 2;
			
			// Calculate Sms Unit
			if ($charLength <= $this->firstUTF8SmsLength)
			{
				$smsUnit = 1;
			}
			else
			{
				$smsUnit = ceil($charLength / $this->nextUTF8SmsLength);
			}			
		}
		elseif ($textEncoding == 'ASCII') 
		{
			$byteLength = $charLength;
			
			// Calculate Sms Unit
			if ($charLength <= $this->firstASCIISmsLength)
			{
				$smsUnit = 1;
			}
			else
			{
				$smsUnit = ceil($charLength / $this->nextASCIISmsLength);
			}				
		}
		else
		{
			$byteLength = 'undefiend';
			$smsUnit = 'undefiend';
		}
		
		// return values
		return array(
			'textEncoding' 	=> $textEncoding, 
			'charLength' 		=> $charLength, 
			'byteLength' 		=> $byteLength,
			'smsUnit' 			=> $smsUnit
		);
	}	

	// Authenticate user, check phone number and credit
	private function authenticateUserAndEtc($username, $password, $from, $toCount, $recursiveFrom = false)
	{
		// Prepare and execute query
		$preparedStatement = $this->db->prepare("SELECT `id`, `username`, `password`, `credit`, `numbers` FROM `users` WHERE `username` = :username");
		$preparedStatement->execute(array(":username" => $username));
		$dbUserData = $preparedStatement->fetch();
		
		// Check "From Numbers" correctness
		$dbFromNumbersArray = explode(',', $dbUserData['numbers']);
		$fromNumberCorrectness = false;
		// From is an array of numbers
		if ($recursiveFrom)
		{
			$clientFromNumbersArray = explode(',', $from);
			$fromHasDiff = array_diff($clientFromNumbersArray, $dbFromNumbersArray);
			if (empty($fromHasDiff)) { $fromNumberCorrectness = true; }
		}
		// From is a single number
		else
		{
			foreach ($dbFromNumbersArray as $dbFromNumber)
			{
				if ($dbFromNumber == $from) { $fromNumberCorrectness = true;	break; }
			}
			$fromHasDiff = $from;
		}
		
		if ($dbUserData)
		{
			// Password is correct
			if ($dbUserData['password'] == $password)
			{
				// From number is Correct
				if ($fromNumberCorrectness)
				{
					if ($dbUserData['credit'] < $toCount )
					{
						// Credit is low
						return array(
							'authentication' => 'low_credit', 
							'user_credit' => $dbUserData['credit']
						);
					}
					else
					{
						// Every thing is OK!
						return array(
							'authentication' => 'correct_all',
							'user_credit' => $dbUserData['credit'],
							'user_id' => $dbUserData['id']
						);
					}
				}
				// From number is Wrong
				else
				{
					return array(
						'authentication' => 'wrong_from',
						'wrong_number' => $fromHasDiff
					);
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
   
	// Authenticate user, check phone number and credit
	private function authenticateUserOnly($username, $password)
	{
		// Prepare and execute query
		$preparedStatement = $this->db->prepare("SELECT `id`, `username`, `password`, `credit`, `numbers` FROM `users` WHERE `username` = :username");
		$preparedStatement->execute(array(":username" => $username));
		$dbUserData = $preparedStatement->fetch();
		
		if ($dbUserData)
		{
			// Password is correct
			if ($dbUserData['password'] == $password)
			{
				// Every thing is OK!
				return array(
					'authentication' => 'correct_all',
					'user_id' => $dbUserData['id'],
					'credit' => $dbUserData['credit']
				);
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
      
   /**
     * Send one SMS to many phone numbers
     *
	 * @param string $username
	 * @param string $password
	 * @param string $from
	 * @param string $to
	 * @param string $text
     * @return integer recipient_id
     */
    public function sendSmsOneToMany($username, $password, $from, $to, $text) 
    {
		// Implode "to" string to array
		$toArray = explode(',', $to);
		$toCount = count($toArray);
		
		// Authenticate user
		$userData = $this->authenticateUserAndEtc($username, $password, $from, $toCount);
		if ($userData['authentication'] != 'correct_all') { return $userData; }
		$userId = $userData['user_id'];
		$userCredit = $userData['user_credit'];
		
		// Calculate text properties
		$textMetaData = $this->calculateStringLengh($text);
		$textEncoding = $textMetaData['textEncoding'];
		$byteLength = $textMetaData['byteLength'];
		$charLength = $textMetaData['charLength'];
		$smsUnit = $textMetaData['smsUnit'];

		// Subtract user credit by sent sms
		$newCredit = $userCredit - ($toCount * $smsUnit);
		
		// Initialize insert query
		$insertSmsQuery = 'INSERT INTO `all_sms` (`id`, `user_id`, `from`, `to`, `text`, `encoding`, `byte_length`, `char_length`, `sms_unit`, `date`) VALUES ';
		$insertQueryValuesArray = array_fill(0, count($toArray), "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$insertSmsQuery .= implode(',', $insertQueryValuesArray);

		// Append to insert query
		$idCounter = 1;
		foreach ($toArray as $toSingle)
		{
			$bindParamsArray[] = microtime(true) * mt_rand(10000,11000) . $idCounter++;
			$bindParamsArray[] = $userId;
			$bindParamsArray[] = $from;
			$bindParamsArray[] = $toSingle;
			$bindParamsArray[] = $text;
			$bindParamsArray[] = $textEncoding;
			$bindParamsArray[] = $byteLength;
			$bindParamsArray[] = $charLength;
			$bindParamsArray[] = $smsUnit;
			$bindParamsArray[] = time();
		}

		try
		{
			// Begin database transaction
			$this->db->beginTransaction();
			
			// Prepare and execute insert sms query
			$preparedStatement = $this->db->prepare($insertSmsQuery);
			$insertSmsResult = $preparedStatement->execute($bindParamsArray);
			
			// Prepare and execute update credit query
			$updateCreditQuery = 'UPDATE  `users` SET  `credit` =  ? WHERE `id` = ?;';
			$preparedStatement = $this->db->prepare($updateCreditQuery);
			$updateCreditResult = $preparedStatement->execute(array($newCredit, $userId));
			
			// If we arrive here, it means that no exception was thrown
			// i.e. no query has failed, and we can commit the transaction
			$this->db->commit();	
			
			// Return true
			return true;
		}
		catch (Exception $e)
		{
			// An exception has been thrown
			// We must rollback the transaction
			$this->db->rollback();		
			
			// Return false
			return false;
		}
    }
    
	/**
     * Send many SMS to many phone numbers peer to peer
     *
	 * @param string $username
	 * @param string $password
	 * @param string $from
	 * @param string $to
	 * @param array $text
     * @return integer recipient_id
     */
    public function sendSmsManyToMany($username, $password, $from, $to, $text) 
    {
		// Implode "to" string to array
		$toArray = explode(',', $to);
		$fromArray = explode(',', $from);
		$toCount = count($toArray);
		
		// Authenticate user
		$userData = $this->authenticateUserAndEtc($username, $password, $from, $toCount, true);
		if ($userData['authentication'] != 'correct_all') { return $userData; }
		$userId = $userData['user_id'];
		$userCredit = $userData['user_credit'];
		
		// Initialize insert query
		$insertSmsQuery = 'INSERT INTO `all_sms` (`id`, `user_id`, `from`, `to`, `text`, `encoding`, `byte_length`, `char_length`, `sms_unit`, `date`) VALUES ';
		$insertQueryValuesArray = array_fill(0, count($toArray), "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$insertSmsQuery .= implode(',', $insertQueryValuesArray);

		// Initialize counter
		$textCounter = 0;
		$fromCounter = 0;
		$idCounter = 1;
		
		// Append to insert query
		foreach ($toArray as $toSingle)
		{	
			// Calculate text properties
			$textSingle = $text[$textCounter++];
			$fromSingle = $fromArray[$fromCounter++];
			$textMetaData = $this->calculateStringLengh($textSingle);
			$textEncoding = $textMetaData['textEncoding'];
			$byteLength = $textMetaData['byteLength'];
			$charLength = $textMetaData['charLength'];
			$smsUnit = $textMetaData['smsUnit'];
			$allSmsUnit += $smsUnit;

			// Bind parameters to insert sms query
			$bindParamsArray[] = microtime(true) * mt_rand(10000,11000) . $idCounter++;
			$bindParamsArray[] = $userId;
			$bindParamsArray[] = $fromSingle;
			$bindParamsArray[] = $toSingle;
			$bindParamsArray[] = $textSingle;
			$bindParamsArray[] = $textEncoding;
			$bindParamsArray[] = $byteLength;
			$bindParamsArray[] = $charLength;
			$bindParamsArray[] = $smsUnit;
			$bindParamsArray[] = time();
		}

		// Subtract user credit by sent sms
		$newCredit = $userCredit - $allSmsUnit;
			
		try
		{
			// Begin database transaction
			$this->db->beginTransaction();
			
			// Prepare and execute insert sms query
			$preparedStatement = $this->db->prepare($insertSmsQuery);
			$insertSmsResult = $preparedStatement->execute($bindParamsArray);
			
			// Prepare and execute update credit query
			$updateCreditQuery = 'UPDATE  `users` SET  `credit` =  ? WHERE `id` = ?;';
			$preparedStatement = $this->db->prepare($updateCreditQuery);
			$updateCreditResult = $preparedStatement->execute(array($newCredit, $userId));
			
			// If we arrive here, it means that no exception was thrown
			// i.e. no query has failed, and we can commit the transaction
			$this->db->commit();	
			
			// Return true
			return true;
		}
		catch (Exception $e)
		{
			// An exception has been thrown
			// We must rollback the transaction
			$this->db->rollback();		
			
			// Return false
			return false;
		}
    }

	/**
     * Change user's password
     *
	 * @param string $username
	 * @param string $oldPassword
	 * @param string $newPassword
     * @return integer changedUserId
     */	
	public function changePassword($username, $oldPassword, $newPassword) 
	{
		$userData = $this->authenticateUserOnly($username, $oldPassword);
		if ($userData['authentication'] != 'correct_all') { return $userData; }
		$userId = $userData['user_id'];

		// Initialize update password query
		$updateQuery = 'UPDATE  `users` SET  `password` = ? WHERE  `id` = ?';
		
		// Prepare and execute query
		$preparedStatement = $this->db->prepare($updateQuery);
		$result = $preparedStatement->execute(array($newPassword, $userId));
		
		if ($result)
		{
			return $userId;
		}
	}

	/**
     * Get user credit
     *
	 * @param string $username
	 * @param string $password
     * @return integer credit
     */		
	public function getCredit($username, $password) 
	{
		$userData = $this->authenticateUserOnly($username, $password);
		if ($userData['authentication'] != 'correct_all') { return $userData; }
		$userCredit = $userData['credit'];
		return $userCredit;
	}
	
	public function getStatus() {}
	
}
