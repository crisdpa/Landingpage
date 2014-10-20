<?php
	 
	/**
	 * PHP 5
	 *
	 * SoulPHP : Mini-Framework (http://soulphp.com)
	 * Copyright 2013, Christopher Díaz Pantoja
	 *
	 * Licensed under The MIT License
	 * Redistributions of files must retain the above copyright notice.
	 *
	 * @copyright     Copyright 2013, Copyright 2013, Christopher Díaz Pantoja
	 * @link          http://soulphp.com SoulPHP Mini-Framework
	 * @since         SoulPHP v 1.0.0
	 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
	 */
	 
	 session_start();
	
	//ini_set('display_errors','off');
	
	require_once("configuration.php");
	require_once("classes/query.class.php");
	require_once("classes/core.class.php");
	require_once("classes/xtemplate.class.php");
	require_once("classes/class.phpmailer.php");
	
	date_default_timezone_set($config->timezone);

	$tplSite = new XTemplate("templates/default.html");
	$config = new Configuration();
	
	$core = new Core();
	$db = new QueryClass(array("hostname"=>$config->host,"database"=>$config->db,"username"=>$config->user,"password"=>$config->password));
	
	$core->createSession();
	
	$qs_action = $_GET["action"];
	$qs_section = !empty($_GET["section"])?$_GET["section"]:$config->section;
	$qs_module = $_GET["module"];
	$qs_module_response = $_GET["response"];
	
	
	if(empty($qs_action)){
	
		if(!empty($qs_module)){
		
			
			if(!file_exists("modules/".$qs_module."/index.php")){
				echo "fail loading module";
			}
			else{
			
				$tplModule = new XTemplate("modules/".$qs_module."/templates/default.html");
				
				$tplModule -> assign("__SITE_DOMAIN__", $config -> domain);
				
				require_once("modules/".$qs_module."/index.php");
				
				
				
				if($qs_module_response != "json"){
				
					$tplModule -> parse("main");
					echo $tplModule -> render("main");
				
				}
				
			}
		
		}
		else{
			
			$tplSite -> assign("__SITE_TITLE__", $config->title);
			$tplSite -> assign("__META_AUTHOR__",  $config->author);
			$tplSite -> assign("__SITE_INDEX__", $config->index);
			
			if($core -> getMessage()){
				
				$message = $core -> getMessage();
				
				foreach($message['text'] as $text){
					$tplSite -> assign("__MESSAGE_TEXT__", $text);
					$tplSite -> parse("main.messages.message");
				}
				$tplSite -> assign("__MESSAGE_TYPE__", $message["type"]);
				
				$tplSite -> parse("main.messages");
				$core -> clearSession("message");
				
			}
			
			
			if(!file_exists("sections/".$qs_section."/index.php")){
				$qs_section = 'error404';
			}


			/**********************************************
			* Customer Rules
			**********************************************/
			
			$tplSite -> assign("__CURRENT_YEAR__", date('Y'));
			$tplSite -> assign("__SITE_DOMAIN__", $config -> domain);
			$tplSite -> assign("__WEBSITE_URL__", $config -> website);
			$tplSite -> assign("__WEBSITE_FACEBOOK__", $config -> facebook);
			$tplSite -> assign("__WEBSITE_TWITTER__", $config -> twitter);
			$tplSite -> assign("__WEBSITE_GPLUS__", $config -> gplus);
			
			if($qs_section != 'reportes'){
				$core -> setStyle($config -> domain.'sections/'.$qs_section.'/style.css');
			}
			
			
			/**********************************************/
			
			
			
			$tplSection = new XTemplate("sections/".$qs_section."/template.html");
			require_once("sections/".$qs_section."/index.php");
			
			$core -> setCurrentURL();
			$tplSection -> assign('__SECTION_PATH__', $config -> domain.'sections/'.$qs_section);
			
			if($qs_section != 'reportes' and $qs_section != 'error404'){
				
				$section_copy = $core -> getCopyFromFile('sections/'.$qs_section.'/copy.txt');
			
				$core->setTitle(empty($section_copy['h1'])?'':$section_copy['h1']);
				$core->setMetaDescription(empty($section_copy['main-content'])?'':$section_copy['main-content']);
				$core->setMetaKeywords(empty($section_copy['keywords'])?'':$section_copy['keywords']);
				
				$tplSection -> assign('__H2__', empty($section_copy['h2'])?'':$section_copy['h2']);
				$tplSection -> assign('__H3__', empty($section_copy['h3'])?'':$section_copy['h3']);
				$tplSection -> assign('__H3_COMPLEMENTARY__', empty($section_copy['h3-complementary'])?'':$section_copy['h3-complementary']);
				$tplSection -> assign('__CONTACT_MODULE__', $core -> loadModule($section_contact_form, $qs_section));
				
				$core -> setStyle($config -> domain.'modules/'.$section_contact_form.'/styles/default.css');
				
				if($_GET['item'] == 'gracias'){
					$tplSection -> parse("main.thanks");
				}
				else{
					$tplSection -> parse("main.landing");
				}
				
				
				
			}
			
			$tplSection -> parse("main");
			
			$tplSite -> assign("__CONTENT__", $tplSection -> render("main"));
			$tplSite -> parse("main");
			$tplSite -> out("main");
		
		}
		
		
	}
	else{
		
		if(!file_exists("actions/".$qs_action.".php")){
			header("Location: {$config -> site}error404");
		}
		else{
			require_once("actions/".$qs_action.".php");
		}
		
		
	}

?>