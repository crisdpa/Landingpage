<?php
	
	$access_client_request = $_GET['client'];
	$access_token_request = $_GET['token'];
	
	if($config -> access_client == $access_client_request and $config -> access_token == $access_token_request){
		
		$filter_date_begin = $core -> cleanVar($_GET['filter_date_begin']);
        $filter_date_end = $core -> cleanVar($_GET['filter_date_end']);
		$filter_source = $core -> cleanVar($_GET['source']);
		
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
		
		$params['order'] = array('created_date' => 'DESC');
		
		$leads_list = $db -> select('leads', $params);
		
		require_once ('classes/PHPExcel.php');
		
	  	$objPHPExcel = new PHPExcel();

	  	$objPHPExcel -> getProperties() -> setCreator("Virket")
									  	-> setLastModifiedBy("Virket")
										-> setTitle("reporte_leads")
										-> setSubject("Reporte Leads")
										-> setDescription("Reporte de leads")
										-> setKeywords("")
										-> setCategory("");
										
		$objPHPExcel -> setActiveSheetIndex(0)
	  											-> setCellValue('A1', 'ID')
            									-> setCellValue('B1', 'Nombre')
												-> setCellValue('C1', 'Teléfono')
												-> setCellValue('D1', 'Email')
												-> setCellValue('E1', 'Comentarios')
												-> setCellValue('F1', 'IP')
												-> setCellValue('G1', 'Origen')
												-> setCellValue('H1', 'Fecha de creación');
												
		
		$row_counter = 2;
	  
	  	foreach($leads_list as $record){
			
			$objPHPExcel -> setActiveSheetIndex(0) -> setCellValue('A'.$row_counter, $record -> id)
												   -> setCellValue('B'.$row_counter, $record -> name)
												   -> setCellValue('C'.$row_counter, $record -> phone)
												   -> setCellValue('D'.$row_counter, $record -> email)
												   -> setCellValue('E'.$row_counter, $record -> comments)
												   -> setCellValue('F'.$row_counter, $record -> ip)
												   -> setCellValue('G'.$row_counter, $record -> source)
												   -> setCellValue('H'.$row_counter, $record -> created_date)
												   ;
												   
										
			$row_counter ++;
			
		}
		
		$objPHPExcel->getActiveSheet()->setTitle("reporte");
		$objPHPExcel->setActiveSheetIndex(0);
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="reporte_leads.xls"');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header ('Cache-Control: cache, must-revalidate');
		header ('Pragma: public');
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter -> save('php://output');
		
		exit;
		
	}
	else{
		$core -> redirect($config -> domain);
	}
	
?>