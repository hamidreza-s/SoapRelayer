<?php
	// Define path to application directory
	defined('APPLICATION_PATH')
		|| define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../apps'));

	// Ensure library/ is on include_path
	set_include_path(implode(PATH_SEPARATOR, array(
		realpath(APPLICATION_PATH . '/../apps/library'),
		APPLICATION_PATH.'/../apps/library/Zend',
		get_include_path(),
	)));	
	
	// Include Zend Autoloader
	require "Zend/Loader/Autoloader.php";
	$autoloader = Zend_Loader_Autoloader::getInstance();
?>