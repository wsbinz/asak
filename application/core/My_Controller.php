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



    }
}

class Admin_Controller extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('admin/Admin_model');

        if(!check_group(array('admin','moderator')))
        {
            $this->session->set_flashdata('alert',"Nie masz dostępu do tej częsci serwisu!");
            redirect('account');
        }


    }
}