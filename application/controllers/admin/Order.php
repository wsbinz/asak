<?php
/**
 * Created by PhpStorm.
 * User: Boruś
 * Date: 2017-12-14
 * Time: 13:19
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('admin/Admin_model');
        $this->load->helper('My');
        $this->load->library('pagination');
    }


    public function order()
    {
        $this->twig->display('admin/product/order_products');

    }
}
