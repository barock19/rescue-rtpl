<? if (! defined('BASEPATH')) exit('No direct script access');

class Sriwijaya extends Comp_maskapai_base{
	
	private $username 			= 'idbooking5';
	private $password 			= 'indonesia';
	private $_default_url 		= 'https://agent.sriwijayaair.co.id/b2b/secure/home.jsp';
	private $_login_url 		= 'https://agent.sriwijayaair.co.id/b2b/secure/j_security_check';
	private $_user_agent 		= 'Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1';
	private $_logout_url 		= 'https://agent.sriwijayaair.co.id/b2b/secure/logout.jsp';
	private $_src_action_url 	= 'https://agent.sriwijayaair.co.id/b2b/secure/AvailabilityAction';
	private $_booking_url		= 'https://agent.sriwijayaair.co.id/b2b/secure/PNRAction';
	private $_src_result_url 	= 'https://agent.sriwijayaair.co.id/b2b/secure/createpnr.jsp';
	private $_start_url 		= 'https://agent.sriwijayaair.co.id/b2b/secure/';
	
	function __construct() {
		parent::__construct();
		$this->roundTrip = false;
		$this->_cookies_file = "./components/service/third_party/comp_maskapai/cookies/sriwijaya_airline.txt";
		/*
		$this->_ci->load->library('my_curl');
		$this->_ci->load->helper('array');
		*/
		$this->login();
	}
	
	function index() {}
		
	function mainPage(){
		$conf = array(
			'url'				=> $this->_default_url,
			'timeout'			=> 30,
			'header'			=> 0,
			'followlocation'	=> 1,
			'cookiejar'			=> $this->_cookies_file,
			'cookiefile'		=> $this->_cookies_file,
			'returntransfer'	=> 1,
			'ssl_verifyhost'	=> 0,
			'useragent'			=> $this->_user_agent,
		);	
		$this->_ci->my_curl->setup($conf);
		return $this->_ci->my_curl->exc();
	}
	function topage($url , $return = true){
		$conf = array(
				'url' => $url,
				'cookiejar' 		=> $this->_cookies_file,
				'cookiefile' 		=> $this->_cookies_file,
				'header'		=> 0,
				'nobody'	=> false,
				'returntransfer' => 1,
			//	'returntransfer' => 1
			);
		$this->_ci->my_curl->setup($conf);
		$exc = $this->_ci->my_curl->exc();
		if($return = true ) return $this->_ci->my_curl;
		echo $exc;
	}
	public function start()
	{
		$conf = array(
			'url' => $this->_start_url,
			'cookiejar' 		=> $this->_cookies_file,
			'cookiefile' 		=> $this->_cookies_file,
			'header'		=> 0,
			'nobody'	=> true,
		//	'returntransfer' => 1
		);
		$this->_ci->my_curl->setup($conf);
		$this->_ci->my_curl->exc();
		
	}
	function login(){
		$this->start();
		$post_data = array(
			'j_username' => $this->username,
			'j_password' => $this->password,
			
		);
		$conf = array(
			'url' 				=> $this->_login_url,
			'timeout' 			=> 30,
		
			'header' 			=> 1,
			'nobody' 			=> false,
			'followlocation' 	=> 1,
			'cookiejar' 		=> $this->_cookies_file,
			'cookiefile' 		=> $this->_cookies_file,
			'returntransfer' 	=> true,
			'post' 				=> true,
			'MAXREDIRS' 		=> 10,
			'ssl_verifyhost' 	=> 0,
			//'referer' 			=> $this->referer_url,
			'AUTOREFERER' 		=> true,
			'FAILONERROR' 		=> false,
			'postfields' 		=> http_build_query( $post_data , NULL, '&' ),
		);
		$this->_ci->my_curl->setup($conf);
		$this->_ci->my_curl->exc();
		
		
		//https://agent.sriwijayaair.co.id/b2b/secure/logout.jsp
	
		
	}
	function somepage(){
		$conf = array(
				'url' => 'https://agent.sriwijayaair.co.id/b2b/secure/home.jsp',
				'cookiejar' 		=> $this->_cookies_file,
				'cookiefile' 		=> $this->_cookies_file,
				'header'		=> 0,
				'nobody'	=> false,
			//	'returntransfer' => 1
			);
		$this->_ci->my_curl->setup($conf);
		echo $this->_ci->my_curl->exc();
	}
	function logout(){
		
			$conf = array(
					'url' => 'https://agent.sriwijayaair.co.id/b2b/secure/logout.jsp',
					'cookiejar' 		=> $this->_cookies_file,
					'cookiefile' 		=> $this->_cookies_file,
					'header'		=> 0,
					'nobody'	=> true,


				//	'returntransfer' => 1
				);
			$this->_ci->my_curl->setup($conf);
			$this->_ci->my_curl->exc();
	}
	
