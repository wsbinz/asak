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

        //print_r($_SESSION);

        $data['indk_mwym'] = $this->Admin_model->get("VIEW_INDK_MWYM");
        //print_r($data['indk_mwym']);
        $data['validation'] = $this->session->flashdata('alert');
        $this->twig->display('site/product/lists_products',$data);

    }

    public function show($id)
    {
        $where = array('id' => $id);
        $data['single_indk_mwym'] = $this->Admin_model->get_signle("VIEW_INDK_MWYM");
        echo "lalla";
    }

    public function add_product()
    {

        $create_date = time();

        if(!empty($_POST)) {

           // if ($this->form_validation->run('add_product') == TRUE) {

                $this->db->trans_start(); //Otwieranie tranzakcji

                if($this->input->post('empty_vend',true) == true) {
                    //Tabela DOST
                    $data = array(
                        'vend_name' => $this->input->post('vend_name', true),
                        'vend_adress' => $this->input->post('vend_adress', true),
                        'vend_code' => $this->input->post('vend_code', true),
                        'vend_city' => $this->input->post('vend_city', true),
                        'vend_tax' => $this->input->post('vend_tax', true),
                    );

                    $this->Admin_model->create("VEND", $data);
                    $max = "id_dost";
                    $kod = $this->Admin_model->get_max("VEND", $max); //Pobieranie ostatniego ID z tabeli DOST
                    $kod[0] = $kod[0]->nr_mat;
                }
                else
                {
                    $kod[0] =  $this->input->post('select_dost',true);
                }

                //Tabela INDK
                $data = array(
                    'load_group' => $this->input->post('load_group', true),
                    'pkwiu_code' => $this->input->post('pkwiu_code', true),
                    'tax' => $this->input->post('tax', true),
                    'create_date' => date('d-m-Y'),
                    'create_user' => $_SESSION['id'],
                    'prod_hier' => $this->input->post('prod_hier', true),
                );


                $this->Admin_model->create("INDK", $data); //Tworzenie wpisu do INDK

                $max = "nr_mat";
                $nr_mat = $this->Admin_model->get_max("INDK", $max); //Pobieranie ostatniego ID z tabeli INDK
                $nr_mat = $nr_mat[0] -> nr_mat;
                $this->db->trans_complete();//Zakończenie tranzakcji

                //Tabela NAZW
                $data = array(
                    'name_short' => $this->input->post('name_short', true),
                    'name_long' => $this->input->post('name_long', true),
                    'nr_mat' => $nr_mat,
                    'lang' => "PL",
                );

                $this->Admin_model->create("MNAME", $data);


                //Tabela STORAGE  - grupa zaladunkowa
                $data = array(
                    'load_group' => $this->input->post('load_group', true),
                    'load_group_descr' => "dodatki",
                );
                $this->Admin_model->create("STORAGE", $data);

                //Tabela MWYM
                $data = array(
                    'unit_structure' => $this->input->post('unit_structure', true),
                    'value_struct' => $this->input->post('value_struct', true), //Poprawic
                    'weight_net' => $this->input->post('weight_net', true),
                    'weight_gross' => $this->input->post('weight_gross', true),
                    'value_length' => $this->input->post('value_length', true),
                    'value_width' => $this->input->post('value_width', true),
                    'value_height' => $this->input->post('value_height', true),
                    'value_capacit' => "24",//$this->input->post('wart_obj',true),
                    'unit_capacity' => "cm3",//$this->input->post("j_obj",true),
                    'unit_weight' => $this->input->post('unit_weight', true),
                    'unit_dim' => $this->input->post('unit_dim', true),
                    'ean_code' => $this->input->post('ean_code', true),
                    'nr_mat' => $nr_mat,
                );
                $this->Admin_model->create("MSIZE", $data);

                //Tabela dostzwr
                $data = array(
                    'vendrefund_name' =>$this->input->post('vendrefund_name',true),
                    'vendrefund_adress' => $this->input->post('vendrefund_adress',true),
                    'vendrefund_code' => $this->input->post('vendrefund_code',true),
                    'vendrefund_city' => $this->input->post('vendrefund_city',true)
                );

                $this->Admin_model->create("VEND_REFUND", $data);

                $this->session->set_flashdata('alert', "Pomyślnie dadano!");
                redirect('admin/product');
           /* }
            else {
                $this->session->set_flashdata('alert', validation_errors());
            }*/
        }



        $zmienna['dost'] = $this->Admin_model->get('VEND');
        $zmienna['validation'] = $this->session->flashdata('alert');
        $this->twig->display('admin/product/add_product',$zmienna);
    }

    public function add_product_ref ($id='')
    {
        if(!empty($id))
        {
            $where = array('id_indk'=>$id);
            $data['product'] = $this->Admin_model->get_single("VIEW_INDK_MWYM",$where);

            if(!empty($data['product']))
            {
                $this->twig->display('admin/product/add_product_ref', $data);
            }
            else
            {
                $this->session->set_flashdata('alert', "Nie ma takiego produktu");
                redirect('/account');
            }
        }
        else
        {
            $this->twig->display('admin/product/add_refproduct');
        }
    }

    public function search_product()
    {
        $col = array('mat_nazwd'=>$this->input->post('mat_nazwk',true));
        $search = $this->Admin_model->search("VIEW_INDK_MWYM",$col);
        echo json_encode($search);
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