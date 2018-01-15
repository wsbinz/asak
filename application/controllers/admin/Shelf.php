<?php
/**
 * Created by PhpStorm.
 * User: PizzaLap
 * Date: 13.12.2017
 * Time: 20:21
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Shelf extends Admin_Controller
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

    public function add_shelf() //dodanie półek
    {
        if(!empty($_POST))
        {
            $count = $this->input->post('count',true);
            $shelf = $this->input->post('shelf',true);
            $data['post_1'] = $count;
            $data['post_2'] = $shelf;

            if (is_numeric($count))
                {
                    if (($count <= 100)&&(($count > 0)))
                        {
                            $data['post_1'] = $count;
                            $data['post_2'] = $shelf;

                            //$data['validation'] = $this->session->flashdata('alert');
                            $this->twig->display('admin/magazine/shelf/generating',$data);
                        }
                    else
                        {
                            $data['post_1'] = $count;
                            $this->session->set_flashdata('alert', "Ilość półek nie może przekraczać zakresu od 1 do 100");
                            $data['shel_result'] = $this->Admin_model->get("STOR_SHELVES");
                            $data['validation'] = $this->session->flashdata('alert');
                            $this->twig->display('admin/magazine/shelf/add_shelf', $data);
                        }
                }
            else
                {
                    $data['post_1'] = $count;
                    $this->session->set_flashdata('alert', "Ilość półek musi być wartością numeryczną");
                    $data['shel_result'] = $this->Admin_model->get("STOR_SHELVES");
                    $data['validation'] = $this->session->flashdata('alert');
                    $this->twig->display('admin/magazine/shelf/add_shelf', $data);
                }
        }
        else
            {
                $this->session->set_flashdata('alert', "Ilość półek nie może być puste");
                $this->twig->display('admin/magazine/shelf/add_shelf');

                $data['shel_result'] = $this->Admin_model->get("STOR_SHELVES");

                $data['validation'] = $this->session->flashdata('alert');
                $this->twig->display('admin/magazine/shelf/add_shelf',$data);
            }


    }
    public function generating()
    {
        if(!empty($_POST))
        {
        $data['validation'] = $this->session->flashdata('alert');
        $this->twig->display('admin/magazine/shelf/generating',$data);
        }

    }

    public function add_shelf_finally()
    {
        set_time_limit(120);
        //$count = $this->input->post('count',true);
        //$data['post_1'] = $count;
        //print_r($count);
        echo "<br/>Adam<br/>";

//        if(!empty($_POST))
//        {
            for ($i=0; $i<3; $i++)   //tymczasowo jest ustalone na 3 indeksy, potem domyślnie będzie ilościowa liczba
            {
                (int) $counter_[$i] = $this->input->post("counter_$i",true);
                echo "<br/>"+ $counter_[$i];
            }
//
//        }



    }


    public function view_shelf()         //wyświetlenie i edycja wszystkich dostępnych magazynów
    {
        set_time_limit(120);

        $total_rows = $this->Admin_model->num_rows("STOR_SIZE");
        $start_index = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0 ;

        $config['total_rows'] = $total_rows;
        $config['per_page'] = 10;
        $config['uri_segment'] = 4;
        $config['base_url'] = base_url('admin/shelf/view_shelf');

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

        $data['views'] = $this->Admin_model->get("STOR_SIZE",$config['per_page'],$start_index);

        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['validation'] = $this->session->flashdata('alert');
        $this->twig->display('site/magazine/shelf/list_shelf',$data);

    }


}