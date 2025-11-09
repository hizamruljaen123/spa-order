<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Authentication Controller (Admin)
 * Handles admin login/logout
 * @property CI_Input $input
 * @property CI_Session $session
 * @property CI_Form_validation $form_validation
 * @property User_model $User_model
 */
class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model('User_model');
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['url', 'form']);
        // Ensure admin_user table and default admin exists
        $this->User_model->ensure_bootstrap();
    }

    // GET: show login form; POST: process login
    public function login()
    {
        // If already logged in, redirect to admin
        if ($this->session->userdata('admin_logged_in')) {
            redirect('admin');
            return;
        }

        if ($this->input->method(true) === 'POST') {
            $this->form_validation->set_rules('username', 'Username', 'required|trim|min_length[3]');
            $this->form_validation->set_rules('password', 'Password', 'required');

            if (!$this->form_validation->run()) {
                $this->session->set_flashdata('error', validation_errors());
                redirect('login');
                return;
            }

            $username = $this->input->post('username', true);
            $password = $this->input->post('password', true);

            $user = $this->User_model->verify_login($username, $password);
            if ($user) {
                $this->session->set_userdata('admin_logged_in', true);
                $this->session->set_userdata('admin_user', [
                    'id' => (int)$user->id,
                    'username' => $user->username,
                    'name' => isset($user->name) ? $user->name : $user->username,
                    'role' => isset($user->role) ? $user->role : 'admin',
                ]);
                $this->session->set_flashdata('success', 'Login berhasil. Selamat datang!');
                redirect('admin');
                return;
            } else {
                $this->session->set_flashdata('error', 'Username atau password salah.');
                redirect('login');
                return;
            }
        }

        $data = [
            'title' => 'Login Admin',
            'flash' => [
                'success' => $this->session->flashdata('success'),
                'error' => $this->session->flashdata('error'),
            ],
        ];
        $this->load->view('admin/login', $data);
    }

    public function logout()
    {
        // Clear only our keys, then destroy session
        $this->session->unset_userdata('admin_logged_in');
        $this->session->unset_userdata('admin_user');
        $this->session->sess_destroy();
        redirect('login');
    }
}