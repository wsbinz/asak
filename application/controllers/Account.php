<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends CI_Controller {


    public function __construct()
    {
        parent::__construct();

    }

    public function index()
    {

        $this->twig->display('site/account/login');
    }

    public function login()
    {

        if(!empty($_POST)){

            if($this->form_validation->run('site_login') == TRUE)
            {

                $where = array('email' => $this->input->post('email',true) );
                $user = $this->Site_model->get_signle();

                $data = array(
                    'email' => $this->input->post('email',true),
                    'password' => password_hash($this->input->post('password',true),PASSWORD_DEFAULT),
                );

                if($user->active == 0)
                {
                    $data_login = array(
                        'id' => $user->id,
                        'username' => $user->username,
                        'email' => $user->email,
                        'logged_in' => 1,
                    );

                    $this->session->set_flashdata('alert',"Zalogowałeś się pomyślnie !");
                }



                else
                {
                    $this->session->set_flashdata('alert',"Musisz aktywować konto !");
                }

            }


        }


        $this->twig->display('site/account/login');
    }



}
