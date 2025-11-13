<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Advertisement Management Controller
 *
 * @property CI_Input $input
 * @property CI_Output $output
 * @property CI_Session $session
 * @property CI_Form_validation $form_validation
 * @property CI_Upload $upload
 * @property CI_Loader $load
 * @property Ad_model $Ad_model
 */
class Ad_management extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Ad_model');
        $this->load->library('form_validation');
        $this->load->library('upload');
        $this->load->helper('url');
        $this->load->helper('form');
        
        // Require admin login for all routes
        if (!$this->session->userdata('admin_logged_in')) {
            redirect('login');
        }
    }

    // Display list of all ads
    public function index() {
        $data['ads'] = $this->Ad_model->get_all_ads();
        $data['title'] = 'Manage Advertisements';
        $this->load->view('admin/ad_list', $data);
    }

    // Show form to create a new ad
    public function create() {
        $data['title'] = 'Create New Advertisement';
        $this->load->view('admin/ad_form', $data);
    }

    // Store a new ad in the database
    public function store() {
        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('link_url', 'Link URL', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->create();
        } else {
            $upload_data = $this->do_upload();
            if ($upload_data['error'] == '') {
                $data = array(
                    'title' => $this->input->post('title'),
                    'image_url' => 'assets/uploads/ads/' . $upload_data['file_name'],
                    'link_url' => $this->input->post('link_url'),
                    'is_active' => $this->input->post('is_active') ? 1 : 0,
                    'display_order' => $this->input->post('display_order') ? $this->input->post('display_order') : 0,
                );

                $this->Ad_model->insert_ad($data);
                $this->session->set_flashdata('success', 'Advertisement created successfully!');
                redirect('admin/ad_management');
            } else {
                $this->session->set_flashdata('error', $upload_data['error']);
                $this->create();
            }
        }
    }

    private function do_upload() {
        $config['upload_path'] = './assets/uploads/ads/';
        $config['allowed_types'] = 'jpg|jpeg|png|gif';
        $config['max_size'] = 2048; // 2MB
        $config['file_name'] = time() . '_' . $_FILES['image_url']['name'];

        $this->upload->initialize($config);

        if (!$this->upload->do_upload('image_url')) {
            return ['error' => $this->upload->display_errors()];
        } else {
            return ['error' => '', 'file_name' => $this->upload->data('file_name')];
        }
    }

    // Show form to edit an existing ad
    public function edit($id) {
        $data['ad'] = $this->Ad_model->get_ad_by_id($id);
        if (empty($data['ad'])) {
            show_404();
        }
        $data['title'] = 'Edit Advertisement';
        $this->load->view('admin/ad_form', $data);
    }

    // Update an existing ad in the database
    public function update($id) {
        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('link_url', 'Link URL', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->edit($id);
        } else {
            $ad = $this->Ad_model->get_ad_by_id($id);
            $image_url = $ad['image_url'];

            if (!empty($_FILES['image_url']['name'])) {
                $upload_data = $this->do_upload();
                if ($upload_data['error'] == '') {
                    $image_url = 'assets/uploads/ads/' . $upload_data['file_name'];
                    // Delete old image
                    if (file_exists('./' . $ad['image_url'])) {
                        unlink('./' . $ad['image_url']);
                    }
                } else {
                    $this->session->set_flashdata('error', $upload_data['error']);
                    $this->edit($id);
                    return;
                }
            }

            $data = array(
                'title' => $this->input->post('title'),
                'image_url' => $image_url,
                'link_url' => $this->input->post('link_url'),
                'is_active' => $this->input->post('is_active') ? 1 : 0,
                'display_order' => $this->input->post('display_order') ? $this->input->post('display_order') : 0,
            );

            $this->Ad_model->update_ad($id, $data);
            $this->session->set_flashdata('success', 'Advertisement updated successfully!');
            redirect('admin/ad_management');
        }
    }

    // Delete an ad from the database
    public function delete($id) {
        $this->Ad_model->delete_ad($id);
        $this->session->set_flashdata('success', 'Advertisement deleted successfully!');
        redirect('admin/ad_management');
    }
}
?>