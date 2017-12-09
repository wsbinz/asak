<?php
/**
 * Created by PhpStorm.
 * User: PizzaLap
 * Date: 08.12.2017
 * Time: 09:08
 */

defined('BASEPATH') OR exit('No direct script access allowed');


class Magazine extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('admin/Admin_model');
        $this->load->helper('My');
    }

    public function index()
    {


    }

    public function add_magazine()
    {
        if(!empty($_POST))
        {

            $magazyn = $this->input->post('magazyn',true);
            $NazwaMagazynu = $this->input->post('NazwaMagazynu',true);

            if(!empty($magazyn))
            {
                if(!empty($NazwaMagazynu))
                {
                $data = array(
                    'load_group' => $magazyn,
                    'load_group_descr' => $NazwaMagazynu,
                );


                $this->Admin_model->create("STORAGE", $data);
                    $this->session->set_flashdata('alert', "Hura dodales magazyn !");

                }
                else{
                    $this->session->set_flashdata('alert', "Uzupełnij wszystkie pola!");
                    $data['post_2'] = $magazyn;
                }

            }
            else
            {
                $this->session->set_flashdata('alert', "Uzupełnij wszystkie pola!");
                $data['post_1'] = $NazwaMagazynu;
            }

        }


        $data['validation'] = $this->session->flashdata('alert');
        $this->twig->display('admin/magazine/add_magazine',$data);
    }
}
