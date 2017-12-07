<?php

/**
 * Created by PhpStorm.
 * User: s.manczak
 * Date: 16.10.2017
 * Time: 07:56
 */
class My_Controller extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('admin/Admin_model');

        if (!empty(get_cookie('remember_me')))
        {

            $user = json_decode((get_cookie('remember_me')));


            $where = array('id'=>$user->id);
            $user_code_db = $this->Admin_model->get_single('USERS',$where);

            //print_r($user);
            if (get_cookie('remember_me') == true && $user->remember_code == $user_code_db->remember_code) {
            $data_login = array(
                'id' => $user->id,
                'email' => $user->email,
                'logged_in' => true,
                'remembe_me' => true,
            );

            $this->session->set_userdata($data_login);
        }
        }
        else
        {

            if($this->session->logged_in == 1 && $this->session->remember_me == 1)
            {

                $where = array('id'=>$this->session->id);
                $user_code_db = $this->Admin_model->get_single('USERS',$where);

                echo '<pre>';
                print_r($user_code_db);
                echo '</pre>';

                $user_info = array(
                    'id' => $user_code_db->id,
                    'email' => $user_code_db->email,
                    'logged_in' => true,
                    'remember_code' => $user_code_db->remember_code,
                );

                $user_info_json = json_encode($user_info);

                $data_cookie=array(
                    "name"=>'remember_me',
                    "value" =>$user_info_json,
                    "expire" => 60*60*24*365,
                    'path' => '/',
                );
                set_cookie($data_cookie);
            }
        }

    }
}

class Admin_Controller extends My_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('admin/Admin_model');

        if(!check_group(array('moderator','uzytkownik','admin')))
        {
            $this->session->set_flashdata('alert',"Nie masz dostępu do tej częsci serwisu!");
            redirect('login');
        }


    }
}