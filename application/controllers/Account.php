<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends CI_Controller {


    public function __construct()
    {
        parent::__construct();
        $this->load->library('twig');
    }

    public function index()
    {
        $this->twig->display('site/login');

    }

    public function login()
    {
        $data['lal'] = 'sdfd';

        $this->twig->display('site/login');
    }


}
