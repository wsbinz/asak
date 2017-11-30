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
        $this->load->library('image_lib');
        $this->load->library('pagination');
    }

    public function index($id='')
    {
        if(logged_in()!= 1)
        {
            redirect('account');
        }

        $total_rows = $this->Admin_model->num_rows("MNAME");
        $start_index = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0 ;

        $config['total_rows'] = $total_rows;
        $config['per_page'] = 3;
        $config['uri_segment'] = 4;
        $config['base_url'] = base_url('admin/product/index');

/*        $config['num_links'] = 2;
        $config['use_page_numbers'] = TRUE;
        $config['reuse_query_string'] = TRUE;*/

        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';

        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li class="">';
        $config['first_tag_close'] = '</li>';

        $config['last_link'] = 'Last';
        $config['last_tag_open'] = '<li class="">';
        $config['last_tag_close'] = '</li>';

        $config['next_link'] = 'Next';
        $config['next_tag_open'] = '<li class="page-item"><span class="page-link">';
        $config['next_tag_close'] = '</span></li>';

        $config['prev_link'] = 'Previous';
        $config['prev_tag_open'] = '<li class="page-item"><span class="page-link">';
        $config['prev_tag_close'] = '</span></li>';

        $config['cur_tag_open'] = '<li class="page-item active"><a href="">';
        $config['cur_tag_close'] = '</a></li>';

        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';

        $data['indk_mwym'] = $this->Admin_model->get("MNAME",$config['per_page'],$start_index);

        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['validation'] = $this->session->flashdata('alert');
        $this->twig->display('site/product/lists_products',$data);

    }

    public function show($id)
    {
        $where = array('nr_mat' => $id);
        $data['single_indk_mwym'] = $this->Admin_model->get_single("VIEW_INDK_MWYM",$where);
        $this->twig->display('site/product/list_product',$data);
    }

    public function add_product()
    {

       $create_date = time();

        if(!empty($_POST)) {

            if ($this->form_validation->run('add_product') == TRUE) {

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
                    $max = "id_vend";
                    $kod = $this->Admin_model->get_max("VEND", $max); //Pobieranie ostatniego ID z tabeli DOST
                    $kod[0] = $kod[0]->nr_mat;
                }
                else
                {
                    $kod[0] =  $this->input->post('select_vend',true);
                }


                if ($this->input->post("empty_vend_refund",true) == true)
                {
                    //Tabela dostzwr
                    $data = array(
                        'vendrefund_name' =>$this->input->post('vendrefund_name',true),
                        'vendrefund_adress' => $this->input->post('vendrefund_adress',true),
                        'vendrefund_code' => $this->input->post('vendrefund_code',true),
                        'vendrefund_city' => $this->input->post('vendrefund_city',true)
                    );

                    $this->Admin_model->create("VEND_REFUND", $data);

                    $max = "id_vendrefund";
                    $kod_refund = $this->Admin_model->get_max("VEND_REFUND", $max); //Pobieranie ostatniego ID z tabeli DOST
                    $kod_refund[0] = $kod_refund[0]->id_vendrefund;

                }
                else
                {
                    $kod_refund[0] =  $this->input->post('select_vend_refund',true);
                }


                //Tabela INDK
                $data = array(
                    'load_group' => $this->input->post('load_group', true),
                    'pkwiu_code' => $this->input->post('pkwiu_code', true),
                    'tax' => $this->input->post('tax', true),
                    'create_date' => date('d-m-Y'),
                    'create_user' => $_SESSION['id'],
                    'prod_hier' => $this->input->post('prod_hier', true),
                    'id_vend' => $kod[0],
                    'id_vendrefund' => $kod_refund[0],
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
            for($i=0; $i<4; $i++) {
                $data = array(
                    'unit_structure' => $this->input->post('unit_structure', true)[$i],
                    'value_struct' => $this->input->post('value_struct', true)[$i], //Poprawic
                    'weight_net' => $this->input->post('weight_net', true)[$i],
                    'weight_gross' => $this->input->post('weight_gross', true)[$i],
                    'value_length' => $this->input->post('value_length', true)[$i],
                    'value_width' => $this->input->post('value_width', true)[$i],
                    'value_height' => $this->input->post('value_height', true)[$i],
                    'value_capacit' => $this->input->post('value_capacit',true)[$i],
                    'unit_capacity' => "cm3",//$this->input->post("j_obj",true),
                    'unit_weight' => $this->input->post('unit_weight', true)[$i],
                    'unit_dim' => $this->input->post('unit_dim', true)[$i],
                    'ean_code' => $this->input->post('ean_code', true)[$i],
                    'nr_mat' => $nr_mat,
                );
                $this->Admin_model->create("MSIZE", $data);
            }



            //Upload IMG
                if(!empty($_FILES))
                {
                    $move = true;
                    foreach ($_FILES as $file) {
                        $tempFile = $file['tmp_name'];
                        $fileName = $file['name'];
                        $targetPath = BASEPATH . "../asset/img/product/";
                        $targetFile = $targetPath . $fileName;
                        $file_exist = file_exists($targetFile);
                    }

                    if (!$file_exist) {
                        $move = move_uploaded_file($tempFile, $targetFile);

                        $config['image_library'] = 'gd2';
                        $config['source_image'] = $targetFile;
                        $config['create_thumb'] = FALSE;
                        // $config['new_image'] = BASEPATH.'../asset/file/home/thumbs';
                        $config['maintain_ratio'] = TRUE;
                        $config['width'] = 1000;
                        // $config['height'] = 200;

                        $this->image_lib->initialize($config);
                        $this->image_lib->resize();
                    } else {
                        $duplicate = true;
                        $this->session->set_flashdata('alert', "Taki plik już istnieje !");
                    }

                    if($move == true)
                    {
                        $targetFile = strstr($targetFile, 'asset');
                        $data = array(
                            'nr_mat' => $nr_mat,
                            'adr_ph' => $targetFile,

                        );
                        $this->Admin_model->create("PHOT", $data);
                    }

                }
                else
                {
                    $this->session->set_flashdata('alert', "Nie przesłano pliku");
                }


                $this->session->set_flashdata('alert', "Pomyślnie dadano!");
                redirect('admin/product');
            }
            else {
                $this->session->set_flashdata('alert', validation_errors());
            }
        }



        $variable['dost'] = $this->Admin_model->get('VEND');
        $variable['dost_zwrot'] = $this->Admin_model->get('VEND_REFUND');
        $variable['pkwiu'] = $this->Admin_model->get('PKWI');
        $variable['validation'] = $this->session->flashdata('alert');
        $this->twig->display('admin/product/add_product',$variable);
    }

    public function add_product_ref ($id='')
    {
        if(!empty($id))
        {
            $where = array('id_indk'=>$id);
            $data['product'] = $this->Admin_model->get_single("VIEW_CARGO",$where);

            $where = array('nr_mat'=>$id);
            $data['msize'] = $this->Admin_model->get_where("MSIZE",$where);

            $data['pkwiu'] = $this->Admin_model->get('PKWI');
            $data['dost'] = $this->Admin_model->get('VEND');
            $data['dost_zwrot'] = $this->Admin_model->get('VEND_REFUND');

            print_r($data['msize']);

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
        $col = array('mat_nazwd'=>strtolower($this->input->post('mat_nazwk',true)));
        $search = $this->Admin_model->search("VIEW_CARGO",$col);
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