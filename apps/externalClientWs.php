<?php
	// Include Bootstrap
	require_once './DB.php';
	require_once './Loader.php';
	
	try
	{
		// Initialize Client
		$client = new Zend_Soap_Client(null,
			array(
				//'location'	=>	'http://local.relay.armaghan.com/public/internalServerWs.php?handle',
				'location'	=>	'http://95.142.225.103/internalServerWs.php',
				//'uri'			=>	'http://local.relay.armaghan.com/public/internalServerWs.php?handle'
				'uri'			=>	'http://95.142.225.103/internalServerWs.php'
			)
		);
		
		// Set WSDL to client
		//$client->setWsdl('http://local.relay.armaghan.com/public/internalServerWs.php?wsdl');
		$client->setWsdl('http://95.142.225.103/internalServerWs.php?wsdl');
	}
	catch (SoapFault $s) { Zend_Debug::dump($s); }
	catch (Exception $e) { Zend_Debug::dump($e); }
	
	$start = microtime(true);
	// Fire!
	for ($i=1; $i < 100000; $i++)
	{
	$result = $client->sendSmsOneToMany('mahdi', 'mahdi', '50001', '1111,2222', 'hamid');
	//$result = $client->sendSmsManyToMany('test', 'test', '50001,50002,50003', '1111,2222,3333', array('text1','text2','text3'));
	//$result = $client->getCredit('hamidreza', 'password');
	//$result = $client->changePassword('hamidreza', 'nn', 'password');
	//$result = $client->getStatus('149352141401431');
	Zend_Debug::dump($result);
	//Zend_Debug::dump($client->getLastRequest());
	//Zend_Debug::dump($client->getLastResponse());
	}
	$end = microtime(true);
	$duration = $end - $start;
	echo "$duration";
	

?>









