<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class User extends CI_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->model('User_model');
    $this->load->model('Logging_model');
  }

  public function login() {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

      $this->load->library('form_validation');
      $this->form_validation->set_error_delimiters('<div class=\'error\'>', '</div>');

      $this->form_validation->set_rules('username', 'Username', 'trim|required|strtolower');
      $this->form_validation->set_rules('password', 'Password', 'trim|required|strtolower|md5');

      if ($this->form_validation->run()) {

        if ($this->User_model->validate_login($this->input->post('username'), $this->input->post('password'))) {

          $this->Logging_model->log_user_location(
                  $this->input->post('lat'), $this->input->post('lng'), $this->input->post('postcode'), $this->input->post('locality')
          );

          $redirect = $this->input->post('redirect');
          if ($redirect) {
            redirect($redirect);
          }
          if ($this->input->post('username') === "reports") {
            redirect('reports/management_information');
          } else {
            redirect('leads/search');
          }
        }

        $this->session->set_flashdata('error', 'Invalid username or password.');
        redirect('user/login'); //Need to redirect to show the flash error.
      }
    }

    session_destroy();

    $redirect = isset($_GET['r']) ? urldecode($_GET['r']) : false;

    $data = array(
        'pageId' => 'login',
        'pageClass' => 'login',
        'title' => 'Prospect Login',
        'redirect' => $redirect
    );
    $this->template->load('default', 'user/login', $data);
  }

  public function logout() {
    redirect('user/login');
  }

  public function account() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

      $this->load->library('form_validation');
      $this->form_validation->set_error_delimiters('<div class=\'error\'>', '</div>');

      $this->form_validation->set_rules('current_pass', 'current password', 'trim|required|strtolower|md5');
      $this->form_validation->set_rules('new_pass', 'new password', 'trim|required|strtolower|min_length[5]|matches[conf_pass]');
      $this->form_validation->set_rules('conf_pass', 'confirm password', 'trim|required|strtolower|min_length[5]');



      if ($this->form_validation->run()) {

        if ($this->User_model->validate_login($_SESSION['login'], $this->input->post('current_pass'))) {
          $response = $this->User_model->set_password($this->input->post('new_pass'));
          $this->firephp->log($response);
          $this->session->set_flashdata('success', 'Password was updated');
        } else {
          $this->session->set_flashdata('error', 'Current password was incorrect');
        }
          redirect('user/account');
        
      }
    }

      $data = array(
          'pageId' => 'my-account',
          'pageClass' => 'my-account',
          'title' => 'My Account'
      );
      $this->template->load('default', 'user/account', $data);
    }
    
    
    public function index() {
      redirect('user/account');
    }

  }