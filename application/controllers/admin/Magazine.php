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
        if(!check_group(array('moderator','admin','uzytkownik')))
        {
            $this->session->set_flashdata('alert',"Nie masz dostępu do tej częsci serwisu!");
            redirect('account');
        }
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

    public function add_magazine() //dodanie magazynu
    {
        if(!check_group(array('moderator','admin')))
        {
            $this->session->set_flashdata('alert',"Nie masz dostępu do tej częsci serwisu!");
            redirect('account');
        }

        if(!empty($_POST))
        {

            $magazine = $this->input->post('magazine',true);
            $NameOfMagazin = $this->input->post('NameOfMagazin',true);
            $countRack = $this->input->post('countRack',true);
            $highRack = $this->input->post('highRack',true);
            $auxiliary = "A"; //pomocnicza
            if(!empty($magazine))
            {

                $where = array('load_group' => $magazine);
                $data['storage'] = $this->Admin_model->get_single("STORAGE", $where);

                if(!empty($data['storage']))
                {
                    $this->session->set_flashdata('alert', "Podane oznaczenie magazynu już istnieje w bazie!");
                    $data['validation'] = $this->session->flashdata('alert');
                    $data['post_1'] = $magazine;
                    $data['post_2'] = $magazine;
                    $data['post_3'] = $countRack;
                    $data['post_4'] = $highRack;
                    $this->twig->display('admin/magazine/add_magazine', $data);
                }

                else if (!empty($NameOfMagazin))
                {

                    if ((!empty($countRack)) && (!empty($highRack)))
                    {
                        if ((is_numeric($countRack)) && (is_numeric($highRack)))          //generowanie regałów
                        {
                            if (($countRack <= 100)&&($countRack > 0)) {
                                $max = $countRack;
                                $auxiliary ="B";

                                $regal = array("a" => 0, "b" => 0);
                                $pom = 0;

                                for ($i = 0; $i <= 9; $i++) {
                                    $regal["b"] = 0;
                                    for ($j = 0; $j <= 9; $j++) {
                                        //echo $regal["a"];
                                        $data = array(
                                            'load_group' => $magazine,
                                            'shel_descr' => $regal["a"] . $regal["b"],
                                            'shel_result' => $magazine . "-" . $regal["a"] . $regal["b"],
                                            'shel_max_h' =>$highRack);

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

                                $data = array(
                                    'load_group' => $magazine,
                                    'load_group_descr' => $NameOfMagazin,
                                );

                                $this->Admin_model->create("STORAGE", $data);
                                $this->session->set_flashdata('alert', "Magazyn został dodany oraz wygenerowano $countRack regałów");

                            }

                            else {
                                $this->session->set_flashdata('alert', "Ilość dopuszczalnych regałów w jednej strefie magazynowej musi wynosić od 1-100.");
                                $data['post_1'] = $NameOfMagazin;
                                $data['post_2'] = $magazine;
                                $data['post_3'] = $countRack;
                                $data['post_4'] = $highRack;
                                $this->twig->display('admin/magazine/add_magazine', $data);
                                $auxiliary ="B";

                            }
                        } else {
                            $this->session->set_flashdata('alert', "Ilość i wysokość regałów musi być wartością numeryczną!");
                            //refresh();
                            $data['post_1'] = $NameOfMagazin;
                            $data['post_2'] = $magazine;
                            $data['post_3'] = $countRack;
                            $data['post_4'] = $highRack;
                            $this->twig->display('admin/magazine/add_magazine', $data);
                            $auxiliary ="B";
                        }


                    }
                        if($auxiliary=="B")
                        {
                            $data['post_1'] = $NameOfMagazin;
                            $data['post_2'] = $magazine;
                            $data['post_3'] = $countRack;
                            $data['post_4'] = $highRack;
                            $this->twig->display('admin/magazine/add_magazine', $data);
                        }
                        else{
                        $data = array(
                            'load_group' => $magazine,
                            'load_group_descr' => $NameOfMagazin,
                        );

                        $this->Admin_model->create("STORAGE", $data);
                        $this->session->set_flashdata('alert', "Magazyn został dodany!");
                        fileLog("Pomyslnie dodano magazyn o nazwie:" . $NameOfMagazin,'Success');

                        }

                    } else {
                        $this->session->set_flashdata('alert', "Uzupełnij wszystkie pola!");
                        $data['post_2'] = $magazine;
                        $data['post_3'] = $countRack;
                        $data['post_4'] = $highRack;
                        $this->twig->display('admin/magazine/add_magazine', $data);

                    }
            }
            else
            {
                $this->session->set_flashdata('alert', "Uzupełnij wszystkie pola!");
                $data['post_1'] = $NameOfMagazin;
                $data['post_3'] = $countRack;
                $data['post_4'] = $highRack;
                $this->twig->display('admin/magazine/add_magazine', $data);
                    //refresh();
            }


        }
        else
            {

            }


        $data['validation'] = $this->session->flashdata('alert');
        $this->twig->display('admin/magazine/add_magazine',$data);
    }

    public function view_magazine()         //wyświetlenie i edycja wszystkich dostępnych magazynów
    {
        if(!check_group(array('moderator','admin','uzytkownik')))
        {
            $this->session->set_flashdata('alert',"Nie masz dostępu do tej częsci serwisu!");
            redirect('account');
        }

        set_time_limit(120);

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
        if(!check_group(array('moderator','admin')))
        {
            $this->session->set_flashdata('alert',"Nie masz dostępu do tej częsci serwisu!");
            redirect('account');
        }

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

        if(!check_group(array('moderator','admin')))
        {
            $this->session->set_flashdata('alert',"Nie masz dostępu do tej częsci serwisu!");
            redirect('account');
        }

        $where = array('id_storage' => $id);
        $data['load_group']= $this->input->post('magazine',true);
        $data['load_group_descr']= $this->input->post('NameOfMagazin',true);
        $data['storage'] = $this->Admin_model->update("STORAGE",$data,$where);
        $this->session->set_flashdata('alert', "Magazyn edytowany pomyślnie !");
        fileLog("Pomyslnie edytowano magazyn o nazwie:" . $this->input->post('NameOfMagazin',true),'Success');

        redirect(base_url("admin/magazine/view_magazine"));

    }

    public function delete_magazine($id)             //Usunięcie magazynu o określonym ID
    {
        if(!check_group(array('admin')))
        {
            $this->session->set_flashdata('alert',"Nie masz dostępu do tej częsci serwisu!");
            redirect('account');
        }

        $where = array('id_storage' => $id);

        $data['storage'] = $this->Admin_model->get_single("STORAGE",$where);
        if(!empty($data['storage']))
            {
                $data['validation'] = $this->session->flashdata('alert');
                $this->twig->display('admin/magazine/delete_magazine', $data);
            }
        else
            {
                $this->session->set_flashdata('alert', "Brak podanego ID magazynu !");
                redirect('admin/magazine/view_magazine');
            }

    }

    public function pernament_delete($id)
    {
        if(!check_group(array('admin')))
        {
            $this->session->set_flashdata('alert',"Nie masz dostępu do tej częsci serwisu!");
            redirect('account');
        }

        $where = array('id_storage' => $id);
        $data['storage'] = $this->Admin_model->get_single("STORAGE",$where);

        $sec= json_encode($data['storage']);                     //
        $sec = json_decode($sec, true);                    // Usuwanie regałów
        $third = $sec['load_group'];                             //
        $where2 = array('load_group'=> $third);                  //


        $data['stor_shelves'] = $this->Admin_model->delete("STOR_SHELVES",$where2);


        $this->twig->display('site/magazine/list_magazine',$data);


        $data['storage'] = $this->Admin_model->delete("STORAGE",$where);
        redirect('admin/magazine/view_magazine');




    }

}