	public function _search()
	{
		$date = explode('-',$this->_opt->date_depart);	
		$posted = array(
			'isReturn' 			=> false,
			'from'		 		=> $this->_opt->route_from,
			'to'				=> $this->_opt->route_to,
			'departDate1' 		=> element('2',$date),
			'departDate2'		=> $this->dateConvertMin(element('1',$date)).'-'.element('0',$date),
			'adult' 			=> $this->_opt->passengers,
			'child' 			=> 0,
			'infant'			=> 0,
			'returndaterange' 	=> 0,
			'Submit' 			=> 'Search',
		);
		
		$conf = array(
			'url' 				=> $this->_src_action_url,
			'post' 				=> true,
			'postfields' 		=> http_build_query($posted),
			'timeout'			=> 150,
			'header'			=> 0,
			'followlocation'	=> true,
			'maxredirs'			=> 10,
			'cookiefile'		=> $this->_cookies_file,
			'returntransfer'	=> 1,
			'post'				=> true,
			'referer'			=> $this->_default_url,
			'ssl_verifyhost'	=> 0,
			'useragent'			=> $this->_user_agent,
		);
		
		$this->_ci->my_curl->setup($conf);
		$this->_ci->my_curl->exc();
		$res = $this->topage($this->_ci->my_curl->res_info('url'));
		return $res->res();
	}
	
