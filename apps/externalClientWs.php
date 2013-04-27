<?php
	// Include Bootstrap
	require_once './Bootstrap.php';
	
	try
	{
		// Initialize Client
		$client = new Zend_Soap_Client(null,
			array(
				'location'	=>	'http://local.relay.armaghan.com/apps/internalServerWs.php?handle',
				'uri'			=>	'http://local.relay.armaghan.com/apps/internalServerWs.php?handle'
			)
		);
		
		// Set WSDL to client
		$client->setWsdl('http://local.relay.armaghan.com/apps/internalServerWs.php?wsdl');
	}
	catch (SoapFault $s) { Zend_Debug::dump($s); }
	catch (Exception $e) { Zend_Debug::dump($e); }
		
	// Fire!
	//$result = $client->sendSmsOneToMany('test', 'test', '50003', '1111,2222', 'testText');
	$result = $client->sendSmsManyToMany('test', 'test', '50001,50002,50003', '1111,2222,3333', array('text1','text2','text3'));
	//$result = $client->getCredit('hamidreza', 'password');
	//$result = $client->changePassword('hamidreza', 'nn', 'password');
	Zend_Debug::dump($result);

?>









