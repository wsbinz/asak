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
        $this->load->library('form_validation');
    }

    public function index($id='')
    {

        check_group(array('admin','moderator'));

        if(logged_in()!= 1)
        {
            redirect('login');
        }

        $total_rows = $this->Admin_model->num_rows("MNAME");
        $start_index = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0 ;

        $config['total_rows'] = $total_rows;
        $config['per_page'] = 25;
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
        $user_arr = array();

        $where = array('nr_mat' => $id);
        $data['product'] = $this->Admin_model->get_single("VIEW_CARGO",$where);
        $users = $this->Admin_model->get("USERS");

        foreach ($users as $user)
        $user_arr[$user->id] = $user->email;

        $data['users'] = $user_arr;

        $data['msize'] = $this->Admin_model->get_where("MSIZE",$where);

        $data['photo'] = $this->Admin_model->get_single("PHOT",$where);

        if(empty($data['product']))
        {
            $this->session->set_flashdata('alert', "Podany produkt nie istnieje !");
            redirect('account');
        }

        $this->twig->display('admin/product/show_product',$data);
    }

    public function add_product()
    {

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
                    'retire' => 0,
                );


                $this->Admin_model->create("INDK", $data); //Tworzenie wpisu do INDK

                $max = "nr_mat";
                $nr_mat = $this->Admin_model->get_max("INDK", $max); //Pobieranie ostatniego ID z tabeli INDK
                $nr_mat = $nr_mat[0] -> nr_mat;
                $this->db->trans_complete();//Zakończenie tranzakcji

                //Tabela NAZW
                $data = array(
                    'name_short' => strtolower($this->input->post('name_short', true)),
                    'name_long' => strtolower($this->input->post('name_long', true)),
                    'nr_mat' => $nr_mat,
                    'lang' => "PL",
                );

                $this->Admin_model->create("MNAME", $data);

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
                    $move = false;
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
                        $where = array("nr_mat" => $nr_mat);
                        $this->Admin_model->delete("MSIZE",$where);
                        $this->Admin_model->delete("MNAME",$where);
                        $this->Admin_model->delete("INDK",$where);

                    }

                    if($move == true)
                    {
                        $targetFile = strstr($targetFile, 'asset');
                        $data = array(
                            'nr_mat' => $nr_mat,
                            'adr_ph' => $targetFile,

                        );
                        $this->Admin_model->create("PHOT", $data);
                        $this->session->set_flashdata('alert', "Pomyślnie dadano!");
                    }

                }
                else
                {
                    $variable['post'] = $_POST;
                    $this->twig->display('admin/product/add_product',$variable);
                    $this->session->set_flashdata('alert', "Nie przesłano pliku");
                }
                if ($duplicate==true)
                {
                    $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    $variable['post'] = $_POST;
                    fileLog('Żle wypełnił formularz dodawania produktu. Treść błędu: '.validation_errors(),"Error");

                }
                else {
                    fileLog("Pomyślnie dodano produkt o numerze:" . $nr_mat . " Nazwa produktu to: " . strtolower($this->input->post('name_short', true), 'Success'));
                    redirect('admin/product');
                }
            }
            else {
                print_r($_POST);
                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                $variable['post'] = $_POST;
                fileLog('Żle wypełnił formularz dodawania produktu. Treść błędu: '.validation_errors(),"Error");
                $this->session->set_flashdata('alert', validation_errors());
            }
        }



        $variable['dost'] = $this->Admin_model->get('VEND');
        $variable['dost_zwrot'] = $this->Admin_model->get('VEND_REFUND');
        $variable['pkwiu'] = $this->Admin_model->get('PKWI');
        $variable['load_group'] = $this->Admin_model->get('STORAGE');
        $variable['gr_tow'] = $this->Admin_model->get('PROD');
        $variable['unit_weight'] = $this->Admin_model->get_where('UNITS', array('unit_weight' => '1'));
        $variable['unit_dim'] = $this->Admin_model->get_where('UNITS', array('unit_dim' => '1'));
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
            $data['photo'] = $this->Admin_model->get_single("PHOT",$where);
           // print_r($data['msize']);

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
        $col = array('mat_nazwk'=>strtolower($this->input->post('mat_nazwk',true)));
        $or = array('kod_pkwiu' => $this->input->post('mat_nazwk',true));
        $where = array('retire !=' => '1');
        $search = $this->Admin_model->search("VIEW_CARGO",$col,$or,$where);
        echo json_encode($search);
    }


   public function edit_product($id='')
   {

       if(!check_group(array('admin','moderator')))
       {
           $this->session->set_flashdata('alert', "Nie możesz edytować produktów");
           redirect('account');
       }

       if(!empty($id))
       {

           if(!is_numeric($id))
           {
               $this->session->set_flashdata('alert', "Nie ma takiego produktu");
               redirect("/account");
           }
           $where = array('id_indk'=>(int)$id);
           $data['product'] = $this->Admin_model->get_single("VIEW_CARGO",$where);


           $where = array('nr_mat'=>$id);
           $data['msize'] = $this->Admin_model->get_where("MSIZE",$where);

           $data['pkwiu'] = $this->Admin_model->get('PKWI');
           $data['dost'] = $this->Admin_model->get('VEND');
           $data['dost_zwrot'] = $this->Admin_model->get('VEND_REFUND');
           $data['load_group'] = $this->Admin_model->get('STORAGE');

           if(!empty($data['product']))
           {

               if(!empty($_POST) && isset($_POST['submit']))
               {
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
                       'prod_hier' => $this->input->post('prod_hier', true),
                       'id_vend' => $kod[0],
                       'id_vendrefund' => $kod_refund[0],
                       'change_date' => date('d-m-Y'),
                       'change_user' => $_SESSION['id'],
                   );

                   $where = array('nr_mat' => $id);
                   $this->Admin_model->update("INDK", $data,$where); //Tworzenie wpisu do INDK

/*                   $max = "nr_mat";
                   $nr_mat = $this->Admin_model->get_max("INDK", $max); //Pobieranie ostatniego ID z tabeli INDK
                   $nr_mat = $nr_mat[0] -> nr_mat;*/
                   $this->db->trans_complete();//Zakończenie tranzakcji

                   //Tabela NAZW
                   $data = array(
                       'name_short' => $this->input->post('name_short', true),
                       'name_long' => $this->input->post('name_long', true),
                       'nr_mat' => $id,
                       'lang' => "PL",
                   );
                   $where = array('nr_mat' => $id);
                   $this->Admin_model->update("MNAME", $data,$where);


/*                   //Tabela STORAGE  - grupa zaladunkowa
                   $data = array(
                       'load_group' => $this->input->post('load_group', true),
                       'load_group_descr' => "dodatki",
                   );
                   $this->Admin_model->update("STORAGE", $data,$where);*/

                   //Tabela MWYM
                   $where = array('nr_mat' => $id);
                   $id_msize = $this->Admin_model->get_where("MSIZE", $where);
                   for($i=0; $i<4; $i++) {
                       $data = array(
                           'unit_structure' => $this->input->post('unit_structure', true)[$i],
                           'value_struct' => $this->input->post('value_struct', true)[$i],
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
                           'nr_mat' => $id,
                       );
                       $where = array('id_msize' => $id_msize[$i]->id_msize);
                       $this->Admin_model->update("MSIZE", $data,$where);
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
                               'nr_mat' => $id,
                               'adr_ph' => $targetFile,

                           );
                           if(!empty($fileName))
                           $this->Admin_model->create("PHOT", $data);
                       }

                   }
                   else
                   {
                       fileLog("Nie przesłano pliku do  produkt o numerze:" . $id . " Nazwa produktu to: " . strtolower($this->input->post('name_short', true)),'Warning');
                       $this->session->set_flashdata('alert', "Nie przesłano pliku");
                   }

                   fileLog("Pomyślnie edytowano produkt o numerze:" . $id . " Nazwa produktu to: " . strtolower($this->input->post('name_short', true)),'Success');
                   $this->session->set_flashdata('alert', "Pomyślnie edytowano produkt!");
                   redirect('admin/product');
               }

               $data['photo'] = $this->Admin_model->get_single("PHOT",$where);

               $this->twig->display('admin/product/edit_product', $data);
           }
           else
           {
               fileLog("Użytkowik o id: " . $_SESSION['id'] . " odwołał się do produkty który nie istnieje !", 'Warning');
               $this->session->set_flashdata('alert', "Nie ma takiego produktu");
               redirect('/account');
           }
       }
       else
       {
           $this->twig->display('admin/product/edit_product_search');
       }

       }


    public function remove_img()
    {


       if(!empty($_POST))
       {
           $photo = $this->Admin_model->get_single("PHOT",array('id_phot' => $_POST['id_img']));

           if(!empty($photo)) {
               if ((int)$_POST['segment_url'][7] == $photo->nr_mat) {
                   $this->load->helper("file");
                   unlink(FCPATH . $photo->adr_ph);
                   $this->Admin_model->delete('PHOT', array('id_phot' => $_POST['id_img']));
                   echo json_encode(array("message" => "Pomyślnie usuięto zdjęcie, a link to $photo->adr_ph", "code" => 200));

               }
           }
           else
           {
               echo json_encode(array("message"=>"chciałeś mnie oszukać ?!","code"=>400),JSON_UNESCAPED_UNICODE);
           }

          //echo var_dump($photo);

       }
    }

   public function retire_product()
   {
       $retire_product = $this->input->post('retire_product',true);

       if(!empty($this->input->post('retire_product',true))) {
           foreach ($retire_product as $key => $value) {

               if($value == 'on')
               {

                   $data = array(
                       'retire' => 1,
                   );

                   $where = array("nr_mat" => $key);
                   $this->Admin_model->update("INDK", $data,$where); //Tworzenie wpisu do INDK
                   $this->session->set_flashdata('alert', "Produkt o id: $key został wycofany.");
               }

           }
       }

      /* echo "<pre>";
       print_r($_POST);
       echo "</pre>";*/
       $data['validation'] = $this->session->flashdata('alert');
       $this->twig->display('admin/product/retire_product',$data);
   }


}