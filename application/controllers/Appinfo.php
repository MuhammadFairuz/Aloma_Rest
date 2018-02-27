<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Appinfo extends REST_Controller {

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
		$infoKey = $this->uri->segment(2);
		
		if($infoKey) {
			$condition['key'] = $infoKey;
		}

		$query = $this->model_adm->select(@$condition, 'app_info');
		$row = $query->num_rows();

		$response = array(
				'is_ok' => $row > 0 ? true : false,
				$row > 0 ? 'data' : 'error_message' =>
				$row > 0 ? ($infoKey ? $query->row() : $query->result())
				: 'Data masih kosong'
			);

		$this->response($response);
	}

	public function index_post()
	{
		$key = $this->uri->segment(2);
		$action = $this->uri->segment(3);
		$this->responseMessage = $this->defaultMessage;
		$this->responseCode = $this->defaultCode;

		if($key) {
			$params = $this->post();

			if($key == 'url') {
				if($params['url-1'] && $params['url-2'] && $params['url-3']) {

					foreach($params as $v => $dt) {
						$condition['key'] = $v;
						$dataUpdate = array(
								'content' => $dt
							);
						$this->model_adm->update($condition, $dataUpdate, 'app_info');
					}

					$this->responseMessage = array('is_ok' => true, 'message' => 'Data berhasil diubah!');
					$this->responseCode = REST_Controller::HTTP_OK;
				}
			} else {
				$conditon['key'] = $key;
				$query = $this->model_adm->select($conditon, 'app_info');
				$row = $query->num_rows();

				if($row > 0 && $action) {
					switch($action) {
						case 'update':
							$params = $this->post();
							$data = $query->row();
							$dataUpdate = array(
									'name' => $params['title'] ? $params['title'] : $data->name,
									'content' => $params['content'] ? $params['content'] : $data->content,
									'last_modified' => date('Y-m-d H:i:s')
								);
							$this->model_adm->update($conditon, $dataUpdate, 'app_info');
							$this->responseMessage = array('is_ok' => true, 'message' => 'Data berhasil diubah!');
							$this->responseCode = REST_Controller::HTTP_OK;
						break;
					}
				}
			}
		}

		$this->response($this->responseMessage, $this->responseCode);
	}
}

/* End of file Appinfo.php */
/* Location: ./application/controllers/Appinfo.php */