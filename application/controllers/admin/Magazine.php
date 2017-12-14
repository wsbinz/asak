<?php
/**
 * Created by PhpStorm.
 * User: PizzaLap
 * Date: 08.12.2017
 * Time: 09:08
 */

defined('BASEPATH') OR exit('No direct script access allowed');


class Magazine extends Admin_Controller
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

    public function add_magazine() //dodanie magazynu
    {
        if(!empty($_POST))
        {

            $magazine = $this->input->post('magazine',true);
            $NameOfMagazin = $this->input->post('NameOfMagazin',true);

            if(!empty($magazine))
            {
                if(!empty($NameOfMagazin))
                {
                $data = array(
                    'load_group' => $magazine,
                    'load_group_descr' => $NameOfMagazin,
                );


                $this->Admin_model->create("STORAGE", $data);
                    $this->session->set_flashdata('alert', "Dodales magazyn!");
                    refresh();

                }
                else{
                    $this->session->set_flashdata('alert', "Uzupełnij wszystkie pola!");
                    $data['post_2'] = $magazine;
                    refresh();
                }

            }
            else
            {
                $this->session->set_flashdata('alert', "Uzupełnij wszystkie pola!");
                $data['post_1'] = $NameOfMagazin;
                refresh();
            }

        }


        $data['validation'] = $this->session->flashdata('alert');
        $this->twig->display('admin/magazine/add_magazine',$data);
    }

    public function view_magazine()         //wyświetlenie i edycja wszystkich dostępnych magazynów
    {
        $total_rows = $this->Admin_model->num_rows("STORAGE");
        $start_index = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0 ;

        $config['total_rows'] = $total_rows;
        $config['per_page'] = 10;
        $config['uri_segment'] = 4;
        $config['base_url'] = base_url('admin/magazine/view_magazine');

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

        $data['views'] = $this->Admin_model->get("STORAGE",$config['per_page'],$start_index);

        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $data['validation'] = $this->session->flashdata('alert');
        $this->twig->display('site/magazine/list_magazine',$data);

    }

    public function edit_magazine($id)             //edycja magazynu o określonym ID
    {
        if(is_numeric($id)) {

            $where = array('id_storage' => $id);

            $data['storage'] = $this->Admin_model->get_single("STORAGE", $where);

            $data['validation'] = $this->session->flashdata('alert');

            $this->twig->display('admin/magazine/edit_magazine', $data);
        }
        else
        {
            $this->session->set_flashdata('alert', "Podany magazyn nie istnieje !");
            //refresh();
           redirect(base_url("admin/magazine/view_magazine"));
        }

    }

    public function save_magazine($id)             //edycja magazynu o określonym ID
    {
        $where = array('id_storage' => $id);
        $data['load_group']= $this->input->post('magazine',true);
        $data['load_group_descr']= $this->input->post('NameOfMagazin',true);
        $data['storage'] = $this->Admin_model->update("STORAGE",$data,$where);
        $this->session->set_flashdata('alert', "Magazyn edytowany pomyślnie !");
        redirect(base_url("admin/magazine/view_magazine"));

    }

    public function delete_magazine($id)             //Usunięcie magazynu o określonym ID
    {

        $where = array('id_storage' => $id);

        $data['storage'] = $this->Admin_model->get_single("STORAGE",$where);

        $data['validation'] = $this->session->flashdata('alert');


        $this->twig->display('admin/magazine/delete_magazine', $data);

    }

    public function pernament_delete($id)
    {
        $where = array('id_storage' => $id);

        $data['storage'] = $this->Admin_model->delete("STORAGE",$where);
        redirect('admin/magazine/view_magazine');
       // $data['validation'] = $this->session->flashdata('alert');
        //$this->twig->display('site/magazine/list_magazine',$data);
    }

    //Algorytm do regałów/półek
/*$wys = 222.6;
$pol_wys = 8.6;

$max = $wys/$pol_wys;
echo $max;

$regal = array("a"=>0, "b"=>0);
$pom = 0;

for ($i=0; $i<=9; $i++)
{
$regal["b"] = 0;
for ($j = 0; $j<=9; $j++)
{
echo  $regal["a"];
echo  $regal["b"]++."<br>";
$pom++;
if($pom>=$max)
exit();
}
$regal["a"]++;
}*/

}
