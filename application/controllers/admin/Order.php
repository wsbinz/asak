<?php
/**
 * Created by PhpStorm.
 * User: Boruś
 * Date: 2017-12-14
 * Time: 13:19
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Order extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('admin/Admin_model');
        $this->load->helper('My');
        $this->load->library('pagination');
    }
    public function order_products()
    {
        if(!empty($_POST)) {

            $docum_value = $this->input->post('docum_value',true);
            $nr_mat = 85;

            if (is_numeric($docum_value))
            {
                if (($docum_value >= 0)&&($docum_value <=100))
                {
                    $data = array(
                        'nr_docum' => $this->input->post('nr_docum', true),
                        'nr_mat' => $nr_mat,
                        'docum_value' => $docum_value,
                        //'value_sign' => $this->input->post('value_sign', true),
                    );
                    $this->Admin_model->create("DOCUM_ITEM", $data);

                    $data = array(
                        'nr_docum' => $this->input->post('nr_docum', true),
                        'docum_type' => 'ZM',
                        'vend_name' => $this->input->post('vend_name', true),
                        'docum_desc' => $this->input->post('docum_desc', true),
                    );
                    $this->Admin_model->create("DOCUM_HEAD", $data);

                    $this->session->set_flashdata('alert', "Zamówienie zostało wysłane");
                    redirect('account');
                }
                else
                {
                    $this->session->set_flashdata('alert', "Liczba zamawianego towaru musi być w przedziale od 0 do 100!");
                }
            }
            else
            {
                $this->session->set_flashdata('alert', "Liczba zamawianego towaru musi być wartością numeryczną!");
            }
        }

        $data['zam'] = $this->Admin_model->get("VIEW_ORDER");
        $data['dost'] = $this->Admin_model->get('VEND');
        $data['validation'] = $this->session->flashdata('alert');
        $this->twig->display('admin/docs/view_order',$data);
    }
    public function cpz()
    {
        if(!empty($_POST)) {

            $mat_amount = $this->input->post('mat_amount',true);
            $nr_mat = 85;

            if (is_numeric($mat_amount))
            {
                $data = array(
                    'nr_mat' => $nr_mat,
                    'mat_amount' => $mat_amount,
                );
                $where = array('nr_mat' => $nr_mat);
                $this->Admin_model->update("STOR_AMOUNTS", $data, $where);

                $max = "id_docum_head";
                $docum = $this->Admin_model->get_max("DOCUM_HEAD", $max);
                $docum[0] = $docum[0]->id_docum_head;

                $data = array(
                    'nr_docum' => $docum[0],
                    'nr_mat' => $nr_mat,
                    'docum_value' => $mat_amount,
                    //'value_sign' => $this->input->post('value_sign', true),
                );
                $this->Admin_model->create("DOCUM_ITEM", $data);

                $data = array(
                    'nr_docum' => $docum[0],
                    'docum_type' => 'PZ',
                    'docum_desc' => 'PZ techniczna',
                );
                $this->Admin_model->create("DOCUM_HEAD", $data);

                $this->session->set_flashdata('alert', "Utworzono dokument PZ");
                redirect('account');
            }
            else
            {
                $this->session->set_flashdata('alert', "Liczba przyjmowanego towaru musi być wartością numeryczną!");
            }


        }
        $data['indk_mwym'] = $this->Admin_model->get("VIEW_ORDER");
        $data['validation'] = $this->session->flashdata('alert');
        $this->twig->display('admin/docs/create_pz',$data);
    }
    public function cwz()
    {
        if(!empty($_POST)) {

            $mat_amount = $this->input->post('mat_amount',true);
            $nr_mat = 85;

            if (is_numeric($mat_amount))
            {
                $data = array(
                    'nr_mat' => $nr_mat,
                    'mat_amount' => $mat_amount,
                );
                $where = array('nr_mat' => $nr_mat);
                $this->Admin_model->update("STOR_AMOUNTS", $data, $where);

                $max = "id_docum_head";
                $docum = $this->Admin_model->get_max("DOCUM_HEAD", $max);
                $docum[0] = $docum[0]->id_docum_head;

                $data = array(
                    'nr_docum' => $docum[0],
                    'nr_mat' => $nr_mat,
                    'docum_value' => $mat_amount,
                    //'value_sign' => $this->input->post('value_sign', true),
                );
                $this->Admin_model->create("DOCUM_ITEM", $data);

                $data = array(
                    'nr_docum' => $docum[0],
                    'docum_type' => 'WZ',
                    'docum_desc' => 'WZ techniczna',
                );
                $this->Admin_model->create("DOCUM_HEAD", $data);

                $this->session->set_flashdata('alert', "Utworzono dokument WZ");
                redirect('account');
            }
            else
            {
                $this->session->set_flashdata('alert', "Liczba wydawanego towaru musi być wartością numeryczną!");
            }


        }
        $data['indk_mwym'] = $this->Admin_model->get("VIEW_ORDER");
        $data['validation'] = $this->session->flashdata('alert');
        $this->twig->display('admin/docs/create_wz',$data);
    }
    public function show($id)
    {
        $where = array('nr_docum' => $id);
        $data['zam'] = $this->Admin_model->get_where("VIEW_DOCS", array('nr_docum' => $id),$where);

        if(empty($data['zam']))
        {
            $this->session->set_flashdata('alert', "Podany dokument nie istnieje!");
            redirect('account');
        }
        $this->twig->display('admin/docs/view_docum',$data);
    }
    public function pdf()
    {
    }

    public function zm()
    {
        $total_rows = $this->Admin_model->num_rows("DOCUM_HEAD");
        $start_index = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0 ;
        $config['total_rows'] = $total_rows;
        $config['per_page'] = 10;
        $config['uri_segment'] = 4;
        $config['base_url'] = base_url('admin/order/zm');
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
        $data['views'] = $this->Admin_model->get_where("DOCUM_HEAD", array('docum_type' => 'ZM'),$config['per_page'],$start_index);
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();
        $data['validation'] = $this->session->flashdata('alert');
        $this->twig->display('site/docs/list_order',$data);
    }
    public function pz()
    {
        $total_rows = $this->Admin_model->num_rows("DOCUM_HEAD");
        $start_index = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0 ;
        $config['total_rows'] = $total_rows;
        $config['per_page'] = 10;
        $config['uri_segment'] = 4;
        $config['base_url'] = base_url('admin/order/pz');
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
        $data['views'] = $this->Admin_model->get_where("DOCUM_HEAD", array('docum_type' => 'PZ'),$config['per_page'],$start_index);
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();
        $data['validation'] = $this->session->flashdata('alert');
        $this->twig->display('site/docs/list_pz',$data);
    }
    public function wz()
    {
        $total_rows = $this->Admin_model->num_rows("DOCUM_HEAD");
        $start_index = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0 ;
        $config['total_rows'] = $total_rows;
        $config['per_page'] = 10;
        $config['uri_segment'] = 4;
        $config['base_url'] = base_url('admin/order/wz');
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
        $data['views'] = $this->Admin_model->get_where("DOCUM_HEAD", array('docum_type' => 'WZ'),$config['per_page'],$start_index);
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();
        $data['validation'] = $this->session->flashdata('alert');
        $this->twig->display('site/docs/list_wz',$data);
    }
}