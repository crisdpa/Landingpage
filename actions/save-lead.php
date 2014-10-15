<?php
	
	$lead_name = $core -> cleanVar($_POST['name']);
	$lead_phone = $core -> cleanVar($_POST['phone_number']);
	$lead_email = $core -> cleanVar($_POST['email']);
	$lead_comments = $core -> cleanVar(strip_tags($_POST['comments']));
	$lead_source = $core -> cleanVar($_POST['source']);
	$captcha = $_POST['captcha'];
	
	$errors = array();
	$lead_fields = array(
							'name' => $lead_name,
							'phone_number' => $lead_phone,
							'email' => $lead_email,
							'comments' => $comments
					);
	
	if(empty($lead_name)){
		$errors[] = 'Indica tu nombre';
	}
	
	if(empty($lead_phone)){
		$errors[] = 'Indica tu teléfono';
	}
	
	if(empty($lead_email)){
		$errors[] = 'Indica tu e-mail';
	}
	
	else if(!preg_match( "/^[A-Za-z0-9][A-Za-z0-9_.-]*@[A-Za-z0-9_-]+\.[A-Za-z0-9_.]+[A-za-z]$/", $lead_email)){
		$errors[] = 'El e-mail indicado es incorrecto';
	}
	
	if(empty($lead_source)) {
		$errors[] = 'No se ha indicado la fuente de este contacto';
	}
	
	if(!empty($captcha)) {
		$errors[] = 'Hemos detectado que eres un robot o spammer y no podemos procesar tu solicitud';
	}
	
	if(count($errors) > 0){
		
		$core -> setSession('lead_fields', $lead_fields);
		$core -> setMessage($errors,'error');
		$core -> redirect($core -> getURL());
		
	}
	else{
		
		$lead_ip = $_SERVER['REMOTE_ADDR'];
		$created_date = date("Y-m-d H:m:i");
		
		$query = "INSERT INTO leads 
				  SET
				 		id = 0,
						name = '{$lead_name}',
						phone = '{$lead_phone}',
						email = '{$lead_email}',
						comments = '{$lead_comments}',
						ip = '{$lead_ip}',
						source = '{$lead_source}',
						created_date = '{$created_date}'
				 ";
				 
		$query_result = $db -> query($query,'',false);
		
		if($query_result){
			
			$thanks_url = ($core -> getURL() == $config -> domain)?$config -> domain.$config->section:$core -> getURL();
			$core -> redirect($thanks_url.'/gracias');
		}
		else{
			$core -> setMessage(array('Hubo un problema con tu solicitud, inténtalo nuevamente'),'error');
			$core -> redirect($core -> getURL());
		}
		
	}
	
	
	
	
?>