<?php

/**
 * Created by PhpStorm.
 * User: s.manczak
 * Date: 04.10.2017
 * Time: 09:51
 */
defined('BASEPATH') OR exit('No direct script access allowed');


class Product extends Admin_Controller  {


    public function __construct()
    {
        parent::__construct();
        //date_default_timezone_set('UTC');
        $this->load->model('admin/Admin_model');
        $this->load->helper('My');
    }

    public function index()
    {
        if(logged_in()!= 1)
        {
            redirect('account');
        }

        $this->twig->display('site/product/list_product');


    }

    public function show($id, $alias)
    {
        // TODO: Implement show() method.
    }

    public function add_product()
    {
        $this->twig->display('admin/product/add_product');
    }

   public function edit_product($id)
   {
       // TODO: Implement edit_product() method.
   }

   public function change_product($id)
   {
       // TODO: Implement change_product() method.
   }

   public function delete_product($id)
   {
       // TODO: Implement delete_product() method.
   }


}