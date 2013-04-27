<?php
$microtime = microtime(true);
$uniqid = mt_rand(0,100) . $microtime;
echo $microtime;
echo '<br/>';
echo $uniqid
	
?>