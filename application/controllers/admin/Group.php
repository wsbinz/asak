<?php

/**
 * Created by PhpStorm.
 * User: Sebastian
 * Date: 2017-10-14
 * Time: 16:50
 */
defined('BASEPATH') OR exit('No direct script access allowed');


class Group extends Admin_Controller
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

    public function edit_group($id)
    {
        $where= array("id" => $id);
        $data['group'] = $this->Admin_model->get_single("GROUPS",$where);

        if(!empty($_POST))
        {
            if($this->form_validation->run('admin_group_edit') == TRUE)
            {
                $data = array(
                    'group_name' => $this->input->post('group_name',true),
                    'alias' => trim($this->input->post('alias',true)),
                );
                //Tworzenie grupy w bazie
                $where = array('id'=>$id);
                $this->Admin_model->update('GROUPS',$data,$where);

                $this->session->set_flashdata('alert',"Grupa została edytowana !");
            }
            else
            {
                $this->session->set_flashdata('alert',validation_errors());
            }

        }

        $data['validation']= $this->session->flashdata('alert');
        $this->twig->display('admin/groups/edit',$data);

    }

    public function edit_alias($alias)
    {
             $alias_id = $this->uri->segment(4);

             print_r($alias_id);
            $where = array('alias' => $alias);
            $group_alias = $this->Admin_model->get_single("GROUPS",$where);

            if(!empty($group_alias) && $group_alias->id == $alias_id)
            {
                return true;
            }
            else
            {
                $this->form_validation->set_message('edit_alias', 'Ktoś posiada już taki alias');
                return false;
            }
    }
}
