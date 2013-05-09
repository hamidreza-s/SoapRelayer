<?php
error_reporting(E_ALL);

	function amqpConnection() {
		$amqpConnection = new AMQPConnection();
		$amqpConnection->setHost('172.16.16.23');
		$amqpConnection->setLogin("admin");
		$amqpConnection->setPassword("123456");
		$amqpConnection->setVhost("/");
		$amqpConnection->connect();

		if(!$amqpConnection->isConnected()) {
			die("Cannot connect to the broker, existing!\n");
		}

		return $amqpConnection;
	}

	function amqpSend($text, $routingKey, $exchangeName) {
		$amqpConnection = amqpConnection();
		$channel = new AMQPChannel($amqpConnection);
		$exchange = new AMQPExchange($channel);
		$exchange->setName($exchangeName);
		$exchange->setType('direct');
		$message = $exchange->publish($text, $routingKey);
		
		if(!$message) {
			echo "Error: Message '" . $message . "' was not sent. \n";
		} else {
			echo "Message '" . $message . "' send. \n";
		}
		
		if(!$amqpConnection->disconnect()) {
			throw new Exception("Could not disconnect!");
		}
	}
	
	for($i = 0; $i < 1000000; $i++)
	{
		amqpSend("Get new message!", "MCI", "sms-exchange");
	}
?>
