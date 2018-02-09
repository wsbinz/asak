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

        $this->twig->display('admin/magazine/magazine_geography');

    }

    public function add_rack() //dodanie regału
    {
        if(!check_group(array('moderator','admin')))
        {
            $this->session->set_flashdata('alert',"Nie masz dostępu do tej częsci serwisu!");
            redirect('account');
        }

        if(!empty($_POST))
        {
            $count = $this->input->post('count',true);
            $storage = $this->input->post('storage',true);
            $highRack = $this->input->post('highRack',true);
            $widthRack = $this->input->post('widthRack',true);
            $lengthRack = $this->input->post('lengthRack',true);

            $data['post_1'] = $count;
            $data['post_2'] = $highRack;
            $data['post_3'] = $widthRack;
            $data['post_4'] = $lengthRack;

            $where = array('load_group' => $storage);
            $rows = $this->Admin_model->num_rows_where("STOR_SHELVES",$where);

            $len = strlen($rows);
            $arr = array();
            for($i=0; $i<$len;$i++){
                $arr[] = substr($rows, $i, 1);
            }

            $len = strlen($count);
            $arra = array();
            for($i=0; $i<$len;$i++){
                $arra[] = substr($count, $i, 1);
            }

            if($rows>9)
                {
                    $rows1 = $arr[0];
                    $rows2 = $arr[1];
                }

            else
                {
                    $rows1 = $arr[0];
                    $rows2 = 0;
                }

            if((!empty($count))&&(!empty($highRack)))               //generowanie regałów
            {
                if ((is_numeric($count))&&(is_numeric($highRack))&&(is_numeric($widthRack))&&(is_numeric($lengthRack)))
                    {
                        if (($count <= 100)&&($count > 0)&&($count+$rows<100))
                            {
                                $regal = array("a" => $rows1, "b" => $rows2);
                                $pom1 = $rows1;
                                $pom2 = $rows2;
                                $pom3 = $count;

                                for ($i = 0; $i <= 9; $i++) {
                                    for ($j = 0; $j <= 9; $j++) {
                                        $data = array(
                                            'load_group' => $storage,
                                            'shel_descr' => $regal["a"] . $regal["b"],
                                            'shel_result' => $storage . "-" . $regal["a"] . $regal["b"],
                                            'shel_max_h' => $highRack,
                                            'shel_width'=> $widthRack,
                                            'shel_length'=> $lengthRack
                                        );


                                        $this->Admin_model->create("STOR_SHELVES", $data);
                                        $regal["b"]++;
                                        $pom2++;
                                        $pom3--;
                                        if ($regal["b"] >= 10)
                                        {
                                            $regal["b"]=0;
                                            break;
                                        }

                                        else if($pom3==0)
                                        {
                                            break;
                                        }
                                    }
                                    $pom1++;
                                    $regal["a"]++;
                                    if ($pom1 >= 9)
                                        break;
                                    else if($pom3==0)
                                    {
                                        break;
                                    }
                                    else continue;


                                }
                                $this->session->set_flashdata('alert', "W magazynie $storage, ilość regałów wygenerowanych:  $count maksymalnej wysokości:  $highRack");
                                $this->twig->display('admin/magazine/rack/add_rack', $data);
                            }

                        else
                            {
                                $data['post_1'] = $count;
                                $data['post_2'] = $highRack;
                                $data['post_3'] = $widthRack;
                                $data['post_4'] = $lengthRack;
                                $false= $count + $rows - 100;
                                $this->session->set_flashdata('alert', "Ilość dopuszczalnych regałów w jednej strefie magazynowej musi wynosić od 1-100.<br/>
                                Przekroczyłeś daną ilość o: $false");
                                $this->twig->display('admin/magazine/rack/add_rack', $data);
                            }
                    }

                else
                    {
                        $data['post_1'] = $count;
                        $data['post_2'] = $highRack;
                        $data['post_3'] = $widthRack;
                        $data['post_4'] = $lengthRack;
                        $this->session->set_flashdata('alert', "Ilość i wysokość regałów musi być wartością numeryczną!");
                        $this->twig->display('admin/magazine/rack/add_rack', $data);

                    }
            }
            else
                {
                    $data['post_1'] = $count;
                    $data['post_2'] = $highRack;
                    $data['post_3'] = $widthRack;
                    $data['post_4'] = $lengthRack;
                    $this->session->set_flashdata('alert', 'Pole "Ilość regałów" i "Maksymalna wysokość" nie może być puste!');
                    $this->twig->display('admin/magazine/rack/add_rack', $data);
                }

        }
        $data['load_group'] = $this->Admin_model->get("STORAGE");

        $data['validation'] = $this->session->flashdata('alert');
        $this->twig->display('admin/magazine/rack/add_rack',$data);


    }

    public function view_rack()         //wyświetlenie i edycja wszystkich dostępnych magazynów
    {
        if(!check_group(array('moderator','admin','uzytkownik')))
        {
            $this->session->set_flashdata('alert',"Nie masz dostępu do tej częsci serwisu!");
            redirect('account');
        }

        set_time_limit(120);

        $total_rows = $this->Admin_model->num_rows("STOR_SHELVES");
        $start_index = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0 ;

        $config['total_rows'] = $total_rows;
        $config['per_page'] = 10;
        $config['uri_segment'] = 4;
        $config['base_url'] = base_url('admin/rack/view_rack');

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

        $data['views'] = $this->Admin_model->get("STOR_SHELVES",$config['per_page'],$start_index);

        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['validation'] = $this->session->flashdata('alert');
        $this->twig->display('site/magazine/rack/list_rack',$data);

    }

    public function edit_rack($id)             //edycja regału o określonym ID
    {
        if(!check_group(array('moderator','admin')))
        {
            $this->session->set_flashdata('alert',"Nie masz dostępu do tej częsci serwisu!");
            redirect('account');
        }

        if(is_numeric($id)) {

            $where = array('id_stor_shelve' => $id);

            $data['stor_shelves'] = $this->Admin_model->get_single("STOR_SHELVES", $where);

            $data['validation'] = $this->session->flashdata('alert');

            $this->twig->display('admin/magazine/rack/edit_rack', $data);
        }
        else
        {
            $this->session->set_flashdata('alert', "Podany magazyn nie istnieje !");
            //refresh();
            redirect(base_url('admin/magazine/rack/edit_rack'));
        }

    }

    public function save_rack($id)             //edycja regału o określonym ID
    {

        if(!check_group(array('moderator','admin')))
        {
            $this->session->set_flashdata('alert',"Nie masz dostępu do tej częsci serwisu!");
            redirect('account');
        }

        $where = array('id_stor_shelve' => $id);
        $data['shel_result']= $this->input->post('rack',true);
        $data['shel_max_h']= $this->input->post('highRack',true);
        $data['stor_shelves'] = $this->Admin_model->update("STOR_SHELVES",$data,$where);
        $this->session->set_flashdata('alert', "Regał edytowany pomyślnie !");
        redirect(base_url("admin/rack/view_rack"));

    }



}