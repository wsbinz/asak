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
        date_default_timezone_set('UTC');
        $this->load->model('admin/Admin_model');
        $this->load->helper('My');
    }

    public function index()
    {

    }

    public function create_user()
    {

        $activation_code = random_string();
        if(!empty($_POST)){

            if($this->form_validation->run('admin_user_create') == TRUE)
            {
                $data = array(
                    'username' => $this->input->post('username',true),
                    'email' => $this->input->post('email',true),
                    'password' => password_hash($this->input->post('password',true),PASSWORD_DEFAULT),
                    //'create_date' => time(),
                    'active' => 0
                );

                $user = $this->Admin_model->create('users',$data);
                $this->session->set_flashdata('alert',"Użytkownik został dodany !");
            }
            else
                {
                    $this->session->set_flashdata('alert',validation_errors());
                }

            //Wysyłanie meila do uzytkownika

            $to      = $data['email'];
            $subject = 'Aktywacja konta';
            $message = 'Witaj, <br> Konto w systemie ASAK zostało pomyślnie utworzone ! \n Aby aktywować konto kliknij w poniższy link: \n '.base_url('account/activation/'.$activation_code ).'';
            $headers = 'From: biuro@asak.com' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();
            mail($to, $subject, $message, $headers);

        }

        $data['validation']= $this->session->flashdata('alert');
        $this->twig->display('admin/user/create',$data);
    }



}