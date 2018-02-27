<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Inbox extends REST_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_adm');
		$this->defaultMessage = array(
				'is_ok' => false,
				'error_message' => 'Unknown method'
			);
		$this->defaultCode = REST_Controller::HTTP_BAD_REQUEST;
	}

	public function index_get($action = '')
	{
		$result = $this->getInbox(@$action);
		$this->response($result['responseMessage'], $result['responseCode']);
	}

	private function getInbox($id = '')
	{
		if($id) $condition['id'] = $id;

		$query = $this->model_adm->select(@$condition, 'inbox', 'ReceivingDateTime', 'DESC');

		$num = $query->num_rows();

		$responseMessage = array(
				'is_ok' => $num > 0 ? true : false,
				$num > 0 ? 'data' : 'error_message' =>
				$num > 0 ? ($num == 1 && $id ? $query->row() : $query->result()) : 'Data masih kosong'
			);

		return array(
				'responseMessage' => $responseMessage,
				'responseCode' => $num > 0 ? REST_Controller::HTTP_OK : REST_Controller::HTTP_FORBIDDEN
			);
	}
}

/* End of file Inbox.php */
/* Location: ./application/controllers/Inbox.php */