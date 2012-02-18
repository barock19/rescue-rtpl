<?
/**
* 
*/
class Tiketcom extends REST_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->library('comp_tiketcom');
	}
	public function test_get()
	{
		try {
			$opt = array(
						'checkin' => '2012-02-18',
						'checkout' => '2012-02-19',
						'room'	=> '1',
						'adult' => '1',
						'query' => 'band',
						'id_identifier' => 'ydbmzc_Y19StlZU',
			);
			$res = $this->comp_tiketcom->do_search($opt) ;
			$this->response($res);
		} catch (Exception $e) {
			$this->response_error($e->getMessage());
		}
			
	}
	public function test_detail_get()
	{
		$opt = array(
				'checkin' => '2012-02-18',
				'checkout' => '2012-02-19',
				'room'	=> '1',
				'adult' => '1',
				'query' => 'band'
		);
		$path = 'ztPUy8agztDazsI';
		$id = 'mJU';
		$this->response($this->comp_tiketcom->get_detail($path, $id, $opt));
	}
	public function location_get()
	{
		$q = $this->uri->rsegment(3);
		try {
			$res = $this->comp_tiketcom->get_Location($q);
		} catch (Exception $e) {
			$this->response_error($e->getMessage());
		}
		$this->response($res);
	}
	public function search_post()
	{
		if(!$post = $this->post('src')) $this->response_error('please provide the variable to search');
		$opt  = $this->post('opt');
		try {
			$log = new Search_hotel_log($post);
			if(!$log->is_valid()) $this->response_error($log->errors->full_messages());
			$log->save();
			$this->response($log->to_array());
		} catch (Exception $e) {
			$this->response_error($e->getMessage());
		}
	
	}
	public function search_get()
	{
		if(!$id = $this->uri->rsegment(3)) $this->response_error('please provide the id');
		$option = $this->get('opt');
		try {
			$log = Search_hotel_log::find($id);
			$param = $log->to_array();
			$param['checkin']  = show_date($log->checkin, 'Y-m-d');
			$param['checkoout'] = show_date($log->checkout, 'Y-m-d');
			
			try {
				$res = $this->comp_tiketcom->do_search($param, $option);
			} catch (Exception $e) {
				throw new Exception($e->getMesage());
			}
		
			$return = array(
				'log' => $param,
				'option' => $option,
				'results' => $res,
			);
			$this->response($return);
			
		} catch (Exception $e) {
			$this->response_error($e->getMessage());
		}
		
	}
	public function detail_get()
	{
		if(!$idlog = $this->uri->rsegment(3)) $this->response_error('Please provide the log id') ;
		if(!$id_identifier = $this->uri->rsegment(4)) $this->response_error('Please provide the id idetifier');
		if(!$id_path = $this->uri->rsegment(5)) $this->response_error('Please provide the path identifier');
		
		
		try {
			$log = Search_hotel_log::find($idlog);
			$param = $log->to_array();
			$param['checkin']  = show_date($log->checkin, 'Y-m-d');
			$param['checkoout'] = show_date($log->checkout, 'Y-m-d');
			
			try {
				$res = $this->comp_tiketcom->get_detail($id_path, $id_identifier, $param);
				$this->response($res);
			} catch (Exception $e) {
				throw new Exception($e->getMessage());
			}
		} catch (Exception $e) {
			$this->response($e->getMessage());
		}
	}

}
