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
	//$result = $client->sendSmsOneToMany('Hamid', '123', '123', '111,222', 'testText');
	//Zend_Debug::dump($result);
	$result = $client->sendSmsManyToMany('hamid', '123', '123,123', '111,222', array('text1','text2'));
	Zend_Debug::dump($result);
?>