	function search(){
		if ($this->roundTrip) {
			$type = 'return';
		}else{
			$type = 'depart';
		}
		
		$page = str_get_html($this->_search());
		if (!$page) {
			return false;
		}
		if(!$table = $page->find('div[id=pagewrapper] div[id=mainWrapper]',0)->find('form[action=./PNRAction]',0)) return false;
		if(!$flight = $page->find('table[id=table_go]',0)->find('tr table[class=flightInfo]')) return false;
		
		//$class = $page->find('table[id=table_go] table[class=classTable] td');
		$classTable = $page->find('table[id=table_go] table[class=classTable]');
		$Table = $page->find('table[id=table_go]',0);
		$date = $page->find('span[class=avTableLabel2]',0)->plaintext;
		$cdate = explode('-',$date);
		$dateFormated = '20'.element('2',$cdate).'-'.$this->monthConvert(element('1',$cdate)).'-'.element('0',$cdate);
		$insideFlight = $flight[0]->find('tr');
		$cntInsideFlight = count($insideFlight);
		$cnt_flight = count($flight);
		//$cnt_classFlight = count($class);
		if (!$Table) return false;
		$data = array();
		$index=0;
 		$cell = $table->find('table[id=table_go]',0)->find('tbody tr td[class=rightTD]');	
		for ($i=0; $i < count($classTable); $i++) {
			$class = $classTable[$i]->find('td');
			//return array('cnt'=> count($class));
			for ($j=0; $j < count($class); $j++) { 
				//echo $i.','.$j.'<br/>';
				if ($cell[$i]->find('div[class=avcell] input[class=avcellRadio]',$j)->getAttribute('disabled') == 'disabled') {continue;}
				
				$clas = $class[$j]->find('span',0)->plaintext;
				$price = $class[$j]->find('span',2)->plaintext;
				$t_depart = $flight[$i]->find('tr',0)->find('td',1)->find('span',1)->plaintext;
				$route_from = $flight[$i]->find('tr',0)->find('td',1)->find('span',0)->plaintext;
				$flight_number = $flight[$i]->find('tr',0)->find('td',0)->find('span',0)->plaintext;

				if ($cnt_flight == 1 || $cntInsideFlight ==1) {
					$route_arr = $flight[$i]->find('tr',0)->find('td',2)->find('span',0)->plaintext;
					$route_transit = '';
					$t_transit_arrive_time = NULL;
					$t_transit_depart_time = NULL;
					$t_arival = $flight[$i]->find('tr',0)->find('td',2)->find('span',1)->plaintext;
					$time_arrive = strtotime($dateFormated.' '.$t_arival);
					$ttarrive = $t_transit_arrive_time;
					$ttdepart = $t_transit_depart_time;
				}else {
					$route_arr = $flight[$i]->find('tr',1)->find('span',4)->plaintext;
					$route_transit =  ','.$flight[$j]->find('tr',0)->find('td',2)->find('span',0)->plaintext;
					$t_transit_arrive_time = $dateFormated.' '.$flight[$i]->find('tr',0)->find('td',2)->find('span',1)->plaintext;
					$t_transit_depart_time = $dateFormated.' '.$flight[$i]->find('tr',1)->find('td',1)->find('span',1)->plaintext;
					$t_arival = $flight[$i]->find('tr',1)->find('span',5)->plaintext;
					$time_arrive = strtotime($dateFormated.' '.$t_arival);
					$time_transit_arrive = strtotime($t_transit_arrive_time);
					$time_transit_depart = strtotime($t_transit_depart_time);
					$ttarrive = date("Y-m-d h:i",$time_transit_arrive);
					$ttdepart = date("Y-m-d h:i",$time_transit_depart);
				}
				$nprice = str_replace(',','',$price).'000';
				$fp = ($nprice+12000)*$this->_opt->passengers;
				$activeCell = $cell[$i]->find('div input',$j)->getAttribute('value');
				$time_depart = strtotime($dateFormated.' '.$t_depart);


				$meta = array(
					'company'				=> 	'SRIWIJAYA',
					'flight_no'				=> 	$flight_number,
					't_depart'				=>	date("Y-m-d h:i",$time_depart),
					't_arrive'				=>	date("Y-m-d h:i",$time_arrive),
					'type'					=>	$type,
					'class'					=>	$clas,
					'price'					=>	$fp,
					'route'					=>	$route_from.$route_transit.','.$route_arr,
					't_transit_arrive'		=>	$ttarrive,
					't_transit_depart'		=>	$ttdepart,
					'log_id'				=>	$this->_opt->id,
					'arrayIndex'			=>	$i.','.$j,
					'radio_value'			=>	$activeCell,
					'time_depart'			=>	$dateFormated,
					'passangers'			=>	$this->_opt->passengers,
				);

				$data[$index][$j]['company'] 				= 'SRIWIJAYA';
				$data[$index][$j]['flight_no']				= $flight_number;
				$data[$index][$j]['t_depart'] 				= date("Y-m-d h:i",$time_depart)	;
				$data[$index][$j]['t_arrive']				= date("Y-m-d h:i",$time_arrive);
				$data[$index][$j]['type'] 					= $type;
				$data[$index][$j]['class'] 					= $clas;
				$data[$index][$j]['price'] 					= $fp;
				$data[$index][$j]['route'] 					= $route_from.$route_transit.','.$route_arr;
				$data[$index][$j]['t_transit_arrive'] 		= $ttarrive;
				$data[$index][$j]['t_transit_depart'] 		= $ttdepart;
				$data[$index][$j]['log_id']					= $this->_opt->id;
				$data[$index][$j]['meta_data']				= json_encode($meta);
			}
			$index++;
		}
		
		$final = array();
		$i  = 0;
		foreach($data as $fl => $item ){
			foreach($item as $fare){
				$final[$i] = $fare;
				$i++;
			}
		}
		
		return $final;
	}
		
	function dateConvertMin($month){
		return $month - 1;
	}
	
