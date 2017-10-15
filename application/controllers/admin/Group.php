<?php

/**
 * Created by PhpStorm.
 * User: Sebastian
 * Date: 2017-10-14
 * Time: 16:50
 */
defined('BASEPATH') OR exit('No direct script access allowed');


class Group extends CI_Controller
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
        //Tutaj będziemy wyświetlać wszystkie grupy jakie sa

        $groups = $this->Admin_model->get('GROUPS');
        $data['groups'] = $groups;

        $this->twig->display('admin/groups/index',$data);
    }

    public function add_group()
    {

        print_r($_POST);

        if(!empty($_POST)){

            if(empty($_POST['alias']))
            {
                $_POST['alias'] = alias($_POST['group_name']);
            }
            else
            {
                $_POST['alias'] = alias($_POST['alias']);
            }

            if($this->form_validation->run('admin_group_create') == TRUE)
            {
                $data = array(
                    'group_name' => $this->input->post('group_name',true),
                    'alias' => trim($this->input->post('alias',true)),
                );
                //Tworzenie grupy w bazie
                $groups = $this->Admin_model->create('GROUPS',$data);


                $this->session->set_flashdata('alert',"Grupa została dodana !");
            }
            else
            {
                $this->session->set_flashdata('alert',validation_errors());
            }


        }

        $data['validation']= $this->session->flashdata('alert');
        $this->twig->display('admin/groups/create',$data);
    }

}
