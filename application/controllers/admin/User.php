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
                    'create_date' => time(),
                    'active' => 0,
                    'activation_code' => $activation_code,
                );
                //Tworzenie uzytkownika w bazie
                $user = $this->Admin_model->create('users',$data);

                //Wysyłanie meila do uzytkownika
                $do = $data['email'];
                $from = "biuro@ts3-tnt.pl <biuro@ts3-tnt.pl>";
                $mailheaders="From: $from\n";
                $mailheaders.="Reply-To: $from\n";
                $mailheaders.="X-Mailer: PHP\n";
                $mailheaders.="MIME-version: 1.0\n";
                $mailheaders.="Content-type: text/html; charset=utf-8";
                $message = '<p>Witaj ' . $data['username'].'. Aby aktytować konto kliknij w poniższy link:'
                    .base_url('account/activation/'.$activation_code ).'</p>';

                $mail = mail("$do", "Aktywacja konta w serwisie ASAK", "$message", "$mailheaders");

                $this->session->set_flashdata('alert',"Użytkownik został dodany !");
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