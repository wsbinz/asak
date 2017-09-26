<?php

/**
 * Created by PhpStorm.
 * User: s.manczak
 * Date: 26.09.2017
 * Time: 08:06
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {


    public function __construct()
    {
        parent::__construct();
        $this->load->model('admin/Admin_model');

    }

    public function index()
    {

    }

    public function create_user()
    {

        if(!empty($_POST)){

            if($this->form_validation->run('admin_user_create') == TRUE)
            {
                $data = array(
                    'ID' => '22',
                    'username' => $this->input->post('username',true),
                    'email' => $this->input->post('email',true),
                    'password' => password_hash($this->input->post('password',true),PASSWORD_DEFAULT),
                    'create_date' => time(),
                    'active' => 1
                );
                $user = $this->Admin_model->create('users',$data);
                $this->session->set_flashdata('alert',"Użytkownik został dodany !");
                print_r($user);
            }
            else
                {
                    $this->session->set_flashdata('alert',validation_errors());
                }
        }

        $data['validation']= $this->session->flashdata('alert');
        $this->twig->display('admin/user/create',$data);
    }



}