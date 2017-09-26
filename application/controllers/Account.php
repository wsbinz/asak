<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends CI_Controller {


    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->library('twig');
        $this->load->model('site/Site_model');
    }

    public function index()
    {

        echo "test";
    }


}
