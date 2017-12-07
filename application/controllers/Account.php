<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends My_Controller {


    public function __construct()
    {
        parent::__construct();
        $this->load->model('site/Site_model');
    }

    public function index()
    {
        if(logged_in() != 1)
        {
            redirect('account/login');
        }

        $data['validation'] = $this->session->flashdata('alert');
        $this->twig->display('admin/include/dashboard',$data);
    }

    public function login()
    {

        if(isset($_SESSION['logged_in']) && $_SESSION['logged_in']==1)
        {
            redirect('account');
        }

        if(!empty($_POST)){

            if($this->form_validation->run('site_login') == TRUE)
            {

                $where = array('email' => $this->input->post('email',true) );
                $user = $this->Site_model->get_single('USERS',$where);

                $data = array(
                    'email' => $this->input->post('email',true),
                    'password' => $this->input->post('password',true),
                );

                if(!empty($user)) {
                    if (password_verify($data['password'], $user->password)) {
                        if ($user->active == 1) {
                            $data_login = array(
                                'id' => $user->id,
                                'username' => $user->username,
                                'email' => $user->email,
                                'logged_in' => 1,
                            );
                            $this->session->set_userdata($data_login);
                            $this->session->set_flashdata('alert', "Zalogowałeś się pomyślnie !");

                            if($this->input->post('remember_me',true) == 1)
                            {
                                $remember_code = random_string();

                                $user_info = array(
                                    'id' => $user->id,
                                    'username' => $user->username,
                                    'email' => $user->email,
                                    'logged_in' => 1,
                                    'remember_me' => 1,
                                    'remember_code' => $remember_code
                                );

                                $user_info_json = json_encode($user_info);

                                $data_cookie=array(
                                    "name"=>'remember_me',
                                    "value" =>$user_info_json,
                                    "expire" => 60*60*60*24,
                                    'path' => '/',
                                );

                                set_cookie($data_cookie);
                                $data = array('remember_code'=>$remember_code);
                                $where = array('id'=>$user->id);
                                $this->Site_model->update("USERS",$data,$where); //model od update użytkownika
                            }

                            redirect('');
                        } else {
                            $this->session->set_flashdata('alert', "Musisz aktywować konto !");
                        }
                    } else {
                        $this->session->set_flashdata('alert', "Twoje hasło jest niepoprawne");
                    }
                }
                else
                {
                    $this->session->set_flashdata('alert', "Podany użytkownik nie istnieje");
                }
            }
            else
            {
                $this->session->set_flashdata('alert', validation_errors());
            }
        }

        $data['validation'] = $this->session->flashdata('alert');
        $this->twig->display('site/account/login',$data);
    }

    public function logout()
    {
        session_destroy();
        delete_cookie("remember_me");
        redirect('login');
    }

    public function active_account($activation_code)
    {
        $where = array("activation_code" => $activation_code);
        $code = $this->Site_model->get_single("USERS",$where);

        if(empty($code))
        {
            echo "Podałeś niepoprawny kod aktywacji !";
            exit();
        }
        elseif ($code->active==1)
        {
            echo "Twoje konto jest już aktywne !";
        }
        else
        {
            $data = array('active' => 1);
            $where = array('activation_code' => $activation_code);
            $this->Site_model->update("USERS",$data,$where);
            echo "Gratulację ! Twoje konto zostało aktywowane możesz się zalogować :)";
            echo "<br>";
            echo "<a href=".base_url()."account/login>Zaloguj się</a>";
        }

    }

}