	function monthConvert($month){
		
		$month_number = "";
		for($i=1;$i<=12;$i++){ 
			if(date("M", mktime(0, 0, 0, $i, 1, 0)) == $month){ 
				$month_number = $i; 
				break; 
			} 
		}
		return $month_number;
	}
	

	
	function abooking(){
		$passangerData = array();
		$ip = 1;
		foreach ($this->passangers as $key => $value) {
			$passangerData['adult.title.'.$ip] 					= element('title',$value);
			$passangerData['adult.name.'.$ip] 					= element('name',$value);
			$passangerData['adult.id.'.$ip]						= element('no_id',$value);
			$passangerData['adult.specialRequestSelect.'.$ip]	= '';
			$ip++;
		}
		
		
		$contactData = array(
			'contactcustomer.name'			=>	element('f_name',$this->contact).' '.element('l_name',$this->contact),
			'contactcustomer.phone'			=>	$this->contact['user_detail']['phone'],
			'contactcustomer.otherphone'	=>	'<Other Phone>',
		);
		
		
		$Agentdata = array(
				'contactagent.name'		=>	'PT. REA TOUR',
				'contactagent.email'	=>	'boyarie_zag@yahoo.com',
				'contactagent.phone'	=>	'081252676799',
				'extracover'			=>	'extracover',
				'term'					=>	1,
				'procceedType'			=>	'Book'			
		);
		
		$post_data = array_merge($this->data,$passangerData,$contactData,$Agentdata);
		
		$conf = array(
			'url' 				=> $this->_booking_url,
			'post' 				=> true,
			'postfields' 		=> http_build_query($post_data),
			'timeout'			=> 150,
			'header'			=> 0,
			'followlocation'	=> true,
			'maxredirs'			=> 10,
			'cookiefile'		=> $this->_cookies_file,
			'returntransfer'	=> 1,
			'post'				=> true,
			'referer'			=> $this->_src_result_url,
			'ssl_verifyhost'	=> 0,
			'useragent'			=> $this->_user_agent,
		);
		
		$this->_ci->my_curl->setup($conf);
		$this->_ci->my_curl->exc();
		$res = $this->topage($this->_ci->my_curl->res_info('url'));
		return $res->res();
	}
	
