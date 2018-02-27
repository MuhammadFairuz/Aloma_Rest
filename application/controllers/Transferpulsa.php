<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Transferpulsa extends REST_Controller {
    private $auth;
    public function __construct()
    {
        parent::__construct();
//		0 false
//      1 true
        $this->auth = "0";
        $this->load->model('model_adm');
        $this->defaultMessage = array(
            'is_ok' => false,
            'error_message' => 'Unknown method'
        );
        $this->defaultCode = REST_Controller::HTTP_BAD_REQUEST;
    }

    public function index_get($action = '')
    {
        $result = $this->getTransferPulsa(@$action);
        $this->response($result['responseMessage'], $result['responseCode']);
    }
    public function testindex_post($action = '')
    {
        $params = $this->post();
        $this->user_auth($params['key']);
        print_r($this->auth);
    }
    public function user_auth($uniq_key){
        $condition['uniq_key'] = $uniq_key;
        $check = $this->model_adm->select($condition, 'user');
        $this->auth = $check->num_rows()>0?"1":"0";
    }


    private function getTransferPulsa($id = '')
    {
        $condition['deleted'] = 0;
        if($id) $condition['id'] = $id;

        $query = $this->model_adm->select(@$condition, 't_transfer_pulsa', 'tanggal', 'DESC');

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

    public function index_post($id = '' , $action = '')
    {
        $this->responseMessage = "User tidak ditemukan";
        $this->responseCode = REST_Controller::HTTP_FORBIDDEN;

        $params = $this->post();
        $this->user_auth($params['key']);
//	    print_r($this->auth);
        if ($this->auth == 1){

            $this->responseMessage = $this->defaultMessage;
            $this->responseCode = $this->defaultCode;

            if($id && $action) {
                $result = $this->getTransferPulsa($id);
                $isOk = $result['responseMessage']['is_ok'];

                switch($action)
                {
                    case 'url':
                        if($isOk){
                            $params = $this->post();
                            $this->user_auth($params['key']);
                            echo $this->auth;
                        }
                        break;
                    case 'update':
                        if($isOk) {
                            $params = $this->post();
                            $data = $result['responseMessage']['data'];

                            $condition['id'] = $id;
                            $dataUpdate = array(
                                'nomor_pengirim' => isset($params['nomor_pengirim']) ? $params['nomor_pengirim'] : $data->nomor_pengirim,
                                'nomor_tujuan' => isset($params['nomor_tujuan']) ? $params['nomor_tujuan'] : $data->nomor_tujuan,
                                'denominasi' => isset($params['denominasi']) ? $params['denominasi'] : $data->denominasi,
                                'total_pulsa_transfer' => isset($params['total_pulsa_transfer']) ? $params['total_pulsa_transfer'] : $data->total_pulsa_transfer,
                                'verifikasi' => isset($params['verifikasi']) ? $params['verifikasi'] : $data->verifikasi,
                                'sent' => isset($params['sent']) ? $params['sent'] : $data->sent
                            );

                            $this->model_adm->update($condition, $dataUpdate, 't_transfer_pulsa');

                            $this->response(array('is_ok' => true, 'message' => 'Data berhasil diupdate'), REST_Controller::HTTP_OK);
                        } else {
                            $this->response(array('is_ok' => false, 'error_message' => 'ID tidak ditemukan') , $this->defaultCode);
                        }
                        break;

                    case 'delete':
                        if($isOk) {
                            $condition['id'] = $id;
                            $data = array(
                                'deleted' => 1
                            );
                            $this->model_adm->update($condition, $data, 't_transfer_pulsa');

                            $this->response(array('is_ok' => true, 'message' => 'Data berhasil dihapus'), REST_Controller::HTTP_OK);
                        } else {
                            $this->response(array('is_ok' => false, 'error_message' => 'ID tidak ditemukan'), $this->defaultCode);
                        }
                        break;

                    default:
                        $this->response($this->responseMessage, $this->responseCode);
                        break;
                }
            } else {
                $this->response($this->responseMessage, $this->responseCode);
            }
        }else{
            $this->response($this->responseMessage, $this->responseCode);
        }
    }
}

/* End of file Transferpulsa.php */
/* Location: ./application/controllers/Transferpulsa.php */