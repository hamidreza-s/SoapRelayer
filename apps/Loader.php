<?php
	// Include Zend Library
	set_include_path(implode(PATH_SEPARATOR, array(
		realpath('./library'),
		get_include_path(),
	)));
	
	// Include Zend Autoloader
	require "Zend/Loader/Autoloader.php";
	$autoloader = Zend_Loader_Autoloader::getInstance();
?>