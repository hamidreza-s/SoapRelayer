<?php
	var_dump(calculateStringLengh('Hey! Calc my length!'));
	
	function calculateStringLengh($text)
	{
		$textEncoding = mb_detect_encoding($text);
		$charLength = mb_strlen($text, $textEncoding);
		
		if ($textEncoding == 'UTF-8') 
		{
			$byteLength = $charLength * 2;
		}
		elseif ($textEncoding == 'ASCII') 
		{
			$byteLength = $charLength;
		}
		else
		{
			$byteLength = 'undefiend';
		}
		
		return array('textEncoding' => $textEncoding, 'charLength' => $charLength, 'byteLength' => $byteLength);
	}
?>