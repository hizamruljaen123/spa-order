<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Product Management Controller
 *
 * @property CI_Input $input
 * @property CI_Output $output
 * @property CI_Session $session
 * @property CI_Form_validation $form_validation
 * @property CI_Upload $upload
 * @property CI_Loader $load
 * @property Product_model $Product_model
 */
class Product_management extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Product_model');
        $this->load->library('form_validation');
        $this->load->library('upload');
        $this->load->helper('url');
        $this->load->helper('form');

        // Require admin login for all routes
        if (!$this->session->userdata('admin_logged_in')) {
            redirect('login');
        }
    }

    // Display list of all products
    public function index() {
        $data['products'] = $this->Product_model->get_all_products();
        $data['title'] = 'Manage Products';
        $this->load->view('admin/product_list', $data);
    }

    // Show form to create a new product
    public function create() {
        $data['title'] = 'Create New Product';
        $this->load->view('admin/product_form', $data);
    }

    // Store a new product in the database
    public function store() {
        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('price', 'Price', 'required|numeric');
        $this->form_validation->set_rules('currency', 'Currency', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->create();
        } else {
            $upload_data = $this->do_upload();
            $image_url = '';
            if ($upload_data['error'] == '') {
                $image_url = 'assets/uploads/products/' . $upload_data['file_name'];
            }

            $data = array(
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
                'price' => $this->input->post('price'),
                'currency' => $this->input->post('currency'),
                'image_url' => $image_url,
                'is_active' => $this->input->post('is_active') ? 1 : 0,
                'display_order' => $this->input->post('display_order') ? $this->input->post('display_order') : 0,
            );

            $this->Product_model->insert_product($data);
            $this->session->set_flashdata('success', 'Product created successfully!');
            redirect('admin/product_management');
        }
    }

    // Show form to edit an existing product
    public function edit($id) {
        $data['product'] = $this->Product_model->get_product_by_id($id);
        if (empty($data['product'])) {
            show_404();
        }
        $data['title'] = 'Edit Product';
        $this->load->view('admin/product_form', $data);
    }

    // Update an existing product in the database
    public function update($id) {
        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('price', 'Price', 'required|numeric');
        $this->form_validation->set_rules('currency', 'Currency', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->edit($id);
        } else {
            $product = $this->Product_model->get_product_by_id($id);
            $image_url = $product['image_url'];

            if (!empty($_FILES['image_url']['name'])) {
                $upload_data = $this->do_upload();
                if ($upload_data['error'] == '') {
                    $image_url = 'assets/uploads/products/' . $upload_data['file_name'];
                    // Delete old image
                    if (file_exists('./' . $product['image_url'])) {
                        unlink('./' . $product['image_url']);
                    }
                } else {
                    $this->session->set_flashdata('error', $upload_data['error']);
                    $this->edit($id);
                    return;
                }
            }

            $data = array(
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
                'price' => $this->input->post('price'),
                'currency' => $this->input->post('currency'),
                'image_url' => $image_url,
                'is_active' => $this->input->post('is_active') ? 1 : 0,
                'display_order' => $this->input->post('display_order') ? $this->input->post('display_order') : 0,
            );

            $this->Product_model->update_product($id, $data);
            $this->session->set_flashdata('success', 'Product updated successfully!');
            redirect('admin/product_management');
        }
    }

    // Delete a product from the database
    public function delete($id) {
        $product = $this->Product_model->get_product_by_id($id);
        if ($product && !empty($product['image_url']) && file_exists('./' . $product['image_url'])) {
            unlink('./' . $product['image_url']);
        }

        $this->Product_model->delete_product($id);
        $this->session->set_flashdata('success', 'Product deleted successfully!');
        redirect('admin/product_management');
    }

    private function do_upload() {
        $config['upload_path'] = './assets/uploads/products/';
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
}
?>