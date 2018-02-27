<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Kabarburung extends REST_Controller {

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

	private function isResult($id = null) {
		if($id)	$condition['id'] = $id;

		$condition['deleted'] = 0;
		$query = $this->model_adm->select(@$condition, 'berita', 'tanggal_waktu', 'DESC');

		$row = $query->num_rows();

		if($row > 0) {	
			$data = null;
			foreach($query->result() as $value){
				$selectGambar = $this->model_adm->select(array('id_berita' => $value->id), 'gambar_berita');

				if($selectGambar->num_rows() > 0) {
					$dataGambar = null;
					foreach($selectGambar->result() as $gambar) {
						$dataGambar[] = array(
								'id' => $gambar->id,
								'url' => $gambar->url,
								'submitted_at' => $gambar->submitted_at
							);
					}
				}

				$data[] = array(
						'id' => $value->id,
						'judul' => $value->judul,
						'author' => $value->author,
						'slug' => $value->slug,
						'tanggal_waktu' => array(
								'timestamp' => strtotime($value->tanggal_waktu),
								'datetime' => $value->tanggal_waktu
							),
						'terakhir_diubah' => array(
								'timestamp' => strtotime($value->terakhir_diubah),
								'datetime' => $value->terakhir_diubah
							),
						'images' => $selectGambar->num_rows() > 0 ? $dataGambar : null
					);
			}
		}

		return array(
				'is_ok' => $row > 0 ? true : false,
				$row > 0 ? 'data' : 'error_message' =>
				$row > 0 ? ($id ? $data[0] : $data) : 'Something error'
			);
	}

	public function index_get()
	{
		$this->responseMessage = $this->defaultMessage;
		$this->responseCode = $this->defaultCode;
		$id = $this->uri->segment(2);

		$result = $this->isResult(@$id);
		if($result['is_ok']) {
			$this->responseMessage = $result;
			$this->responseCode = REST_Controller::HTTP_OK;
		}

		$this->response($this->responseMessage, $this->responseCode);
	}

	public function index_post()
	{
		$id = $this->uri->segment(2);
		$action = $this->uri->segment(3);
		$this->responseMessage = $this->defaultMessage;
		$this->responseCode = $this->defaultCode;
		if($id) {
			if($id == 'add') {
				$params = $this->post();
				$imgUpload = null;

				$insertBerita = array(
						'judul' => $params['title'],
						'author' => $params['author'],
						'slug' => createSlug($params['title']),
						'tanggal_waktu' => date('Y-m-d H:i:s'),
						'terakhir_diubah' => date('Y-m-d H:i:s')
					);

				$createBerita = $this->model_adm->insert_id($insertBerita, 'berita');

				foreach($_FILES as $fileName => $value) {
					$this->insertImage($fileName, $createBerita);
				}

				$this->responseMessage = array(
					'is_ok' => true,
					'message' => 'Berhasil menambah kabar burung!' 
				);
				
				$this->responseCode = REST_Controller::HTTP_OK;
			} else {
				$condition['id'] = $id;
				$condition['deleted'] = 0;
				$query = $this->model_adm->select($condition, 'berita');
				$row = $query->num_rows();
				if($row > 0 && $action){
					$params = $this->post();
					switch($action) {
						case 'delete':
							$condition['id'] = $id;
							$dataUpdate = array(
									'deleted' => 1
								);
							$this->model_adm->update($condition, $dataUpdate, 'berita');

							$this->responseMessage = array(
									'is_ok' => true,
									'message' => 'Data kabar burung berhasil dihapus'
								);
							$this->responseCode = REST_Controller::HTTP_OK;
						break;

						case 'update_berita':
							if($params['title'] && $params['author']) {
								$condition['id'] = $id;
								$dataUpdate = array(
										'judul' => $params['title'],
										'author' => $params['author'],
										'slug' => createSlug($params['title']),
										'terakhir_diubah' => date('Y-m-d H:i:s')
									);

								$this->model_adm->update($condition, $dataUpdate, 'berita');

								$this->responseMessage = array(
										'is_ok' => true,
										'message' => 'Data kabar burung berhasil diubah'
									);
								$this->responseCode = REST_Controller::HTTP_OK;
							}
						break;

						case 'update_gambar':
							$this->responseMessage = 'UpdateGambar';
							$this->responseCode = REST_Controller::HTTP_OK;
						break;

						case 'add_gambar':
							$this->responseMessage = 'AddGambar';
							$this->responseCode = REST_Controller::HTTP_OK;
						break;

						case 'delete_gambar':
							if($params['id_gambar']) {
								$gambar['id'] = $params['id_gambar'];
								$this->model_adm->delete($gambar, 'gambar_berita');

								$this->responseMessage = array(
										'is_ok' => true,
										'message' => 'Gambar berhasil dihapus'
									);
								$this->responseCode = REST_Controller::HTTP_OK;
							}
						break;
					}
				}
			}
		}

		$this->response($this->responseMessage, $this->responseCode);
	}

	private function insertImage($field_name, $id = null) {
		$uploadDir = 'resources/images/';
  	$config['upload_path'] = FCPATH.$uploadDir;
  	$config['allowed_types'] = 'gif|jpg|png';
  	$this->load->library('upload', $config);

  	if(!$this->upload->do_upload($field_name)) {
  		$status = 0;
  		$data = $this->upload->display_errors();
  	}
  	else {
  		$status = 1;
  		$data = $this->upload->data();

  		$dataInsert = array(
  				'id_berita' => $id,
  				'url' => base_url().$uploadDir.$data['file_name'],
  				'submitted_at' => date('Y-m-d H:i:s')
  			);

  		$this->model_adm->insert($dataInsert, 'gambar_berita');
  	}

  	return array('status' => $status, 'data' => $data);
  }

}

/* End of file Kabarburung.php */
/* Location: ./application/controllers/Kabarburung.php */