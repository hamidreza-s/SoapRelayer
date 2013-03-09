<?php
	// Include Bootstrap
	require_once './Bootstrap.php';
	
	try
	{
		// Initialize Client
		$client = new Zend_Soap_Client(null,
			array(
				'location'	=>	'http://local.relay.armaghan.com/internalServerWs.php?handle',
				'uri'			=>	'http://local.relay.armaghan.com/internalServerWs.php?handle'
			)
		);
		
		// Set WSDL to client
		$client->setWsdl('http://local.relay.armaghan.com/internalServerWs.php?wsdl');
	}
	catch (SoapFault $s) { Zend_Debug::dump($s); }
	catch (Exception $e) { Zend_Debug::dump($e); }
		
	// Fire!
	$result = $client->sendSmsOneToMany('usernameTest', 'passwordTest', 'fromNumberTest', 'toNumberTest', 'textTest');
	Zend_Debug::dump($result);
?>