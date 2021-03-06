<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GradeAssessment extends CI_Controller {


	public function __construct() {
		parent::__construct();
		$this->load->library('session');
		$this->load->model('Crud_model');
	}

	public function index()
	{
		$info = $this->session->userdata('userInfo');
		
		
		$ident = $info['identifier'];
		$ident.="_department";
		$program = 0;

		switch ($info['user']->$ident) {
			case 'CE':
			$program = 1;
			break;
			case 'ECE':
			$program = 2;
			break;
			case 'EE':
			$program = 3;
			break;
			case 'ME':
			$program = 4;
			break;

			default:
			break;
		}


		if ($info['logged_in'] && $info['identifier'] != "administrator") {
			$data = array(
				"title" => "Home - Learning Management System | FEU - Institute of Techonology",
				"info" => $info,
				"program" => $program,
				"s_h" => "",
				"s_a" => "",
				"s_f" => "",
				"s_c" => "",
				"s_t" => "",
				"s_s" => "",
				"s_co" => "",
				"s_ss" => "",
				"s_rc" => "",
				"s_ga" => "selected-nav",
			);
			$this->load->view('includes/header', $data);
			$this->load->view('grade_assessment');
			$this->load->view('includes/footer');
		} elseif ($info['identifier'] == "administrator") {
			redirect('Admin');
		} else {
			redirect('Welcome', 'refresh');
		}
	}

}

/* End of file GradeAssessment.php */
/* Location: ./application/controllers/GradeAssessment.php */
?>