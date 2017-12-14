<?php
/**
 * Created by PhpStorm.
 * User: PizzaLap
 * Date: 13.12.2017
 * Time: 20:21
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Rack extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('admin/Admin_model');
        $this->load->helper('My');
        $this->load->library('pagination');
    }



public function index()
{

//    $this->twig->display('admin/magazine/magazine_geography');

}

    public function add_magazine() //dodanie regały
    {
        if(!empty($_POST))
        {

            $magazine = $this->input->post('magazine',true);
            $NameOfMagazin = $this->input->post('NameOfMagazin',true);

            if(!empty($magazine))
            {
                if(!empty($NameOfMagazin))
                {
                    $data = array(
                        'load_group' => $magazine,
                        'load_group_descr' => $NameOfMagazin,
                    );


                    $this->Admin_model->create("STORAGE", $data);
                    $this->session->set_flashdata('alert', "Dodales magazyn!");
                    refresh();

                }
                else{
                    $this->session->set_flashdata('alert', "Uzupełnij wszystkie pola!");
                    $data['post_2'] = $magazine;
                    refresh();
                }

            }
            else
            {
                $this->session->set_flashdata('alert', "Uzupełnij wszystkie pola!");
                $data['post_1'] = $NameOfMagazin;
                refresh();
            }

        }


        $data['validation'] = $this->session->flashdata('alert');
        $this->twig->display('admin/magazine/add_magazine',$data);
    }







}