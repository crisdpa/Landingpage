<?php

	/*
	 * Prints a client and token string 
	 */
	
	$client = sha1(uniqid());
	$token = sha1(md5(uniqid()));
	
	echo 'client: '.$client.'<br>token: '.$token;
	
?>