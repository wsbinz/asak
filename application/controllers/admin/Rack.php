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

    public function add_rack() //dodanie regały
    {



        if(!empty($_POST))
        {
            $count = $this->input->post('count',true);
            $storage = $this->input->post('storage',true);
            $highRack = $this->input->post('highRack',true);
            $data['post_1'] = $count;



            if((!empty($count))&&(!empty($highRack)))               //generowanie regałów
            {
                if ((is_numeric($count))&&(is_numeric($highRack)))
                    {
                        if (($count <= 100)&&($count > 0))
                            {
                                $max = $count;

                                $regal = array("a" => 0, "b" => 0);
                                $pom = 0;

                                for ($i = 0; $i <= 9; $i++) {
                                    $regal["b"] = 0;
                                    for ($j = 0; $j <= 9; $j++) {
                                        //echo $regal["a"];
                                        $data = array(
                                            'load_group' => $storage,
                                            'shel_descr' => $regal["a"] . $regal["b"],
                                            'shel_result' => $storage . "-" . $regal["a"] . $regal["b"],
                                            'shel_max_h' => $highRack);


                                        $this->Admin_model->create("STOR_SHELVES", $data);
                                        $regal["a"];
                                        //echo $regal["b"]++."<br>";
                                        $regal["b"]++;
                                        $pom++;
                                        if ($pom >= $max)
                                            break;
                                    }
                                    $regal["a"]++;
                                    if ($pom >= $max)
                                        break;
                                    else continue;


                                }
                                $this->session->set_flashdata('alert', "W magazynie $storage, ilość regałów wygenerowanych:  $count");
                                $this->twig->display('admin/magazine/rack/add_rack', $data);
                            }

                        else
                            {
                                $data['post_1'] = $count;
                                $data['post_2'] = $highRack;
                                $this->session->set_flashdata('alert', "Ilość dopuszczalnych regałów w jednej strefie magazynowej musi wynosić od 1-100.");
                                $this->twig->display('admin/magazine/rack/add_rack', $data);
                            }
                    }

                else
                    {
                        $data['post_1'] = $count;
                        $data['post_2'] = $highRack;
                        $this->session->set_flashdata('alert', "Ilość i wysokość regałów musi być wartością numeryczną!");
                        $this->twig->display('admin/magazine/rack/add_rack', $data);

                    }
            }
            else
                {
                    $data['post_1'] = $count;
                    $data['post_2'] = $highRack;
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
        set_time_limit(120);

        $total_rows = $this->Admin_model->num_rows("STOR_SHELVES");
        $start_index = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0 ;

        $config['total_rows'] = $total_rows;
        $config['per_page'] = 10;
        $config['uri_segment'] = 4;
        $config['base_url'] = base_url('admin/magazine/rack/view_rack');

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







}