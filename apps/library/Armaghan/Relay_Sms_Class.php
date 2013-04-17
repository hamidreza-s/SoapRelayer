<?php

class Relay_Sms_Class
{
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
		/*** Store input data from EXTERNAL CLIENT ***/	
		
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
}
