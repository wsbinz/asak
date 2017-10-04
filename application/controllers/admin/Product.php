<?php

/**
 * Created by PhpStorm.
 * User: s.manczak
 * Date: 04.10.2017
 * Time: 09:51
 */
defined('BASEPATH') OR exit('No direct script access allowed');


class Product extends CI_Controller implements iProducts {


    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('UTC');
        $this->load->model('admin/Admin_model');
        $this->load->helper('My');
    }

    public function index()
    {
        if(logged_in()!= 1)
        {
            redirect('account');
        }


    }

    public function add_product()
    {
        // TODO: Implement add_product() method.
    }

   public function edit_product()
   {
       // TODO: Implement edit_product() method.
   }


}