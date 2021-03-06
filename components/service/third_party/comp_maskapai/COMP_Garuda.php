<? 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Garuda extends Comp_maskapai_base {


	function __construct(){
		parent::__construct();		
		//define variable
		$this->_cookies_file = './components/service/third_party/comp_maskapai/cookies/garuda.txt';
		$this->login_url = 'http://gos.garuda-indonesia.com/saci/client.php';
		$this->_refer_url = 'http://gos.garuda-indonesia.com/sac/';
		$this->src_url = 'http://gos.garuda-indonesia.com/saci/clientavail.php';
		$this->book_url = 'http://gos.garuda-indonesia.com/saci/clientbook.php';						  
			
		$this->idd = '170111A';
		$this->username = 'sa3jado';
		$this->password = 'sa3jado';
		
		/*$this->_opt->date_depart =  '2012-02-13';
		$this->_opt->date_return =  null;
		$this->_opt->adult = 2;
		$this->_opt->route_from = 'DPS';
		$this->_opt->route_to = 'JOG';
		$this->_opt->id = null;
		$this->_opt->max_fare = 5;*/
		
		$this->_ci->load->library('my_curl');
	}
	
		
	function login(){	
																
		$post_data = array(			
			'idd' 	   => $this->idd,
			'username' => $this->username,
			'password' => $this->password
		);		
				
		
		$conf = array(
			'url' 				=> $this->login_url,
			'timeout'			=> 150,
			'header'			=> 0,
			'nobody'			=> true,
			'followlocation'	=> 1,
			'cookiejar' 		=> $this->_cookies_file,
			'cookiefile' 		=> $this->_cookies_file,
			'returntransfer'	=> 1,
			'post'				=> true,
			'referer' 			=> $this->_refer_url,
			'postfields' 		=> http_build_query($post_data , NULL, '&'),
			'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2'
		);
		
		$data = $this->_ci->my_curl->setup($conf);
		$res = $this->_ci->my_curl->exc($data);
				
		return json_decode($res)->Session->SessionId;							
	}
	
	public function doSearch($opt=array()){
		
		//foreach($opt as $key => $val) $this->_opt->$key = $val;
		
		$this->_opt->date_depart =  '2012-03-20';
		$this->_opt->date_return =  null;
		$this->_opt->adult = 1;
		$this->_opt->child = 2;
		$this->_opt->infant = 0;
		$this->_opt->route_from = 'BDJ';
		$this->_opt->route_to = 'BTH';
		$this->_opt->id = null;
		$this->_opt->max_fare = 5;
				
		//print_r($this->src_flight());
		foreach($opt as $key => $val) $this->_opt->$key = $val;
		$fare_result = $this->src_flight();
				
		if($fare_result == null) 
			throw new ResultFareNotFound($log);
		else 
			return $fare_result;
	}
	
	function src_flight(){
		$depart_flight = array();
		$return_flight = array();
		if($this->_opt->date_return!=null){
			$depart_flight = $this->src('depart');
			
			//swap route
			$temp = $this->_opt->route_from;
			$this->_opt->route_from = $this->_opt->route_to;
			$this->_opt->route_to = $temp;
			
			$this->_opt->date_depart = $this->_opt->date_return;
			
			$return_flight = $this->src('return');
		}else{
			$depart_flight = $this->src('depart');
		}
		
		//print_r($depart_flight);				
		//print_r($return_flight);
		
		return array_merge($depart_flight, $return_flight);
	}
	
	function src($flight_type){
				
		//define variable
		$this->ssx = $this->login(); //get session id
		$this->idd = '170111A';		
						
		$idx=0;										
		
		//curl economy config
		$post_data_economy = array(			
			'idd'=>$this->idd,
			'ssx'=>$this->ssx,
			'Triptype'=> 'o',
			'dOriginLocation'=> $this->_opt->route_from,
			'dDestinationLocation'=> $this->_opt->route_to,
			'dDepartureDate'=> $this->_opt->date_depart,
			'ServiceClass' => 'Economy',
			'aPassengers'=> $this->_opt->adult,
			'cPassengers'=> $this->_opt->child
		);
				
		
		$conf1 = array(
			'url' 				=> $this->src_url,
			'timeout'			=> 150,
			'header'			=> 0,
			'nobody'			=> true,
			'followlocation'	=> 1,
			'cookiejar' 		=> $this->_cookies_file,
			'cookiefile' 		=> $this->_cookies_file,
			'returntransfer'	=> 1,
			'post'				=> true,
			'referer' 			=> $this->_refer_url,
			'postfields' 		=> http_build_query($post_data_economy , NULL, '&'),
			'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2'
		);
		//////
		
		//curl executive config
		$post_data_executive = array(			
			'idd'=>$this->idd,
			'ssx'=>$this->ssx,
			'Triptype'=> 'o',
			'dOriginLocation'=> $this->_opt->route_from,
			'dDestinationLocation'=> $this->_opt->route_to,
			'dDepartureDate'=> $this->_opt->date_depart,
			'ServiceClass' => 'Executive',
			'aPassengers'=> $this->_opt->adult,
			'cPassengers'=> $this->_opt->child
		);
																
		$conf2 = array(
			'url' 				=> $this->src_url,
			'timeout'			=> 150,
			'header'			=> 0,
			'nobody'			=> true,
			'followlocation'	=> 1,
			'cookiejar' 		=> $this->_cookies_file,
			'cookiefile' 		=> $this->_cookies_file,
			'returntransfer'	=> 1,
			'post'				=> true,
			'referer' 			=> $this->_refer_url,
			'postfields' 		=> http_build_query($post_data_executive , NULL, '&'),
			'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2'
		);					
		///////
		
		
		$cu_economy = curl_init();
		$cu_executive = curl_init();		
		
		//set curl option
		$data_economy = $this->_ci->my_curl->setup_($conf1);
		$data_executive = $this->_ci->my_curl->setup_($conf2);
		
		//get returntransfer		
		$res_all = $this->_ci->my_curl->multi_curl_exc($data_economy, $data_executive);		
		$res_eco = $res_all[0];
		$res_exe = $res_all[1];									
		
		
		
		
		
		//get flight and fare info (economy)
		$final_data_eco = array();
		if(isset(json_decode($res_eco)->AvailabilityResult)){ //ensure there is a flight available			
			$data_eco = json_decode($res_eco)->AvailabilityResult->Depart->AvailabilityInformation->Option; //get available flight
			$fare_eco = json_decode($res_eco)->AvailabilityResult->FareInfomation->FareInfo; //get available fare
			$fare_list = str_split(json_decode($res_eco)->AvailabilityResult->FareInfomation->FareList); //get fare class list		
			
			$fare_info_eco = json_decode($res_eco)->AvailabilityResult->FareInfomation;
			
			
			
			$bsr_eco = $fare_info_eco->BSR;
			$this->vat_percent_eco = $fare_info_eco->VAT;
			$this->iwjr_eco = $fare_info_eco->IWJR;
			$this->yi_eco   = $fare_info_eco->YI;
			
			
			if(is_array($data_eco)){ //more than one flight avail
				foreach($data_eco as $r){				
					if(is_array($r->FlightSegment)){ //check whether the flight is undirect		
						
						//TRANSIT FLIGHT						
						$transit = true;
						$route = $r->FlightSegment[0]->DepartureAirport.",".$r->FlightSegment[0]->ArrivalAirport.",".$r->FlightSegment[1]->ArrivalAirport;						
						$class_avail = $r->FlightSegment[0]->BookingClassAvail;
						$upper_limit = $this->getUpperLimit($class_avail,$fare_list); //get fare class upper limit			
						for ($i=0; $i<=$upper_limit; $i++){
							foreach($fare_eco as $fare){					
								if($fare->FareClass==$fare_list[$i]){//find the match 'fare class'						
									//define return variable
									$final_data_eco[$idx] = array(
										'company' => 'GARUDA',
										't_depart' => date('Y-m-d H:i:s',strtotime($r->FlightSegment[0]->DepartureTime)),
										't_transit_arrive' => date('Y-m-d H:i:s',strtotime($r->FlightSegment[0]->ArrivalTime)),
										't_transit_depart' => date('Y-m-d H:i:s',strtotime($r->FlightSegment[1]->DepartureTime)),
										't_arrive' => date('Y-m-d H:i:s',strtotime($r->FlightSegment[1]->ArrivalTime)),
										'type' => $flight_type,							
										'class' => $fare->FareClass,
										'price' => '',										
										'flight_no' => $r->FlightSegment[0]->FlightNumber,
										'log_id' => $this->_opt->id,
										'log_id' => '',
										'route' => $route,
										'meta_data' => array(														
											'flight_number_transit' => $r->FlightSegment[1]->FlightNumber,
											'fare' => $fare->PublishFare,
											'fare_c' => $fare->BasicFarec,
											'bfare' => '',
											'fare_code' => $fare->FareBasisCode,											
											'segment_no' => $r->FlightSegment[0]->SegmentNo,											
											'adult' => $this->_opt->adult,
											'child' => $this->_opt->child
										)					
									);									
									$additionalData = $this->setPrice($final_data_eco[$idx],true);
									$final_data_eco[$idx]['price'] = $additionalData['final_price'];
									$final_data_eco[$idx]['meta_data']['bfare'] = $additionalData['bfare'];			
									$final_data_eco[$idx]['meta_data']['fare'] = $additionalData['fare'];
									$final_data_eco[$idx]['meta_data']['fare_c'] = $additionalData['fare_c'];
									$final_data_eco[$idx]['meta_data'] = json_encode($final_data_eco[$idx]['meta_data']);				
								}
							}//end of inner foreach	
							$idx++;			
						}//end for
					}else{
						//more than one flight avail
						//DIRECT FLIGHT
						$route = $r->FlightSegment->DepartureAirport.",".$r->FlightSegment->ArrivalAirport;						
						$upper_limit = $this->getUpperLimit($r->FlightSegment->BookingClassAvail,$fare_list); //get fare class upper limit
						for ($i=0; $i<=$upper_limit; $i++){
							foreach($fare_eco as $fare){					
								if($fare->FareClass==$fare_list[$i]){	//find the match 'fare class'											
									//define return variable
									$final_data_eco[$idx]= array(
										'company' => 'GARUDA',
										't_depart' => date('Y-m-d H:i:s',strtotime($r->FlightSegment->DepartureTime)),
										't_transit_arrive' => '',
										't_transit_depart' => '',
										't_arrive' => date('Y-m-d H:i:s',strtotime($r->FlightSegment->ArrivalTime)),
										'type' => $flight_type,							
										'class' => $fare->FareClass,
										'price' => '',										
										'flight_no' => $r->FlightSegment->FlightNumber,
										'log_id' => $this->_opt->id,
										'route' => $route,										
										'meta_data' => array(											
											'flight_number_transit' => '',		
											'fare' => $fare->PublishFare,
											'fare_c' => $fare->BasicFarec,
											'bfare' => '',											
											'fare_code' => $fare->FareBasisCode,
											'segment_no' => $r->FlightSegment->SegmentNo,
											'adult' => $this->_opt->adult,
											'child' => $this->_opt->child											
									)
								);
								$additionalData = $this->setPrice($final_data_eco[$idx]);
								$final_data_eco[$idx]['price'] = $additionalData['final_price'];
								$final_data_eco[$idx]['meta_data']['bfare'] = $additionalData['bfare'];			
								$final_data_eco[$idx]['meta_data']['fare'] = $additionalData['fare'];
								$final_data_eco[$idx]['meta_data']['fare_c'] = $additionalData['fare_c'];
								$final_data_eco[$idx]['meta_data'] = json_encode($final_data_eco[$idx]['meta_data']);
								$idx++;								
								}
							}//end of inner foreach			
						}//end for		
					}
				}//end of outer foreach
			}else{ //if only one flight available
				
				
				$r = $data_eco;
				if(is_array($r->FlightSegment)){ //check whether the flight is undirect								
					
					//TRANSIT FLIGHT, ONLY ONE AVAILABLE
					$transit = true;
					$route = $r->FlightSegment[0]->DepartureAirport.",".$r->FlightSegment[0]->ArrivalAirport.",".$r->FlightSegment[1]->ArrivalAirport;
					$class_avail = $r->FlightSegment[0]->BookingClassAvail;
					$upper_limit = $this->getUpperLimit($class_avail,$fare_list); //get fare class upper limit			
					for ($i=0; $i<=$upper_limit; $i++){
						foreach($fare_eco as $fare){					
							if($fare->FareClass==$fare_list[$i]){//find the match 'fare class'						
								//define return variable
								$final_data_eco[$idx] = array(
									'company' => 'GARUDA',
									't_depart' => date('Y-m-d H:i:s',strtotime($r->FlightSegment[0]->DepartureTime)),
									't_transit_arrive' => date('Y-m-d H:i:s',strtotime($r->FlightSegment[0]->ArrivalTime)),
									't_transit_depart' => date('Y-m-d H:i:s',strtotime($r->FlightSegment[1]->DepartureTime)),
									't_arrive' => date('Y-m-d H:i:s',strtotime($r->FlightSegment[1]->ArrivalTime)),
									'type' => $flight_type,							
									'class' => $fare->FareClass,
									'price' => '',									
									'flight_no' => $r->FlightSegment[0]->FlightNumber,
									'log_id' => $this->_opt->id,
									'route' => $route,
									/*
									'log' => array(
											'id' => '',
											'date_depart' => date('Y-m-d H:i:s',strtotime($r->FlightSegment[0]->DepartureTime)),
											'date_return' => '',
											'route_from' => $this->_opt->route_from,
											'route_to' => $this->_opt->route_to,
											'adult' => $this->_opt->adult,
											'comp_include' => json_encode(array("Sriwijaya","Garuda","Merpati","Batavia","Citilink")),
											'c_time' => '',
											'max_fare' => $this->_opt->max_fare,
											'actor' => 'CUS',
									),
									*/
									'meta_data' => array(														
											'flight_number_transit' => $r->FlightSegment[1]->FlightNumber,
											'fare' => $fare->BasicFare,
											'fare_c' => $fare->BasicFarec,
											'bfare' => '',
											'fare_code' => $fare->FareBasisCode,											
											'segment_no' => $r->FlightSegment[0]->SegmentNo,											
											'adult' => $this->_opt->adult,
											'child' => $this->_opt->child
										)							
									);									
									$additionalData = $this->setPrice($final_data_eco[$idx],true);
									$final_data_eco[$idx]['price'] = $additionalData['final_price'];
									$final_data_eco[$idx]['meta_data']['bfare'] = $additionalData['bfare'];			
									$final_data_eco[$idx]['meta_data']['fare'] = $additionalData['fare'];
									$final_data_eco[$idx]['meta_data']['fare_c'] = $additionalData['fare_c'];
									$final_data_eco[$idx]['meta_data'] = json_encode($final_data_eco[$idx]['meta_data']);			
							}
						}//end of inner foreach	
						$idx++;			
					}//end for
				}else{
					
					//DIRECT FLIGHT, ONLY ONE AVAILABLE
					$route = $r->FlightSegment->DepartureAirport.",".$r->FlightSegment->ArrivalAirport;
					$upper_limit = $this->getUpperLimit($r->FlightSegment->BookingClassAvail,$fare_list); //get fare class upper limit
					for ($i=0; $i<=$upper_limit; $i++){
						foreach($fare_eco as $fare){					
							if($fare->FareClass==$fare_list[$i]){	//find the match 'fare class'											
								//define return variable
								$final_data_eco[$idx]= array(
									'company' => 'GARUDA',
									't_depart' => date('Y-m-d H:i:s',strtotime($r->FlightSegment->DepartureTime)),
									't_transit_arrive' => '',
									't_transit_depart' => '',
									't_arrive' => date('Y-m-d H:i:s',strtotime($r->FlightSegment->ArrivalTime)),
									'type' => $flight_type,							
									'class' => $fare->FareClass,
									'price' => '',
									//'price' => $fare->PublishFare,
									'flight_no' => $r->FlightSegment->FlightNumber,
									'log_id' => $this->_opt->id,
									'route' => $route,
									/*
									'log' => array(
										'id' => '',
										'date_depart' => date('Y-m-d H:i:s',strtotime($r->FlightSegment->DepartureTime)),
										'date_return' => '',
										'route_from' => $this->_opt->route_from,
										'route_to' => $this->_opt->route_to,
										'adult' => $this->_opt->adult,
										'comp_include' => json_encode(array("Sriwijaya","Garuda","Merpati","Batavia","Citilink")),
										'c_time' => '',
										'max_fare' => $this->_opt->max_fare,
										'actor' => 'CUS',
									),
									*/
									'meta_data' => array(											
										'flight_number_transit' => '',		
										'fare' => $fare->PublishFare,
										'fare_c' => $fare->BasicFarec,
										'bfare' => '',											
										'fare_code' => $fare->FareBasisCode,
										'segment_no' => $r->FlightSegment->SegmentNo,											
										'adult' => $this->_opt->adult,
										'child' => $this->_opt->child
									)
								);
								$additionalData = $this->setPrice($final_data_eco[$idx]);
								$final_data_eco[$idx]['price'] = $additionalData['final_price'];
								$final_data_eco[$idx]['meta_data']['bfare'] = $additionalData['bfare'];			
								$final_data_eco[$idx]['meta_data']['fare'] = $additionalData['fare'];
								$final_data_eco[$idx]['meta_data']['fare_c'] = $additionalData['fare_c'];
								$final_data_eco[$idx]['meta_data'] = json_encode($final_data_eco[$idx]['meta_data']);
								$idx++;								
							}
						}//end of inner foreach			
					}//end for		
				}
			}//end else
		}			
	
		//-------end of processing data economy-------------------------------------//
										
		
		//get flight and fare info (executive)--------------------------------------//
		$final_data_exe = array();
		if(isset(json_decode($res_exe)->AvailabilityResult)){ //ensure there is a flight available			
			$data_exe = json_decode($res_exe)->AvailabilityResult->Depart->AvailabilityInformation->Option;
			$fare_exe = json_decode($res_exe)->AvailabilityResult->FareInfomation->FareInfo;
			
			$fare_info_exe = json_decode($res_exe)->AvailabilityResult->FareInfomation;
			$bsr_exe = $fare_info_exe->BSR;
			$this->vat_percent_exe = $fare_info_exe->VAT;
			$this->iwjr_exe = $fare_info_exe->IWJR;
			$this->yi_exe   = $fare_info_exe->YI;
			
			if(is_array($data_exe)){
				foreach ($data_exe as $s) {
					if(is_array($s->FlightSegment)){ //check whether the flight is undirect
							
							//TRANSIT FLIGHT EXECUTIVE							
							$transit = true;
							$route = $s->FlightSegment[0]->DepartureAirport.",".$s->FlightSegment[0]->ArrivalAirport.",".$s->FlightSegment[1]->ArrivalAirport;													
							//define return variable for undirect route
							$final_data_exe[$idx] = array(
								'company' => 'GARUDA',
								't_depart' => date('Y-m-d H:i:s',strtotime($s->FlightSegment[0]->DepartureTime)),
								't_transit_arrive' => date('Y-m-d H:i:s',strtotime($s->FlightSegment[0]->ArrivalTime)),
								't_transit_depart' => date('Y-m-d H:i:s',strtotime($s->FlightSegment[1]->DepartureTime)),
								't_arrive' => date('Y-m-d H:i:s',strtotime($s->FlightSegment[1]->ArrivalTime)),
								'type' => $flight_type,							
								'class' => $fare_exe->FareClass,
								//'price' => $this->setPrice($iwjr_exe,$yi_exe,$vat_percent_exe,$fare_exe->PublishFare,$transit),
								'price' => '',
								'flight_no' => $s->FlightSegment[0]->FlightNumber,
								'log_id' => $this->_opt->id,
								'route' => $route,
								/*
								'log' => array(
									'id' => '',
									'date_depart' => date('Y-m-d H:i:s',strtotime($s->FlightSegment[0]->DepartureTime)),
									'date_return' => '',
									'route_from' => $this->_opt->route_from,
									'route_to' => $this->_opt->route_to,
									'adult' => $this->_opt->adult,
									'comp_include' => json_encode(array("Sriwijaya","Garuda","Merpati","Batavia","Citilink")),
									'c_time' => '',
									'max_fare' => $this->_opt->max_fare,
									'actor' => 'CUS',
								),	
								*/							
								'meta_data' => array(																		
									'flight_number_transit' => $s->FlightSegment[1]->FlightNumber,
									'fare' => $fare_exe->PublishFare,
									'fare_c' => $fare_exe->BasicFarec,
									'bfare' => '',									
									'fare_code' => $fare_exe->FareBasisCode,
									'segment_no' => $s->FlightSegment[0]->SegmentNo,									
									'adult' => $this->_opt->adult,
									'child' => $this->_opt->child
								)							
							);
							$additionalData = $this->setPrice($final_data_exe[$idx],true);
							$final_data_exe[$idx]['price'] = $additionalData['final_price'];
							$final_data_exe[$idx]['meta_data']['bfare'] = $additionalData['bfare'];			
							$final_data_exe[$idx]['meta_data']['fare'] = $additionalData['fare'];
							$final_data_exe[$idx]['meta_data']['fare_c'] = $additionalData['fare_c'];
							$final_data_exe[$idx]['meta_data'] = json_encode($final_data_exe[$idx]['meta_data']);
							
					}else{	
						
						//DIRECT FLIGHT EXECUTIVE
						$route = $s->FlightSegment->DepartureAirport.",".$s->FlightSegment->ArrivalAirport;
						//define return variable					
						$final_data_exe[$idx] = array(								
							'company' => 'GARUDA',
							't_depart' => date('Y-m-d H:i:s',strtotime($s->FlightSegment->DepartureTime)),
							't_transit_arrive' => '',
							't_transit_depart' => '',
							't_arrive' => date('Y-m-d H:i:s',strtotime($s->FlightSegment->ArrivalTime)),
							'type' => $flight_type,							
							'class' => $fare_exe->FareClass,
							//'price' => $fare_exe->PublishFare,
							'price' => '',
							'flight_no' => $s->FlightSegment->FlightNumber,
							'log_id' => $this->_opt->id,
							'route' => $route,
							/*
							'log' => array(
								'id' => '',
								'date_depart' => date('Y-m-d H:i:s',strtotime($s->FlightSegment->DepartureTime)),
								'date_return' => '',
								'route_from' => $this->_opt->route_from,
								'route_to' => $this->_opt->route_to,
								'adult' => $this->_opt->adult,
								'comp_include' => json_encode(array("Sriwijaya","Garuda","Merpati","Batavia","Citilink")),
								'c_time' => '',
								'max_fare' => $this->_opt->max_fare,
								'actor' => 'CUS',
							),
							*/
							'meta_data' => array(								
								'flight_number_transit' => '',								
								'bfare' => '',
								'fare' => $fare_exe->PublishFare,
								'fare_c' => $fare_exe->BasicFarec,
								'fare_code' => $fare_exe->FareBasisCode,
								'segment_no' => $s->FlightSegment->SegmentNo,
								'adult' => $this->_opt->adult,
								'child' => $this->_opt->child
						)
					);
					$additionalData = $this->setPrice($final_data_exe[$idx]);
					$final_data_exe[$idx]['price'] = $additionalData['final_price'];
					$final_data_exe[$idx]['meta_data']['bfare'] = $additionalData['bfare'];			
					$final_data_exe[$idx]['meta_data']['fare'] = $additionalData['fare'];
					$final_data_exe[$idx]['meta_data']['fare_c'] = $additionalData['fare_c'];
					$final_data_exe[$idx]['meta_data'] = json_encode($final_data_exe[$idx]['meta_data']);
					}				
					$idx++;
				}//end foreach	
			}else{ //if only one flight available
				$s= $data_exe;
				if(is_array($s->FlightSegment)){ //check whether the flight is undirect
					
					//TRANSIT FLIGHT EXECUTIVE WITH ONLY ONE FLIGHT AVAILABLE
					$transit = true;
					$route = $s->FlightSegment[0]->DepartureAirport.",".$s->FlightSegment[0]->ArrivalAirport.",".$s->FlightSegment[1]->ArrivalAirport;
					//define return variable for undirect route
					$final_data_exe[$idx] = array(
						'company' => 'GARUDA',
						't_depart' => date('Y-m-d H:i:s',strtotime($s->FlightSegment[0]->DepartureTime)),
						't_transit_arrive' => date('Y-m-d H:i:s',strtotime($s->FlightSegment[0]->ArrivalTime)),
						't_transit_depart' => date('Y-m-d H:i:s',strtotime($s->FlightSegment[1]->DepartureTime)),
						't_arrive' => date('Y-m-d H:i:s',strtotime($s->FlightSegment[1]->ArrivalTime)),
						'type' => $flight_type,							
						'class' => $fare_exe->FareClass,
						//'price' => $fare_exe->PublishFare,
						'price' => '',
						'flight_no' => $s->FlightSegment[0]->FlightNumber,
						'log_id' => $this->_opt->id,
						'route' => $route,
						/*
						'log' => array(
							'id' => '',
							'date_depart' => date('Y-m-d H:i:s',strtotime($s->FlightSegment[0]->DepartureTime)),
							'date_return' => '',
							'route_from' => $this->_opt->route_from,
							'route_to' => $this->_opt->route_to,
							'adult' => $this->_opt->adult,
							'comp_include' => json_encode(array("Sriwijaya","Garuda","Merpati","Batavia","Citilink")),
							'c_time' => '',
							'max_fare' => $this->_opt->max_fare,
							'actor' => 'CUS',
						),	
						*/
						'meta_data' => array(																	
								'flight_number_transit' => $s->FlightSegment[1]->FlightNumber,
								'fare' => $fare_exe->PublishFare,
								'fare_c' => $fare_exe->BasicFarec,
								'bfare' => '',								
								'fare_code' => $fare_exe->FareBasisCode,
								'segment_no' => $s->FlightSegment[0]->SegmentNo,								
								'adult' => $this->_opt->adult,
								'child' => $this->_opt->child
							)							
						);
						$additionalData = $this->setPrice($final_data_exe[$idx],true);
						$final_data_exe[$idx]['price'] = $additionalData['final_price'];
						$final_data_exe[$idx]['meta_data']['bfare'] = $additionalData['bfare'];			
						$final_data_exe[$idx]['meta_data']['fare'] = $additionalData['fare'];
						$final_data_exe[$idx]['meta_data']['fare_c'] = $additionalData['fare_c'];
						$final_data_exe[$idx]['meta_data'] = json_encode($final_data_exe[$idx]['meta_data']);
				}else{
					
					//DIRECT FLIGHT EXECUTIVE WITH ONLY ONE FLIGHT AVAILABLE
						
					//define return variable					
					$final_data_exe[$idx] = array(								
						'company' => 'GARUDA',
						't_depart' => date('Y-m-d H:i:s',strtotime($s->FlightSegment->DepartureTime)),
						't_transit_arrive' => '',
						't_transit_depart' => '',
						't_arrive' => date('Y-m-d H:i:s',strtotime($s->FlightSegment->ArrivalTime)),
						'type' => $flight_type,							
						'class' => $fare_exe->FareClass,
						//'price' => $fare_exe->PublishFare,
						'price' => '',
						'flight_no' => $s->FlightSegment->FlightNumber,
						'log_id' => $this->_opt->id,
						'route' => $s->FlightSegment->DepartureAirport.",".$s->FlightSegment->ArrivalAirport,
						/*
						'log' => array(
							'id' => '',
							'date_depart' => date('Y-m-d H:i:s',strtotime($s->FlightSegment->DepartureTime)),
							'date_return' => '',
							'route_from' => $this->_opt->route_from,
							'route_to' => $this->_opt->route_to,
							'adult' => $this->_opt->adult,
							'comp_include' => json_encode(array("Sriwijaya","Garuda","Merpati","Batavia","Citilink")),
							'c_time' => '',
							'max_fare' => $this->_opt->max_fare,
							'actor' => 'CUS',
						),
						*/
						'meta_data' => array(								
							'flight_number_transit' => '',								
							'bfare' => '',
							'fare' => $fare_exe->PublishFare,
							'fare_c' => $fare_exe->BasicFarec,
							'fare_code' => $fare_exe->FareBasisCode,
							'segment_no' => $s->FlightSegment->SegmentNo,
							'adult' => $this->_opt->adult,
							'child' => $this->_opt->child
						)
					);
					$additionalData = $this->setPrice($final_data_exe[$idx]);
					$final_data_exe[$idx]['price'] = $additionalData['final_price'];
					$final_data_exe[$idx]['meta_data']['bfare'] = $additionalData['bfare'];			
					$final_data_exe[$idx]['meta_data']['fare'] = $additionalData['fare'];
					$final_data_exe[$idx]['meta_data']['fare_c'] = $additionalData['fare_c'];	
					$final_data_exe[$idx]['meta_data'] = json_encode($final_data_exe[$idx]['meta_data']);
				}				
				$idx++;	
			}//end else
		}// end outer if
				
		//-----------------end of processing data executive-------------------------------------//
	/*	
		//print_r($final_data);			
		echo "<h2>Full Res Executive</h2>";
		echo $res_exe;				
		echo "<h2>Full Res Economy</h2>";
		echo $res_eco;
	*/
	
		$final_data = array_merge($final_data_eco,$final_data_exe);
		
		return $final_data;		
	}
	
	function getUpperLimit($class_avail,$fare_list){						
		if(is_array($class_avail)){				
			switch ($class_avail[1]) {
				case $fare_list[1]:
					$upper_limit = 1;
					break;
				case $fare_list[2]:
					$upper_limit = 2;
					break;
				case $fare_list[3]:
					$upper_limit = 3;
					break;
				case $fare_list[4]:
					$upper_limit = 4;
					break;
				case $fare_list[5]:
					$upper_limit = 5;
					break;
				case $fare_list[6]:
					$upper_limit = 6;
					break;
				case $fare_list[7]:
					$upper_limit = 7;
					break;
			}			
		}else{
			if($class_avail == 'Y'){
				$upper_limit = count($fare_list)-1;	
			}			
		}	
		return $upper_limit;
	}
	
	//FARE CALCULATION GARUDA//
	
	//VAT = $vat*(Basic Fare * passengers)	
	//pax = (Basic Fare * passengers) + VAT + Insurance + Extra Cover	
	
	
	//Agent Total Payment = Pax Total Payment - Agent Fee
	
	function setPrice($flight_detail=null,$transit=false){		
				
		$meta_data=$flight_detail['meta_data'];
		$departure_date = date('Y-m-d',strtotime($flight_detail['t_depart']));		
		$fare = $meta_data['fare'];
		$fare_c = $meta_data['fare_c'];
		
		$twoWeeksLater =  date('Y-m-d', mktime(0,0,0,date('m'),date('d')+14,date('Y'))); 		
		
		if($departure_date>=$twoWeeksLater){		
			$fare = ceil(($fare/1000)*0.9) * 1000;			
			$fare_c = ceil(($fare_c/1000)*0.9) * 1000;
		}
						
		$total_fare_without_taxes = $fare * $this->_opt->adult + $fare_c * $this->_opt->child;
		
		if($flight_detail['class']!='C'){
			$vat_percent = $this->vat_percent_eco;
			$iwjr = $this->iwjr_eco * ($this->_opt->adult + $this->_opt->child);
			$yi   = $this->yi_eco * ($this->_opt->adult + $this->_opt->child);
		}else{
			$vat_percent = $this->vat_percent_exe;
			$iwjr = $this->iwjr_exe * ($this->_opt->adult + $this->_opt->child);
			$yi   = $this->yi_exe * ($this->_opt->adult + $this->_opt->child);
		}
		
						
		if($transit){			
			$iwjr=$iwjr*2;
			$yi=$yi*2;
		}
										
		$vat = $total_fare_without_taxes * $vat_percent;						
		
		$bfare = $fare."*ID|".$vat."*IW|".$iwjr;
		
				
		return array(
			'final_price' => $total_payment = $total_fare_without_taxes + $vat + $iwjr + $yi,
			'bfare' => $bfare,
			'fare' => $fare,
			'fare_c' => $fare_c
		);
	}
			
	//check latest fare flight before book
	function prebook($flight_detail=null){		
		
		$meta_data = json_decode($flight_detail['meta_data']);
		
		$transit = false;
					
		$routeArr = explode(',',$flight_detail['route']);
		$route_first = $routeArr[0];
		$route_second = $routeArr[1];
		$route_third = '';
		if(isset($routeArr[2])){
			$route_third = $routeArr[2];
			$transit =true;
		} 
		
		$adult = $meta_data->adult;
		$child = $meta_data->child;
		$paxc = 0; //boolean for any child pax
		if($child!=0) $paxc = 1;
		
		$this->ssx =  $this->login();
		$post_data = array(
			'idd' => $this->idd,
			'userid' => $this->username,
			'BSR' => 1, //from meta data
			'ssx' =>  $this->ssx,
			'Triptype' => 'o', //round trip = false
			'mode' => '1',
			'paxc' => $paxc,
			'allpax' => $adult + $child, //total passengers
			'dfs1depsta' => $route_first,
			'dfs1arrsta' => $route_second,
			'dfs1fltno' => $flight_detail['flight_no'], //from meta data
			'dfs1segno' => $meta_data->segment_no, //
			'dfs1deptim' => date('Y-m-d H:i',strtotime($flight_detail['t_depart'])), //departure time
			'dfs1arrtim' => date('Y-m-d H:i',strtotime($flight_detail['t_arrive'])), //arrival time
			'dfs1fclass' => $flight_detail['class'], //from meta data, fare class
			'dfifcode' => $meta_data->fare_code, //from meta data
			'dfifclass' => $flight_detail['class'], //from meta data, fare class
			'dfibfare' => $meta_data->fare, //from meta data, fare
			'dfitcode' => 'X',
			'dfs2depsta' => '', //transit only
			'dfs2arrsta' => $route_third, //transit only
			'dfs2fltno' => $meta_data->flight_number_transit, //transit only
			'dfs2deptim' => '', //transit only
			'dfs2arrtim' => '', //transit only
			'dfs2fclass' => '', //transit only	
		);
		

		if($transit) {
			$post_data['dfs1arrtim'] = date('Y-m-d H:i',strtotime($flight_detail['t_transit_arrive'])); //transit only
			$post_data['dfs2deptim'] = date('Y-m-d H:i',strtotime($flight_detail['t_transit_depart'])); //transit only
			$post_data['dfs2arrtim'] = date('Y-m-d H:i',strtotime($flight_detail['t_arrive'])); //transit only
			$post_data['dfs2fclass'] = $flight_detail['class'];
			$post_data['dfs2depsta'] = $route_second;
		}				
		
		//print_r($post_data);
		//echo "<br/>";
		
		$conf = array(
			'url' 				=> $this->book_url,
			'timeout'			=> 150,
			'header'			=> 0,
			'nobody'			=> true,
			'followlocation'	=> 1,
			'cookiejar' 		=> $this->_cookies_file,
			'cookiefile' 		=> $this->_cookies_file,
			'returntransfer'	=> 1,
			'post'				=> true,
			'referer' 			=> $this->_refer_url,
			'postfields' 		=> http_build_query($post_data, NULL, '&'),
			'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2'
		);
		
		$this->_ci->my_curl->setup($conf);
		$exc = $this->_ci->my_curl->exc();
		
		//exception if prebook fail
		$err_msg = json_encode(
			array(
			'Error' => array(
				'Code' => '10',
				'Message' => "Can't book flight "
				)
			));														
		
		$incorrect_res = "Incorrect response id (request id: jsonRPCClient Object<br />
							(<br />
							    [debug:private] => <br />
							    [url:private] => http://192.168.16.228/ibookxmlet/rpc/servicerpc.php<br />
							    [id:private] => 1<br />
							    [notification:private] => <br />
							    [proxy] => <br />
							)<br />
							1, response id: )<br />";
			
		if($exc == $err_msg || $exc == $incorrect_res){
			return FALSE;
		}
		
		//echo "Result prebook : ".$exc."<br/><br/>";
		
		$bookRes = json_decode($exc)->BookResult;
		//print_r($exc);		
		//echo "<br/>";
		
		//EXTRACT FROM BOOK RES
		$basicFare = $bookRes->BFare;
		
		$vat = explode('|',$bookRes->Tax->option[0]);
		$vat = $vat[1];
		$iwjr = explode('|',$bookRes->Tax->option[1]);
		$iwjr = $iwjr[1];
		$yi = explode('|',$bookRes->Tax->option[2]);
		$yi = $yi[1];
				
		//
		
		$total_vat = $vat*($adult);
		if($child>0) $total_vat = $total_vat + ($bookRes->taxc*$child);
																
		$bFare = $basicFare."*ID|".$total_vat."*IW|".$iwjr*($adult+$child);
		
		if($child>0) $bFare_c = $bookRes->BFarec;
								
		if($basicFare<=$meta_data->fare){
			if($child>0 && $bFare_c<=$meta_data->fare_c) return array('bFare' => $bFare,'bFare_c' => $meta_data->fare_c);
			else if($child>0 && $bFare_c>$meta_data->fare_c) return array('bFare' => $bFare,'bFare_c' => $bFare_c);
			else return array('bFare' => $meta_data->bfare,'bFare_c' => '');
		}else return array('bFare' => $bFare,'bFare_c' => '');
	}
	
	function testBook(){		
		//DUMMY PAXTITLE
		$customer_data = array(
			'id' => '55',
			'f_name' => 'Zidni',
			'l_name' => 'Mubarock',
			'email' => 'lavalyst@gmail.com',
			'password' => 'aca9fd21ff5e08cf88a3929ef5c4f346',
			'role_id' => 1,
			'c_time' => date('Y-m-d H:i:s',strtotime('2011-12-11 21:04:04')),
			'm_time' => '',
			'status' => 'active',
			'actv_key' => '',
			'user_detail' => array(
				'id' => 10,
				'user_id' => 26,
				'phone' => '02518329245',
				'mobile' => '085697586581',
				'address' => 'Jl. Pajajaran Bogor',
				'gender' => 'M',
			)						
		);
		
		$passengers_data = array(
			'ADULT' => array
				(
					0 => array(
						'name' => 'Zidni Mubarock',
						'no_id' => '3426238910220',
						'title' => 'Mr.',					
					),										
				),
			'CHILD' => array
				(
					0 => array(
						'name' => 'Zidni Mubarock',
						'no_id' => '3426238910220',
						'title' => 'Mr.',					
					),
					1 => array(
						'name' => 'Zidni Mubarock',
						'no_id' => '3426738910220',
						'title' => 'Mr.',					
							),
				)
		);
		
		//DUMMY FLIGHT DETAIL
		$flight_detail = array(
			'company' => 'GARUDA',
	        't_depart' => date('Y-m-d H:i:s',strtotime('2012-03-20 19:35:00')),
	        't_transit_arrive' => date('Y-m-d H:i:s',strtotime('2012-03-20 20:10:00')),
	        't_transit_depart' => date('Y-m-d H:i:s',strtotime('2012-03-21 06:40:00')),
	        't_arrive' => date('Y-m-d H:i:s',strtotime('2012-03-21 08:15:00')),
	        'type' => 'depart',
	        'class' => 'B',
	        'price' => '4118700',
	        'flight_no' => '537',	        
	        'log_id' => '',
	        'route' => 'BDJ,CGK,BTH',
	        'meta_data' => '{"flight_number_transit":"150","fare":1239000,"fare_c":1239000,"bfare":"1239000*ID|371700*IW|30000","fare_code":"BOXG","segment_no":"11","adult":1,"child":2}'
				);
		
		//NO TRANSIT
		/*$flight_detail = array(
			'company' => 'GARUDA',
	        't_depart' => date('Y-m-d H:i:s',strtotime('2012-03-20 06:00:00')),
	        't_transit_arrive' => '',
	        't_transit_depart' => '',
	        't_arrive' => date('Y-m-d H:i:s',strtotime('2012-03-20 07:25:00')),
	        'type' => 'depart',
	        'class' => 'L',
	        'price' => '3447000',
	        'flight_no' => '302',	        
	        'log_id' => '',
	        'route' => 'CGK,SUB',
			'meta_data' => '{"flight_number_transit":"","fare":1040000,"fare_c":780000,"bfare":"1040000*ID|312000*IW|15000","fare_code":"LOXG","segment_no":"01","adult":3,"child":0}'
				);*/
														
		return $this->doBook($flight_detail,$passengers_data,$customer_data);
	}
		
	function doBook($flight_detail,$passengers_data,$customer_data){
		
		$transit = false;		
		$meta_data = json_decode($flight_detail['meta_data']);
		$adult = $meta_data->adult;
		$child = $meta_data->child;
		//do prebook, check for the price changes								
													
		$allBasicFare = $this->prebook($flight_detail);				
		
		if($allBasicFare == FALSE){
			$message = 'fare not found , its sold out , perhaps :)';
				throw new BookingFailed($fare_data, $message);
		}
		else {
			$bfare = $allBasicFare['bFare'];
			$bfare_c = $allBasicFare['bFare_c'];
		}																			
			
		//split route
		$route = explode(',',$flight_detail['route']);		
		$route_first = $route[0];
		$route_second = $route[1];
		$route_third = '';
		
		if(isset($route[2])){
			$transit = true;
			$route_third = $route[2];
		} 
											
		$post_data = array(			
			'idd' => $this->idd,
			'userid' => $this->username,
			'BSR' => 1, //from meta data
			'ssx' =>  $this->login(),
			'Triptype' => 'o', //round trip = false
			'mode' => '',
			'bfare' => $bfare, //'1409000*ID|422700*IW|15000',
			'bfarec' => '', 
			'userbca' => 'OLP', //payment method
			'bankname' => 'GA_OLP', //payment method
			'phone' => $customer_data['user_detail']['mobile'], //phone number, from baseapp
			'email' => 'ceo@rumahtiket.com',
			'booker' => '1', //
			'allpax' => $adult + $child, //total passengers
			'dfs1depsta' => $route_first,
			'dfs1arrsta' => $route_second,
			'dfs1fltno' => $flight_detail['flight_no'], //from meta data
			'dfs1segno' => $meta_data->segment_no, //
			'dfs1deptim' => date('Y-m-d H:i',strtotime($flight_detail['t_depart'])), //departure time
			'dfs1arrtim' => date('Y-m-d H:i',strtotime($flight_detail['t_arrive'])), //arrival time
			'dfs1fclass' => $flight_detail['class'], //from meta data, fare class
			'dfifcode' => $meta_data->fare_code, //from meta data
			'dfifclass' => $flight_detail['class'], //from meta data, fare class
			'dfibfare' => $meta_data->fare, //from meta data, fare
			'dfitcode' => 'X',
			'dfs2depsta' => '', //transit only
			'dfs2arrsta' => $route_third, //transit only
			'dfs2fltno' => $meta_data->flight_number_transit, //transit only
			'dfs2deptim' => '', //transit only
			'dfs2arrtim' => '', //transit only
			'dfs2fclass' => '', //transit only			
		);
		
		if($child>0){
			$post_data['bfarec'] = $bfare_c;
		}
		
		$total_pax = $adult + $child;
		$j=1;
		for ($i=0; $i < $adult; $i++) {			
			$pax_name = explode(' ',$passengers_data['ADULT'][$i]['name'],2);
			$pax_title = strtoupper(str_replace('.','',$passengers_data['ADULT'][$i]['title']));
			$pax_fname = $pax_name[0];
			$pax_lname = $pax_name[1];
			
			$key = 'pax'.($j);									
			$pax = array(
				$key => 'ADT|'.$pax_title.'|'.$pax_fname.'|'.$pax_lname.'|',
			);						
			$post_data = array_merge($post_data,$pax);
			$j++;
		}
		
		for ($i=0; $i < $child; $i++) {			
			$pax_name = explode(' ',$passengers_data['CHILD'][$i]['name'],2);
			$pax_title = strtoupper(str_replace('.','',$passengers_data['CHILD'][$i]['title']));
			$pax_fname = $pax_name[0];
			$pax_lname = $pax_name[1];
			
			$key = 'pax'.($j);									
			$pax = array(
				$key => 'CHD|MSTR|'.$pax_fname.'|'.$pax_lname.'|5',
			);						
			$post_data = array_merge($post_data,$pax);
			$j++;
		}
		
		
		if($transit) {
			$post_data['dfs1arrtim'] = date('Y-m-d H:i',strtotime($flight_detail['t_transit_arrive'])); //transit only
			$post_data['dfs2deptim'] = date('Y-m-d H:i',strtotime($flight_detail['t_transit_depart'])); //transit only
			$post_data['dfs2arrtim'] = date('Y-m-d H:i',strtotime($flight_detail['t_arrive'])); //transit only
			$post_data['dfs2fclass'] = $flight_detail['class'];
			$post_data['dfs2depsta'] = $route_second;
		}
		
		//return $post_data;
		
		
		$conf = array(
			'url' 				=> $this->book_url,
			'timeout'			=> 150,
			'header'			=> 0,
			'nobody'			=> true,
			'followlocation'		=> 1,
			'cookiejar' 			=> $this->_cookies_file,
			'cookiefile' 			=> $this->_cookies_file,
			'returntransfer'		=> 1,
			'post'				=> true,
			'referer' 			=> $this->_refer_url,
			'postfields' 			=> http_build_query($post_data, NULL, '&'),
			'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2'
		);
		
		$this->_ci->my_curl->setup($conf);
		$exc = $this->_ci->my_curl->exc();
		
		//exception if book fail
		$err_msg = json_encode(
			array(
			'Error' => array(
				'Code' => '10',
				'Message' => "Can't book flight "
				)
			));														
		
		$incorrect_res = 'Incorrect response id (request id: jsonRPCClient Object<br />
							(<br />
							    [debug:private] => <br />
							    [url:private] => http://192.168.16.228/ibookxmlet/rpc/servicerpc.php<br />
							    [id:private] => 1<br />
							    [notification:private] => <br />
							    [proxy] => <br />
							)<br />
								1, response id: )<br />';
			
		if($exc == $err_msg || $exc == $incorrect_res || $exc == null){
			$message = 'fare not found, its sold out, perhaps :)';
				throw new BookingFailed($fare_data, $message);			
		}
		
		$bookRes = json_decode($exc)->BookResult;
		$bookingCode = $bookRes->BookingCode;
		
		//init return var
		$return_var = array(
			'booking_number' => $bookingCode,
			'fare_id' => $flight_detail['log_id'],
			'meta_data' => json_encode(array(
				'payment_code'	=> $bookRes->PaymentCode,
				'validate'		=> $bookRes->Validate,
				)),
			'final_price' => (int) $bookRes->TotalPrice
		);
		
		if($return_var['final_price'] > $flight_detail['price']){			
			throw new BookingFarePriceChanged($fare_data, $finalBookingPrice);
		}else{
			return $return_var;
		}
						
		/*{"BookResult":{"BookingCode":"RSUJXU","PaymentCode":"1261059103712","TimeLimit":"2011-12-01 11:00","TourCode":null,"FareKlas":"YOWG|","Bsr":"1","NetPrice":"2946000","Tax":"294600","Iwjr":"15000","YI":"15000","Surcharge":"NO","TotalPrice":"3270600","AgentFee":"3","KomInt":"3","pkp":"0","Discount":"0","Validate":"401 Y 12DEC DPSCGK;HARIZ\/MUBAROKMR|QADRI\/LUTHFIMR","Mode":null}}*/
		
		//print_r($post_data);
	}
	
	function index(){
		echo 'index';
	}
	// API REQUIREMENT

	public function closing()
	{
		# code...
	}
	
	

}
