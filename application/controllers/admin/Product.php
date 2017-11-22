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

            if ($this->form_validation->run('add_product') == TRUE) {

                $this->db->trans_start(); //Otwieranie tranzakcji

                if($this->input->post('empty_dost',true) == true) {
                    //Tabela DOST
                    $data = array(
                        'dost_nazw' => $this->input->post('dost_nazw', true),
                        'dost_adres' => $this->input->post('dost_adres', true),
                        'dost_kod' => $this->input->post('dost_kod', true),
                        'dost_miasto' => $this->input->post('dost_miasto', true),
                        'dost_nip' => $this->input->post('dost_nip', true),
                    );

                    $this->Admin_model->create("DOST", $data);
                    $max = "id_dost";
                    $kod = $this->Admin_model->get_max("DOST", $max); //Pobieranie ostatniego ID z tabeli DOST
                    $kod[0] = $kod[0]->nr_mat;
                }
                else
                {
                    $kod[0] =  $this->input->post('select_dost',true);
                }

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


                $this->Admin_model->create("INDK", $data); //Tworzenie wpisu do INDK

                $max = "nr_mat";
                $kod = $this->Admin_model->get_max("INDK", $max); //Pobieranie ostatniego ID z tabeli INDK
                $this->db->trans_complete();//Zakończenie tranzakcji

                //Tabela NAZW
                $data = array(
                    'mat_nazwk' => $this->input->post('mat_nazwk', true),
                    'mat_nazwd' => $this->input->post('mat_nazwd', true),
                    'nr_mat' => $kod[0],
                    'kl_jez' => "PL",
                );

                $this->Admin_model->create("NAZW", $data);


                //Tabela GRTW
                $data = array(
                    'prod_hier' => $this->input->post('prod_hier', true),
                    'prod_opis' => "dodatki",
                    'nr_mar' => $kod[0],
                );
                $this->Admin_model->create("GRTW", $data);

                //Tabela MWYM
                $data = array(
                    'j_str' => $this->input->post('j_str', true),
                    'wart_str' => "1",//$this->input->post('wart_str', true), //Poprawic
                    'waga_ne' => $this->input->post('waga_ne', true),
                    'waga_br' => $this->input->post('waga_br', true),
                    'wart_dl' => $this->input->post('wart_dl', true),
                    'wart_szer' => $this->input->post('wart_szer', true),
                    'wart_wys' => $this->input->post('wart_wys', true),
                    'wart_obj' => "24",//$this->input->post('wart_obj',true),
                    'j_obj' => "cm3",//$this->input->post("j_obj",true),
                    'j_wag' => $this->input->post('j_wag', true),
                    'j_wym' => $this->input->post('j_wym', true),
                    'ean_kod' => $this->input->post('ean_kod', true),
                    'nr_mat' => $kod[0],
                );
                $this->Admin_model->create("MWYM", $data);

                //Tabela dostzwr
                $data = array(
                    'dostzwr_name' =>$this->input->post('dostzwr_name',true),
                    'dostzwr_adres' => $this->input->post('dostzwr_adres',true),
                    'dostzwr_kod' => $this->input->post('dostzwr_kod',true),
                    'dostzwr_miasto' => $this->input->post('dostzwr_miasto',true)
                );

                //$this->Admin_model->create("DOSTZWR", $data);

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

    public function add_product_ref ($id='')
    {
        if(!empty($id))
        {
            $where = array('id_indk'=>$id);
            $data['product'] = $this->Admin_model->get_single("VIEW_INDK_MWYM",$where);

            print_r($data);

            $this->twig->display('admin/product/add_product_ref',$data);
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