<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Transferpulsa extends REST_Controller {
    private $auth;
    private $dataTransfer;
    private $dataUser;
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
    }
    public function user_auth($uniq_key){
        $condition['uniq_key'] = $uniq_key;
        $check = $this->model_adm->select($condition, 'user');
        $this->auth = $check->num_rows()>0?"1":"0";
    }

    public function get_user($key){

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

    public function index_post($action = '')
    {
        $this->responseMessage = "User tidak ditemukan";
        $this->responseCode = REST_Controller::HTTP_FORBIDDEN;

        $params = $this->post();
        $this->user_auth($params['key']);
        if ($this->auth == 1){

            $this->responseMessage = $this->defaultMessage;
            $this->responseCode = $this->defaultCode;

            if($action != null) {
//                $result = $this->getTransferPulsa($id);
//                $isOk = $result['responseMessage']['is_ok'];

                switch($action)
                {
                    case 'url':
//                        if($isOk){
                            $params = $this->post();
                            $key = $this->user_auth($params['key']);
                            $keys = $params['key'];

//                            get data user dengan key
//                            ambil id dari data user
//                            ambil data transfer pulsa dengan id yang barusan di ambil

                            $db_Name = 'aloma_go';
                            $username = 'beni';
                            $password = 'pbaArAsMrU';
                            $host ='128.199.246.132';

                            $connect = mysqli_connect($host, $username, $password, $db_Name);
                            $sql = "select * from t_transfer_pulsa 
                                    INNER JOIN user on user.id = t_transfer_pulsa.user_token
                                    WHERE uniq_key ='$keys'";
                            $res = mysqli_query($connect, $sql);
                            $response = array();
                            while($row= mysqli_fetch_array($res)){
                                array_push( $response, $row);
                            }

                            $this->response(array('is_ok' => true, 'message' => $response), REST_Controller::HTTP_OK);

//                        }
                        break;
                    case 'update':
//                        if($isOk) {
                            $params = $this->post();
//                            $data = $result['responseMessage']['data'];

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
//                        } else {
//                            $this->response(array('is_ok' => false, 'error_message' => 'ID tidak ditemukan') , $this->defaultCode);
//                        }
                        break;

                    case 'delete':
//                        if($isOk) {
                            $condition['id'] = $id;
                            $data = array(
                                'deleted' => 1
                            );
                            $this->model_adm->update($condition, $data, 't_transfer_pulsa');

                            $this->response(array('is_ok' => true, 'message' => 'Data berhasil dihapus'), REST_Controller::HTTP_OK);
//                        } else {
                            $this->response(array('is_ok' => false, 'error_message' => 'ID tidak ditemukan'), $this->defaultCode);
//                        }
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