<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends My_Controller {


    public function __construct()
    {
        parent::__construct();
        $this->load->model('site/Site_model');
        $this->load->model('site/Calendar_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        if(logged_in() != 1)
        {
            redirect('account/login');
        }

        $fileLog = json_decode(file_get_contents(base_url('asset/log/log1.txt')),true);


        $data['file'] = $fileLog;
        $data['validation'] = $this->session->flashdata('alert');
        print_r($fileLog);
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
        redirect('account/login');
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

    public function forgot_password()
    {
        if(!empty($_POST)){

            if($this->form_validation->run('site_user_forgot')==TRUE)
            {

                $email = $this->input->post('email',true);

                $where = array('email'=>$email);
                $user = $this->Site_model->get_single('USERS',$where);

                if(!empty($user))
                {

                    $reset_password_code = random_string(); // tworzenie unikalnego kodu do resetowania

                    $where = array('email'=>$email);
                    $data = array('reset_password_code'=>$reset_password_code);
                    $this->Site_model->update("USERS",$data,$where); //model od update użytkownika

                    //Wysyłanie meila do uzytkownika
                    $do = $_POST['email'];
                    $from = "biuro@ts3-tnt.pl <biuro@ts3-tnt.pl>";
                    $mailheaders="From: $from\n";
                    $mailheaders.="Reply-To: $from\n";
                    $mailheaders.="X-Mailer: PHP\n";
                    $mailheaders.="MIME-version: 1.0\n";
                    $mailheaders.="Content-type: text/html; charset=utf-8";
                    $message = '<p>Witaj ' . $user->username.'. Aby zmienić hasło w poniższy link:'
                        .base_url('account/reset_password/'.$reset_password_code ).'</p>';
                    $subject = "Zmiana hasła w serwisie ASAK";
                    $do = (string)$user->email;



                    if(mail($do,$subject, $message, $mailheaders)) {
                        $this->session->set_flashdata('alert',"Sprawdź swoją skrzynkę odbiorczą");
                        redirect('account/login');
                    }



                }
                else
                {
                    $this->session->set_flashdata('alert',"Adres e-mail nie istnieje !");
                }


            }
            else
            {
                $this->session->set_flashdata('alert',validation_errors());
                redirect($this->uri->uri_string(),'refresh');
            }
        }

        $data['validation']= $this->session->flashdata('alert');

        $this->twig->display('site/account/forgot_password',$data);

    }

    public function reset_password($reset_password_code)
    {
        //$this->session->set_flashdata('alert',"");
        $where = array('reset_password_code'=>$reset_password_code);
        $user = $this->Site_model->get_single('USERS',$where);

        if(!empty($user))
        {
            if(!empty($_POST))
            {

                if ($this->form_validation->run('site_user_reset')==TRUE)
                {
                    $this->session->set_flashdata('alert',''); //Resetowanie informacji o walidacji

                    $data = array(
                        'password' => password_hash($this->input->post("password",true),PASSWORD_DEFAULT),
                        'reset_password_code' => '',
                    );

                    $where = array('id'=>$user->id);
                    $this->Site_model->update("USERS",$data,$where); //model od update użytkownika
                    $this->session->set_flashdata('alert','Hasło zostało zmienione poprawnie :)');
                    redirect('account/login');
                }
                else
                {
                    $this->session->set_flashdata('alert',validation_errors());
                }

            }
            $data_code['code'] = $reset_password_code;
            $data_code['validation']= $this->session->flashdata('alert');
            $this->twig->display('site/account/reset_password',$data_code);
        }
        else
        {
            $this->session->set_flashdata('alert',"Podany kod nie istnieje !");
            redirect('account/login');
        }
    }

}