	function booking(){
		$html = "./mod_office/o_partner/third_party/comp_maskapai/ojankillbooking_data/HTML/sriwijaya/sriwijaya.html";
		//$page = file_get_html($html);
		$page = str_get_html($this->aBooking());
		if (!$page) {
			return array();
		}
		$table = $page->find('div[id=mainWrapper] div[id=pnr] table tbody');
		$cntTable = count($table);
		if ($cntTable == 0) { return array();}
		$data = array();
		$bookingDetails = $table[0];
		$passangerDetail = $table[1];
		$roouteDetail = $table[2];
		$paymentDetail = $table[3];
		$bookingCode = $bookingDetails->find('tr',1)->find('td',0)->plaintext;
		
	 	$limit	= preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$bookingDetails->find('tr',1)->find('td',1)->plaintext);
		$bookingDate = $bookingDetails->find('tr',2)->find('td',0)->find('span',0)->plaintext;
		$status	= $bookingDetails->find('tr',2)->find('td',1)->plaintext;
		$routeDet = $roouteDetail->find('tr',1)->find('th',0)->plaintext;
		$flightNumber = $roouteDetail->find('tr',2)->find('td',0)->plaintext;
		$departTime = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',str_replace('Departing at','',$roouteDetail->find('tr',2)->find('td',1)->plaintext));
		$arrTime = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',str_replace('Arriving at','',$roouteDetail->find('tr',2)->find('td',2)->plaintext));
		$class = str_replace('Class : ','',$roouteDetail->find('tr',2)->find('td',3)->plaintext);
		$countPassanger = count($passangerDetail->find('tr'));
		$price = str_replace(',','',str_replace('IDR','',$paymentDetail->find('tr',4)->find('td[class=fareTotal]',0)->plaintext));
		$limit_time = explode(',',$limit);
		$dateLimit = explode('-',$limit_time[0]);
		$flghtnum = str_split($flightNumber,6);
		$data['booking_number'] 	=	$bookingCode;
		$data['fare_id']			=	$this->fare_id;
		$data['meta_data']			=	json_encode($this->meta_data);
		$data['passangers']			=	$this->passangers;
		$data['final_price']		=	$price;
		//$data['limit'] 			=	$limit_time[0].' '.str_replace(' (GMT+0700)','',$limit_time[1]);
		//$data['bookingDate']	=	$bookingDate;
		//$data['status']			=	$status;
		//$data['routeDet'] 		=	$routeDet;
		//$data['flightNumber'] 	=	$flghtnum[0];
		//$data['departTime'] 	= 	$departTime;
		//$data['arrTime']		=	$arrTime;
		//$data['class']			=	$class;
		//$data['price']			= 	$price;
		$ip = 1;
		for ($i=2; $i < $countPassanger; $i++) {
			$name = $passangerDetail->find('tr',$i)->find('td',1)->plaintext.' '.$passangerDetail->find('tr',$i)->find('td',2)->plaintext;
			$ticketNumber = $passangerDetail->find('tr',$i)->find('td',3)->plaintext;
			$specialReq = $passangerDetail->find('tr',$i)->find('td',4)->plaintext;
			//$data['penumpang_'.$ip.'_name'] = $name;
			//$data['ticketnumber_'.$ip] = $ticketNumber;
			//$data['specialRequest_'.$ip] = $specialReq;
			$ip++;
		}
		for ($i=0; $i < $cntTable; $i++) { 
			//echo $table[$i];
		}
		$this->closing();
		return $data;
	}
		
	private function forBooking(){
			//foreach($opt as $key => $val ){$this->_opt->$key = $val;}
		$final = $this->search();
		return $final;
	}
	
	function closing(){
		$this->logOut();
	}
	
	public function doSearch($opt = array())
	//public function doSearch()
	{
		$this->_opt->route_from 	= 'CGK';
		$this->_opt->route_to 		= 'BPN';
		$this->_opt->date_depart 	= '2012-03-25';
		$this->_opt->date_return 	= NULL;
		$this->_opt->passengers 	= 1;
		$this->_opt->id				= 1;		
		foreach($opt as $key => $val ){$this->_opt->$key = $val;}
			
		if ($this->_opt->date_return) {
			$result1 = (is_array($rs1 = $this->search())) ? $rs1 : array();
			
			$temp = '';
			$temp = $this->_opt->route_from;
			$this->_opt->route_from = $this->_opt->route_to;
			$this->_opt->route_to = $temp;
			$this->_opt->date_depart = $this->_opt->date_return;
			$this->roundTrip = true;
			
			$result2 = (is_array($rs2 = $this->search())) ? $rs2 : array();
			$this->logOut();
			//print_r(array_merge($result1,$result2));
			$final = array_merge($result1,$result2);
		}else{
			$final = $this->search();
			$this->logOut();
		}
		if (count($final) == 0 || is_array($final) == false) {
			throw new ResultFareNotFound($opt);
		}
		return array_values($final);	
	}
	
	//function preBooking(){
	function preBooking($fare_data){
		//public function doBooking(){
			//$getMeta = $this->doSearch();
			/*$fare_data = array(
				'id'		=>	7323,
				'log_id'	=>	34,
				'company'	=>	'SRIWIJAYA',
				't_depart'	=>	'2011-12-31 10:05',
				't_arrive'	=>	'2011-12-31 10:45',
				'type'		=>	'depart',
				'class'		=>	'T',
				'route'		=>	'CGK,TKG',
				'meta_data'	=>	 '{"comapny":"SRIWIJAYA","flight_no":"SJ 096","t_depart":"2011-12-31 10:05","t_arrive":"2011-12-31 10:45","t_transit_arrive":null,"t_transit_depart":null,"type":"depart","price":392000,"class":"T","route":"CGK,TKG","log_id":34,"arrayIndex":"1,6","passangers":1,"time_depart":"2012-01-28","radio_value":"62bcdd33-81b1-4daa-846f-f5070d61d771|e3150e41-7796-45d8-910e-17dd01d4feb9|cfc9543d-1a37-4010-aa90-620f98854186"}',
				't_transit_arrive'	=>	'',
				't_transit_depart'	=>	'',
				'price'				=>	'392000',
				'flight_no'			=>	'SJ 096',
				'log'				=>	array(
					'id'				=>	34,
					'date_depart'		=>	'2012-01-28 00:00:00',
					'date_return'		=>	'',
					'route_from'		=>	'CGK',
					'route_to'			=>	'DPS',
					'passangers'		=>	1,
					'comp_include'		=>	'["Sriwijaya","Garuda","Merpati","Batavia","Citilink"]',
					'c_time'			=>	'2011-12-20 11:56:15',
					'max_fare'			=>	5,
					'actor'				=> 'CUS',
				),
			);*/

			$forBooking = json_decode($fare_data['meta_data'],1);
			//$route = explode(',',$forBooking['route']);
			$log = element('log',$fare_data);
			$route_from = element('route_from',$log);
			$route_to = element('route_to',$log);


			$this->_opt->route_from 	= $route_from;
			$this->_opt->route_to 		= $route_to;
			$this->_opt->date_depart 	= element('time_depart',$forBooking);
			$this->_opt->date_return 	= NULL;
			$this->_opt->passengers 	= element('passangers',$forBooking);
			$this->_opt->id				= element('log_id',$forBooking);

			//search again

			$reSearch = $this->research();
			for ($i=0; $i < count($reSearch); $i++) { 
				$meta[$i] = json_decode($reSearch[$i]['meta_data'],1);
			}

			$arrayIndex = $this->multidimensional_search($meta,array('arrayIndex' => $forBooking['arrayIndex']));
			if ($arrayIndex=='nothing') {
				$this->logout();
				return false;
			}else{
				$price = $meta[$arrayIndex-1]['price'];
				if ($price > $forBooking['price']) {
					$this->logout();
					return $price;
				}else{
					$this->logout();
					return true;
				}
			}
	}
	
	//function doBooking(){
	function doBooking($fare_data,$passangers_data,$customer_data){
		//fordebug
		/*$fare_data = array(
			'id'		=>	7323,
			'log_id'	=>	34,
			'company'	=>	'SRIWIJAYA',
			't_depart'	=>	'2012-01-31 06:10',
			't_arrive'	=>	'2012-01-31 09:10',
			'type'		=>	'depart',
			'class'		=>	'G',
			'route'		=>	'CGK,BPN',
			'meta_data'	=>	 '{"company":"SRIWIJAYA","flight_no":"SJ 160","t_depart":"2012-03-25 06:10","t_arrive":"2012-03-25 09:10","type":"depart","class":"G","price":652000,"route":"CGK,BPN","t_transit_arrive":null,"t_transit_depart":null,"log_id":1,"arrayIndex":"0,4","radio_value":"bdc3e6a3-6cfb-4b8d-a086-e00d3eaf556c|8f00af66-94ba-4987-a3ff-5f6983a91ad8|6c1db049-d518-4c10-88ab-e77950b5a095","time_depart":"2012-3-25","passangers":1}',
			't_transit_arrive'	=>	'',
			't_transit_depart'	=>	'',
			'price'				=>	'1612000',
			'flight_no'			=>	'SJ 160',
			'log'				=>	array(
				'id'				=>	34,
				'date_depart'		=>	'2012-03-31 00:00:00',
				'date_return'		=>	'',
				'route_from'		=>	'CGK',
				'route_to'			=>	'BPN',
				'passangers'		=>	1,
				'comp_include'		=>	'["Sriwijaya","Garuda","Merpati","Batavia","Citilink"]',
				'c_time'			=>	'2011-12-20 11:56:15',
				'max_fare'			=>	5,
				'actor'				=> 'CUS',
			),
		);
		
		
		
		$passangers_data = array(
			array(
					'title' 			=>	'Mr',
					'name' 				=>	'Zidni Mubarock',
					'no_id'				=>	'3671081902880001',

			),
		);
		
		$customer_data = array(
			'f_name'	=>	'Zidni',
			'l_name'	=>	'Mubarok',
			'email'		=>	'zidmubarock@gmail.com',
			'password'	=>	'aca9fd21ff5e08cf88a3929ef5c4f346',
			'role_id'	=>	1,
			'c_time'	=>	'2011-12-11 21:04:04',
			'm_time'	=>	'',
			'status'	=>	'active',
			'actv_key'	=>	'',

			'user_detail'	=> 	array(
				'user_id'	=>	26,
				//'no_id'		=>	'3671081902880001'
				'phone'		=>	'0215579315134',
				'mobile'	=>	'0215579315134',
				'address'	=>	'jalan anggrek no',
				'gender'	=>	'M',
								
			),
			
		);*/
		
		$this->contact = $customer_data;
	
		$forBooking = json_decode($fare_data['meta_data'],1);
		$route = explode(',',$forBooking['route']);
		$log = element('log',$fare_data);
		$route_from = element('route_from',$log);
		$route_to = element('route_to',$log);
	
	
		$this->_opt->route_from 	= $route_from;
		$this->_opt->route_to 		= $route_to;
		$this->_opt->date_depart 	= element('time_depart',$forBooking);
		$this->_opt->date_return 	= NULL;
		$this->_opt->passengers 	= element('passangers',$forBooking);
		$this->_opt->id				= element('log_id',$forBooking);
		$this->passangers	=	$passangers_data;
	
		$this->fare_id		=	element('id',$fare_data);
		$this->meta_data	=	$forBooking;
	
		$aSearch = $this->forBooking();
					
		for ($i=0; $i < count($aSearch); $i++) { 
			$metafinnal[$i] = json_decode($aSearch[$i]['meta_data'],1);
		}
		$arrayIndex = $this->multidimensional_search($metafinnal,array('arrayIndex' => $forBooking['arrayIndex']));
		$radioValue = explode('|',$metafinnal[$arrayIndex-1]['radio_value']);
		$this->data = array(
			$radioValue[1]	=> $metafinnal[$arrayIndex-1]['radio_value'],
		);
		
		$this->selectSummary(element('2',$radioValue));
		$booking = $this->booking();
		if (!is_array($booking)) {
			throw new BookingFailed($fare_data);
		}
		if (element('price',$booking) > element('price',$forBooking)) {
			throw new BookingFarePriceChanged($fare_data, element('final_price',$booking));
		}
		$this->logout();
		return $booking;
	}
	
	function selectSummary($var){
		$conf = array(
				'url' => 'https://agent.sriwijayaair.co.id/b2b/secure/selectedsummary.jsp?selected='.$var.'&extracover=true',
				'cookiejar' 		=> $this->_cookies_file,
				'cookiefile' 		=> $this->_cookies_file,
				'header'		=> 0,
				'nobody'	=> false,
			//	'returntransfer' => 1
			);
		$this->_ci->my_curl->setup($conf);
		$this->_ci->my_curl->exc();
	}
	
	function research(){
		$this->login();
		if ($this->_opt->date_return) {
			$result1 = $this->search();
			
			$temp = '';
			$temp = $this->_opt->route_from;
			$this->_opt->route_from = $this->_opt->route_to;
			$this->_opt->route_to = $temp;
			$this->_opt->date_depart = $this->_opt->date_return;
			$this->roundTrip = true;
			
			$result2 = $this->search();
			$this->logOut();
			//print_r(array_merge($result1,$result2));
			$final = array_merge($result1,$result2);
		}else{
			$final = $this->search();
			$this->logOut();
		}
		return array_values($final);
	}
	
	function multidimensional_search($parents, $searched) { 
		if (empty($searched) || empty($parents)) { 
		    return 'nothing'; 
		  } 

		  foreach ($parents as $key => $value) { 
		    $exists = true; 
		    foreach ($searched as $skey => $svalue) { 
		      $exists = ($exists && IsSet($parents[$key][$skey]) && $parents[$key][$skey] == $svalue); 
		    } 
		    if($exists){ return $key+1; } 
		  } 

		  return 'nothing';
	}

}
