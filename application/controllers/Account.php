<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends CI_Controller {


    public function __construct()
    {
        parent::__construct();
        $this->load->model('site/Site_model');
    }

    public function index()
    {
        if($_SESSION['logged_in'] == 1)
        {
            echo "Witaj " . $_SESSION['username'] . " Zostałeś pomyślnie zalogowany !";
            /*$this->twig->display('site/account/login');*/
            echo "<pre>";
            var_dump($_SESSION);
            echo "</pre>";
        }
        else
        {
            redirect('account/login');
        }
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
                $user = $this->Site_model->get_single('users',$where);

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
                            redirect('account');
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



}
