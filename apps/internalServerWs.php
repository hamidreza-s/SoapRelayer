<?php
	// Include Bootstrap
	require_once './Bootstrap.php';

	// Include nessecary classes
	require "library/Armaghan/Relay_Sms_Class.php";
	
	// get and log HTTP requests
	//$httpRequest = apache_request_headers();
	//file_put_contents('/http_request.txt', $httpRequest, FILE_APPEND | LOCK_EX);	
	
	if(isset($_GET['handle']))
	{	
		// initialize non-WSDL SOAP server
		$server = new Zend_Soap_Server(null,
			array('uri' => 'http://local.relay.armaghan.com/internalServerWs.php')
		);
		
		// set SOAP service calss
		$server->setClass('Relay_Sms_Class');
		
		// handle request
		$server->handle();
		
		// get and last requests
        $soapRequest = $server->getLastRequest();
        file_put_contents('/soap_requst.txt', $soapRequest, FILE_APPEND | LOCK_EX);
		
		// get and log last responses
        $soapResponse = $server->getLastResponse();
        file_put_contents('/soap_response.txt', $soapResponse, FILE_APPEND | LOCK_EX);
	}

	elseif(isset($_GET['wsdl']))
	{
		// set up WSDL auto-discovery
		$wsdl = new Zend_Soap_AutoDiscover();
		
		// attach Relay_Sms_Class
		$wsdl->setClass('Relay_Sms_Class');
		
		// set SOAP server URI
		$wsdl->setUri('http://local.relay.armaghan.com/internalServerWs.php');
		
		// show WSDL
		$wsdl->handle();
	}
	
	elseif(isset($_GET['functions']))
	{
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
			
			// Show Functions
			echo "<h3>Functions List:</h3>";
			echo "<pre><strong>";
			print_r($client->getFunctions());
			echo "</strong></pre>";
			
			// Show Web Service methods
			echo "<h3>Web Service Methods List:</h3>";
			echo "<pre><strong>";
			print_r(get_class_methods($client));
			echo "</strong></pre>";
			
		}
		catch (SoapFault $s) { Zend_Debug::dump($s); }
		catch (Exception $e) { Zend_Debug::dump($e); }	
	}
	
	else
	{
		echo "Web service documentation!";
	}


?>