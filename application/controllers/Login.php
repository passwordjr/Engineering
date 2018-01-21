<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->library('session'); 
		$this->load->model('Crud_model');
	}
	public function index()
	{


	}
	public function logout()
	{
		session_destroy();
		redirect('Welcome','refresh');
	}
	//professor = 68d5fef94c7754840730274cf4959183b4e4ec35
	//administrator = b3aca92c793ee0e9b1a9b0a5f5fc044e05140df3
	public function verify(){
		$bool = "";
		$where = array(
			"username"=>$this->input->post("username"),
			"password"=>$this->input->post("password")
		);

		if ($this->Crud_model->fetch("professor",$where)) {
			$bool = sha1("professor");
		}elseif ($this->Crud_model->fetch("admin",$where)) {
			$bool = sha1("administrator");
		}elseif ($this->Crud_model->fetch("student",$where)) {
			$bool = sha1("student");
		}else{
			$bool = false;
		}

		echo json_encode($bool);
	}

	public function redirect_page()
	{
		// echo "<pre>";
		// print_r( $this->session->userdata('userInfo'));
		$data = $this->session->userdata('userInfo');
		echo $data["identifier"];
		switch ( $data["identifier"]) {
			case 'professor':
			redirect('Home');
			break;
			case 'administrator':
			redirect('Admin');
			break;
			case 'student':
			redirect('Home');
			break;
			
			default:
				# code...
			break;
		}

	}

	public function redirect()
	{
		/*=============================================================
        =            FETCH ACTIVE SEASON/TERM - ENROLLMENT            =
        =============================================================*/
        
        $active_enrollment = $this->Crud_model->fetch("enrollment", array("enrollment_is_active"=>1));
        $active_enrollment = $active_enrollment[0];
        
        /*=====  End of FETCH ACTIVE SEASON/TERM - ENROLLMENT  ======*/

        switch ($this->uri->segment(3)) {
        	
        	case '68d5fef94c7754840730274cf4959183b4e4ec35':
				# professor
        	$info = $this->Crud_model->fetch("professor",array("username"=>$this->input->post("username")));
        	$info = $info[0];
        	$userData = array(
        		'user'=> $info,
        		'logged_in' => TRUE,
        		"identifier" => "professor",
        		"active_enrollment"=>  $active_enrollment->enrollment_id
        	);
        	$this->session->set_userdata('userInfo',$userData);
        	echo json_encode(base_url()."Login/redirect_page");
        	break;
        	case 'b3aca92c793ee0e9b1a9b0a5f5fc044e05140df3':
				# administrator
        	$info = $this->Crud_model->fetch("admin",array("username"=>$this->input->post("username")));
        	$info = $info[0];
        	$userData = array(
        		'user'=> $info,
        		'logged_in' => TRUE,
        		"identifier" => "administrator",
        		"active_enrollment"=>  $active_enrollment->enrollment_id
        	);
        	$this->session->set_userdata('userInfo',$userData);
        	
        	echo json_encode(base_url()."Login/redirect_page");
        	break;
        	case '204036a1ef6e7360e536300ea78c6aeb4a9333dd':
				# student
        	$info = $this->Crud_model->fetch("student",array("username"=>$this->input->post("username")));
        	$info = $info[0];
        	$userData = array(
        		'user'=> $info,
        		'logged_in' => TRUE,
        		"identifier" => "student",
        		"active_enrollment"=>  $active_enrollment->enrollment_id
        	);
        	$this->session->set_userdata('userInfo',$userData);
        	echo json_encode(base_url()."Login/redirect_page");
        	break;

        	default:
				# code...
        	break;
        }
    }

}

/* End of file Login.php */
/* Location: ./application/controllers/Login.php */