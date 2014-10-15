<?php
	
	$access_client_request = $_GET['client'];
	$access_token_request = $_GET['token'];
	//http://domain.com/landingpage/index.php?section=reportes&client=799ec4094ec0742812d6d0dae61110ae9a67158a&token=4d014f4c90a811f99d085f36a50b89a67f40dac0
	
	if($config -> access_client == $access_client_request and $config -> access_token == $access_token_request){
	
		$core->setTitle('Reportes');
		$core->setMetaDescription('Reportes');
		$core -> setStyle($config -> domain.'sections/reportes/style.css');
		$core -> setStyle($config -> domain.'js/vendor/jquery-ui/jquery-ui.css');
		$core -> setScript($config -> domain.'js/vendor/jquery-ui/jquery-ui.js');
		$core -> setScript($config -> domain.'js/reports.js');
		
		$tplSection -> assign('__ACCESS_CLIENT__', $access_client_request);
		$tplSection -> assign('__ACCESS_TOKEN__', $access_token_request);
		
		$respages = 20;
    	$curpage = empty($_GET['page'])?1:$_GET['page'];
		
		$filter_date_begin = $core -> cleanVar($_GET['filter_date_begin']);
        $filter_date_end = $core -> cleanVar($_GET['filter_date_end']);
		$filter_source = $core -> cleanVar($_GET['source']);
		
		$tplSection -> assign('__FILTER_DATE_BEGIN__', $filter_date_begin);
		$tplSection -> assign('__FILTER_DATE_END__', $filter_date_end);
		$tplSection -> assign('__FILTER_SOURCE__', $filter_source);
		
		$params = array('fields' => 'count');
		
		if(!empty($filter_date_begin) and !empty($filter_date_end)){
			$params['conditions'] = array("(created_date BETWEEN '{$filter_date_begin} 00:00:00' AND '{$filter_date_end} 23:59:59')");
		}
		
		if(!empty($filter_source)){
			if(count($params['conditions']) > 0){
				$params['conditions']['AND'] = "source = '{$filter_source}'";
			}
			else{
				$params['conditions'] = array("source = '{$filter_source}'");
			}
		}
		
		$leads_count = $db -> select('leads', $params);
		$paginator = $core -> pagerResults($leads_count, $respages, $curpage);
		
		unset($params['fields']);
		$params['order'] = array('created_date' => 'DESC');
		$params['limit'] = $paginator['begin'].','.$respages;
		
		$leads_list = $db -> select('leads', $params);
		
		$sources_list = $db -> select('leads', array('fields' => array('DISTINCT(source)')));
		
		foreach($sources_list as $source){
			$tplSection -> assign('__FILTER_SOURCE_VALUE__', $source -> source);
			
			if($source -> source == $filter_source){
				$tplSection -> assign('__FILTER_SOURCE_SELECTED__', 'selected="selected"');
			}
			else{
				$tplSection -> assign('__FILTER_SOURCE_SELECTED__', '');	
			}
			
			$tplSection -> parse('main.source_filter');
		}
		
		if($leads_count > 0){
			
			$tplSection -> assign('__TOTAL_LEADS__', $leads_count);
		
			foreach($leads_list as $lead){
				
				$tplSection -> assign('__LEAD_NAME__', $lead -> name);
				$tplSection -> assign('__LEAD_PHONE__', $lead -> phone);
				$tplSection -> assign('__LEAD_EMAIL__', $lead -> email);
				$tplSection -> assign('__LEAD_COMMENTS__', $lead -> comments);
				$tplSection -> assign('__LEAD_IP__', $lead -> ip);
				$tplSection -> assign('__LEAD_SOURCE__', $lead -> source);
				$tplSection -> assign('__LEAD_CREATED__', $lead -> created_date);
				
				$tplSection -> parse('main.leads.lead');
				
			}
			
			$tplSection -> assign('__TOTAL_PAGES__', $paginator["pages"]);
			
			if($paginator["pages"] > 1){ 
     		
				if($paginator["range"]["prev"] !== false){ 			
					$tplSection -> assign("__PAGER_PREV__", $paginator["range"]["prev"]); 			
					$tplSection -> parse("main.leads.pager.prev"); 		
				} 	 		
		
				foreach($paginator["links"] as $page){
					$tplSection -> assign("__PAGER_NUMBER__", $page);
					
					if($page == $curpage){
						$tplSection -> assign("__PAGER_CLASS__", "selected"); 			
					} 			
					else{
						$tplSection -> assign("__PAGER_CLASS__", ""); 			
					}
		
					$tplSection -> parse("main.leads.pager.number"); 			 		
				} 		 		
		
		
				if($paginator["range"]["next"] !== false){ 			
					$tplSection -> assign("__PAGER_NEXT__", $paginator["range"]["next"]);
					$tplSection -> parse("main.leads.pager.next");
				} 		 		
		
				$tplSection -> parse("main.leads.pager");
		
			}
			
			$tplSection -> parse('main.leads');
		
		}
		else{
			$tplSection -> parse('main.msg');	
		}
	
	}
	else{
		$core -> redirect($config -> domain);
	}

?>