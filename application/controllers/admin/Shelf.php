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

        if(!check_group(array('moderator','admin','uzytkownik')))
        {
            $this->session->set_flashdata('alert',"Nie masz dostępu do tej częsci serwisu!");
            redirect('account');
        }

        $this->twig->display('admin/magazine/magazine_geography');

    }

    public function add_shelf() //dodanie półek
    {

        if(!check_group(array('moderator','admin')))
        {
            $this->session->set_flashdata('alert',"Nie masz dostępu do tej częsci serwisu!");
            redirect('account');
        }

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
    public function generatnig()
    {
        $count = $this->input->post('count',true);
        $shelf = $this->input->post('shelf',true);
        $data['post_1'] = $count;
        $data['post_2'] = $shelf;
        if(!empty($_POST))
        {


        $data['validation'] = $this->session->flashdata('alert');
        $this->twig->display('admin/magazine/shelf/generating',$data);
        }

    }

    public function add_shelf_finally($id)
    {
        set_time_limit(120);

        $data['post_1'] = $this->input->post('param1',true);
        $data['post_2'] = $this->input->post('param2',true);
        $rack = $data['post_2'];
        $where = array('shel_result' => $data['post_2']);
        $data['shel_max_h'] = $this->Admin_model->get_single("STOR_SHELVES",$where);
        $sec= json_encode($data['shel_max_h']);
        $sec = json_decode($sec, true);
        $shel_max_h = $sec['shel_max_h'];           //maksymalna wysokość
        $shel_width = $sec['shel_width'];           //szerokość
        $shel_length = $sec['shel_length'];         //długość

        $cou= ((int)$shel_width * (int)$shel_length);

        $rows1=0;
        $rows2=0;

        $test=0;
        $pomo=0;
        for ($i=0; $i<$id; $i++)                       //sprawdaznie czy suma maksymalna półek jest mniejsza lub równa wszystkich półek
        {


            (int) $counter[$i] = $this->input->post("counter_$i",true);
            $pomo+=$counter[$i];

            if($pomo<=$shel_max_h)
            {
                $test =0;
            }

            else $test=1;
        }
            if($test==0) {
                for ($i = 0; $i < $id; $i++)   //tymczasowo jest ustalone na 3 indeksy, potem domyślnie będzie ilościowa liczba
                {
                    (int)$counter[$i] = $this->input->post("counter_$i", true);
                    if ((is_numeric($counter[$i])) && (($counter[$i]) < 99999) && (($counter[$i]) >= 1)) {
                        $regal = array("a" => $rows1, "b" => $rows2);

                        $seco = (int)$counter[$i] * (int)$cou;
                        $data = array(
                            'stor_height' => $counter[$i],
                            'stor_width_shel' => $shel_width,
                            'stor_length' => $shel_length,
                            'stor_capacity' => $seco,
                            'shel_result' => $rack,
                            'stor_shelve' => $regal["a"] . $regal["b"],
                            'stor_result' => $rack . "-" . $regal["a"] . $regal["b"]
                        );

                        $rows2++;
                        $this->Admin_model->create("STOR_SIZE", $data);
                        if ($rows2 == 10) {
                            $rows2 = 0;
                            $rows1++;
                        }


                    } else if (empty($counter[$i])) {

                        $this->session->set_flashdata('alert', "Wszystkie wartości muszą być uzupełnione");
                        $data['validation'] = $this->session->flashdata('alert');
                        //redirect(base_url('admin/magazine/rack/add_shelf'));

                    } else {
                        $counter[$i] = '';
                        $this->session->set_flashdata('alert', "Ilość półek musi być wartością numeryczną, w zakresie od 1-99999");
                        $data['validation'] = $this->session->flashdata('alert');
                    }


                }


                $data['counter'] = $counter;
                $this->session->set_flashdata('alert', "Półki wygenerowane!");
                $data['shel_result'] = $this->Admin_model->get("STOR_SHELVES");
                $this->twig->display('admin/magazine/shelf/add_shelf', $data);
            }
        else
            {
                $data['counter'] = $counter;
                $this->session->set_flashdata('alert', "Suma wysokości półek musi być mniejsza równa od maksymalnej wysokości tj: ". $shel_max_h ." !");
                $data['shel_result'] = $this->Admin_model->get("STOR_SHELVES");
                redirect(base_url('admin/shelf/add_shelf'));
                $this->twig->display('admin/magazine/shelf/add_shelf',$data);
            }

    }


    public function view_shelf()         //wyświetlenie i edycja wszystkich dostępnych magazynów
    {
        if(!check_group(array('moderator','admin','uzytkownik')))
        {
            $this->session->set_flashdata('alert',"Nie masz dostępu do tej częsci serwisu!");
            redirect('account');
        }

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