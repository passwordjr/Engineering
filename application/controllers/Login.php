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

		redirect('Home');
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

		if ($data = $this->Crud_model->fetch("professor",$where)) {
			if ($data[0]->professor_status == 1) {
				$bool = sha1("professor");
			}else{
				$bool = "Account Deactivated - Contact CSO-MIS";
			}
		}elseif ($this->Crud_model->fetch("admin",$where)) {
			$bool = sha1("administrator");
		}elseif ($data = $this->Crud_model->fetch_last("student","student_id",$where)) {

			// fetch offering 
			$offering = $this->Crud_model->fetch("offering",array("offering_id"=>$data->offering_id));
			$offering = $offering[0];
			// fetch course
			$course = $this->Crud_model->fetch("course",array("course_id"=>$offering->course_id));
			$course = $course[0];
			// fetch enrollment 
			$enrollment = $this->Crud_model->fetch("enrollment",array("enrollment_id"=>$course->enrollment_id));
			$enrollment = $enrollment[0];
			if ($enrollment->enrollment_is_active == 1) {
				// check if user has an active session
				$session = $this->Crud_model->fetch("login_sessions",array(
					"login_sessions_identifier"=>$data->student_num,
					"login_sessions_status"=>1
				));
				$data = array(
					"login_sessions_identifier"=>$data->student_num,
					"login_sessions_status"=>1
				);
				$session = $session[0];
				if ($session) {
					$this->Crud_model->update("login_sessions",array("login_sessions_status"=>0),array("login_sessions_id"=>$session->login_sessions_id));
				} 
				if ($this->Crud_model->insert("login_sessions",$data)) {
					$bool = sha1("student"); 
				};
			}else{
				$bool = "You're not currently enrolled in this term."; 
			}
		}elseif ($data = $this->Crud_model->fetch("fic",$where)) {
			if ($data[0]->fic_status == 1) {
				$bool = sha1("fic");
			}else{
				$bool = "Account Deactivated - Contact CSO-MIS";
			}

		}else{
			$bool = "No Account Found";
		}

		echo json_encode($bool);
	}

	public function redirect_page()
	{
		$active_enrollment = $this->Crud_model->fetch("enrollment", array("enrollment_is_active"=>1));
		$active_enrollment = $active_enrollment[0];
		// echo "<pre>";
		// print_r( $this->session->userdata('userInfo'));
		if ($active_enrollment) {
			$data = $this->session->userdata('userInfo');
			echo $data["identifier"];
			switch ( $data["identifier"]) {
				case 'professor':
				$log_data = array(
					"log_user_id"=>$data['user']->professor_id,
					"log_timedate"=>time(),
					"log_platform"=>1,
					"log_content_id"=>1
				);
				$this->Crud_model->insert("log",$log_data);
				redirect('Home');
				break;
				case 'administrator':
				$log_data = array(
					"log_user_id"=>$data['user']->admin_id,
					"log_timedate"=>time(),
					"log_platform"=>1,
					"log_content_id"=>1
				);
				$this->Crud_model->insert("log",$log_data);
				redirect('Admin');
				break;
				case 'student':
				$log_data = array(
					"log_user_id"=>$data['user']->student_id,
					"log_timedate"=>time(),
					"log_platform"=>1,
					"log_content_id"=>1,
				);
				$this->Crud_model->insert("log",$log_data);
				redirect('Home');
				break;
				case 'fic':
				$log_data = array(
					"log_user_id"=>$data['user']->fic_id,
					"log_timedate"=>time(),
					"log_platform"=>1,
					"log_content_id"=>1
				);
				$this->Crud_model->insert("log",$log_data);
				redirect('Home');
				break;

				default:
				# code...
				break;
			}
		}else{
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
				case 'fic':
				redirect('Home');
				break;
				
				default:
				# code...
				break;
			}
		}

	}

	public function redirect()
	{
		/*=============================================================
        =            FETCH ACTIVE SEASON/TERM - ENROLLMENT            =
        =============================================================*/
        

        $active_enrollment = $this->Crud_model->fetch("enrollment", array("enrollment_is_active"=>1));
        $active_enrollment = $active_enrollment[0];

        
        // echo "<pre>";
        // print_r($active_enrollment);

        // die();

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
        		"active_enrollment"=>  $active_enrollment ? $active_enrollment->enrollment_id : "none" 
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
        		"active_enrollment"=>  $active_enrollment ? $active_enrollment->enrollment_id : "none" 
        	);
        	$this->session->set_userdata('userInfo',$userData);
        	
        	echo json_encode(base_url()."Login/redirect_page");
        	break;

        	case '204036a1ef6e7360e536300ea78c6aeb4a9333dd':
				# student
        	$info = $this->Crud_model->fetch("student",array("username"=>$this->input->post("username")));
        	$sess_id = $this->Crud_model->fetch_last("login_sessions","login_sessions_identifier",array("login_sessions_identifier"=>$info[0]->student_num));

        	$info = $info[0];
        	$userData = array(
        		'user'=> $info,
        		'logged_in' => TRUE,
        		"identifier" => "student",
        		"active_enrollment"=>$active_enrollment ? $active_enrollment->enrollment_id : "none",
        		"sess_id"=>$sess_id->login_sessions_id,
        	);
        	$this->session->set_userdata('userInfo',$userData);
        	echo json_encode(base_url()."Login/redirect_page");
        	break;

        	case 'ea1462c1fe6251c885dce5002ad73edb0f613628':
				# fic
        	$info = $this->Crud_model->fetch("fic",array("username"=>$this->input->post("username")));
        	$info = $info[0];
        	// echo "<pre>";
        	// print_r($info);
        	// die();
        	$userData = array(
        		'user'=> $info,
        		'logged_in' => TRUE,
        		"identifier" => "fic",
        		"active_enrollment"=>  $active_enrollment ? $active_enrollment->enrollment_id : "none" 
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