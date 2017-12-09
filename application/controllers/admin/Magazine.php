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
        $data['validation'] = $this->session->flashdata('alert');
        $data['post_2'] = $this->session->flashdata('post2');
        $data['post_1'] = $this->session->flashdata('post1');
      //  print_r($data);

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
                    //'id_storage' => 'DEFAULT',
                    'load_group' => $magazyn,
                    'load_group_descr' => $NazwaMagazynu,
                );


                $this->Admin_model->create("STORAGE", $data);
                    $this->session->set_flashdata('alert', "Hura dodales magazyn !");
                    $this->session->set_flashdata('post', $data);
                    redirect('admin/magazine',$data);
                }
                else{
                    $this->session->set_flashdata('alert', "Uzupełnij wszystkie pola!");
                    $this->session->set_flashdata('post2', $magazyn);//a juz wiem :D
                    redirect('admin/magazine');
                }


            }
            else
            {
                $this->session->set_flashdata('alert', "Uzupełnij wszystkie pola!");
                $this->session->set_flashdata('post1', $NazwaMagazynu);
                redirect('admin/magazine');
            }

        }

        $this->twig->display('admin/magazine/add_magazine',$data);
    }
}
