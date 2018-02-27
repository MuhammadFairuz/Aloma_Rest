<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class User extends REST_Controller {

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

	public function index_post($action = '')
	{
		$this->responseMessage = $this->defaultMessage;
		$this->responseCode = $this->defaultCode;

		if ($action) 
		{
			switch($action)
			{
				case 'login':
					$result = $this->doLogin('administrator');
					$this->response($result['responseMessage'], $result['responseCode']);
				break;

				case 'masuk':
					$result = $this->doLogin('user');
					$this->response($result['responseMessage'], $result['responseCode']);
				break;

				case 'daftar':
					$result = $this->doRegister('user');
					$this->response($result['responseMessage'], $result['responseCode']);
				break;
				
				default:
					$this->response($this->responseMessage, $this->responseCode);
				break;
			}
		} else {
			$this->response($this->responseMessage, $this->responseCode);
		}
	}

	private function doLogin($actor)
	{
		$params = $this->post();

		$condition['username'] = $params['username'];
		$condition['password'] = md5($params['password']);

		$query = $this->model_adm->select($condition, $actor);

		$num = $query->num_rows();

		if($num > 0){
			$data = $query->row();

			$result = array(
					'is_ok' => true,
					'id' => $data->id,
					'username' => $data->username,
					'email' => $data->email,
					'terdaftar' => $data->terdaftar
				);
		} else {
			$result = array(
					'is_ok' => false,
					'error_message' => 'Username atau Password salah'
				);
		}
		return array(
				'responseMessage' => $result,
				'responseCode' => REST_Controller::HTTP_OK
			);
	}

	private function doRegister($actor)
	{
		$params = $this->post();
		$this->responseMessage = array('is_ok' => false, 'error_message' => 'Masih ada field yang kosong!');
		$this->responseCode = $this->defaultCode;

		if($params['username'] && $params['email'] && $params['password'])
		{
			$sql = "SELECT * FROM user WHERE username = '".$params['username']."' OR email = '".$params['email']."'";
			$getUser = $this->model_adm->rawQuery($sql);
			$isExist = $getUser->num_rows();
			
			if(!$isExist) {
				$dataInsert = array(
						'username' => $params['username'],
						'password' => md5($params['password']),
						'email' => $params['email'],
                        'uniq_key' => hash('sha256', $params['username'])
					);

				$this->model_adm->insert($dataInsert, 'user');
			}

			$this->responseMessage = array(
					'is_ok' => $isExist ? false : true,
					$isExist ? 'error_message' : 'message' =>
					$isExist ? 'Username atau Email sudah terdaftar' : 'Berhasil menambah akun'
			);
		}	

		return array(
				'responseMessage' => $this->responseMessage,
				'responseCode' => $this->responseCode
			);
	}

}

/* End of file User.php */
/* Location: ./application/controllers/User.php */	
