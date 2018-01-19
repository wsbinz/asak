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

            $data = array(
                'nr_docum' => $this->input->post('nr_docum', true),
                'docum_type' => $this->input->post('docum_type', true),
                'vend_name' => $this->input->post('vend_name', true),
                'docum_desc' => $this->input->post('docum_desc', true),
            );
            $this->Admin_model->create("DOCUM_HEAD", $data);

            $data = array(
                //'value_sign' => $this->input->post('value_sign', true),
            );

            $this->Admin_model->create("DOCUM_ITEM", $data);
        }

        $data['docum_type'] = $this->Admin_model->get("DOCUM_HEAD");
        $data['zam'] = $this->Admin_model->get("VIEW_ORDER");
        $data['dost'] = $this->Admin_model->get('VEND');
        $data['validation'] = $this->session->flashdata('alert');
        $this->twig->display('admin/product/order_products',$data);
    }
    public function order()
    {
        $data['indk_mwym'] = $this->Admin_model->get("MNAME");
        $zam = $this->input->post('zam',true);
        if (!empty($zam))
        {
            if (is_numeric($zam))
            {
                if (($zam >= 0))
                {
                    $data = array(
                        //'nr_docum' => $?,
                        //'nr_mat' => $?,
                        'docum_value' => $zam,
                    );
                    $this->Admin_model->create("DOCUM_ITEM", $data);
                    $this->session->set_flashdata('alert', "Zamówienie zostało wysłane");
                }
                else {
                    $this->session->set_flashdata('alert', "Liczba zamawianego towaru musi być równa lub większa od zera!");
                    $data['ilosc'] = $zam;
                    $this->twig->display('admin/product/order_products_cd', $data);
                }
            } else {
                $this->session->set_flashdata('alert', "Liczba do zamówienia musi być wartością numeryczną!");
                $data['ilosc'] = $zam;
                $this->twig->display('admin/product/order_products_cd', $data);
            }
        }
        $data['validation'] = $this->session->flashdata('alert');
        $this->twig->display('admin/product/order_products_cd', $data);
    }
    public function pz()
    {
        $total_rows = $this->Admin_model->num_rows("DOCUM_HEAD");
        $start_index = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0 ;
        $config['total_rows'] = $total_rows;
        $config['per_page'] = 4;
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
        $data['views'] = $this->Admin_model->get("DOCUM_HEAD",$config['per_page'],$start_index);
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();
        $data['validation'] = $this->session->flashdata('alert');
        $this->twig->display('site/product/list_pz',$data);
    }
    public function show($id)
    {
        if(is_numeric($id)) {
            $where = array('nr_docum' => $id);
            $data['docum_item'] = $this->Admin_model->get_single("DOCUM_ITEM", $where);
            $data['validation'] = $this->session->flashdata('alert');
            $this->twig->display('admin/product/show', $data);
        }
        else
        {
            $this->session->set_flashdata('alert', "Podany dokument nie istnieje !");
            redirect(base_url("admin/product/pz"));
        }
    }
    public function pdf()
    {
    }
}