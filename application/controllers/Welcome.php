<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Welcome Controller
 *
 * @property CI_Input $input
 * @property CI_Output $output
 * @property CI_Loader $load
 * @property Ad_model $Ad_model
 */
class Welcome extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Ad_model');
	}

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/userguide3/general/urls.html
	 */
	public function index()
	{
		// Get the active ad with the highest display order
		$data['active_ad'] = $this->Ad_model->get_active_ad();
		
		// Load the view with the ad data
		$this->load->view('welcome_message', $data);
	}
}
