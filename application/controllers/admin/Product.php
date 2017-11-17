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

        print_r($_SESSION);
        $data['validation'] = $this->session->flashdata('alert');
        $this->twig->display('site/product/lists_products',$data);

    }

    public function show($id, $alias)
    {
        // TODO: Implement show() method.
    }

    public function add_product()
    {

        $create_date = time();

        if(!empty($_POST)) {

            if ($this->form_validation->run('add_product') == TRUE) {
                //Tabela INDK
                $data = array(
                    'gr_zalad' => $this->input->post('gr_zalad', true),
                    'kod_pkwiu' => $this->input->post('kod_pkwiu', true),
                    'vat' => $this->input->post('vat', true),
                    'utw_data' => date('d-m-Y'),
                    'utw_user' => $_SESSION['id'],
                    'zm_data' => date('d-m-Y'),
                    'zm_user' => $_SESSION['id'],
                    'prod_hier' => $this->input->post('prod_hier', true),
                );

                $this->db->trans_start(); //Otwieranie tranzakcji
                $this->Admin_model->create("INDK", $data); //Tworzenie wpisu do INDK

                $max = "nr_mat";
                $kod = $this->Admin_model->get_max("INDK", $max); //Pobieranie ostatniego ID z tabeli INDK
                $this->db->trans_complete();//Zakończenie tranzakcji

                //Tabela NAZW
                $data = array(
                    'mat_nazwk' => $this->input->post('mat_nazwk', true),
                    'mat_nazwd' => $this->input->post('mat_nazwd', true),
                    'nr_mat' => $kod[0]->nr_mat,
                    'kl_jez' => "PL",
                );

                $this->Admin_model->create("NAZW", $data);


                //Tabela GRTW
                $data = array(
                    'prod_hier' => $this->input->post('prod_hier', true),
                );
                $this->Admin_model->create("GRTW", $data);

                //Tabela MWYM
                $data = array(
                    'j_str' => $this->input->post('j_str', true),
                    'wart_str' => $this->input->post('wart_str', true),
                    'waga_ne' => $this->input->post('waga_ne', true),
                    'waga_br' => $this->input->post('waga_br', true),
                    'wart_dl' => $this->input->post('wart_dl', true),
                    'wart_szer' => $this->input->post('wart_szer', true),
                    'wart_wys' => $this->input->post('wart_wys', true),
                    'j_wag' => $this->input->post('j_wag', true),
                    'j_wym' => $this->input->post('j_wym', true),
                    'ean_kod' => $this->input->post('ean_kod', true),
                    'nr_mat' => $kod[0]->nr_mat,
                );
                $this->Admin_model->create("MWYM", $data);

                $this->session->set_flashdata('alert', "Pomyślnie dadano!");
                redirect('admin/product');
            }
            else {
                $this->session->set_flashdata('alert', validation_errors());
            }
        }

        $val['validation'] = $this->session->flashdata('alert');
        $this->twig->display('admin/product/add_product',$val);
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