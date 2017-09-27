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

            }


        }


        $this->twig->display('site/account/login');
    }



}
