<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Welcome extends REST_Controller {

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

	public function index_get()
	{
		$this->response($this->defaultMessage, $this->defaultCode);
	}
}

/* End of file Welcome.php */
/* Location: ./application/controllers/Welcome.php */